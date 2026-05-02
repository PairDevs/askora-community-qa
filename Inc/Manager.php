<?php
/**
 * Manager class.
 *
 * Bootstraps Admin and Frontend layers — mirrors PrimeKit's Manager pattern.
 *
 * @package QuestionHub
 * @since   1.0.0
 */

namespace QuestionHub;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use QuestionHub\Admin\AdminManager;
use QuestionHub\Frontend\Frontend;

/**
 * Class Manager
 *
 * Instantiates the AdminManager and Frontend orchestrators.
 */
class Manager {

	/**
	 * Admin manager instance.
	 *
	 * @var AdminManager
	 */
	protected $admin_manager;

	/**
	 * Frontend instance.
	 *
	 * @var Frontend
	 */
	protected $frontend;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Initialise Admin and Frontend layers.
	 *
	 * @since 1.0.0
	 */
	public function init() {
		$this->admin_manager = new AdminManager();
		$this->frontend      = new Frontend();

		/**
		 * Fires after all QuestionHub modules are loaded.
		 *
		 * @since 1.0.0
		 */
		do_action( 'questionhub_modules_loaded' );
	}
}
