<?php
/**
 * Permission helper.
 *
 * @package QuestionHub\Frontend\Inc\Helpers
 * @since   1.0.0
 */

namespace QuestionHub\Frontend\Inc\Helpers;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Permission
 *
 * Checks whether the current user can perform Q&A actions.
 */
class Permission {

	/**
	 * Returns plugin settings array.
	 *
	 * @return array
	 */
	private static function settings() {
		return get_option( 'questionhub_settings', [] );
	}

	public static function can_submit_question() {
		$settings = self::settings();
		if ( ! empty( $settings['require_login_to_ask'] ) ) {
			return is_user_logged_in();
		}
		return true;
	}

	public static function can_reply() {
		$settings = self::settings();
		if ( ! empty( $settings['require_login_to_reply'] ) ) {
			return is_user_logged_in();
		}
		if ( ! empty( $settings['allow_guest_replies'] ) ) {
			return true;
		}
		return is_user_logged_in();
	}

	public static function can_vote() {
		return is_user_logged_in();
	}

	public static function can_mark_best_answer( $post_id ) {
		if ( ! is_user_logged_in() ) {
			return false;
		}
		$user_id = get_current_user_id();
		$post    = get_post( $post_id );
		if ( current_user_can( 'manage_options' ) ) {
			return true;
		}
		return $post && (int) $post->post_author === $user_id;
	}
}
