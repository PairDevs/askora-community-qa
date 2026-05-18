<?php
/**
 * Plugin Name: Askora Community Q&A
 * Plugin URI:  https://github.com/PairDevs/askora-community-qa
 * Description: A modern Question & Answer plugin for WordPress with frontend question submission, AJAX replies, phone number login, search, views, badges, and beautiful UI.
 * Version:     1.0.0
 * Author:      Md Abul Bashar
 * Author URI:  https://github.com/hmbashar
 * License:     GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: askora-community-qa
 * Requires at least: 6.0
 * Requires PHP: 7.4
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

/**
 * Main Askora Community Q&A class.
 *
 * Uses the singleton pattern, defines constants, loads Composer autoload,
 * and initialises all hooks — mirroring PrimeKit Addons architecture.
 *
 * @package ASKORA
 * @since   1.0.0
 */
final class AskoraCommunityQA
{

	/**
	 * Singleton instance.
	 *
	 * @var AskoraCommunityQA|null
	 */
	private static $instance = null;

	/**
	 * Constructor — private to enforce singleton.
	 *
	 * @since 1.0.0
	 */
	private function __construct()
	{
		$this->define_constants();
		$this->include_files();
		$this->init_hooks();
	}

	/**
	 * Returns the singleton instance.
	 *
	 * @since  1.0.0
	 * @return AskoraCommunityQA
	 */
	public static function get_instance()
	{
		if (null === self::$instance) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Defines all plugin constants.
	 *
	 * @since 1.0.0
	 */
	private function define_constants()
	{
		define('ASKORA_VERSION', '1.0.0');
		define('ASKORA_FILE', __FILE__);
		define('ASKORA_PATH', plugin_dir_path(__FILE__));
		define('ASKORA_URL', plugin_dir_url(__FILE__));
		define('ASKORA_BASENAME', plugin_basename(__FILE__));
		define('ASKORA_NAME', 'Askora');
	}

	/**
	 * Loads Composer autoloader.
	 *
	 * @since 1.0.0
	 */
	private function include_files()
	{
		if (file_exists(ASKORA_PATH . 'vendor/autoload.php')) {
			require_once ASKORA_PATH . 'vendor/autoload.php';
		}
	}

	/**
	 * Registers WordPress hooks.
	 *
	 * @since 1.0.0
	 */
	private function init_hooks()
	{
		add_action('plugins_loaded', [$this, 'plugin_loaded']);

		register_activation_hook(ASKORA_FILE, [$this, 'activate']);
		register_deactivation_hook(ASKORA_FILE, [$this, 'deactivate']);
	}

	/**
	 * Fires after all plugins are loaded.
	 * Bootstraps the Manager which spins up Admin and Frontend.
	 *
	 * @since 1.0.0
	 */
	public function plugin_loaded()
	{
		if (class_exists('ASKORA\\Manager')) {
			new \ASKORA\Manager();
		}

		/**
		 * Fires after Askora Community Q&A is fully loaded.
		 *
		 * @since 1.0.0
		 */
		do_action('askora_loaded');
	}


	/**
	 * Plugin activation handler.
	 *
	 * @since 1.0.0
	 */
	public function activate()
	{
		\ASKORA\Activate::activate();
	}

	/**
	 * Plugin deactivation handler.
	 *
	 * @since 1.0.0
	 */
	public function deactivate()
	{
		\ASKORA\Deactivate::deactivate();
	}
}

/**
 * Initialises the Askora Community Q&A plugin.
 *
 * @since  1.0.0
 * @return AskoraCommunityQA
 */
if (!function_exists('askora_initialize')) {
	function askora_initialize()
	{
		return AskoraCommunityQA::get_instance();
	}

	askora_initialize();
}
