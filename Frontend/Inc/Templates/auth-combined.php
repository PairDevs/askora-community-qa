<?php
/**
 * Template: auth-combined.php
 * Shortcode: [askora_auth] — tabbed login/register.
 *
 * @package ASKORA
 */

defined( 'ABSPATH' ) || exit;

$askora_settings       = get_option( 'askora_settings', [] );
$askora_email_required = ! empty( $askora_settings['email_required'] );
?>
<div class="askora-wrapper askora-auth-wrapper">
	<div class="askora-card askora-auth-combined">

		<div class="askora-auth-tabs">
			<button class="askora-auth-tab active" data-tab="login"><?php esc_html_e( 'Sign In', 'askora-community-qa' ); ?></button>
			<button class="askora-auth-tab" data-tab="register"><?php esc_html_e( 'Create Account', 'askora-community-qa' ); ?></button>
		</div>

		<!-- Login Tab -->
		<div class="askora-auth-tab-content active" id="askora-tab-login">
			<div class="askora-alert askora-alert-success" id="askora-login-success" style="display:none;"></div>
			<div class="askora-alert askora-alert-error"   id="askora-login-error"   style="display:none;"></div>
			<form id="askora-login-form" class="askora-form" novalidate>
				<?php wp_nonce_field( 'askora_nonce', 'askora_nonce_field' ); ?>
				<div class="askora-form-group">
					<label for="qh-login-phone"><?php esc_html_e( 'Phone Number', 'askora-community-qa' ); ?></label>
					<input type="tel" id="qh-login-phone" name="phone" class="askora-input" placeholder="<?php esc_attr_e( '+1 555 000 0000', 'askora-community-qa' ); ?>" required>
				</div>
				<div class="askora-form-group">
					<label for="qh-login-password"><?php esc_html_e( 'Password', 'askora-community-qa' ); ?></label>
					<input type="password" id="qh-login-password" name="password" class="askora-input" placeholder="••••••••" required>
				</div>
				<button type="submit" class="askora-button askora-button-primary askora-full-width">
					<span class="askora-btn-text"><?php esc_html_e( 'Sign In', 'askora-community-qa' ); ?></span>
					<span class="askora-spinner" style="display:none;"></span>
				</button>
			</form>
		</div>

		<!-- Register Tab -->
		<div class="askora-auth-tab-content" id="askora-tab-register" style="display:none;">
			<div class="askora-alert askora-alert-success" id="askora-register-success" style="display:none;"></div>
			<div class="askora-alert askora-alert-error"   id="askora-register-error"   style="display:none;"></div>
			<form id="askora-register-form" class="askora-form" novalidate>
				<?php wp_nonce_field( 'askora_nonce', 'askora_nonce_field' ); ?>
				<div class="askora-form-group">
					<label for="qh-reg-name"><?php esc_html_e( 'Full Name', 'askora-community-qa' ); ?></label>
					<input type="text" id="qh-reg-name" name="name" class="askora-input" placeholder="<?php esc_attr_e( 'Your full name', 'askora-community-qa' ); ?>" required>
				</div>
				<div class="askora-form-group">
					<label for="qh-reg-phone"><?php esc_html_e( 'Phone Number', 'askora-community-qa' ); ?></label>
					<input type="tel" id="qh-reg-phone" name="phone" class="askora-input" placeholder="<?php esc_attr_e( '+1 555 000 0000', 'askora-community-qa' ); ?>" required>
				</div>
				<div class="askora-form-group">
					<label for="qh-reg-email"><?php esc_html_e( 'Email', 'askora-community-qa' ); ?> <?php echo wp_kses_post( $askora_email_required ? '<span class="required">*</span>' : '<span class="askora-optional">(' . esc_html__( 'optional', 'askora-community-qa' ) . ')</span>' ); ?></label>
					<input type="email" id="qh-reg-email" name="email" class="askora-input" placeholder="<?php esc_attr_e( 'you@example.com', 'askora-community-qa' ); ?>" <?php echo esc_attr( $askora_email_required ? 'required' : '' ); ?>>
				</div>
				<div class="askora-form-group">
					<label for="qh-reg-pass"><?php esc_html_e( 'Password', 'askora-community-qa' ); ?></label>
					<input type="password" id="qh-reg-pass" name="password" class="askora-input" placeholder="••••••••" required minlength="6">
				</div>
				<div class="askora-form-group">
					<label for="qh-reg-confirm"><?php esc_html_e( 'Confirm Password', 'askora-community-qa' ); ?></label>
					<input type="password" id="qh-reg-confirm" name="confirm_password" class="askora-input" placeholder="••••••••" required>
				</div>
				<button type="submit" class="askora-button askora-button-primary askora-full-width">
					<span class="askora-btn-text"><?php esc_html_e( 'Create Account', 'askora-community-qa' ); ?></span>
					<span class="askora-spinner" style="display:none;"></span>
				</button>
			</form>
		</div>

	</div>
</div>
