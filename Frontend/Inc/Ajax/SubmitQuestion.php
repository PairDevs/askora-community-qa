<?php
/**
 * AJAX: Submit Question.
 *
 * @package ASKORA\Frontend\Inc\Ajax
 * @since   1.0.0
 */

namespace ASKORA\Frontend\Inc\Ajax;

use ASKORA\Frontend\Inc\Helpers\Permission;
use ASKORA\Frontend\Inc\Questions\QuestionService;
use ASKORA\Frontend\Inc\Helpers\Response;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SubmitQuestion {

	public function __construct() {
		add_action( 'wp_ajax_askora_submit_question',        [ $this, 'handle' ] );
		// Guests are only let through when "Require Login to Ask" is switched off
		// (enforced by Permission::can_submit_question() below).
		add_action( 'wp_ajax_nopriv_askora_submit_question', [ $this, 'handle' ] );
	}

	public function handle(): void {
		check_ajax_referer( 'askora_nonce', 'nonce' );

		if ( ! Permission::can_submit_question() ) {
			Response::die_error( __( 'You must be logged in to ask a question.', 'askora-community-qa' ) );
		}

		$title   = isset( $_POST['title'] ) ? sanitize_text_field( wp_unslash( $_POST['title'] ) ) : '';
		$content = isset( $_POST['content'] ) ? wp_kses_post( wp_unslash( $_POST['content'] ) ) : '';
		$cats    = isset( $_POST['categories'] ) ? array_map( 'absint', wp_unslash( (array) $_POST['categories'] ) ) : [];
		$tags    = isset( $_POST['tags'] ) ? sanitize_text_field( wp_unslash( $_POST['tags'] ) ) : '';

		if ( empty( $title ) ) {
			Response::die_error( __( 'Question title is required.', 'askora-community-qa' ) );
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
			__( 'Your question has been submitted successfully.', 'askora-community-qa' ),
			[ 'post_id' => $result ]
		);
	}
}
