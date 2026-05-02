<?php
/**
 * Settings orchestrator — tabs + submenu registration.
 *
 * @package QuestionHub\Admin\Inc\Dashboard\Settings
 * @since   1.0.0
 */

namespace QuestionHub\Admin\Inc\Dashboard\Settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use QuestionHub\Admin\Inc\Dashboard\Settings\SettingsTabs\General;
use QuestionHub\Admin\Inc\Dashboard\Settings\SettingsTabs\Advanced;

/**
 * Class Settings
 */
class Settings {

	protected $general;
	protected $advanced;

	public function __construct() {
		add_action( 'admin_menu', [ $this, 'register_submenu_page' ], 99 );
		$this->classes_initialize();
	}

	public function register_submenu_page() {
		add_submenu_page(
			'questionhub_home',
			esc_html__( 'QuestionHub Settings', 'questionhub' ),
			esc_html__( 'Settings', 'questionhub' ),
			'manage_options',
			'questionhub_settings',
			[ $this, 'render_settings_page' ]
		);
	}

	public function render_settings_page() {
		if ( isset( $_SERVER['REQUEST_METHOD'] ) && 'POST' === $_SERVER['REQUEST_METHOD'] ) {
			$nonce = isset( $_POST['questionhub_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['questionhub_nonce'] ) ) : '';
			if ( ! $nonce || ! wp_verify_nonce( $nonce, 'questionhub_save_settings' ) ) {
				wp_die( esc_html__( 'Nonce verification failed.', 'questionhub' ) );
			}
		}
		$active_tab = $this->get_active_tab();
		?>
		<div class="wrap questionhub-settings-wrap">
			<?php settings_errors(); ?>
			<h1><?php esc_html_e( 'QuestionHub Settings', 'questionhub' ); ?></h1>
			<nav class="nav-tab-wrapper">
				<a href="?page=questionhub_settings&tab=general" class="nav-tab <?php echo 'general' === $active_tab ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'General', 'questionhub' ); ?></a>
				<a href="?page=questionhub_settings&tab=advanced" class="nav-tab <?php echo 'advanced' === $active_tab ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Advanced', 'questionhub' ); ?></a>
			</nav>
			<div class="tab-content">
				<form method="post" action="options.php">
					<?php
					if ( 'general' === $active_tab ) {
						settings_fields( 'questionhub_general_group' );
						do_settings_sections( 'questionhub_general_settings' );
					} elseif ( 'advanced' === $active_tab ) {
						settings_fields( 'questionhub_advanced_group' );
						do_settings_sections( 'questionhub_advanced_settings' );
					}
					wp_nonce_field( 'questionhub_save_settings', 'questionhub_nonce' );
					submit_button();
					?>
				</form>
			</div>
		</div>
		<?php
	}

	private function get_active_tab() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'general';
	}

	public function classes_initialize() {
		$this->general  = new General();
		$this->advanced = new Advanced();
	}

	/**
	 * Proxy method so AdminManager can read settings values.
	 *
	 * @param string $key     Option key.
	 * @param mixed  $default Default fallback.
	 * @return mixed
	 */
	public function get_option( $key, $default = null ) {
		return $this->general->get( $key, $this->advanced->get( $key, $default ) );
	}
}
