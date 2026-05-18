<?php
/**
 * Plugin deactivation class.
 *
 * @package ASKORA
 * @since   1.0.0
 */

namespace ASKORA;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Deactivate
 *
 * Handles cleanup on plugin deactivation.
 */
class Deactivate {

	/**
	 * Run on plugin deactivation.
	 *
	 * @since 1.0.0
	 */
	public static function deactivate() {
		flush_rewrite_rules();
	}
}
