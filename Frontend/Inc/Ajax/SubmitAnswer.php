<?php
/**
 * AJAX: Submit Answer.
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

class SubmitAnswer {

	public function __construct() {
		add_action( 'wp_ajax_askora_submit_answer',        [ $this, 'handle' ] );
		add_action( 'wp_ajax_nopriv_askora_submit_answer', [ $this, 'handle' ] );
	}

	public function handle(): void {
		check_ajax_referer( 'askora_nonce', 'nonce' );

		if ( ! Permission::can_reply() ) {
			Response::die_error( __( 'You must be logged in to submit an answer.', 'askora-community-qa' ) );
		}

		$post_id = isset( $_POST['post_id'] ) ? absint( wp_unslash( $_POST['post_id'] ) ) : 0;
		$content = isset( $_POST['content'] ) ? wp_kses_post( wp_unslash( $_POST['content'] ) ) : '';

		if ( ! $post_id || empty( trim( $content ) ) ) {
			Response::die_error( __( 'Answer content is required.', 'askora-community-qa' ) );
		}

		$post = get_post( $post_id );
		if ( ! $post || 'questions' !== $post->post_type ) {
			Response::die_error( __( 'Invalid question.', 'askora-community-qa' ) );
		}

		do_action( 'askora_before_answer_form' );

		$user    = is_user_logged_in() ? wp_get_current_user() : null;
		$comment = [
			'comment_post_ID'  => $post_id,
			'comment_content'  => $content,
			'comment_approved' => 1,
			'comment_author'   => $user ? $user->display_name : __( 'Guest', 'askora-community-qa' ),
			'comment_author_email' => $user ? $user->user_email : '',
			'user_id'          => $user ? $user->ID : 0,
		];

		$comment_id = wp_insert_comment( $comment );

		do_action( 'askora_after_answer_form' );

		if ( ! $comment_id ) {
			Response::die_error( __( 'Failed to submit answer.', 'askora-community-qa' ) );
		}

		Response::success(
			__( 'Your answer has been submitted.', 'askora-community-qa' ),
			[ 'comment_id' => $comment_id ]
		);
	}
}
