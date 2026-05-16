<?php
/**
 * AJAX: Submit Question.
 *
 * @package QuestionHub\Frontend\Inc\Ajax
 * @since   1.0.0
 */

namespace QuestionHub\Frontend\Inc\Ajax;

use QuestionHub\Frontend\Inc\Questions\QuestionService;
use QuestionHub\Frontend\Inc\Helpers\Response;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SubmitQuestion {

	public function __construct() {
		add_action( 'wp_ajax_questionhub_submit_question', [ $this, 'handle' ] );
	}

	public function handle(): void {
		check_ajax_referer( 'questionhub_nonce', 'nonce' );

		if ( ! is_user_logged_in() ) {
			Response::die_error( __( 'You must be logged in to ask a question.', 'questionhub' ) );
		}

		$title   = isset( $_POST['title'] ) ? sanitize_text_field( wp_unslash( $_POST['title'] ) ) : '';
		$content = isset( $_POST['content'] ) ? wp_kses_post( wp_unslash( $_POST['content'] ) ) : '';
		$cats    = isset( $_POST['categories'] ) ? array_map( 'absint', wp_unslash( (array) $_POST['categories'] ) ) : [];
		$tags    = isset( $_POST['tags'] ) ? sanitize_text_field( wp_unslash( $_POST['tags'] ) ) : '';

		if ( empty( $title ) ) {
			Response::die_error( __( 'Question title is required.', 'questionhub' ) );
		}

		$service = new QuestionService();
		$result  = $service->submit( [
			'title'      => $title,
			'content'    => $content,
			'categories' => $cats,
			'tags'       => $tags,
			'author_id'  => get_current_user_id(),
		] );

		if ( is_wp_error( $result ) ) {
			Response::die_error( $result->get_error_message() );
		}

		Response::success(
			__( 'Your question has been submitted successfully.', 'questionhub' ),
			[ 'post_id' => $result ]
		);
	}
}
