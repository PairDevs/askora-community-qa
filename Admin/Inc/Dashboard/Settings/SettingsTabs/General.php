<?php
/**
 * General settings tab.
 *
 * @package ASKORA\Admin\Inc\Dashboard\Settings\SettingsTabs
 * @since   1.0.0
 */

namespace ASKORA\Admin\Inc\Dashboard\Settings\SettingsTabs;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class General
 *
 * Registers and renders the General settings tab fields.
 */
class General {

	/**
	 * Option key.
	 *
	 * @var string
	 */
	protected $option_key = 'askora_settings';

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'admin_init', [ $this, 'register_settings' ] );
	}

	/**
	 * Returns all options merged with defaults.
	 *
	 * @return array
	 * @since  1.0.0
	 */
	public function get_all() {
		return wp_parse_args( get_option( $this->option_key, [] ), $this->defaults() );
	}

	/**
	 * Returns a single option value.
	 *
	 * @param  string $name    Option key.
	 * @param  mixed  $fallback Fallback value.
	 * @return mixed
	 * @since  1.0.0
	 */
	public function get( $name, $fallback = null ) {
		$options = $this->get_all();
		return isset( $options[ $name ] ) ? $options[ $name ] : $fallback;
	}

	/**
	 * Default values.
	 *
	 * @return array
	 * @since  1.0.0
	 */
	public function defaults() {
		return [
			'question_status'        => 'pending',
			'submit_form_page_id'    => 0,
			'questions_list_page_id' => 0,
			'allow_guest_replies'    => 0,
			'require_login_to_ask'   => 1,
			'require_login_to_reply' => 1,
			'enable_voting'          => 1,
			'enable_best_answer'     => 1,
			'enable_question_views'  => 1,
			'questions_per_page'     => 10,
		];
	}

	/**
	 * Registers settings, sections, and fields.
	 *
	 * @since 1.0.0
	 */
	public function register_settings() {
		register_setting(
			'askora_general_group',
			$this->option_key,
			[ $this, 'sanitize' ]
		);

		add_settings_section(
			'askora_general_section',
			esc_html__( 'General Settings', 'askora-community-qa' ),
			[ $this, 'section_info' ],
			'askora_general_settings'
		);

		// Submit form page.
		add_settings_field(
			'submit_form_page_id',
			esc_html__( 'Ask a Question Page', 'askora-community-qa' ),
			[ $this, 'render_submit_form_page' ],
			'askora_general_settings',
			'askora_general_section'
		);

		// Questions list page.
		add_settings_field(
			'questions_list_page_id',
			esc_html__( 'Questions List Page', 'askora-community-qa' ),
			[ $this, 'render_questions_list_page' ],
			'askora_general_settings',
			'askora_general_section'
		);

		// Question default status.
		add_settings_field(
			'question_status',
			esc_html__( 'Default Question Status', 'askora-community-qa' ),
			[ $this, 'render_question_status' ],
			'askora_general_settings',
			'askora_general_section'
		);

		// Allow guest replies.
		add_settings_field(
			'allow_guest_replies',
			esc_html__( 'Allow Guest Replies', 'askora-community-qa' ),
			[ $this, 'render_guest_replies' ],
			'askora_general_settings',
			'askora_general_section'
		);

		// Require login to ask.
		add_settings_field(
			'require_login_to_ask',
			esc_html__( 'Require Login to Ask', 'askora-community-qa' ),
			[ $this, 'render_require_login_ask' ],
			'askora_general_settings',
			'askora_general_section'
		);

		// Require login to reply.
		add_settings_field(
			'require_login_to_reply',
			esc_html__( 'Require Login to Reply', 'askora-community-qa' ),
			[ $this, 'render_require_login_reply' ],
			'askora_general_settings',
			'askora_general_section'
		);

		// Enable voting.
		add_settings_field(
			'enable_voting',
			esc_html__( 'Enable Voting', 'askora-community-qa' ),
			[ $this, 'render_enable_voting' ],
			'askora_general_settings',
			'askora_general_section'
		);

		// Enable best answer.
		add_settings_field(
			'enable_best_answer',
			esc_html__( 'Enable Best Answer', 'askora-community-qa' ),
			[ $this, 'render_enable_best_answer' ],
			'askora_general_settings',
			'askora_general_section'
		);

		// Enable views.
		add_settings_field(
			'enable_question_views',
			esc_html__( 'Enable Question Views', 'askora-community-qa' ),
			[ $this, 'render_enable_views' ],
			'askora_general_settings',
			'askora_general_section'
		);

		// Questions per page.
		add_settings_field(
			'questions_per_page',
			esc_html__( 'Questions Per Page', 'askora-community-qa' ),
			[ $this, 'render_questions_per_page' ],
			'askora_general_settings',
			'askora_general_section'
		);
	}

	/**
	 * Section description.
	 *
	 * @since 1.0.0
	 */
	public function section_info() {
		echo '<p>' . esc_html__( 'Configure general Q&A behavior for Askora Community Q&A.', 'askora-community-qa' ) . '</p>';
	}

	/**
	 * Renders question status field.
	 *
	 * @since 1.0.0
	 */
	public function render_question_status() {
		$options = $this->get_all();
		$value   = $options['question_status'];
		?>
		<select name="<?php echo esc_attr( $this->option_key ); ?>[question_status]" id="askora_question_status">
			<option value="pending" <?php selected( $value, 'pending' ); ?>><?php esc_html_e( 'Pending Review', 'askora-community-qa' ); ?></option>
			<option value="publish" <?php selected( $value, 'publish' ); ?>><?php esc_html_e( 'Publish Immediately', 'askora-community-qa' ); ?></option>
			<option value="draft"   <?php selected( $value, 'draft' ); ?>><?php esc_html_e( 'Save as Draft', 'askora-community-qa' ); ?></option>
		</select>
		<p class="description"><?php esc_html_e( 'Status assigned to newly submitted questions.', 'askora-community-qa' ); ?></p>
		<?php
	}

	/**
	 * Renders allow guest replies checkbox.
	 *
	 * @since 1.0.0
	 */
	public function render_guest_replies() {
		$options = $this->get_all();
		?>
		<label>
			<input type="checkbox" name="<?php echo esc_attr( $this->option_key ); ?>[allow_guest_replies]" id="askora_allow_guest_replies" value="1" <?php checked( 1, (int) $options['allow_guest_replies'] ); ?>>
			<?php esc_html_e( 'Allow non-logged-in users to submit replies.', 'askora-community-qa' ); ?>
		</label>
		<?php
	}

	/**
	 * Renders require login to ask checkbox.
	 *
	 * @since 1.0.0
	 */
	public function render_require_login_ask() {
		$options = $this->get_all();
		?>
		<label>
			<input type="checkbox" name="<?php echo esc_attr( $this->option_key ); ?>[require_login_to_ask]" id="askora_require_login_ask" value="1" <?php checked( 1, (int) $options['require_login_to_ask'] ); ?>>
			<?php esc_html_e( 'Users must be logged in to ask a question.', 'askora-community-qa' ); ?>
		</label>
		<?php
	}

	/**
	 * Renders require login to reply checkbox.
	 *
	 * @since 1.0.0
	 */
	public function render_require_login_reply() {
		$options = $this->get_all();
		?>
		<label>
			<input type="checkbox" name="<?php echo esc_attr( $this->option_key ); ?>[require_login_to_reply]" id="askora_require_login_reply" value="1" <?php checked( 1, (int) $options['require_login_to_reply'] ); ?>>
			<?php esc_html_e( 'Users must be logged in to submit a reply.', 'askora-community-qa' ); ?>
		</label>
		<?php
	}

	/**
	 * Renders enable voting checkbox.
	 *
	 * @since 1.0.0
	 */
	public function render_enable_voting() {
		$options = $this->get_all();
		?>
		<label>
			<input type="checkbox" name="<?php echo esc_attr( $this->option_key ); ?>[enable_voting]" id="askora_enable_voting" value="1" <?php checked( 1, (int) $options['enable_voting'] ); ?>>
			<?php esc_html_e( 'Enable upvoting for questions and answers.', 'askora-community-qa' ); ?>
		</label>
		<?php
	}

	/**
	 * Renders enable best answer checkbox.
	 *
	 * @since 1.0.0
	 */
	public function render_enable_best_answer() {
		$options = $this->get_all();
		?>
		<label>
			<input type="checkbox" name="<?php echo esc_attr( $this->option_key ); ?>[enable_best_answer]" id="askora_enable_best_answer" value="1" <?php checked( 1, (int) $options['enable_best_answer'] ); ?>>
			<?php esc_html_e( 'Allow marking one reply as the Best Answer.', 'askora-community-qa' ); ?>
		</label>
		<?php
	}

	/**
	 * Renders enable views checkbox.
	 *
	 * @since 1.0.0
	 */
	public function render_enable_views() {
		$options = $this->get_all();
		?>
		<label>
			<input type="checkbox" name="<?php echo esc_attr( $this->option_key ); ?>[enable_question_views]" id="askora_enable_views" value="1" <?php checked( 1, (int) $options['enable_question_views'] ); ?>>
			<?php esc_html_e( 'Track and display question view counts.', 'askora-community-qa' ); ?>
		</label>
		<?php
	}

	/**
	 * Renders Ask a Question Page selector.
	 *
	 * @since 1.0.0
	 */
	public function render_submit_form_page() {
		$options = $this->get_all();
		$page_id = (int) ( $options['submit_form_page_id'] ?? 0 );
		wp_dropdown_pages( [
			'name'              => esc_attr( $this->option_key ) . '[submit_form_page_id]',
			'id'                => 'askora_submit_form_page_id',
			'selected'          => wp_kses_post($page_id),
			'show_option_none'  => esc_html__( '— Select a Page —', 'askora-community-qa' ),
			'option_none_value' => '0',
		] );
		echo '<p class="description">' . esc_html__( 'Select the page containing the [askora_submit_form] shortcode. Used for the \'Ask a Question\' button.', 'askora-community-qa' ) . '</p>';
	}

	/**
	 * Renders Questions List Page selector.
	 *
	 * @since 1.0.0
	 */
	public function render_questions_list_page() {
		$options = $this->get_all();
		$page_id = (int) ( $options['questions_list_page_id'] ?? 0 );
		wp_dropdown_pages( [
			'name'              => esc_attr( $this->option_key ) . '[questions_list_page_id]',
			'id'                => 'askora_questions_list_page_id',
			'selected'          => wp_kses_post($page_id),
			'show_option_none'  => esc_html__( '— Select a Page —', 'askora-community-qa' ),
			'option_none_value' => '0',
		] );
		echo '<p class="description">' . esc_html__( 'Select the page containing the [askora_questions] shortcode. Used for the "All Questions" breadcrumb link on single question pages.', 'askora-community-qa' ) . '</p>';
	}

	/**
	 * Renders questions per page field.
	 *
	 * @since 1.0.0
	 */
	public function render_questions_per_page() {
		$options = $this->get_all();
		?>
		<input type="number" name="<?php echo esc_attr( $this->option_key ); ?>[questions_per_page]" id="askora_questions_per_page" value="<?php echo esc_attr( (int) $options['questions_per_page'] ); ?>" min="1" max="100" class="small-text">
		<p class="description"><?php esc_html_e( 'Number of questions to display per page.', 'askora-community-qa' ); ?></p>
		<?php
	}

	/**
	 * Sanitizes submitted values.
	 *
	 * @param  array $input Raw input.
	 * @return array
	 * @since  1.0.0
	 */
	public function sanitize( $input ) {
		if ( ! isset( $_POST['askora_nonce'] ) ||
			! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['askora_nonce'] ) ), 'askora_save_settings' ) ) {
			add_settings_error( 'askora_settings', 'nonce_error', esc_html__( 'Security check failed.', 'askora-community-qa' ), 'error' );
			return get_option( $this->option_key, [] );
		}

		$existing = get_option( $this->option_key, [] );
		$input    = is_array( $input ) ? $input : [];

		// If this is NOT our tab being submitted, pass the $input through unmodified.
		// The sanitize callback for the correct tab will handle merging it with $existing.
		if ( ! isset( $_POST['option_page'] ) || 'askora_general_group' !== sanitize_text_field( wp_unslash( $_POST['option_page'] ) ) ) {
			return $input;
		}

		$existing['submit_form_page_id']    = isset( $input['submit_form_page_id'] ) ? absint( $input['submit_form_page_id'] ) : 0;
		$existing['questions_list_page_id'] = isset( $input['questions_list_page_id'] ) ? absint( $input['questions_list_page_id'] ) : 0;
		$existing['question_status']        = isset( $input['question_status'] ) && in_array( $input['question_status'], [ 'pending', 'publish', 'draft' ], true ) ? $input['question_status'] : 'pending';
		$existing['allow_guest_replies']    = isset( $input['allow_guest_replies'] ) ? 1 : 0;
		$existing['require_login_to_ask']   = isset( $input['require_login_to_ask'] ) ? 1 : 0;
		$existing['require_login_to_reply'] = isset( $input['require_login_to_reply'] ) ? 1 : 0;
		$existing['enable_voting']          = isset( $input['enable_voting'] ) ? 1 : 0;
		$existing['enable_best_answer']     = isset( $input['enable_best_answer'] ) ? 1 : 0;
		$existing['enable_question_views']  = isset( $input['enable_question_views'] ) ? 1 : 0;
		$existing['questions_per_page']     = isset( $input['questions_per_page'] ) ? absint( $input['questions_per_page'] ) : 10;

		add_settings_error( 'askora_settings', 'settings_saved', esc_html__( 'General settings saved.', 'askora-community-qa' ), 'updated' );

		return $existing;
	}
}
