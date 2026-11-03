<?php
/**
 * Template: auth-prompt.php
 * Shown when a visitor tries to access a protected shortcode without logging in.
 *
 * @package ASKORA
 */

defined( 'ABSPATH' ) || exit;

// Prefer the plugin's own phone login/register page (created on activation) over wp-login.php.
$askora_pages        = get_option( 'askora_pages', [] );
$askora_auth_page_id = isset( $askora_pages['auth'] ) ? (int) $askora_pages['auth'] : 0;
$askora_auth_url     = '';
if ( $askora_auth_page_id > 0 && 'publish' === get_post_status( $askora_auth_page_id ) && \ASKORA\Frontend\Inc\Helpers\Permission::phone_auth_enabled() ) {
	$askora_auth_url = get_permalink( $askora_auth_page_id );
}
?>
<div class="askora-wrapper">
	<div class="askora-card askora-auth-prompt">
		<span class="dashicons dashicons-lock askora-prompt-icon"></span>
		<p class="askora-prompt-text">
			<?php esc_html_e( 'Please log in or create an account to continue.', 'askora-community-qa' ); ?>
		</p>
		<div class="askora-prompt-actions">
			<a href="<?php echo esc_url( $askora_auth_url ? $askora_auth_url : wp_login_url( get_permalink() ) ); ?>" class="askora-button askora-button-primary">
				<?php esc_html_e( 'Sign In', 'askora-community-qa' ); ?>
			</a>
			<?php if ( $askora_auth_url ) : ?>
			<a href="<?php echo esc_url( $askora_auth_url ); ?>" class="askora-button askora-button-secondary">
				<?php esc_html_e( 'Create Account', 'askora-community-qa' ); ?>
			</a>
			<?php elseif ( get_option( 'users_can_register' ) ) : ?>
			<a href="<?php echo esc_url( wp_registration_url() ); ?>" class="askora-button askora-button-secondary">
				<?php esc_html_e( 'Create Account', 'askora-community-qa' ); ?>
			</a>
			<?php endif; ?>
		</div>
	</div>
</div>
