<?php
/**
 * Question service — business logic layer.
 *
 * @package QuestionHub\Frontend\Inc\Questions
 * @since   1.0.0
 */

namespace QuestionHub\Frontend\Inc\Questions;

use QuestionHub\Frontend\Inc\Helpers\Permission;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class QuestionService {

	private QuestionRepository $repo;

	public function __construct() {
		$this->repo = new QuestionRepository();
	}

	public function submit( array $data ) {
		if ( ! Permission::can_submit_question() ) {
			return new \WP_Error( 'not_allowed', __( 'You must be logged in to ask a question.', 'questionhub' ) );
		}
		if ( empty( trim( $data['title'] ?? '' ) ) ) {
			return new \WP_Error( 'empty_title', __( 'Question title is required.', 'questionhub' ) );
		}

		do_action( 'questionhub_before_question_form' );
		$result = $this->repo->create_question( $data );
		do_action( 'questionhub_after_question_form' );

		return $result;
	}

	public function get_list( array $args = [] ): array {
		return $this->repo->get_questions( $args );
	}

	public function get( int $post_id ): ?\WP_Post {
		return $this->repo->get_question( $post_id );
	}
}
