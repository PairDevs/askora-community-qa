<?php
/**
 * Template: search-form.php
 * Shortcode: [askora_search]
 *
 * @package ASKORA
 */

defined( 'ABSPATH' ) || exit;

$askora_categories = get_terms( [ 'taxonomy' => 'question_category', 'hide_empty' => true ] );
?>
<div class="askora-wrapper askora-search-wrapper">
	<form class="askora-search-form askora-form" id="askora-search-form" role="search">
		<?php wp_nonce_field( 'askora_nonce', 'askora_nonce_field' ); ?>
		<div class="askora-search-row">
			<div class="askora-search-input-wrap">
				<span class="dashicons dashicons-search askora-search-icon"></span>
				<input type="search" id="askora-search-input" class="askora-input askora-search-input" name="keyword" placeholder="<?php esc_attr_e( 'Search questions…', 'askora-community-qa' ); ?>" autocomplete="off">
			</div>

			<?php if ( ! empty( $askora_categories ) && ! is_wp_error( $askora_categories ) ) : ?>
			<select class="askora-select askora-search-category" name="category">
				<option value=""><?php esc_html_e( 'All Categories', 'askora-community-qa' ); ?></option>
				<?php foreach ( $askora_categories as $askora_cat ) : ?>
					<option value="<?php echo esc_attr( $askora_cat->term_id ); ?>"><?php echo esc_html( $askora_cat->name ); ?></option>
				<?php endforeach; ?>
			</select>
			<?php endif; ?>

			<button type="submit" class="askora-button askora-button-primary"><?php esc_html_e( 'Search', 'askora-community-qa' ); ?></button>
		</div>
	</form>

	<div class="askora-search-results" id="askora-search-results" style="display:none;">
		<div class="askora-spinner" id="askora-search-spinner" style="display:none;"></div>
		<div class="askora-results-list" id="askora-results-list"></div>
	</div>
</div>
