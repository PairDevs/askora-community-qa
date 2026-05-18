<?php
/**
 * Auth provider interface.
 *
 * @package ASKORA\Frontend\Inc\Auth\Contracts
 * @since   1.0.0
 */

namespace ASKORA\Frontend\Inc\Auth\Contracts;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

interface AuthProviderInterface {
	public function login( array $credentials ): bool;
	public function register( array $data ): int;
}
