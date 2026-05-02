<?php
/**
 * Template: auth-login.php
 * Shortcode: [questionhub_login]
 *
 * @package QuestionHub
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="questionhub-wrapper questionhub-auth-wrapper">
	<div class="questionhub-card questionhub-auth-form questionhub-login-form">
		<h2 class="questionhub-auth-title"><?php esc_html_e( 'Sign In', 'questionhub' ); ?></h2>
		<p class="questionhub-auth-subtitle"><?php esc_html_e( 'Login with your phone number and password.', 'questionhub' ); ?></p>

		<div class="questionhub-alert questionhub-alert-success" id="questionhub-login-success" style="display:none;"></div>
		<div class="questionhub-alert questionhub-alert-error"   id="questionhub-login-error"   style="display:none;"></div>

		<form id="questionhub-login-form" class="questionhub-form" novalidate>
			<?php wp_nonce_field( 'questionhub_nonce', 'questionhub_nonce_field' ); ?>

			<div class="questionhub-form-group">
				<label for="questionhub-login-phone"><?php esc_html_e( 'Phone Number', 'questionhub' ); ?></label>
				<input type="tel" id="questionhub-login-phone" name="phone" class="questionhub-input" placeholder="<?php esc_attr_e( '+1 555 000 0000', 'questionhub' ); ?>" required autocomplete="tel">
			</div>

			<div class="questionhub-form-group">
				<label for="questionhub-login-password"><?php esc_html_e( 'Password', 'questionhub' ); ?></label>
				<input type="password" id="questionhub-login-password" name="password" class="questionhub-input" placeholder="••••••••" required autocomplete="current-password">
			</div>

			<div class="questionhub-form-actions">
				<button type="submit" class="questionhub-button questionhub-button-primary questionhub-full-width">
					<span class="questionhub-btn-text"><?php esc_html_e( 'Sign In', 'questionhub' ); ?></span>
					<span class="questionhub-spinner" style="display:none;"></span>
				</button>
			</div>
		</form>
	</div>
</div>
