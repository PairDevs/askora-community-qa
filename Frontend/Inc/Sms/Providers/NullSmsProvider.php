<?php
/**
 * Null SMS provider — no-op default for free version.
 *
 * @package QuestionHub\Frontend\Inc\Sms\Providers
 * @since   1.0.0
 */

namespace QuestionHub\Frontend\Inc\Sms\Providers;

use QuestionHub\Frontend\Inc\Sms\Contracts\SmsProviderInterface;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class NullSmsProvider implements SmsProviderInterface {
	public function send( string $phone_number, string $message ): bool {
		return false;
	}
}
