<?php
/**
 * AJAX: Admin-verify / unverify an answer.
 *
 * Meta key: _askora_verified (1 = verified, removed when unverified).
 * Only users with manage_options capability may call this.
 *
 * @package ASKORA\Frontend\Inc\Ajax
 * @since   1.0.0
 */

namespace ASKORA\Frontend\Inc\Ajax;

use ASKORA\Frontend\Inc\Helpers\Response;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class VerifyAnswer {

	public function __construct() {
		add_action( 'wp_ajax_askora_verify_answer', [ $this, 'handle' ] );
		// No nopriv — admins only.
	}

	public function handle(): void {
		check_ajax_referer( 'askora_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			Response::die_error( __( 'Only administrators can verify answers.', 'askora-community-qa' ) );
		}

		$comment_id = isset( $_POST['comment_id'] ) ? absint( wp_unslash( $_POST['comment_id'] ) ) : 0;

		if ( ! $comment_id || ! get_comment( $comment_id ) ) {
			Response::die_error( __( 'Invalid answer ID.', 'askora-community-qa' ) );
		}

		$is_verified = (int) get_comment_meta( $comment_id, '_askora_verified', true );

		if ( $is_verified ) {
			// Toggle off — remove verification.
			delete_comment_meta( $comment_id, '_askora_verified' );
			delete_comment_meta( $comment_id, '_askora_verified_by' );
			delete_comment_meta( $comment_id, '_askora_verified_at' );

			Response::success(
				__( 'Verification removed.', 'askora-community-qa' ),
				[ 'verified' => false, 'comment_id' => $comment_id ]
			);
		} else {
			// Verify the answer.
			update_comment_meta( $comment_id, '_askora_verified', 1 );
			update_comment_meta( $comment_id, '_askora_verified_by', get_current_user_id() );
			update_comment_meta( $comment_id, '_askora_verified_at', current_time( 'mysql' ) );

			Response::success(
				__( 'Answer verified.', 'askora-community-qa' ),
				[ 'verified' => true, 'comment_id' => $comment_id ]
			);
		}
	}
}
