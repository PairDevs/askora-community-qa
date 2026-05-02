<?php
/**
 * Admin action hooks.
 *
 * @package QuestionHub\Admin\Inc\Hooks
 * @since   1.0.0
 */

namespace QuestionHub\Admin\Inc\Hooks;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class ActionHooks
 *
 * Centralises admin-side WordPress action hook registrations.
 */
class ActionHooks {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		// Plugin row meta links.
		add_filter( 'plugin_row_meta', [ $this, 'plugin_row_meta' ], 10, 2 );

		// Plugin action links (Settings).
		add_filter( 'plugin_action_links_' . QUESTIONHUB_BASENAME, [ $this, 'plugin_action_links' ] );

		// Custom footer text on plugin pages.
		add_filter( 'admin_footer_text', [ $this, 'custom_admin_footer' ] );
	}

	/**
	 * Adds support/rate links to plugin row meta.
	 *
	 * @param  array  $links Plugin row meta links.
	 * @param  string $file  Plugin basename.
	 * @return array
	 * @since  1.0.0
	 */
	public function plugin_row_meta( $links, $file ) {
		if ( QUESTIONHUB_BASENAME === $file ) {
			$row_meta = [
				'docs'    => '<a href="https://github.com/PairDevs/QuestionHub" target="_blank">' . esc_html__( 'Documentation', 'questionhub' ) . '</a>',
				'support' => '<a href="https://wordpress.org/support/plugin/questionhub" target="_blank">' . esc_html__( 'Support', 'questionhub' ) . '</a>',
			];
			return array_merge( $links, $row_meta );
		}
		return (array) $links;
	}

	/**
	 * Adds Settings link to plugin action links.
	 *
	 * @param  array $links Existing action links.
	 * @return array
	 * @since  1.0.0
	 */
	public function plugin_action_links( $links ) {
		$settings_link = sprintf(
			'<a href="%s">%s</a>',
			esc_url( admin_url( 'admin.php?page=questionhub_settings' ) ),
			esc_html__( 'Settings', 'questionhub' )
		);
		array_unshift( $links, $settings_link );
		return $links;
	}

	/**
	 * Customises admin footer text on QuestionHub pages.
	 *
	 * @param  string $footer_text Default footer text.
	 * @return string
	 * @since  1.0.0
	 */
	public function custom_admin_footer( $footer_text ) {
		$qh_pages = [ 'questionhub_home', 'questionhub_settings' ];
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( isset( $_GET['page'] ) && in_array( sanitize_text_field( wp_unslash( $_GET['page'] ) ), $qh_pages, true ) ) {
			return sprintf(
				/* translators: %s: plugin name */
				esc_html__( 'Thank you for using %s!', 'questionhub' ),
				'<strong>' . esc_html( QUESTIONHUB_NAME ) . '</strong>'
			);
		}
		return $footer_text;
	}
}
