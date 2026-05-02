<?php
/**
 * Badge renderer — role-based badges for Q&A participants.
 *
 * @package QuestionHub\Frontend\Inc\Comments
 * @since   1.0.0
 */

namespace QuestionHub\Frontend\Inc\Comments;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Badge {

	/**
	 * Returns HTML badge for a user on a given question post.
	 *
	 * @param int      $user_id User ID (0 = guest).
	 * @param int|null $post_id Question post ID for author check.
	 * @return string HTML badge.
	 * @since 1.0.0
	 */
	public static function get( int $user_id, ?int $post_id = null ): string {
		if ( 0 === $user_id ) {
			$type  = 'guest';
			$label = __( 'Guest', 'questionhub' );
		} elseif ( user_can( $user_id, 'manage_options' ) ) {
			$type  = 'admin';
			$label = __( 'Admin', 'questionhub' );
		} elseif ( user_can( $user_id, 'edit_others_posts' ) ) {
			$type  = 'moderator';
			$label = __( 'Moderator', 'questionhub' );
		} elseif ( $post_id && (int) get_post_field( 'post_author', $post_id ) === $user_id ) {
			$type  = 'author';
			$label = __( 'Author', 'questionhub' );
		} else {
			$type  = 'member';
			$label = __( 'Member', 'questionhub' );
		}

		/**
		 * Filters the badge label.
		 *
		 * @param string   $label   Badge label.
		 * @param int      $user_id User ID.
		 * @param int|null $post_id Post ID.
		 * @since 1.0.0
		 */
		$label = apply_filters( 'questionhub_badge_label', $label, $user_id, $post_id );

		return sprintf(
			'<span class="questionhub-badge questionhub-badge-%s">%s</span>',
			esc_attr( $type ),
			esc_html( $label )
		);
	}
}
