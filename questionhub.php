<?php
/**
 * Plugin Name: QuestionHub
 * Plugin URI:  https://github.com/PairDevs/QuestionHub
 * Description: A modern Question & Answer plugin for WordPress with frontend question submission, AJAX replies, phone number login, search, views, badges, and beautiful UI.
 * Version:     1.0.0
 * Author:      PairDevs
 * Author URI:  https://github.com/PairDevs/QuestionHub
 * License:     GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: questionhub
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 7.4
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main QuestionHub class.
 *
 * Uses the singleton pattern, defines constants, loads Composer autoload,
 * and initialises all hooks — mirroring PrimeKit Addons architecture.
 *
 * @package QuestionHub
 * @since   1.0.0
 */
final class QuestionHub {

	/**
	 * Singleton instance.
	 *
	 * @var QuestionHub|null
	 */
	private static $instance = null;

	/**
	 * Constructor — private to enforce singleton.
	 *
	 * @since 1.0.0
	 */
	private function __construct() {
		$this->define_constants();
		$this->include_files();
		$this->init_hooks();
	}

	/**
	 * Returns the singleton instance.
	 *
	 * @since  1.0.0
	 * @return QuestionHub
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Defines all plugin constants.
	 *
	 * @since 1.0.0
	 */
	private function define_constants() {
		define( 'QUESTIONHUB_VERSION',  '1.0.0' );
		define( 'QUESTIONHUB_FILE',     __FILE__ );
		define( 'QUESTIONHUB_PATH',     plugin_dir_path( __FILE__ ) );
		define( 'QUESTIONHUB_URL',      plugin_dir_url( __FILE__ ) );
		define( 'QUESTIONHUB_BASENAME', plugin_basename( __FILE__ ) );
		define( 'QUESTIONHUB_NAME',     'QuestionHub' );
	}

	/**
	 * Loads Composer autoloader.
	 *
	 * @since 1.0.0
	 */
	private function include_files() {
		if ( file_exists( QUESTIONHUB_PATH . 'vendor/autoload.php' ) ) {
			require_once QUESTIONHUB_PATH . 'vendor/autoload.php';
		}
	}

	/**
	 * Registers WordPress hooks.
	 *
	 * @since 1.0.0
	 */
	private function init_hooks() {
		add_action( 'plugins_loaded', [ $this, 'plugin_loaded' ] );
		add_action( 'init',           [ $this, 'register_textdomain' ] );

		register_activation_hook( QUESTIONHUB_FILE,   [ $this, 'activate' ] );
		register_deactivation_hook( QUESTIONHUB_FILE, [ $this, 'deactivate' ] );
	}

	/**
	 * Fires after all plugins are loaded.
	 * Bootstraps the Manager which spins up Admin and Frontend.
	 *
	 * @since 1.0.0
	 */
	public function plugin_loaded() {
		if ( class_exists( 'QuestionHub\\Manager' ) ) {
			new \QuestionHub\Manager();
		}

		/**
		 * Fires after QuestionHub is fully loaded.
		 *
		 * @since 1.0.0
		 */
		do_action( 'questionhub_loaded' );
	}

	/**
	 * Loads the plugin text domain.
	 *
	 * @since 1.0.0
	 */
	public function register_textdomain() {
		load_plugin_textdomain(
			'questionhub',
			false,
			dirname( plugin_basename( __FILE__ ) ) . '/languages'
		);
	}

	/**
	 * Plugin activation handler.
	 *
	 * @since 1.0.0
	 */
	public function activate() {
		\QuestionHub\Activate::activate();
	}

	/**
	 * Plugin deactivation handler.
	 *
	 * @since 1.0.0
	 */
	public function deactivate() {
		\QuestionHub\Deactivate::deactivate();
	}
}

/**
 * Initialises the QuestionHub plugin.
 *
 * @since  1.0.0
 * @return QuestionHub
 */
if ( ! function_exists( 'questionhub_initialize' ) ) {
	function questionhub_initialize() {
		return QuestionHub::get_instance();
	}

	questionhub_initialize();
}
