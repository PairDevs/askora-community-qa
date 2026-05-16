<?php
/**
 * Template: auth-register.php
 * Shortcode: [questionhub_register]
 *
 * @package QuestionHub
 */

defined( 'ABSPATH' ) || exit;

$questionhub_settings       = get_option( 'questionhub_settings', [] );
$questionhub_email_required = ! empty( $questionhub_settings['email_required'] );
?>
<div class="questionhub-wrapper questionhub-auth-wrapper">
	<div class="questionhub-card questionhub-auth-form questionhub-register-form">
		<h2 class="questionhub-auth-title"><?php esc_html_e( 'Create Account', 'questionhub' ); ?></h2>
		<p class="questionhub-auth-subtitle"><?php esc_html_e( 'Register with your phone number — no email required.', 'questionhub' ); ?></p>

		<div class="questionhub-alert questionhub-alert-success" id="questionhub-register-success" style="display:none;"></div>
		<div class="questionhub-alert questionhub-alert-error"   id="questionhub-register-error"   style="display:none;"></div>

		<form id="questionhub-register-form" class="questionhub-form" novalidate>
			<?php wp_nonce_field( 'questionhub_nonce', 'questionhub_nonce_field' ); ?>

			<div class="questionhub-form-group">
				<label for="questionhub-reg-name"><?php esc_html_e( 'Full Name', 'questionhub' ); ?> <span class="required">*</span></label>
				<input type="text" id="questionhub-reg-name" name="name" class="questionhub-input" placeholder="<?php esc_attr_e( 'Your full name', 'questionhub' ); ?>" required autocomplete="name">
			</div>

			<div class="questionhub-form-group">
				<label for="questionhub-reg-phone"><?php esc_html_e( 'Phone Number', 'questionhub' ); ?> <span class="required">*</span></label>
				<input type="tel" id="questionhub-reg-phone" name="phone" class="questionhub-input" placeholder="<?php esc_attr_e( '+1 555 000 0000', 'questionhub' ); ?>" required autocomplete="tel">
			</div>

			<div class="questionhub-form-group">
				<label for="questionhub-reg-email"><?php esc_html_e( 'Email Address', 'questionhub' ); ?> <?php echo wp_kses_post( $questionhub_email_required ? '<span class="required">*</span>' : '<span class="questionhub-optional">(' . esc_html__( 'optional', 'questionhub' ) . ')</span>' ); ?></label>
				<input type="email" id="questionhub-reg-email" name="email" class="questionhub-input" placeholder="<?php esc_attr_e( 'you@example.com', 'questionhub' ); ?>" <?php echo esc_attr( $questionhub_email_required ? 'required' : '' ); ?> autocomplete="email">
			</div>

			<div class="questionhub-form-group">
				<label for="questionhub-reg-password"><?php esc_html_e( 'Password', 'questionhub' ); ?> <span class="required">*</span></label>
				<input type="password" id="questionhub-reg-password" name="password" class="questionhub-input" placeholder="••••••••" required autocomplete="new-password" minlength="6">
			</div>

			<div class="questionhub-form-group">
				<label for="questionhub-reg-confirm"><?php esc_html_e( 'Confirm Password', 'questionhub' ); ?> <span class="required">*</span></label>
				<input type="password" id="questionhub-reg-confirm" name="confirm_password" class="questionhub-input" placeholder="••••••••" required autocomplete="new-password">
			</div>

			<div class="questionhub-form-actions">
				<button type="submit" class="questionhub-button questionhub-button-primary questionhub-full-width">
					<span class="questionhub-btn-text"><?php esc_html_e( 'Create Account', 'questionhub' ); ?></span>
					<span class="questionhub-spinner" style="display:none;"></span>
				</button>
			</div>
		</form>
	</div>
</div>
