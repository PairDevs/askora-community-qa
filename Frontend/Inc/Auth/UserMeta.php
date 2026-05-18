<?php
/**
 * User meta helper for Askora Community Q&A auth data.
 *
 * @package ASKORA\Frontend\Inc\Auth
 * @since   1.0.0
 */

namespace ASKORA\Frontend\Inc\Auth;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class UserMeta {

	const PHONE_KEY    = '_askora_phone_number';
	const VERIFIED_KEY = '_askora_phone_verified';

	public static function set_phone( int $user_id, string $phone ): void {
		update_user_meta( $user_id, self::PHONE_KEY, $phone );
	}

	public static function get_phone( int $user_id ): string {
		return (string) get_user_meta( $user_id, self::PHONE_KEY, true );
	}

	public static function find_user_by_phone( string $phone ): ?int {
		$users = get_users( [
			'meta_key'   => self::PHONE_KEY, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'meta_value' => $phone,           // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			'number'     => 1,
			'fields'     => 'ID',
		] );
		return ! empty( $users ) ? (int) $users[0] : null;
	}

	public static function is_phone_taken( string $phone ): bool {
		return null !== self::find_user_by_phone( $phone );
	}
}
