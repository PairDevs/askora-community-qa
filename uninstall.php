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

$settings = get_option( 'questionhub_settings', [] );
$delete   = isset( $settings['delete_data_on_uninstall'] ) ? (bool) $settings['delete_data_on_uninstall'] : false;

if ( ! $delete ) {
	return;
}

global $wpdb;

// Remove plugin options.
delete_option( 'questionhub_settings' );
delete_option( 'questionhub_version' );

// Remove transients.
$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_questionhub_%'" );
$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_questionhub_%'" );

// Remove post meta.
$meta_keys = [
	'_questionhub_views',
	'_questionhub_votes',
	'_questionhub_voted_users',
	'_questionhub_best_answer',
	'_questionhub_status',
];
foreach ( $meta_keys as $key ) {
	$wpdb->delete( $wpdb->postmeta, [ 'meta_key' => $key ] ); // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
}

// Remove comment meta.
$comment_meta_keys = [
	'_questionhub_answer_votes',
	'_questionhub_answer_voted_users',
	'_questionhub_is_best_answer',
];
foreach ( $comment_meta_keys as $key ) {
	$wpdb->delete( $wpdb->commentmeta, [ 'meta_key' => $key ] ); // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
}

// Remove user meta.
$user_meta_keys = [
	'_questionhub_phone_number',
	'_questionhub_phone_verified',
];
foreach ( $user_meta_keys as $key ) {
	$wpdb->delete( $wpdb->usermeta, [ 'meta_key' => $key ] ); // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
}
