<?php
/**
 * Admin notices for Askora Community Q&A.
 *
 * @package ASKORA\Admin\Inc\Notices
 * @since   1.0.0
 */

namespace ASKORA\Admin\Inc\Notices;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class AdminNotices
 *
 * Displays a dismissible activation notice on first install.
 */
class AdminNotices {

	/**
	 * Option key for dismissed state.
	 */
	const DISMISSED_OPTION = 'askora_welcome_notice_dismissed';

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'admin_notices', [ $this, 'show_welcome_notice' ] );
		add_action( 'wp_ajax_askora_dismiss_notice', [ $this, 'dismiss_notice' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_notice_script' ] );
	}

	/**
	 * Displays the welcome notice if not dismissed.
	 *
	 * @since 1.0.0
	 */
	public function show_welcome_notice() {
		if ( get_option( self::DISMISSED_OPTION ) ) {
			return;
		}
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		?>
		<div class="notice notice-success is-dismissible askora-welcome-notice" data-nonce="<?php echo esc_attr( wp_create_nonce( 'askora_dismiss_notice' ) ); ?>">
			<p>
				<strong><?php esc_html_e( 'Askora Community Q&A activated!', 'askora-community-qa' ); ?></strong>
				<?php
				printf(
					/* translators: 1: link to settings, 2: link to questions */
					esc_html__( 'Go to %1$s to configure the plugin, or %2$s to manage questions.', 'askora-community-qa' ),
					'<a href="' . esc_url( admin_url( 'admin.php?page=askora_settings' ) ) . '">' . esc_html__( 'Settings', 'askora-community-qa' ) . '</a>',
					'<a href="' . esc_url( admin_url( 'edit.php?post_type=questions' ) ) . '">' . esc_html__( 'All Questions', 'askora-community-qa' ) . '</a>'
				);
				?>
			</p>
		</div>
		<?php
	}

	/**
	 * Enqueues the inline JS for dismissing the welcome notice.
	 *
	 * Attached to admin_enqueue_scripts so we use wp_add_inline_script()
	 * instead of a raw <script> tag.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_notice_script() {
		if ( get_option( self::DISMISSED_OPTION ) ) {
			return;
		}
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// jquery is always available in WP admin — use it as the dependency handle.
		wp_add_inline_script(
			'jquery',
			'(function($){
				$(document).on("click", ".askora-welcome-notice .notice-dismiss", function(){
					$.post(ajaxurl, {
						action: "askora_dismiss_notice",
						nonce:  $(this).closest(".askora-welcome-notice").data("nonce")
					});
				});
			})(jQuery);'
		);
	}

	/**
	 * Marks the notice as dismissed via AJAX.
	 *
	 * @since 1.0.0
	 */
	public function dismiss_notice() {
		check_ajax_referer( 'askora_dismiss_notice', 'nonce' );
		update_option( self::DISMISSED_OPTION, 1 );
		wp_send_json_success();
	}
}
