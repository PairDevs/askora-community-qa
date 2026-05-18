<?php
/**
 * AJAX: Vote on question or answer.
 *
 * @package ASKORA\Frontend\Inc\Ajax
 * @since   1.0.0
 */

namespace ASKORA\Frontend\Inc\Ajax;

use ASKORA\Frontend\Inc\Questions\VoteManager;
use ASKORA\Frontend\Inc\Helpers\Response;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Vote {

	public function __construct() {
		add_action( 'wp_ajax_askora_vote', [ $this, 'handle' ] );
	}

	public function handle(): void {
		check_ajax_referer( 'askora_nonce', 'nonce' );

		if ( ! is_user_logged_in() ) {
			Response::die_error( __( 'You must be logged in to vote.', 'askora-community-qa' ) );
		}

		$type       = isset( $_POST['type'] ) ? sanitize_key( wp_unslash( $_POST['type'] ) ) : 'question';
		$id         = isset( $_POST['id'] ) ? absint( wp_unslash( $_POST['id'] ) ) : 0;
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
