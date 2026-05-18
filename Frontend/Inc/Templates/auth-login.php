<?php
/**
 * Template: auth-login.php
 * Shortcode: [askora_login]
 *
 * @package ASKORA
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="askora-wrapper askora-auth-wrapper">
	<div class="askora-card askora-auth-form askora-login-form">
		<h2 class="askora-auth-title"><?php esc_html_e( 'Sign In', 'askora-community-qa' ); ?></h2>
		<p class="askora-auth-subtitle"><?php esc_html_e( 'Login with your phone number and password.', 'askora-community-qa' ); ?></p>

		<div class="askora-alert askora-alert-success" id="askora-login-success" style="display:none;"></div>
		<div class="askora-alert askora-alert-error"   id="askora-login-error"   style="display:none;"></div>

		<form id="askora-login-form" class="askora-form" novalidate>
			<?php wp_nonce_field( 'askora_nonce', 'askora_nonce_field' ); ?>

			<div class="askora-form-group">
				<label for="askora-login-phone"><?php esc_html_e( 'Phone Number', 'askora-community-qa' ); ?></label>
				<input type="tel" id="askora-login-phone" name="phone" class="askora-input" placeholder="<?php esc_attr_e( '+1 555 000 0000', 'askora-community-qa' ); ?>" required autocomplete="tel">
			</div>

			<div class="askora-form-group">
				<label for="askora-login-password"><?php esc_html_e( 'Password', 'askora-community-qa' ); ?></label>
				<input type="password" id="askora-login-password" name="password" class="askora-input" placeholder="••••••••" required autocomplete="current-password">
			</div>

			<div class="askora-form-actions">
				<button type="submit" class="askora-button askora-button-primary askora-full-width">
					<span class="askora-btn-text"><?php esc_html_e( 'Sign In', 'askora-community-qa' ); ?></span>
					<span class="askora-spinner" style="display:none;"></span>
				</button>
			</div>
		</form>
	</div>
</div>
