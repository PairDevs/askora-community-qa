<?php
/**
 * Template: search-form.php
 * Shortcode: [questionhub_search]
 *
 * @package QuestionHub
 */

defined( 'ABSPATH' ) || exit;

$questionhub_categories = get_terms( [ 'taxonomy' => 'question_category', 'hide_empty' => true ] );
?>
<div class="questionhub-wrapper questionhub-search-wrapper">
	<form class="questionhub-search-form questionhub-form" id="questionhub-search-form" role="search">
		<?php wp_nonce_field( 'questionhub_nonce', 'questionhub_nonce_field' ); ?>
		<div class="questionhub-search-row">
			<div class="questionhub-search-input-wrap">
				<span class="dashicons dashicons-search questionhub-search-icon"></span>
				<input type="search" id="questionhub-search-input" class="questionhub-input questionhub-search-input" name="keyword" placeholder="<?php esc_attr_e( 'Search questions…', 'questionhub' ); ?>" autocomplete="off">
			</div>

			<?php if ( ! empty( $questionhub_categories ) && ! is_wp_error( $questionhub_categories ) ) : ?>
			<select class="questionhub-select questionhub-search-category" name="category">
				<option value=""><?php esc_html_e( 'All Categories', 'questionhub' ); ?></option>
				<?php foreach ( $questionhub_categories as $questionhub_cat ) : ?>
					<option value="<?php echo esc_attr( $questionhub_cat->term_id ); ?>"><?php echo esc_html( $questionhub_cat->name ); ?></option>
				<?php endforeach; ?>
			</select>
			<?php endif; ?>

			<button type="submit" class="questionhub-button questionhub-button-primary"><?php esc_html_e( 'Search', 'questionhub' ); ?></button>
		</div>
	</form>

	<div class="questionhub-search-results" id="questionhub-search-results" style="display:none;">
		<div class="questionhub-spinner" id="questionhub-search-spinner" style="display:none;"></div>
		<div class="questionhub-results-list" id="questionhub-results-list"></div>
	</div>
</div>
