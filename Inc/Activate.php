<?php
/**
 * Plugin activation class.
 *
 * @package QuestionHub
 * @since   1.0.0
 */

namespace QuestionHub;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Activate
 *
 * Handles tasks performed on plugin activation:
 * - Stores plugin version.
 * - Sets default options.
 * - Flushes rewrite rules.
 */
class Activate {

	/**
	 * Run on plugin activation.
	 *
	 * @since 1.0.0
	 */
	public static function activate() {
		// Store plugin version.
		update_option( 'questionhub_version', QUESTIONHUB_VERSION );

		// Set default settings if they don't exist.
		if ( ! get_option( 'questionhub_settings' ) ) {
			$defaults = [
				// General.
				'question_status'         => 'pending',
				'allow_guest_replies'     => 0,
				'require_login_to_ask'    => 1,
				'require_login_to_reply'  => 1,
				'enable_voting'           => 1,
				'enable_best_answer'      => 1,
				'enable_question_views'   => 1,
				'questions_per_page'      => 10,
				// Advanced.
				'enable_phone_auth'       => 1,
				'email_required'          => 0,
				'auto_login_after_reg'    => 1,
				'primary_color'           => '#6C63FF',
				'delete_data_on_uninstall' => 0,
			];
			update_option( 'questionhub_settings', $defaults );
		}

		// Auto-create draft pages with shortcodes (first activation only).
		if ( ! get_option( 'questionhub_pages' ) ) {
			self::create_pages();
		}

		// Register CPT and flush rewrite rules.
		flush_rewrite_rules();
	}

	/**
	 * Create draft pages with plugin shortcodes.
	 *
	 * @since 1.0.0
	 */
	private static function create_pages() {
		$pages = [
			'questions'    => [
				'title'     => __( 'Questions', 'questionhub' ),
				'shortcode' => '[questionhub_questions]',
			],
			'ask_question' => [
				'title'     => __( 'Ask a Question', 'questionhub' ),
				'shortcode' => '[questionhub_submit_form]',
			],
			'auth'         => [
				'title'     => __( 'Login / Register', 'questionhub' ),
				'shortcode' => '[questionhub_auth]',
			],
			'dashboard'    => [
				'title'     => __( 'My Dashboard', 'questionhub' ),
				'shortcode' => '[questionhub_dashboard]',
			],
		];

		$created = [];

		foreach ( $pages as $slug => $page ) {
			if ( get_page_by_path( $slug ) ) {
				continue;
			}

			$id = wp_insert_post( [
				'post_title'   => $page['title'],
				'post_name'    => $slug,
				'post_content' => $page['shortcode'],
				'post_status'  => 'draft',
				'post_type'    => 'page',
			] );

			if ( ! is_wp_error( $id ) ) {
				$created[ $slug ] = (int) $id;
			}
		}

		if ( ! empty( $created ) ) {
			update_option( 'questionhub_pages', $created );
		}
	}
}
