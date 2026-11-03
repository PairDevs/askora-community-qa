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

// Delete every question (and, with it, its answers) plus the plugin's categories and tags.
// The post type and taxonomies are not registered during uninstall, so register them
// just enough for WordPress to delete their terms.
$askora_question_ids = get_posts(
	[
		'post_type'      => 'questions',
		'post_status'    => 'any',
		'posts_per_page' => -1,
		'fields'         => 'ids',
		'no_found_rows'  => true,
	]
);
foreach ( $askora_question_ids as $askora_question_id ) {
	wp_delete_post( (int) $askora_question_id, true );
}

foreach ( [ 'question_category', 'question_tag' ] as $askora_taxonomy ) {
	register_taxonomy( $askora_taxonomy, 'questions' );
	$askora_term_ids = get_terms(
		[
			'taxonomy'   => $askora_taxonomy,
			'hide_empty' => false,
			'fields'     => 'ids',
		]
	);
	if ( ! is_wp_error( $askora_term_ids ) ) {
		foreach ( $askora_term_ids as $askora_term_id ) {
			wp_delete_term( (int) $askora_term_id, $askora_taxonomy );
		}
	}
}

// Remove plugin options.
delete_option( 'askora_settings' );
delete_option( 'askora_version' );
delete_option( 'askora_pages' );
delete_option( 'askora_welcome_notice_dismissed' );

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
	'_askora_verified',
	'_askora_verified_by',
	'_askora_verified_at',
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
