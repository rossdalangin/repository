<?php

namespace FreelanceFlowPro\Services;

/**
 * File Vault Security Service
 *
 * DESIGN DECISION: We store file associations in Post Meta (Attachment Meta)
 * rather than a custom table. This allows us to leverage WP's native
 * media library, attachment metadata, and WP_Query for performant
 * and compatible filtering.
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

		// 1. Administrators always have access
		if ( current_user_can( 'manage_options' ) ) {
			return true;
		}

		$attachment = get_post( $attachment_id );
		if ( ! $attachment ) return false;

		$author_id = (int) $attachment->post_author;

		// 2. Always allow owner
		if ( $author_id === $user_id_current ) {
			return true;
		}

		$visibility = get_post_meta( $attachment_id, 'ffp_vault_visibility', true ) ?: 'admin';
		$user_plan = get_user_meta( $user_id_current, 'ffp_user_plan', true ) ?: 'free';
		$parent_agency = (int) get_user_meta( $user_id_current, 'ffp_parent_agency', true );

		// 3. Agency Owner sees team files
		if ( $user_plan === 'agency' ) {
			$author_parent = (int) get_post_meta( $attachment_id, '_ffp_author_parent', true );
			if ( $author_parent === $user_id_current ) {
				return true;
			}
		}

		// 4. Private: Only uploader (Covered by Rule 2)
		if ( $visibility === 'admin' ) {
			return $author_id === $user_id_current;
		}

		$author_is_admin = user_can( $author_id, 'manage_options' );
		$author_plan = get_user_meta( $author_id, 'ffp_user_plan', true );

		if ( $author_is_admin ) {
			// Admin Uploads: For direct users AND Agency Owners (if tagged Agency)
			if ( $visibility === 'agency' ) {
				return ( $user_plan === 'agency' );
			}

			// For Free/Pro direct users (Agencies are also direct users but handled above)
			if ( $parent_agency !== 0 ) return false;

			if ( $visibility === 'all' ) {
				return ( $user_plan === 'free' );
			}
			if ( $visibility === 'pro' ) {
				return ( $user_plan === 'pro' );
			}
		} elseif ( $author_plan === 'agency' ) {
			// Agency Owner Uploaded Rules: For their referred users only
			if ( $parent_agency !== $author_id ) return false;

			if ( $visibility === 'all' ) {
				return ( $user_plan === 'free' );
			}
			if ( $visibility === 'pro' ) {
				return ( $user_plan === 'pro' );
			}
		}

		return false;
	}

	public function get_access_query_args( $user_id_current ) {
		$user_plan = strtolower( get_user_meta( $user_id_current, 'ffp_user_plan', true ) ?: 'free' );
		$parent_agency = (int) get_user_meta( $user_id_current, 'ffp_parent_agency', true );
		$is_admin = user_can( $user_id_current, 'manage_options' );

		if ( $is_admin ) {
			return [ 'post_type' => 'attachment', 'post_status' => 'inherit', 'posts_per_page' => -1 ];
		}

		$meta_query = [ 'relation' => 'OR' ];

		// 1. Always show user's own files (Robust check)
		$meta_query[] = [
			'key'   => '_ffp_author_id',
			'value' => (int) $user_id_current,
			'type'  => 'NUMERIC'
		];

		// Define visibility mapping based on user plan (Strict 1:1)
		$target_visibility = '';
		if ($user_plan === 'free') $target_visibility = 'all';
		elseif ($user_plan === 'pro') $target_visibility = 'pro';
		elseif ($user_plan === 'agency') $target_visibility = 'agency';

		// 2. Admin Files Rules
		if ( $user_plan === 'agency' ) {
			// Agency users see Admin files tagged 'agency'
			$meta_query[] = [
				'relation' => 'AND',
				[ 'key' => '_ffp_is_admin_file', 'value' => '1' ],
				[ 'key' => 'ffp_vault_visibility', 'value' => 'agency' ]
			];
		} elseif ( $parent_agency === 0 ) {
			// Direct Free/Pro users see relevant Admin files
			$meta_query[] = [
				'relation' => 'AND',
				[ 'key' => '_ffp_is_admin_file', 'value' => '1' ],
				[ 'key' => 'ffp_vault_visibility', 'value' => $target_visibility ]
			];
		}

		// 3. Agency Files Rules
		if ( $parent_agency > 0 ) {
			// Referred sub-users see relevant files from their Agency parent
			$meta_query[] = [
				'relation' => 'AND',
				[ 'key' => '_ffp_author_id', 'value' => $parent_agency, 'type' => 'NUMERIC' ],
				[ 'key' => 'ffp_vault_visibility', 'value' => $target_visibility ]
			];
		}

		// 4. Agency Owners see files belonging to their team
		if ( $user_plan === 'agency' ) {
			$meta_query[] = [
				'key'   => '_ffp_author_parent',
				'value' => (int) $user_id_current,
				'type'  => 'NUMERIC'
			];
		}

		return [
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'numberposts'    => -1,
			'posts_per_page' => -1,
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
