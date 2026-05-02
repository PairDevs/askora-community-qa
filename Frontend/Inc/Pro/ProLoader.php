<?php
/**
 * Pro loader stub.
 *
 * @package QuestionHub\Frontend\Inc\Pro
 * @since   1.0.0
 */

namespace QuestionHub\Frontend\Inc\Pro;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ProLoader {

	public static function is_active(): bool {
		return class_exists( 'QuestionHubPro' );
	}
}
