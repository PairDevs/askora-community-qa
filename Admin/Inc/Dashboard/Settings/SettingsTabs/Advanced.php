<?php
/**
 * Advanced settings tab.
 *
 * @package QuestionHub\Admin\Inc\Dashboard\Settings\SettingsTabs
 * @since   1.0.0
 */

namespace QuestionHub\Admin\Inc\Dashboard\Settings\SettingsTabs;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Advanced
 */
class Advanced {

	protected $option_key = 'questionhub_settings';

	public function __construct() {
		add_action( 'admin_init', [ $this, 'register_settings' ] );
	}

	public function get_all() {
		return wp_parse_args( get_option( $this->option_key, [] ), $this->defaults() );
	}

	public function get( $name, $fallback = null ) {
		$options = $this->get_all();
		return isset( $options[ $name ] ) ? $options[ $name ] : $fallback;
	}

	public function defaults() {
		return [
			'enable_phone_auth'        => 1,
			'email_required'           => 0,
			'auto_login_after_reg'     => 1,
			'primary_color'            => '#6C63FF',
			'delete_data_on_uninstall' => 0,
		];
	}

	public function register_settings() {
		register_setting( 'questionhub_advanced_group', $this->option_key, [ $this, 'sanitize' ] );

		add_settings_section( 'questionhub_advanced_section', esc_html__( 'Advanced Settings', 'questionhub' ), [ $this, 'section_info' ], 'questionhub_advanced_settings' );

		add_settings_field( 'enable_phone_auth', esc_html__( 'Phone Registration/Login', 'questionhub' ), [ $this, 'render_phone_auth' ], 'questionhub_advanced_settings', 'questionhub_advanced_section' );
		add_settings_field( 'email_required', esc_html__( 'Email Required', 'questionhub' ), [ $this, 'render_email_required' ], 'questionhub_advanced_settings', 'questionhub_advanced_section' );
		add_settings_field( 'auto_login_after_reg', esc_html__( 'Auto-Login After Registration', 'questionhub' ), [ $this, 'render_auto_login' ], 'questionhub_advanced_settings', 'questionhub_advanced_section' );
		add_settings_field( 'primary_color', esc_html__( 'Primary Color', 'questionhub' ), [ $this, 'render_primary_color' ], 'questionhub_advanced_settings', 'questionhub_advanced_section' );
		add_settings_field( 'delete_data_on_uninstall', esc_html__( 'Delete All Data on Uninstall', 'questionhub' ), [ $this, 'render_delete_data' ], 'questionhub_advanced_settings', 'questionhub_advanced_section' );
	}

	public function section_info() {
		echo '<p>' . esc_html__( 'Phone authentication, UI color, and data management.', 'questionhub' ) . '</p>';
	}

	public function render_phone_auth() {
		$o = $this->get_all();
		echo '<label><input type="checkbox" name="' . esc_attr( $this->option_key ) . '[enable_phone_auth]" id="questionhub_enable_phone_auth" value="1" ' . checked( 1, (int) $o['enable_phone_auth'], false ) . '> ' . esc_html__( 'Allow users to register and log in with a phone number.', 'questionhub' ) . '</label>';
	}

	public function render_email_required() {
		$o = $this->get_all();
		echo '<label><input type="checkbox" name="' . esc_attr( $this->option_key ) . '[email_required]" id="questionhub_email_required" value="1" ' . checked( 1, (int) $o['email_required'], false ) . '> ' . esc_html__( 'Require an email address during registration.', 'questionhub' ) . '</label>';
	}

	public function render_auto_login() {
		$o = $this->get_all();
		echo '<label><input type="checkbox" name="' . esc_attr( $this->option_key ) . '[auto_login_after_reg]" id="questionhub_auto_login" value="1" ' . checked( 1, (int) $o['auto_login_after_reg'], false ) . '> ' . esc_html__( 'Automatically log users in after registration.', 'questionhub' ) . '</label>';
	}

	public function render_primary_color() {
		$o = $this->get_all();
		echo '<input type="color" name="' . esc_attr( $this->option_key ) . '[primary_color]" id="questionhub_primary_color" value="' . esc_attr( $o['primary_color'] ) . '">';
		echo '<p class="description">' . esc_html__( 'Primary brand color for the frontend UI.', 'questionhub' ) . '</p>';
	}

	public function render_delete_data() {
		$o = $this->get_all();
		echo '<label><input type="checkbox" name="' . esc_attr( $this->option_key ) . '[delete_data_on_uninstall]" id="questionhub_delete_data" value="1" ' . checked( 1, (int) $o['delete_data_on_uninstall'], false ) . '> ' . esc_html__( 'Remove ALL QuestionHub data when the plugin is uninstalled.', 'questionhub' ) . '</label>';
		echo '<p class="description" style="color:#c00;">' . esc_html__( 'Warning: This action cannot be undone.', 'questionhub' ) . '</p>';
	}

	public function sanitize( $input ) {
		if ( ! isset( $_POST['questionhub_nonce'] ) ||
			! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['questionhub_nonce'] ) ), 'questionhub_save_settings' ) ) {
			add_settings_error( 'questionhub_settings', 'nonce_error', esc_html__( 'Security check failed.', 'questionhub' ), 'error' );
			return get_option( $this->option_key, [] );
		}

		$existing = get_option( $this->option_key, [] );
		$input    = is_array( $input ) ? $input : [];

		if ( ! isset( $_POST['option_page'] ) || 'questionhub_advanced_group' !== $_POST['option_page'] ) {
			return wp_parse_args( $input, $existing );
		}

		$existing['enable_phone_auth']        = isset( $input['enable_phone_auth'] ) ? 1 : 0;
		$existing['email_required']           = isset( $input['email_required'] ) ? 1 : 0;
		$existing['auto_login_after_reg']     = isset( $input['auto_login_after_reg'] ) ? 1 : 0;
		$existing['primary_color']            = isset( $input['primary_color'] ) ? sanitize_hex_color( $input['primary_color'] ) : '#6C63FF';
		$existing['delete_data_on_uninstall'] = isset( $input['delete_data_on_uninstall'] ) ? 1 : 0;

		add_settings_error( 'questionhub_settings', 'advanced_saved', esc_html__( 'Advanced settings saved.', 'questionhub' ), 'updated' );
		return $existing;
	}
}
