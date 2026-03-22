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

		// Prepare cumulative access check
		$is_allowed_visibility = false;
		if ( $visibility === 'all' ) {
			$is_allowed_visibility = true; // Everyone can see 'all'
		} elseif ( $visibility === 'pro' ) {
			$is_allowed_visibility = in_array( $user_plan, [ 'pro', 'agency' ] );
		} elseif ( $visibility === 'agency' ) {
			$is_allowed_visibility = ( $user_plan === 'agency' );
		}

		if ( $author_is_admin ) {
			// Admin Uploads: For direct users (no parent agency) OR Agency Owners
			if ( $parent_agency === 0 || $user_plan === 'agency' ) {
				return $is_allowed_visibility;
			}
		} elseif ( $author_plan === 'agency' ) {
			// Agency Owner Uploaded Rules: For their referred users only
			if ( $parent_agency === $author_id ) {
				return $is_allowed_visibility;
			}
		}

		return false;
	}

	public function get_access_query_args( $user_id_current, $is_generated_filter = null ) {
		$user_plan = strtolower( get_user_meta( $user_id_current, 'ffp_user_plan', true ) ?: 'free' );
		$parent_agency = (int) get_user_meta( $user_id_current, 'ffp_parent_agency', true );
		$is_admin = user_can( $user_id_current, 'manage_options' );

		if ( $is_admin && $is_generated_filter === null ) {
			return [ 'post_type' => 'attachment', 'post_status' => 'inherit', 'posts_per_page' => -1 ];
		}

		$meta_query = [ 'relation' => 'OR' ];

		// 1. Always show user's own files (Robust check)
		$meta_query[] = [
			'key'   => '_ffp_author_id',
			'value' => (int) $user_id_current,
			'type'  => 'NUMERIC'
		];

		// Define visibility mapping based on user plan (Cumulative Access)
		$allowed_visibilities = [ 'all' ];
		if ( $user_plan === 'pro' ) {
			$allowed_visibilities[] = 'pro';
		} elseif ( $user_plan === 'agency' ) {
			$allowed_visibilities[] = 'pro';
			$allowed_visibilities[] = 'agency';
		}

		// 2. Admin Files Rules
		if ( $parent_agency === 0 || $user_plan === 'agency' ) {
			// Direct users (Free/Pro) or Agency Owners see relevant Admin files
			$meta_query[] = [
				'relation' => 'AND',
				[ 'key' => '_ffp_is_admin_file', 'value' => '1' ],
				[ 'key' => 'ffp_vault_visibility', 'value' => $allowed_visibilities, 'compare' => 'IN' ]
			];
		}

		// 3. Agency Files Rules
		if ( $parent_agency > 0 ) {
			// Referred sub-users see relevant files from their Agency parent
			$meta_query[] = [
				'relation' => 'AND',
				[ 'key' => '_ffp_author_id', 'value' => $parent_agency, 'type' => 'NUMERIC' ],
				[ 'key' => 'ffp_vault_visibility', 'value' => $allowed_visibilities, 'compare' => 'IN' ]
			];
		}

		// 4. Agency Owners see files belonging to their team (Exclude generated docs from team view)
		if ( $user_plan === 'agency' ) {
			$meta_query[] = [
				'relation' => 'AND',
				[ 'key' => '_ffp_author_parent', 'value' => (int) $user_id_current, 'type' => 'NUMERIC' ],
				[ 'key' => '_ffp_is_generated_doc', 'compare' => 'NOT EXISTS' ]
			];
		}

		// Handle Generated Filter
		$final_meta_query = [ 'relation' => 'AND' ];
		$final_meta_query[] = $meta_query;

		if ( $is_generated_filter === true ) {
			$final_meta_query[] = [ 'key' => '_ffp_is_generated_doc', 'value' => '1' ];
		} elseif ( $is_generated_filter === false ) {
			$final_meta_query[] = [ 'key' => '_ffp_is_generated_doc', 'compare' => 'NOT EXISTS' ];
		}

		return [
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'numberposts'    => -1,
			'posts_per_page' => -1,
			'meta_query'     => $final_meta_query
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
