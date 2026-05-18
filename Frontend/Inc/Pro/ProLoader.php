<?php
/**
 * Pro loader stub.
 *
 * @package ASKORA\Frontend\Inc\Pro
 * @since   1.0.0
 */

namespace ASKORA\Frontend\Inc\Pro;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ProLoader {

	public static function is_active(): bool {
		return class_exists( 'AskoraPro' );
	}
}
