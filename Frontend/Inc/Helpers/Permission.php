<?php
/**
 * Permission helper.
 *
 * @package ASKORA\Frontend\Inc\Helpers
 * @since   1.0.0
 */

namespace ASKORA\Frontend\Inc\Helpers;

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
		return get_option( 'askora_settings', [] );
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

	/**
	 * Whether a boolean feature toggle is on. Missing keys default to enabled,
	 * matching the defaults written on activation.
	 *
	 * @param string $key Settings key.
	 * @return bool
	 */
	private static function feature_enabled( $key ) {
		$settings = self::settings();
		return ! isset( $settings[ $key ] ) || ! empty( $settings[ $key ] );
	}

	public static function phone_auth_enabled() {
		return self::feature_enabled( 'enable_phone_auth' );
	}

	public static function voting_enabled() {
		return self::feature_enabled( 'enable_voting' );
	}

	public static function best_answer_enabled() {
		return self::feature_enabled( 'enable_best_answer' );
	}

	public static function can_vote() {
		return is_user_logged_in() && self::voting_enabled();
	}

	public static function can_mark_best_answer( $post_id ) {
		if ( ! is_user_logged_in() ) {
			return false;
		}
		$user_id = get_current_user_id();
		$post    = get_post( $post_id );
		if ( ! $post || 'questions' !== $post->post_type ) {
			return false;
		}
		if ( current_user_can( 'manage_options' ) ) {
			return true;
		}
		return $post && (int) $post->post_author === $user_id;
	}
}
