<?php
/**
 * SMS manager — resolves active SMS provider.
 *
 * @package ASKORA\Frontend\Inc\Sms
 * @since   1.0.0
 */

namespace ASKORA\Frontend\Inc\Sms;

use ASKORA\Frontend\Inc\Sms\Contracts\SmsProviderInterface;
use ASKORA\Frontend\Inc\Sms\Providers\NullSmsProvider;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SmsManager {

	private SmsProviderInterface $provider;

	public function __construct() {
		$provider_key   = apply_filters( 'askora_sms_provider', 'null' );
		$this->provider = $this->resolve( $provider_key );
	}

	private function resolve( string $key ): SmsProviderInterface {
		// Pro can add real providers via the filter.
		return new NullSmsProvider();
	}

	public function send( string $phone, string $message ): bool {
		return $this->provider->send( $phone, $message );
	}
}
