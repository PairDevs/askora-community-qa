<?php
/**
 * View counter — tracks and deduplicates question views.
 *
 * @package QuestionHub\Frontend\Inc\Questions
 * @since   1.0.0
 */

namespace QuestionHub\Frontend\Inc\Questions;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ViewCounter {

	public function __construct() {
		add_action( 'wp', [ $this, 'track_view' ] );
	}

	/**
	 * Increments view count on single question pages.
	 * Uses a cookie to deduplicate views per user session.
	 *
	 * @since 1.0.0
	 */
	public function track_view(): void {
		$settings = get_option( 'questionhub_settings', [] );
		if ( empty( $settings['enable_question_views'] ) ) {
			return;
		}

		if ( ! is_singular( 'questions' ) ) {
			return;
		}

		$post_id    = get_the_ID();
		$cookie_key = 'questionhub_viewed_' . $post_id;

		$questionhub_viewed_cookie = isset( $_COOKIE[ $cookie_key ] ) ? sanitize_text_field( wp_unslash( $_COOKIE[ $cookie_key ] ) ) : '';

		if ( '1' === $questionhub_viewed_cookie ) {
			return;
		}

		$repo = new QuestionRepository();
		$repo->increment_views( $post_id );

		// Set cookie for 24 hours to avoid duplicate counts.
		setcookie( $cookie_key, '1', time() + DAY_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true );
	}

	/**
	 * Returns the view count for a post.
	 *
	 * @param int $post_id Post ID.
	 * @return int
	 */
	public static function get( int $post_id ): int {
		return (int) get_post_meta( $post_id, '_questionhub_views', true );
	}
}
