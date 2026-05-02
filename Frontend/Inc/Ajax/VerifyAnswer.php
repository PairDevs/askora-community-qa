<?php
/**
 * AJAX: Admin-verify / unverify an answer.
 *
 * Meta key: _questionhub_verified (1 = verified, removed when unverified).
 * Only users with manage_options capability may call this.
 *
 * @package QuestionHub\Frontend\Inc\Ajax
 * @since   1.0.0
 */

namespace QuestionHub\Frontend\Inc\Ajax;

use QuestionHub\Frontend\Inc\Helpers\Response;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class VerifyAnswer {

	public function __construct() {
		add_action( 'wp_ajax_questionhub_verify_answer', [ $this, 'handle' ] );
		// No nopriv — admins only.
	}

	public function handle(): void {
		check_ajax_referer( 'questionhub_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			Response::die_error( __( 'Only administrators can verify answers.', 'questionhub' ) );
		}

		$comment_id = absint( $_POST['comment_id'] ?? 0 );

		if ( ! $comment_id || ! get_comment( $comment_id ) ) {
			Response::die_error( __( 'Invalid answer ID.', 'questionhub' ) );
		}

		$is_verified = (int) get_comment_meta( $comment_id, '_questionhub_verified', true );

		if ( $is_verified ) {
			// Toggle off — remove verification.
			delete_comment_meta( $comment_id, '_questionhub_verified' );
			delete_comment_meta( $comment_id, '_questionhub_verified_by' );
			delete_comment_meta( $comment_id, '_questionhub_verified_at' );

			Response::success(
				__( 'Verification removed.', 'questionhub' ),
				[ 'verified' => false, 'comment_id' => $comment_id ]
			);
		} else {
			// Verify the answer.
			update_comment_meta( $comment_id, '_questionhub_verified', 1 );
			update_comment_meta( $comment_id, '_questionhub_verified_by', get_current_user_id() );
			update_comment_meta( $comment_id, '_questionhub_verified_at', current_time( 'mysql' ) );

			Response::success(
				__( 'Answer verified.', 'questionhub' ),
				[ 'verified' => true, 'comment_id' => $comment_id ]
			);
		}
	}
}
