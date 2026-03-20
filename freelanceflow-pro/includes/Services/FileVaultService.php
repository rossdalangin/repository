<?php

namespace FreelanceFlowPro\Services;

/**
 * File Vault Security Service
 */
class FileVaultService {

	public function get_secure_url( $attachment_id ) {
		// Use a nonce and timestamp to create a signed URL
		$user_id = get_current_user_id();
		if ( ! $user_id ) return '';

		$nonce = wp_create_nonce( 'ffp_vault_file_' . $attachment_id );
		return add_query_arg( [
			'ffp_vault_id' => $attachment_id,
			'ffp_token'    => $nonce,
			'ffp_uid'      => $user_id
		], home_url('/') );
	}

	public function verify_access( $attachment_id, $token, $uid ) {
		$user_id_current = get_current_user_id();
		if ( ! $user_id_current ) return false;

		// Administrators always have access
		if ( current_user_can( 'manage_options' ) ) {
			return true;
		}

		$attachment = get_post( $attachment_id );
		if ( ! $attachment ) return false;

		$author_id = (int) $attachment->post_author;
		$visibility = get_post_meta( $attachment_id, 'ffp_vault_visibility', true ) ?: 'admin';
		$user_plan = get_user_meta( $user_id_current, 'ffp_user_plan', true ) ?: 'free';
		$parent_agency = (int) get_user_meta( $user_id_current, 'ffp_parent_agency', true );

		// 1. Private: Only uploader
		if ( $visibility === 'admin' ) {
			return $author_id === $user_id_current;
		}

		$author_is_admin = user_can( $author_id, 'manage_options' );
		$author_plan = get_user_meta( $author_id, 'ffp_user_plan', true );

		if ( $author_is_admin ) {
			// Admin Uploaded Rules
			if ( $visibility === 'all' ) {
				// 2. Admin Public -> Only Free users NOT in agency
				return ( $user_plan === 'free' && $parent_agency === 0 );
			}
			if ( $visibility === 'pro' ) {
				// 3. Admin Premium -> Only Pro users NOT in agency
				return ( $user_plan === 'pro' && $parent_agency === 0 );
			}
			if ( $visibility === 'agency' ) {
				// 4. Admin Agency -> Only Agency accounts
				return ( $user_plan === 'agency' );
			}
		} elseif ( $author_plan === 'agency' ) {
			// Agency Uploaded Rules
			if ( $visibility === 'all' ) {
				// 5. Agency Public -> Only Free users OF THAT Agency
				return ( $user_plan === 'free' && $parent_agency === $author_id );
			}
			if ( $visibility === 'pro' ) {
				// 6. Agency Premium -> Only Pro users OF THAT Agency
				return ( $user_plan === 'pro' && $parent_agency === $author_id );
			}
		}

		// Default: Deny if no specific rule matched
		return false;
	}

	public function get_access_query_args( $user_id_current ) {
		$user_plan = get_user_meta( $user_id_current, 'ffp_user_plan', true ) ?: 'free';
		$parent_agency = (int) get_user_meta( $user_id_current, 'ffp_parent_agency', true );
		$is_admin = user_can( $user_id_current, 'manage_options' );

		if ( $is_admin ) {
			return [ 'post_type' => 'attachment', 'post_status' => 'inherit', 'posts_per_page' => -1 ];
		}

		// Complex logic to mirror verify_access in a SQL query
		// 1. Files where user is author
		// 2. Admin files where visibility matches user criteria
		// 3. Agency files where visibility matches user criteria AND parent matches

		$meta_query = [ 'relation' => 'OR' ];

		// Admin Files Logic
		$admin_users = get_users([ 'role' => 'administrator', 'fields' => 'ID' ]);
		if ( ! empty($admin_users) ) {
			if ( $user_plan === 'free' && $parent_agency === 0 ) {
				$meta_query[] = [
					'relation' => 'AND',
					[ 'key' => 'ffp_vault_visibility', 'value' => 'all' ],
					[ 'key' => '_ffp_is_admin_file', 'value' => '1' ] // Helper meta needed
				];
			}
			if ( $user_plan === 'pro' && $parent_agency === 0 ) {
				$meta_query[] = [
					'relation' => 'AND',
					[ 'key' => 'ffp_vault_visibility', 'value' => 'pro' ],
					[ 'key' => '_ffp_is_admin_file', 'value' => '1' ]
				];
			}
			if ( $user_plan === 'agency' ) {
				$meta_query[] = [
					'relation' => 'AND',
					[ 'key' => 'ffp_vault_visibility', 'value' => 'agency' ],
					[ 'key' => '_ffp_is_admin_file', 'value' => '1' ]
				];
			}
		}

		// Agency Owner Files Logic (Viewing their team's files)
		if ( $user_plan === 'agency' ) {
			$meta_query[] = [
				'key'   => '_ffp_author_parent',
				'value' => $user_id_current
			];
		}

		// Sub-user logic (Viewing public/pro files from their agency parent)
		if ( $parent_agency > 0 ) {
			if ( $user_plan === 'free' ) {
				$meta_query[] = [
					'relation' => 'AND',
					[ 'key' => 'ffp_vault_visibility', 'value' => 'all' ],
					[ 'key' => '_ffp_author_parent', 'value' => $parent_agency ]
				];
			}
			if ( $user_plan === 'pro' ) {
				$meta_query[] = [
					'relation' => 'AND',
					[ 'key' => 'ffp_vault_visibility', 'value' => 'pro' ],
					[ 'key' => '_ffp_author_parent', 'value' => $parent_agency ]
				];
			}
		}

		return [
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'posts_per_page' => -1,
			'author'         => $user_id_current, // Own files
			'meta_query'     => $meta_query
		];
	}

	public function handle_file_request() {
		if ( ! isset( $_GET['ffp_vault_id'] ) || ! isset( $_GET['ffp_token'] ) ) {
			return;
		}

		$id = absint( $_GET['ffp_vault_id'] );
		$token = sanitize_text_field( $_GET['ffp_token'] );
		$uid = absint( $_GET['ffp_uid'] );

		if ( $this->verify_access( $id, $token, $uid ) ) {
			$file_path = get_attached_file( $id );
			if ( file_exists( $file_path ) ) {
				$mime_type = wp_check_filetype( $file_path )['type'];
				header( 'Content-Type: ' . $mime_type );
				header( 'Content-Disposition: attachment; filename="' . basename( $file_path ) . '"' );
				readfile( $file_path );
				exit;
			}
		}

		wp_die( 'Access Denied' );
	}
}
