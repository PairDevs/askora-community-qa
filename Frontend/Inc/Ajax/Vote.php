<?php
/**
 * AJAX: Vote on question or answer.
 *
 * @package QuestionHub\Frontend\Inc\Ajax
 * @since   1.0.0
 */

namespace QuestionHub\Frontend\Inc\Ajax;

use QuestionHub\Frontend\Inc\Questions\VoteManager;
use QuestionHub\Frontend\Inc\Helpers\Response;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Vote {

	public function __construct() {
		add_action( 'wp_ajax_questionhub_vote', [ $this, 'handle' ] );
	}

	public function handle(): void {
		check_ajax_referer( 'questionhub_nonce', 'nonce' );

		if ( ! is_user_logged_in() ) {
			Response::die_error( __( 'You must be logged in to vote.', 'questionhub' ) );
		}

		$type       = sanitize_key( $_POST['type'] ?? 'question' );
		$id         = absint( $_POST['id'] ?? 0 );
		$user_id    = get_current_user_id();
		$manager    = new VoteManager();

		if ( 'question' === $type ) {
			$result = $manager->vote_question( $id, $user_id );
		} else {
			$result = $manager->vote_answer( $id, $user_id );
		}

		if ( is_wp_error( $result ) ) {
			Response::die_error( $result->get_error_message() );
		}

		Response::success( '', [ 'votes' => $result ] );
	}
}
