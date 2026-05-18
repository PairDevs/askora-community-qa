<?php
/**
 * Manager class.
 *
 * Bootstraps Admin and Frontend layers — mirrors PrimeKit's Manager pattern.
 *
 * @package ASKORA
 * @since   1.0.0
 */

namespace ASKORA;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use ASKORA\Admin\AdminManager;
use ASKORA\Frontend\Frontend;

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
		 * Fires after all Askora Community Q&A modules are loaded.
		 *
		 * @since 1.0.0
		 */
		do_action( 'askora_modules_loaded' );
	}
}
