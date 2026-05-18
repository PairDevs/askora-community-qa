<?php
/**
 * Auth form renderer.
 *
 * @package ASKORA\Frontend\Inc\Auth
 * @since   1.0.0
 */

namespace ASKORA\Frontend\Inc\Auth;

use ASKORA\Frontend\Inc\Helpers\Template;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AuthForms {

	public function render_login(): string {
		return Template::get( 'auth-login.php' );
	}

	public function render_register(): string {
		return Template::get( 'auth-register.php' );
	}

	public function render_combined(): string {
		return Template::get( 'auth-combined.php' );
	}
}
