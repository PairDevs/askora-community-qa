<?php
/**
 * AJAX: Mark best answer.
 *
 * @package QuestionHub\Frontend\Inc\Ajax
 * @since   1.0.0
 */

namespace QuestionHub\Frontend\Inc\Ajax;

use QuestionHub\Frontend\Inc\Helpers\Permission;
use QuestionHub\Frontend\Inc\Helpers\Response;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BestAnswer {

	public function __construct() {
		add_action( 'wp_ajax_questionhub_best_answer', [ $this, 'handle' ] );
	}

	public function handle(): void {
		check_ajax_referer( 'questionhub_nonce', 'nonce' );

		$post_id    = absint( $_POST['post_id'] ?? 0 );
		$comment_id = absint( $_POST['comment_id'] ?? 0 );

		if ( ! Permission::can_mark_best_answer( $post_id ) ) {
			Response::die_error( __( 'You are not allowed to mark the best answer.', 'questionhub' ) );
		}

		// Clear previous best answer.
		$prev = (int) get_post_meta( $post_id, '_questionhub_best_answer', true );
		if ( $prev ) {
			delete_comment_meta( $prev, '_questionhub_is_best_answer' );
		}

		update_post_meta( $post_id, '_questionhub_best_answer', $comment_id );
		update_comment_meta( $comment_id, '_questionhub_is_best_answer', 1 );

		Response::success( __( 'Best answer marked.', 'questionhub' ), [ 'comment_id' => $comment_id ] );
	}
}
