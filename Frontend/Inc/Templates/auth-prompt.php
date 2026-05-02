<?php
/**
 * Template: auth-prompt.php
 * Shown when a visitor tries to access a protected shortcode without logging in.
 *
 * @package QuestionHub
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="questionhub-wrapper">
	<div class="questionhub-card questionhub-auth-prompt">
		<span class="dashicons dashicons-lock questionhub-prompt-icon"></span>
		<p class="questionhub-prompt-text">
			<?php esc_html_e( 'Please log in or create an account to continue.', 'questionhub' ); ?>
		</p>
		<div class="questionhub-prompt-actions">
			<a href="<?php echo esc_url( wp_login_url( get_permalink() ) ); ?>" class="questionhub-button questionhub-button-primary">
				<?php esc_html_e( 'Sign In', 'questionhub' ); ?>
			</a>
			<?php if ( get_option( 'users_can_register' ) ) : ?>
			<a href="<?php echo esc_url( wp_registration_url() ); ?>" class="questionhub-button questionhub-button-secondary">
				<?php esc_html_e( 'Create Account', 'questionhub' ); ?>
			</a>
			<?php endif; ?>
		</div>
	</div>
</div>
