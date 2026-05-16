<?php
/**
 * QuestionHub Uninstall
 *
 * Fired when the plugin is uninstalled.
 * Only removes data when the admin has explicitly enabled the "delete all data on uninstall" option.
 *
 * @package QuestionHub
 * @since   1.0.0
 */

// Exit if uninstall is not called from WordPress.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$questionhub_settings = get_option( 'questionhub_settings', [] );
$questionhub_delete   = isset( $questionhub_settings['delete_data_on_uninstall'] ) ? (bool) $questionhub_settings['delete_data_on_uninstall'] : false;

if ( ! $questionhub_delete ) {
	return;
}

global $wpdb;

// Remove plugin options.
delete_option( 'questionhub_settings' );
delete_option( 'questionhub_version' );

// Remove transients — no WP API exists for bulk-deleting transients by prefix.
// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_questionhub_%'" );
$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_questionhub_%'" );
// phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
wp_cache_flush(); // Clear object cache after bulk transient deletion.

// Remove post meta.
$questionhub_meta_keys = [
	'_questionhub_views',
	'_questionhub_votes',
	'_questionhub_voted_users',
	'_questionhub_best_answer',
	'_questionhub_status',
];
foreach ( $questionhub_meta_keys as $questionhub_key ) {
	delete_post_meta_by_key( $questionhub_key );
}

// Remove comment meta.
$questionhub_comment_meta_keys = [
	'_questionhub_answer_votes',
	'_questionhub_answer_voted_users',
	'_questionhub_is_best_answer',
];
foreach ( $questionhub_comment_meta_keys as $questionhub_key ) {
	delete_metadata( 'comment', 0, $questionhub_key, '', true );
}

// Remove user meta.
$questionhub_user_meta_keys = [
	'_questionhub_phone_number',
	'_questionhub_phone_verified',
];
foreach ( $questionhub_user_meta_keys as $questionhub_key ) {
	delete_metadata( 'user', 0, $questionhub_key, '', true );
}
