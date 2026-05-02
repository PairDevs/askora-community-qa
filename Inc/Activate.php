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

		// Register CPT and flush rewrite rules.
		flush_rewrite_rules();
	}
}
