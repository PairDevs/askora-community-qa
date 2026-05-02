<?php
/**
 * Top-level admin menu for QuestionHub.
 *
 * @package QuestionHub\Admin\Inc\Dashboard\Menu
 * @since   1.0.0
 */

namespace QuestionHub\Admin\Inc\Dashboard\Menu;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class QuestionHub
 *
 * Registers the top-level "QuestionHub" admin menu page.
 */
class QuestionHub {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'admin_menu', [ $this, 'add_menu_page' ] );
	}

	/**
	 * Registers the top-level admin menu.
	 *
	 * @since 1.0.0
	 */
	public function add_menu_page() {
		add_menu_page(
			esc_html__( 'QuestionHub', 'questionhub' ),
			esc_html__( 'QuestionHub', 'questionhub' ),
			'manage_options',
			'questionhub_home',
			[ $this, 'render_home_page' ],
			'dashicons-editor-help',
			25
		);
	}

	/**
	 * Renders the QuestionHub home/welcome dashboard.
	 *
	 * @since 1.0.0
	 */
	public function render_home_page() {
		$version = defined( 'QUESTIONHUB_VERSION' ) ? QUESTIONHUB_VERSION : '1.0.0';
		?>
		<div class="wrap questionhub-admin-wrap">
			<div class="questionhub-admin-header">
				<div class="questionhub-admin-header-inner">
					<h1><?php echo esc_html( QUESTIONHUB_NAME ); ?></h1>
					<p class="questionhub-admin-version">
						<?php
						/* translators: %s: plugin version number */
						printf( esc_html__( 'Version %s', 'questionhub' ), esc_html( $version ) );
						?>
					</p>
					<div class="questionhub-admin-buttons">
						<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=questions' ) ); ?>" class="button button-primary">
							<?php esc_html_e( 'All Questions', 'questionhub' ); ?>
						</a>
						<a href="<?php echo esc_url( admin_url( 'admin.php?page=questionhub_settings' ) ); ?>" class="button">
							<?php esc_html_e( 'Settings', 'questionhub' ); ?>
						</a>
						<a href="https://github.com/PairDevs/QuestionHub" target="_blank" class="button">
							<?php esc_html_e( 'Documentation', 'questionhub' ); ?>
						</a>
					</div>
				</div>
			</div>

			<div class="questionhub-admin-cards">
				<div class="questionhub-admin-card">
					<span class="dashicons dashicons-editor-help"></span>
					<h3><?php esc_html_e( 'Questions', 'questionhub' ); ?></h3>
					<p><?php esc_html_e( 'Manage all user-submitted questions from one place.', 'questionhub' ); ?></p>
					<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=questions' ) ); ?>">
						<?php esc_html_e( 'View Questions →', 'questionhub' ); ?>
					</a>
				</div>
				<div class="questionhub-admin-card">
					<span class="dashicons dashicons-admin-settings"></span>
					<h3><?php esc_html_e( 'Settings', 'questionhub' ); ?></h3>
					<p><?php esc_html_e( 'Configure question status, auth, voting, and more.', 'questionhub' ); ?></p>
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=questionhub_settings' ) ); ?>">
						<?php esc_html_e( 'Go to Settings →', 'questionhub' ); ?>
					</a>
				</div>
				<div class="questionhub-admin-card">
					<span class="dashicons dashicons-chart-bar"></span>
					<h3><?php esc_html_e( 'Shortcodes', 'questionhub' ); ?></h3>
					<p><?php esc_html_e( 'Use shortcodes to display Q&A forms and lists on any page.', 'questionhub' ); ?></p>
					<a href="https://github.com/PairDevs/QuestionHub/blob/main/docs/shortcodes.md" target="_blank">
						<?php esc_html_e( 'View Shortcodes →', 'questionhub' ); ?>
					</a>
				</div>
			</div>
		</div>
		<?php
	}
}
