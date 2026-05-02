<?php
/**
 * Admin notices for QuestionHub.
 *
 * @package QuestionHub\Admin\Inc\Notices
 * @since   1.0.0
 */

namespace QuestionHub\Admin\Inc\Notices;

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
	const DISMISSED_OPTION = 'questionhub_welcome_notice_dismissed';

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'admin_notices', [ $this, 'show_welcome_notice' ] );
		add_action( 'wp_ajax_questionhub_dismiss_notice', [ $this, 'dismiss_notice' ] );
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
		<div class="notice notice-success is-dismissible questionhub-welcome-notice" data-nonce="<?php echo esc_attr( wp_create_nonce( 'questionhub_dismiss_notice' ) ); ?>">
			<p>
				<strong><?php esc_html_e( 'QuestionHub activated!', 'questionhub' ); ?></strong>
				<?php
				printf(
					/* translators: 1: link to settings, 2: link to questions */
					esc_html__( 'Go to %1$s to configure the plugin, or %2$s to manage questions.', 'questionhub' ),
					'<a href="' . esc_url( admin_url( 'admin.php?page=questionhub_settings' ) ) . '">' . esc_html__( 'Settings', 'questionhub' ) . '</a>',
					'<a href="' . esc_url( admin_url( 'edit.php?post_type=questions' ) ) . '">' . esc_html__( 'All Questions', 'questionhub' ) . '</a>'
				);
				?>
			</p>
		</div>
		<script>
		(function($){
			$(document).on('click', '.questionhub-welcome-notice .notice-dismiss', function(){
				$.post(ajaxurl, {
					action: 'questionhub_dismiss_notice',
					nonce:  $(this).closest('.questionhub-welcome-notice').data('nonce')
				});
			});
		})(jQuery);
		</script>
		<?php
	}

	/**
	 * Marks the notice as dismissed via AJAX.
	 *
	 * @since 1.0.0
	 */
	public function dismiss_notice() {
		check_ajax_referer( 'questionhub_dismiss_notice', 'nonce' );
		update_option( self::DISMISSED_OPTION, 1 );
		wp_send_json_success();
	}
}
