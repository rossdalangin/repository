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
		// Administrators always have access
		if ( current_user_can( 'manage_options' ) ) {
			return true;
		}

		// Check file-specific visibility meta
		$visibility = get_post_meta( $attachment_id, 'ffp_vault_visibility', true ) ?: 'all';
		$category   = get_post_meta( $attachment_id, 'ffp_vault_category', true ) ?: '';
		$plugin     = \FreelanceFlowPro\Core\Plugin::instance();
		$user_id    = get_current_user_id();

		// Priority Category Rule: Agency category always restricts to Agency users
		if ( $category === 'agency' ) {
			return $plugin->check_plan_access( 'agency' );
		}

		if ( $visibility === 'admin' ) {
			// Private/Admin files: Only the uploader
			return (int) get_post_field('post_author', $attachment_id) === $user_id;
		}

		if ( $visibility === 'all' ) {
			// Public files: Accessible to all logged-in users
			return is_user_logged_in();
		}

		if ( $visibility === 'agency' ) {
			return $plugin->check_plan_access( 'agency' );
		}

		if ( $visibility === 'pro' ) {
			return $plugin->check_plan_access( 'pro' );
		}

		// Verify the signed token for non-admins to prevent hotlinking
		if ( ! wp_verify_nonce( $token, 'ffp_vault_file_' . $attachment_id ) ) {
			return false;
		}

		return true;
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
