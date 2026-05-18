<?php
/**
 * AJAX: Mark best answer.
 *
 * @package ASKORA\Frontend\Inc\Ajax
 * @since   1.0.0
 */

namespace ASKORA\Frontend\Inc\Ajax;

use ASKORA\Frontend\Inc\Helpers\Permission;
use ASKORA\Frontend\Inc\Helpers\Response;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BestAnswer {

	public function __construct() {
		add_action( 'wp_ajax_askora_best_answer', [ $this, 'handle' ] );
	}

	public function handle(): void {
		check_ajax_referer( 'askora_nonce', 'nonce' );

		$post_id    = isset( $_POST['post_id'] ) ? absint( wp_unslash( $_POST['post_id'] ) ) : 0;
		$comment_id = isset( $_POST['comment_id'] ) ? absint( wp_unslash( $_POST['comment_id'] ) ) : 0;

		if ( ! Permission::can_mark_best_answer( $post_id ) ) {
			Response::die_error( __( 'You are not allowed to mark the best answer.', 'askora-community-qa' ) );
		}

		// Clear previous best answer.
		$prev = (int) get_post_meta( $post_id, '_askora_best_answer', true );
		if ( $prev ) {
			delete_comment_meta( $prev, '_askora_is_best_answer' );
		}

		update_post_meta( $post_id, '_askora_best_answer', $comment_id );
		update_comment_meta( $comment_id, '_askora_is_best_answer', 1 );

		Response::success( __( 'Best answer marked.', 'askora-community-qa' ), [ 'comment_id' => $comment_id ] );
	}
}
