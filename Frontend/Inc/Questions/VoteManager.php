<?php
/**
 * Vote manager — handles upvotes for questions and answers.
 *
 * @package ASKORA\Frontend\Inc\Questions
 * @since   1.0.0
 */

namespace ASKORA\Frontend\Inc\Questions;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class VoteManager {

	/**
	 * Upvote a question. Returns new vote count or WP_Error.
	 *
	 * @param int $post_id Post ID.
	 * @param int $user_id Voter user ID.
	 * @return int|\WP_Error
	 */
	public function vote_question( int $post_id, int $user_id ) {
		$post = get_post( $post_id );
		if ( ! $post || 'questions' !== $post->post_type || 'publish' !== $post->post_status ) {
			return new \WP_Error( 'invalid_question', __( 'Invalid question.', 'askora-community-qa' ) );
		}

		$voted_users = get_post_meta( $post_id, '_askora_voted_users', true );
		$voted_users = is_array( $voted_users ) ? array_map( 'intval', $voted_users ) : [];

		if ( in_array( $user_id, $voted_users, true ) ) {
			return new \WP_Error( 'already_voted', __( 'You have already voted on this question.', 'askora-community-qa' ) );
		}

		$voted_users[] = $user_id;
		update_post_meta( $post_id, '_askora_voted_users', $voted_users );

		// Derive the total from the voter list so the two can never drift apart
		// when requests overlap.
		$new = count( $voted_users );
		update_post_meta( $post_id, '_askora_votes', $new );

		return $new;
	}

	/**
	 * Upvote an answer (comment). Returns new vote count or WP_Error.
	 *
	 * @param int $comment_id Comment ID.
	 * @param int $user_id    Voter user ID.
	 * @return int|\WP_Error
	 */
	public function vote_answer( int $comment_id, int $user_id ) {
		$comment = get_comment( $comment_id );
		$post    = $comment ? get_post( (int) $comment->comment_post_ID ) : null;
		if ( ! $comment || '1' !== (string) $comment->comment_approved || ! $post || 'questions' !== $post->post_type || 'publish' !== $post->post_status ) {
			return new \WP_Error( 'invalid_answer', __( 'Invalid answer.', 'askora-community-qa' ) );
		}

		$voted_users = get_comment_meta( $comment_id, '_askora_answer_voted_users', true );
		$voted_users = is_array( $voted_users ) ? array_map( 'intval', $voted_users ) : [];

		if ( in_array( $user_id, $voted_users, true ) ) {
			return new \WP_Error( 'already_voted', __( 'You have already voted on this answer.', 'askora-community-qa' ) );
		}

		$voted_users[] = $user_id;
		update_comment_meta( $comment_id, '_askora_answer_voted_users', $voted_users );

		$new = count( $voted_users );
		update_comment_meta( $comment_id, '_askora_answer_votes', $new );

		return $new;
	}

	/**
	 * Check if a user has already voted on a question.
	 *
	 * @param int $post_id Post ID.
	 * @param int $user_id User ID.
	 * @return bool
	 */
	public function has_voted_question( int $post_id, int $user_id ): bool {
		$voted = get_post_meta( $post_id, '_askora_voted_users', true );
		return is_array( $voted ) && in_array( $user_id, $voted, true );
	}
}
