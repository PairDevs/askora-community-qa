<?php
/**
 * SMS provider interface — future Pro feature.
 *
 * @package QuestionHub\Frontend\Inc\Sms\Contracts
 * @since   1.0.0
 */

namespace QuestionHub\Frontend\Inc\Sms\Contracts;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

interface SmsProviderInterface {
	public function send( string $phone_number, string $message ): bool;
}
