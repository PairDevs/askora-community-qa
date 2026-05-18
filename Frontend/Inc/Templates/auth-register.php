<?php
/**
 * Template: auth-register.php
 * Shortcode: [askora_register]
 *
 * @package ASKORA
 */

defined( 'ABSPATH' ) || exit;

$askora_settings       = get_option( 'askora_settings', [] );
$askora_email_required = ! empty( $askora_settings['email_required'] );
?>
<div class="askora-wrapper askora-auth-wrapper">
	<div class="askora-card askora-auth-form askora-register-form">
		<h2 class="askora-auth-title"><?php esc_html_e( 'Create Account', 'askora-community-qa' ); ?></h2>
		<p class="askora-auth-subtitle"><?php esc_html_e( 'Register with your phone number — no email required.', 'askora-community-qa' ); ?></p>

		<div class="askora-alert askora-alert-success" id="askora-register-success" style="display:none;"></div>
		<div class="askora-alert askora-alert-error"   id="askora-register-error"   style="display:none;"></div>

		<form id="askora-register-form" class="askora-form" novalidate>
			<?php wp_nonce_field( 'askora_nonce', 'askora_nonce_field' ); ?>

			<div class="askora-form-group">
				<label for="askora-reg-name"><?php esc_html_e( 'Full Name', 'askora-community-qa' ); ?> <span class="required">*</span></label>
				<input type="text" id="askora-reg-name" name="name" class="askora-input" placeholder="<?php esc_attr_e( 'Your full name', 'askora-community-qa' ); ?>" required autocomplete="name">
			</div>

			<div class="askora-form-group">
				<label for="askora-reg-phone"><?php esc_html_e( 'Phone Number', 'askora-community-qa' ); ?> <span class="required">*</span></label>
				<input type="tel" id="askora-reg-phone" name="phone" class="askora-input" placeholder="<?php esc_attr_e( '+1 555 000 0000', 'askora-community-qa' ); ?>" required autocomplete="tel">
			</div>

			<div class="askora-form-group">
				<label for="askora-reg-email"><?php esc_html_e( 'Email Address', 'askora-community-qa' ); ?> <?php echo wp_kses_post( $askora_email_required ? '<span class="required">*</span>' : '<span class="askora-optional">(' . esc_html__( 'optional', 'askora-community-qa' ) . ')</span>' ); ?></label>
				<input type="email" id="askora-reg-email" name="email" class="askora-input" placeholder="<?php esc_attr_e( 'you@example.com', 'askora-community-qa' ); ?>" <?php echo esc_attr( $askora_email_required ? 'required' : '' ); ?> autocomplete="email">
			</div>

			<div class="askora-form-group">
				<label for="askora-reg-password"><?php esc_html_e( 'Password', 'askora-community-qa' ); ?> <span class="required">*</span></label>
				<input type="password" id="askora-reg-password" name="password" class="askora-input" placeholder="••••••••" required autocomplete="new-password" minlength="6">
			</div>

			<div class="askora-form-group">
				<label for="askora-reg-confirm"><?php esc_html_e( 'Confirm Password', 'askora-community-qa' ); ?> <span class="required">*</span></label>
				<input type="password" id="askora-reg-confirm" name="confirm_password" class="askora-input" placeholder="••••••••" required autocomplete="new-password">
			</div>

			<div class="askora-form-actions">
				<button type="submit" class="askora-button askora-button-primary askora-full-width">
					<span class="askora-btn-text"><?php esc_html_e( 'Create Account', 'askora-community-qa' ); ?></span>
					<span class="askora-spinner" style="display:none;"></span>
				</button>
			</div>
		</form>
	</div>
</div>
