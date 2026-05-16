<?php
/**
 * Template: auth-combined.php
 * Shortcode: [questionhub_auth] — tabbed login/register.
 *
 * @package QuestionHub
 */

defined( 'ABSPATH' ) || exit;

$questionhub_settings       = get_option( 'questionhub_settings', [] );
$questionhub_email_required = ! empty( $questionhub_settings['email_required'] );
?>
<div class="questionhub-wrapper questionhub-auth-wrapper">
	<div class="questionhub-card questionhub-auth-combined">

		<div class="questionhub-auth-tabs">
			<button class="questionhub-auth-tab active" data-tab="login"><?php esc_html_e( 'Sign In', 'questionhub' ); ?></button>
			<button class="questionhub-auth-tab" data-tab="register"><?php esc_html_e( 'Create Account', 'questionhub' ); ?></button>
		</div>

		<!-- Login Tab -->
		<div class="questionhub-auth-tab-content active" id="questionhub-tab-login">
			<div class="questionhub-alert questionhub-alert-success" id="questionhub-login-success" style="display:none;"></div>
			<div class="questionhub-alert questionhub-alert-error"   id="questionhub-login-error"   style="display:none;"></div>
			<form id="questionhub-login-form" class="questionhub-form" novalidate>
				<?php wp_nonce_field( 'questionhub_nonce', 'questionhub_nonce_field' ); ?>
				<div class="questionhub-form-group">
					<label for="qh-login-phone"><?php esc_html_e( 'Phone Number', 'questionhub' ); ?></label>
					<input type="tel" id="qh-login-phone" name="phone" class="questionhub-input" placeholder="<?php esc_attr_e( '+1 555 000 0000', 'questionhub' ); ?>" required>
				</div>
				<div class="questionhub-form-group">
					<label for="qh-login-password"><?php esc_html_e( 'Password', 'questionhub' ); ?></label>
					<input type="password" id="qh-login-password" name="password" class="questionhub-input" placeholder="••••••••" required>
				</div>
				<button type="submit" class="questionhub-button questionhub-button-primary questionhub-full-width">
					<span class="questionhub-btn-text"><?php esc_html_e( 'Sign In', 'questionhub' ); ?></span>
					<span class="questionhub-spinner" style="display:none;"></span>
				</button>
			</form>
		</div>

		<!-- Register Tab -->
		<div class="questionhub-auth-tab-content" id="questionhub-tab-register" style="display:none;">
			<div class="questionhub-alert questionhub-alert-success" id="questionhub-register-success" style="display:none;"></div>
			<div class="questionhub-alert questionhub-alert-error"   id="questionhub-register-error"   style="display:none;"></div>
			<form id="questionhub-register-form" class="questionhub-form" novalidate>
				<?php wp_nonce_field( 'questionhub_nonce', 'questionhub_nonce_field' ); ?>
				<div class="questionhub-form-group">
					<label for="qh-reg-name"><?php esc_html_e( 'Full Name', 'questionhub' ); ?></label>
					<input type="text" id="qh-reg-name" name="name" class="questionhub-input" placeholder="<?php esc_attr_e( 'Your full name', 'questionhub' ); ?>" required>
				</div>
				<div class="questionhub-form-group">
					<label for="qh-reg-phone"><?php esc_html_e( 'Phone Number', 'questionhub' ); ?></label>
					<input type="tel" id="qh-reg-phone" name="phone" class="questionhub-input" placeholder="<?php esc_attr_e( '+1 555 000 0000', 'questionhub' ); ?>" required>
				</div>
				<div class="questionhub-form-group">
					<label for="qh-reg-email"><?php esc_html_e( 'Email', 'questionhub' ); ?> <?php echo wp_kses_post( $questionhub_email_required ? '<span class="required">*</span>' : '<span class="questionhub-optional">(' . esc_html__( 'optional', 'questionhub' ) . ')</span>' ); ?></label>
					<input type="email" id="qh-reg-email" name="email" class="questionhub-input" placeholder="<?php esc_attr_e( 'you@example.com', 'questionhub' ); ?>" <?php echo esc_attr( $questionhub_email_required ? 'required' : '' ); ?>>
				</div>
				<div class="questionhub-form-group">
					<label for="qh-reg-pass"><?php esc_html_e( 'Password', 'questionhub' ); ?></label>
					<input type="password" id="qh-reg-pass" name="password" class="questionhub-input" placeholder="••••••••" required minlength="6">
				</div>
				<div class="questionhub-form-group">
					<label for="qh-reg-confirm"><?php esc_html_e( 'Confirm Password', 'questionhub' ); ?></label>
					<input type="password" id="qh-reg-confirm" name="confirm_password" class="questionhub-input" placeholder="••••••••" required>
				</div>
				<button type="submit" class="questionhub-button questionhub-button-primary questionhub-full-width">
					<span class="questionhub-btn-text"><?php esc_html_e( 'Create Account', 'questionhub' ); ?></span>
					<span class="questionhub-spinner" style="display:none;"></span>
				</button>
			</form>
		</div>

	</div>
</div>
