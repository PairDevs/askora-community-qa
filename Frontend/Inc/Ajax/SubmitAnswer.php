<?php
/**
 * AJAX: Submit Answer.
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

class SubmitAnswer {

	public function __construct() {
		add_action( 'wp_ajax_questionhub_submit_answer',        [ $this, 'handle' ] );
		add_action( 'wp_ajax_nopriv_questionhub_submit_answer', [ $this, 'handle' ] );
	}

	public function handle(): void {
		check_ajax_referer( 'questionhub_nonce', 'nonce' );

		if ( ! Permission::can_reply() ) {
			Response::die_error( __( 'You must be logged in to submit an answer.', 'questionhub' ) );
		}

		$post_id = isset( $_POST['post_id'] ) ? absint( wp_unslash( $_POST['post_id'] ) ) : 0;
		$content = isset( $_POST['content'] ) ? wp_kses_post( wp_unslash( $_POST['content'] ) ) : '';

		if ( ! $post_id || empty( trim( $content ) ) ) {
			Response::die_error( __( 'Answer content is required.', 'questionhub' ) );
		}

		$post = get_post( $post_id );
		if ( ! $post || 'questions' !== $post->post_type ) {
			Response::die_error( __( 'Invalid question.', 'questionhub' ) );
		}

		do_action( 'questionhub_before_answer_form' );

		$user    = is_user_logged_in() ? wp_get_current_user() : null;
		$comment = [
			'comment_post_ID'  => $post_id,
			'comment_content'  => $content,
			'comment_approved' => 1,
			'comment_author'   => $user ? $user->display_name : __( 'Guest', 'questionhub' ),
			'comment_author_email' => $user ? $user->user_email : '',
			'user_id'          => $user ? $user->ID : 0,
		];

		$comment_id = wp_insert_comment( $comment );

		do_action( 'questionhub_after_answer_form' );

		if ( ! $comment_id ) {
			Response::die_error( __( 'Failed to submit answer.', 'questionhub' ) );
		}

		Response::success(
			__( 'Your answer has been submitted.', 'questionhub' ),
			[ 'comment_id' => $comment_id ]
		);
	}
}
