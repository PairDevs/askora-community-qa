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

		$active_tab = $this->get_active_tab();
		$tabs       = [
			'general'  => [
				'label' => __( 'General', 'questionhub' ),
				'icon'  => 'dashicons-admin-settings',
			],
			'advanced' => [
				'label' => __( 'Advanced', 'questionhub' ),
				'icon'  => 'dashicons-admin-tools',
			],
		];
		?>
		<div class="qh-dash-wrap qh-settings-page">

			<!-- ══ HEADER ══ -->
			<div class="qh-hero qh-settings-hero">
				<div class="qh-hero-left">
					<div class="qh-hero-logo">
						<span class="dashicons dashicons-admin-settings"></span>
					</div>
					<div>
						<h1 class="qh-hero-title"><?php esc_html_e( 'Settings', 'questionhub' ); ?></h1>
						<p class="qh-hero-subtitle"><?php esc_html_e( 'Configure how QuestionHub behaves on your site.', 'questionhub' ); ?></p>
					</div>
				</div>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=questionhub_home' ) ); ?>" class="qh-btn qh-btn-ghost">
					<span class="dashicons dashicons-dashboard"></span>
					<?php esc_html_e( 'Dashboard', 'questionhub' ); ?>
				</a>
			</div>

			<!-- ══ SETTINGS BODY ══ -->
			<div class="qh-settings-layout">

				<!-- Tab sidebar -->
				<nav class="qh-settings-nav">
					<?php foreach ( $tabs as $slug => $tab ) : ?>
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=questionhub_settings&tab=' . $slug ) ); ?>"
					   class="qh-settings-nav-item <?php echo $slug === $active_tab ? 'active' : ''; ?>">
						<span class="dashicons <?php echo esc_attr( $tab['icon'] ); ?>"></span>
						<?php echo esc_html( $tab['label'] ); ?>
					</a>
					<?php endforeach; ?>
				</nav>

				<!-- Main panel -->
				<div class="qh-settings-main">
					<?php settings_errors( 'questionhub_settings' ); ?>

					<form method="post" action="options.php" class="qh-settings-form">
						<?php
						if ( 'general' === $active_tab ) {
							settings_fields( 'questionhub_general_group' );
							$this->render_general_fields();
						} elseif ( 'advanced' === $active_tab ) {
							settings_fields( 'questionhub_advanced_group' );
							$this->render_advanced_fields();
						}
						wp_nonce_field( 'questionhub_save_settings', 'questionhub_nonce' );
						?>

						<div class="qh-settings-footer">
							<button type="submit" class="qh-btn qh-btn-primary qh-settings-save-btn" id="qh-settings-save">
								<span class="dashicons dashicons-yes-alt"></span>
								<?php esc_html_e( 'Save Changes', 'questionhub' ); ?>
							</button>
						</div>
					</form>
				</div>

			</div><!-- .qh-settings-layout -->
		</div><!-- .qh-settings-page -->
		<?php
	}

	/**
	 * Renders General settings fields with custom markup (bypasses default table layout).
	 */
	private function render_general_fields() {
		$opts = $this->general->get_all();
		$key  = 'questionhub_settings';
		?>

		<div class="qh-settings-section">
			<div class="qh-settings-section-header">
				<span class="dashicons dashicons-admin-page"></span>
				<div>
					<h2 class="qh-settings-section-title"><?php esc_html_e( 'Page Setup', 'questionhub' ); ?></h2>
					<p class="qh-settings-section-desc"><?php esc_html_e( 'Link your question pages to plugin shortcodes.', 'questionhub' ); ?></p>
				</div>
			</div>
			<div class="qh-settings-fields">

				<div class="qh-field-row">
					<label class="qh-field-label" for="questionhub_submit_form_page_id">
						<?php esc_html_e( 'Ask a Question Page', 'questionhub' ); ?>
						<span class="qh-field-badge qh-badge-member">Important</span>
					</label>
					<div class="qh-field-control">
						<?php
						wp_dropdown_pages( [
							'name'              => esc_attr( $key ) . '[submit_form_page_id]',
							'id'                => 'questionhub_submit_form_page_id',
							'selected'          => (int) ( $opts['submit_form_page_id'] ?? 0 ),
							'show_option_none'  => __( '— Select a Page —', 'questionhub' ),
							'option_none_value' => '0',
							'class'             => 'qh-field-select',
						] );
						?>
						<p class="qh-field-desc"><?php esc_html_e( 'Page that contains [questionhub_submit_form]. Used for the "Ask a Question" button.', 'questionhub' ); ?></p>
					</div>
				</div>

				<div class="qh-field-row">
					<label class="qh-field-label" for="questionhub_question_status">
						<?php esc_html_e( 'Default Question Status', 'questionhub' ); ?>
					</label>
					<div class="qh-field-control">
						<select name="<?php echo esc_attr( $key ); ?>[question_status]" id="questionhub_question_status" class="qh-field-select">
							<option value="pending" <?php selected( $opts['question_status'], 'pending' ); ?>><?php esc_html_e( 'Pending Review', 'questionhub' ); ?></option>
							<option value="publish" <?php selected( $opts['question_status'], 'publish' ); ?>><?php esc_html_e( 'Publish Immediately', 'questionhub' ); ?></option>
							<option value="draft"   <?php selected( $opts['question_status'], 'draft' ); ?>><?php esc_html_e( 'Save as Draft', 'questionhub' ); ?></option>
						</select>
						<p class="qh-field-desc"><?php esc_html_e( 'Status assigned to newly submitted questions.', 'questionhub' ); ?></p>
					</div>
				</div>

				<div class="qh-field-row">
					<label class="qh-field-label" for="questionhub_questions_per_page">
						<?php esc_html_e( 'Questions Per Page', 'questionhub' ); ?>
					</label>
					<div class="qh-field-control">
						<input type="number" name="<?php echo esc_attr( $key ); ?>[questions_per_page]" id="questionhub_questions_per_page"
							   value="<?php echo esc_attr( (int) $opts['questions_per_page'] ); ?>"
							   min="1" max="100" class="qh-field-number">
						<p class="qh-field-desc"><?php esc_html_e( 'Number of questions per page in the list.', 'questionhub' ); ?></p>
					</div>
				</div>

			</div>
		</div>

		<div class="qh-settings-section">
			<div class="qh-settings-section-header">
				<span class="dashicons dashicons-admin-users"></span>
				<div>
					<h2 class="qh-settings-section-title"><?php esc_html_e( 'Access Control', 'questionhub' ); ?></h2>
					<p class="qh-settings-section-desc"><?php esc_html_e( 'Control who can ask and reply.', 'questionhub' ); ?></p>
				</div>
			</div>
			<div class="qh-settings-fields">

				<div class="qh-field-row qh-field-toggle-row">
					<div class="qh-field-toggle-info">
						<span class="qh-field-label"><?php esc_html_e( 'Require Login to Ask', 'questionhub' ); ?></span>
						<p class="qh-field-desc"><?php esc_html_e( 'Users must be logged in to ask a question.', 'questionhub' ); ?></p>
					</div>
					<label class="qh-toggle">
						<input type="checkbox" name="<?php echo esc_attr( $key ); ?>[require_login_to_ask]" id="questionhub_require_login_ask" value="1" <?php checked( 1, (int) $opts['require_login_to_ask'] ); ?>>
						<span class="qh-toggle-slider"></span>
					</label>
				</div>

				<div class="qh-field-row qh-field-toggle-row">
					<div class="qh-field-toggle-info">
						<span class="qh-field-label"><?php esc_html_e( 'Require Login to Reply', 'questionhub' ); ?></span>
						<p class="qh-field-desc"><?php esc_html_e( 'Users must be logged in to submit a reply.', 'questionhub' ); ?></p>
					</div>
					<label class="qh-toggle">
						<input type="checkbox" name="<?php echo esc_attr( $key ); ?>[require_login_to_reply]" id="questionhub_require_login_reply" value="1" <?php checked( 1, (int) $opts['require_login_to_reply'] ); ?>>
						<span class="qh-toggle-slider"></span>
					</label>
				</div>

				<div class="qh-field-row qh-field-toggle-row">
					<div class="qh-field-toggle-info">
						<span class="qh-field-label"><?php esc_html_e( 'Allow Guest Replies', 'questionhub' ); ?></span>
						<p class="qh-field-desc"><?php esc_html_e( 'Allow non-logged-in users to submit replies (overrides above).', 'questionhub' ); ?></p>
					</div>
					<label class="qh-toggle">
						<input type="checkbox" name="<?php echo esc_attr( $key ); ?>[allow_guest_replies]" id="questionhub_allow_guest_replies" value="1" <?php checked( 1, (int) $opts['allow_guest_replies'] ); ?>>
						<span class="qh-toggle-slider"></span>
					</label>
				</div>

			</div>
		</div>

		<div class="qh-settings-section">
			<div class="qh-settings-section-header">
				<span class="dashicons dashicons-star-filled"></span>
				<div>
					<h2 class="qh-settings-section-title"><?php esc_html_e( 'Features', 'questionhub' ); ?></h2>
					<p class="qh-settings-section-desc"><?php esc_html_e( 'Enable or disable Q&A features.', 'questionhub' ); ?></p>
				</div>
			</div>
			<div class="qh-settings-fields">

				<div class="qh-field-row qh-field-toggle-row">
					<div class="qh-field-toggle-info">
						<span class="qh-field-label"><?php esc_html_e( 'Enable Voting', 'questionhub' ); ?></span>
						<p class="qh-field-desc"><?php esc_html_e( 'Enable upvoting for questions and answers.', 'questionhub' ); ?></p>
					</div>
					<label class="qh-toggle">
						<input type="checkbox" name="<?php echo esc_attr( $key ); ?>[enable_voting]" id="questionhub_enable_voting" value="1" <?php checked( 1, (int) $opts['enable_voting'] ); ?>>
						<span class="qh-toggle-slider"></span>
					</label>
				</div>

				<div class="qh-field-row qh-field-toggle-row">
					<div class="qh-field-toggle-info">
						<span class="qh-field-label"><?php esc_html_e( 'Enable Best Answer', 'questionhub' ); ?></span>
						<p class="qh-field-desc"><?php esc_html_e( 'Allow marking one reply as the Best Answer.', 'questionhub' ); ?></p>
					</div>
					<label class="qh-toggle">
						<input type="checkbox" name="<?php echo esc_attr( $key ); ?>[enable_best_answer]" id="questionhub_enable_best_answer" value="1" <?php checked( 1, (int) $opts['enable_best_answer'] ); ?>>
						<span class="qh-toggle-slider"></span>
					</label>
				</div>

				<div class="qh-field-row qh-field-toggle-row">
					<div class="qh-field-toggle-info">
						<span class="qh-field-label"><?php esc_html_e( 'Enable Question Views', 'questionhub' ); ?></span>
						<p class="qh-field-desc"><?php esc_html_e( 'Track and display view counts on each question.', 'questionhub' ); ?></p>
					</div>
					<label class="qh-toggle">
						<input type="checkbox" name="<?php echo esc_attr( $key ); ?>[enable_question_views]" id="questionhub_enable_views" value="1" <?php checked( 1, (int) $opts['enable_question_views'] ); ?>>
						<span class="qh-toggle-slider"></span>
					</label>
				</div>

			</div>
		</div>

		<?php
	}

	/**
	 * Renders Advanced settings fields with custom markup.
	 */
	private function render_advanced_fields() {
		$opts = $this->advanced->get_all();
		$key  = 'questionhub_settings';
		?>

		<div class="qh-settings-section">
			<div class="qh-settings-section-header">
				<span class="dashicons dashicons-admin-network"></span>
				<div>
					<h2 class="qh-settings-section-title"><?php esc_html_e( 'Advanced Configurations', 'questionhub' ); ?></h2>
					<p class="qh-settings-section-desc"><?php esc_html_e( 'Phone authentication, UI color, and data management.', 'questionhub' ); ?></p>
				</div>
			</div>
			<div class="qh-settings-fields">

				<div class="qh-field-row qh-field-toggle-row">
					<div class="qh-field-toggle-info">
						<span class="qh-field-label"><?php esc_html_e( 'Phone Registration/Login', 'questionhub' ); ?></span>
						<p class="qh-field-desc"><?php esc_html_e( 'Allow users to register and log in with a phone number.', 'questionhub' ); ?></p>
					</div>
					<label class="qh-toggle">
						<input type="checkbox" name="<?php echo esc_attr( $key ); ?>[enable_phone_auth]" id="questionhub_enable_phone_auth" value="1" <?php checked( 1, (int) $opts['enable_phone_auth'] ); ?>>
						<span class="qh-toggle-slider"></span>
					</label>
				</div>

				<div class="qh-field-row qh-field-toggle-row">
					<div class="qh-field-toggle-info">
						<span class="qh-field-label"><?php esc_html_e( 'Email Required', 'questionhub' ); ?></span>
						<p class="qh-field-desc"><?php esc_html_e( 'Require an email address during registration.', 'questionhub' ); ?></p>
					</div>
					<label class="qh-toggle">
						<input type="checkbox" name="<?php echo esc_attr( $key ); ?>[email_required]" id="questionhub_email_required" value="1" <?php checked( 1, (int) $opts['email_required'] ); ?>>
						<span class="qh-toggle-slider"></span>
					</label>
				</div>

				<div class="qh-field-row qh-field-toggle-row">
					<div class="qh-field-toggle-info">
						<span class="qh-field-label"><?php esc_html_e( 'Auto-Login After Registration', 'questionhub' ); ?></span>
						<p class="qh-field-desc"><?php esc_html_e( 'Automatically log users in after successful registration.', 'questionhub' ); ?></p>
					</div>
					<label class="qh-toggle">
						<input type="checkbox" name="<?php echo esc_attr( $key ); ?>[auto_login_after_reg]" id="questionhub_auto_login" value="1" <?php checked( 1, (int) $opts['auto_login_after_reg'] ); ?>>
						<span class="qh-toggle-slider"></span>
					</label>
				</div>

				<div class="qh-field-row">
					<label class="qh-field-label" for="questionhub_primary_color">
						<?php esc_html_e( 'Primary Color', 'questionhub' ); ?>
					</label>
					<div class="qh-field-control">
						<input type="color" name="<?php echo esc_attr( $key ); ?>[primary_color]" id="questionhub_primary_color" value="<?php echo esc_attr( $opts['primary_color'] ); ?>" class="qh-field-color">
						<p class="qh-field-desc"><?php esc_html_e( 'Primary brand color for the frontend UI.', 'questionhub' ); ?></p>
					</div>
				</div>

				<div class="qh-field-row qh-field-toggle-row qh-field-danger-row">
					<div class="qh-field-toggle-info">
						<span class="qh-field-label"><?php esc_html_e( 'Delete All Data on Uninstall', 'questionhub' ); ?></span>
						<p class="qh-field-desc"><?php esc_html_e( 'Warning: This action removes all questions, answers, and data when the plugin is deleted. Cannot be undone.', 'questionhub' ); ?></p>
					</div>
					<label class="qh-toggle">
						<input type="checkbox" name="<?php echo esc_attr( $key ); ?>[delete_data_on_uninstall]" id="questionhub_delete_data" value="1" <?php checked( 1, (int) $opts['delete_data_on_uninstall'] ); ?>>
						<span class="qh-toggle-slider"></span>
					</label>
				</div>

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
