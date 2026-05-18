<?php
/**
 * Template: question-form.php
 * Shortcode: [askora_submit_form]
 *
 * @package ASKORA
 */

defined( 'ABSPATH' ) || exit;

$askora_categories = get_terms( [ 'taxonomy' => 'question_category', 'hide_empty' => false ] );
?>
<div class="askora-wrapper askora-submit-form-wrapper">
	<div class="askora-card askora-form-card">
		<h2 class="askora-form-title"><?php esc_html_e( 'Ask a Question', 'askora-community-qa' ); ?></h2>

		<div class="askora-alert askora-alert-success" id="askora-submit-success" style="display:none;"></div>
		<div class="askora-alert askora-alert-error"   id="askora-submit-error"   style="display:none;"></div>

		<form id="askora-submit-form" class="askora-form" novalidate>
			<?php wp_nonce_field( 'askora_nonce', 'askora_nonce_field' ); ?>

			<div class="askora-form-group">
				<label for="askora-title"><?php esc_html_e( 'Question Title', 'askora-community-qa' ); ?> <span class="required">*</span></label>
				<input type="text" id="askora-title" name="title" class="askora-input" placeholder="<?php esc_attr_e( 'Enter your question…', 'askora-community-qa' ); ?>" required>
			</div>

			<div class="askora-form-group">
				<label for="askora-content"><?php esc_html_e( 'Question Details', 'askora-community-qa' ); ?></label>
				<textarea id="askora-content" name="content" class="askora-textarea" rows="6" placeholder="<?php esc_attr_e( 'Provide more details about your question…', 'askora-community-qa' ); ?>"></textarea>
			</div>

			<?php if ( ! empty( $askora_categories ) && ! is_wp_error( $askora_categories ) ) : ?>
			<div class="askora-form-group">
				<label for="askora-category"><?php esc_html_e( 'Category', 'askora-community-qa' ); ?></label>
				<select id="askora-category" name="categories[]" class="askora-select">
					<option value=""><?php esc_html_e( '— Select Category —', 'askora-community-qa' ); ?></option>
					<?php foreach ( $askora_categories as $askora_cat ) : ?>
						<option value="<?php echo esc_attr( $askora_cat->term_id ); ?>"><?php echo esc_html( $askora_cat->name ); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<?php endif; ?>

			<div class="askora-form-group">
				<label for="askora-tags"><?php esc_html_e( 'Tags', 'askora-community-qa' ); ?></label>
				<input type="text" id="askora-tags" name="tags" class="askora-input" placeholder="<?php esc_attr_e( 'e.g. wordpress, plugin, php', 'askora-community-qa' ); ?>">
				<p class="askora-field-help"><?php esc_html_e( 'Separate tags with commas.', 'askora-community-qa' ); ?></p>
			</div>

			<div class="askora-form-actions">
				<button type="submit" class="askora-button askora-button-primary" id="askora-submit-btn">
					<span class="askora-btn-text"><?php esc_html_e( 'Submit Question', 'askora-community-qa' ); ?></span>
					<span class="askora-spinner" style="display:none;"></span>
				</button>
			</div>
		</form>
	</div>
</div>
