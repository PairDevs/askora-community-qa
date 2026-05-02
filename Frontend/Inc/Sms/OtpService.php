<?php
/**
 * OTP service stub — future Pro feature.
 *
 * @package QuestionHub\Frontend\Inc\Sms
 * @since   1.0.0
 */

namespace QuestionHub\Frontend\Inc\Sms;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class OtpService {

	const OTP_EXPIRY   = 300; // 5 minutes.
	const OTP_ATTEMPTS = 3;

	/**
	 * Generate and store a hashed OTP for a phone number.
	 * Future Pro: will send via SmsManager.
	 *
	 * @param string $phone Phone number.
	 * @return bool
	 */
	public static function generate( string $phone ): bool {
		// Future Pro implementation.
		return apply_filters( 'questionhub_enable_otp_login', false );
	}

	/**
	 * Verify a submitted OTP against stored hash.
	 *
	 * @param string $phone Phone number.
	 * @param string $otp   Submitted OTP.
	 * @return bool
	 */
	public static function verify( string $phone, string $otp ): bool {
		// Future Pro implementation.
		return false;
	}
}
