<?php
/**
 * Feature gate for Pro features.
 *
 * @package QuestionHub\Frontend\Inc\Pro
 * @since   1.0.0
 */

namespace QuestionHub\Frontend\Inc\Pro;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FeatureGate {

	public static function is_pro_active(): bool {
		return (bool) apply_filters( 'questionhub_is_pro_active', false );
	}

	public static function can_use( string $feature ): bool {
		$features = apply_filters( 'questionhub_pro_features', [] );
		return self::is_pro_active() && in_array( $feature, $features, true );
	}
}
