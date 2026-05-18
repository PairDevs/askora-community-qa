<?php
/**
 * Null SMS provider — no-op default for free version.
 *
 * @package ASKORA\Frontend\Inc\Sms\Providers
 * @since   1.0.0
 */

namespace ASKORA\Frontend\Inc\Sms\Providers;

use ASKORA\Frontend\Inc\Sms\Contracts\SmsProviderInterface;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class NullSmsProvider implements SmsProviderInterface {
	public function send( string $phone_number, string $message ): bool {
		return false;
	}
}
