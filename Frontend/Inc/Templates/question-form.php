<?php
/**
 * Template: question-form.php
 * Shortcode: [questionhub_submit_form]
 *
 * @package QuestionHub
 */

defined( 'ABSPATH' ) || exit;

$categories = get_terms( [ 'taxonomy' => 'question_category', 'hide_empty' => false ] );
?>
<div class="questionhub-wrapper questionhub-submit-form-wrapper">
	<div class="questionhub-card questionhub-form-card">
		<h2 class="questionhub-form-title"><?php esc_html_e( 'Ask a Question', 'questionhub' ); ?></h2>

		<div class="questionhub-alert questionhub-alert-success" id="questionhub-submit-success" style="display:none;"></div>
		<div class="questionhub-alert questionhub-alert-error"   id="questionhub-submit-error"   style="display:none;"></div>

		<form id="questionhub-submit-form" class="questionhub-form" novalidate>
			<?php wp_nonce_field( 'questionhub_nonce', 'questionhub_nonce_field' ); ?>

			<div class="questionhub-form-group">
				<label for="questionhub-title"><?php esc_html_e( 'Question Title', 'questionhub' ); ?> <span class="required">*</span></label>
				<input type="text" id="questionhub-title" name="title" class="questionhub-input" placeholder="<?php esc_attr_e( 'Enter your question…', 'questionhub' ); ?>" required>
			</div>

			<div class="questionhub-form-group">
				<label for="questionhub-content"><?php esc_html_e( 'Question Details', 'questionhub' ); ?></label>
				<textarea id="questionhub-content" name="content" class="questionhub-textarea" rows="6" placeholder="<?php esc_attr_e( 'Provide more details about your question…', 'questionhub' ); ?>"></textarea>
			</div>

			<?php if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) : ?>
			<div class="questionhub-form-group">
				<label for="questionhub-category"><?php esc_html_e( 'Category', 'questionhub' ); ?></label>
				<select id="questionhub-category" name="categories[]" class="questionhub-select">
					<option value=""><?php esc_html_e( '— Select Category —', 'questionhub' ); ?></option>
					<?php foreach ( $categories as $cat ) : ?>
						<option value="<?php echo esc_attr( $cat->term_id ); ?>"><?php echo esc_html( $cat->name ); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<?php endif; ?>

			<div class="questionhub-form-group">
				<label for="questionhub-tags"><?php esc_html_e( 'Tags', 'questionhub' ); ?></label>
				<input type="text" id="questionhub-tags" name="tags" class="questionhub-input" placeholder="<?php esc_attr_e( 'e.g. wordpress, plugin, php', 'questionhub' ); ?>">
				<p class="questionhub-field-help"><?php esc_html_e( 'Separate tags with commas.', 'questionhub' ); ?></p>
			</div>

			<div class="questionhub-form-actions">
				<button type="submit" class="questionhub-button questionhub-button-primary" id="questionhub-submit-btn">
					<span class="questionhub-btn-text"><?php esc_html_e( 'Submit Question', 'questionhub' ); ?></span>
					<span class="questionhub-spinner" style="display:none;"></span>
				</button>
			</div>
		</form>
	</div>
</div>
