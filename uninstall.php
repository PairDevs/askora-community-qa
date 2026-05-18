<?php
/**
 * Askora Community Q&A Uninstall
 *
 * Fired when the plugin is uninstalled.
 * Only removes data when the admin has explicitly enabled the "delete all data on uninstall" option.
 *
 * @package ASKORA
 * @since   1.0.0
 */

// Exit if uninstall is not called from WordPress.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$askora_settings = get_option( 'askora_settings', [] );
$askora_delete   = isset( $askora_settings['delete_data_on_uninstall'] ) ? (bool) $askora_settings['delete_data_on_uninstall'] : false;

if ( ! $askora_delete ) {
	return;
}

global $wpdb;

// Remove plugin options.
delete_option( 'askora_settings' );
delete_option( 'askora_version' );

// Remove transients — no WP API exists for bulk-deleting transients by prefix.
// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_askora_%'" );
$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_askora_%'" );
// phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
wp_cache_flush(); // Clear object cache after bulk transient deletion.

// Remove post meta.
$askora_meta_keys = [
	'_askora_views',
	'_askora_votes',
	'_askora_voted_users',
	'_askora_best_answer',
	'_askora_status',
];
foreach ( $askora_meta_keys as $askora_key ) {
	delete_post_meta_by_key( $askora_key );
}

// Remove comment meta.
$askora_comment_meta_keys = [
	'_askora_answer_votes',
	'_askora_answer_voted_users',
	'_askora_is_best_answer',
];
foreach ( $askora_comment_meta_keys as $askora_key ) {
	delete_metadata( 'comment', 0, $askora_key, '', true );
}

// Remove user meta.
$askora_user_meta_keys = [
	'_askora_phone_number',
	'_askora_phone_verified',
];
foreach ( $askora_user_meta_keys as $askora_key ) {
	delete_metadata( 'user', 0, $askora_key, '', true );
}
