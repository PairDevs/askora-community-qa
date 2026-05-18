<?php
/**
 * Template: question-list.php
 * Shortcode: [askora_questions]
 *
 * @package ASKORA
 * @var array $askora_atts Shortcode attributes.
 */

defined( 'ABSPATH' ) || exit;

$askora_settings   = get_option( 'askora_settings', [] );
$askora_per_page       = ! empty( $askora_atts['per_page'] ) ? absint( $askora_atts['per_page'] ) : ( isset( $askora_settings['questions_per_page'] ) ? absint( $askora_settings['questions_per_page'] ) : 10 );
$askora_category    = ! empty( $askora_atts['category'] ) ? absint( $askora_atts['category'] ) : 0;
$askora_tag            = ! empty( $askora_atts['tag'] ) ? sanitize_text_field( $askora_atts['tag'] ) : '';
$askora_orderby        = ! empty( $askora_atts['orderby'] ) ? sanitize_key( $askora_atts['orderby'] ) : 'date';
$askora_unanswered  = ! empty( $askora_atts['unanswered'] );

// Attr flags — accept 'true'/'false' strings or 1/0.
$askora_show_ask_btn = ! in_array( strtolower( (string) ( $askora_atts['show_ask_btn'] ?? 'true' ) ), [ 'false', '0', 'no' ], true );
$askora_show_search  = ! in_array( strtolower( (string) ( $askora_atts['show_search'] ?? 'true' ) ), [ 'false', '0', 'no' ], true );

// ── Ask a Question URL ────────────────────────────────────────────────────────
$askora_ask_url = '';
if ( $askora_show_ask_btn ) {
	$askora_submit_page_id = (int) ( $askora_settings['submit_form_page_id'] ?? 0 );
	if ( $askora_submit_page_id > 0 && 'publish' === get_post_status( $askora_submit_page_id ) ) {
		$askora_ask_url = get_permalink( $askora_submit_page_id );
	}
	if ( ! $askora_ask_url ) {
		$askora_detected_pages = get_posts( [
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			's'              => 'askora_submit_form',
			'fields'         => 'ids',
		] );

		if ( ! empty( $askora_detected_pages ) ) {
			$askora_ask_url = get_permalink( (int) $askora_detected_pages[0] );
		}
	}
	if ( ! $askora_ask_url ) {
		$askora_ask_url = apply_filters( 'askora_ask_url', home_url() );
	}
}

// ── Query ─────────────────────────────────────────────────────────────────────
$askora_query_args = [
	'post_type'      => 'questions',
	'post_status'    => 'publish',
	'posts_per_page' => $askora_per_page,
	'orderby'        => $askora_orderby,
	'order'          => 'DESC',
];
if ( 'meta_value_num' === $askora_orderby ) {
	$askora_query_args['meta_key'] = '_askora_views'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
}
if ( $askora_category ) {
	$askora_query_args['tax_query'] = [ [ 'taxonomy' => 'question_category', 'field' => 'term_id', 'terms' => $askora_category ] ]; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
}
if ( $askora_unanswered ) {
	$askora_query_args['comment_count'] = 0;
}

$askora_query      = new WP_Query( $askora_query_args );
$askora_categories = get_terms( [ 'taxonomy' => 'question_category', 'hide_empty' => true ] );
?>
<div class="askora-wrapper askora-list-wrapper" data-page="1" data-category="<?php echo esc_attr( $askora_category ); ?>" data-tag="<?php echo esc_attr( $askora_tag ); ?>" data-orderby="<?php echo esc_attr( $askora_orderby ); ?>">

	<?php if ( $askora_show_search ) : ?>
	<div class="askora-search-wrap askora-list-search">
		<span class="dashicons dashicons-search"></span>
		<input type="search"
			   id="askora-search-input"
			   class="askora-input askora-search-input"
			   placeholder="<?php esc_attr_e( 'Search questions…', 'askora-community-qa' ); ?>">
	</div>
	<?php endif; ?>

	<div class="askora-list-controls">
		<?php if ( ! empty( $askora_categories ) && ! is_wp_error( $askora_categories ) ) : ?>
		<select class="askora-filter-category askora-select">
			<option value=""><?php esc_html_e( 'All Categories', 'askora-community-qa' ); ?></option>
			<?php foreach ( $askora_categories as $askora_cat ) : ?>
				<option value="<?php echo esc_attr( $askora_cat->term_id ); ?>" <?php selected( $askora_category, $askora_cat->term_id ); ?>><?php echo esc_html( $askora_cat->name ); ?></option>
			<?php endforeach; ?>
		</select>
		<?php endif; ?>

		<select class="askora-sort askora-select">
			<option value="date"           <?php selected( $askora_orderby, 'date' ); ?>><?php esc_html_e( 'Newest', 'askora-community-qa' ); ?></option>
			<option value="comment_count"  <?php selected( $askora_orderby, 'comment_count' ); ?>><?php esc_html_e( 'Most Answered', 'askora-community-qa' ); ?></option>
			<option value="meta_value_num" <?php selected( $askora_orderby, 'meta_value_num' ); ?>><?php esc_html_e( 'Most Viewed', 'askora-community-qa' ); ?></option>
		</select>

		<?php if ( $askora_show_ask_btn && $askora_ask_url ) : ?>
		<a href="<?php echo esc_url( $askora_ask_url ); ?>" class="askora-button askora-button-primary askora-ask-btn">
			<span class="dashicons dashicons-edit"></span>
			<?php esc_html_e( 'Ask a Question', 'askora-community-qa' ); ?>
		</a>
		<?php endif; ?>
	</div>

	<div class="askora-questions-list">
		<?php
		if ( $askora_query->have_posts() ) {
			while ( $askora_query->have_posts() ) {
				$askora_query->the_post();
				\ASKORA\Frontend\Inc\Helpers\Template::load( 'question-card.php', [ 'post_id' => get_the_ID() ] );
			}
			wp_reset_postdata();
		} else {
			echo '<p class="askora-no-results">' . esc_html__( 'No questions found.', 'askora-community-qa' ) . '</p>';
		}
		?>
	</div>

	<?php if ( $askora_query->max_num_pages > 1 ) : ?>
	<div class="askora-load-more-wrap">
		<button class="askora-button askora-load-more" data-page="2" data-max="<?php echo esc_attr( $askora_query->max_num_pages ); ?>">
			<?php esc_html_e( 'Load More', 'askora-community-qa' ); ?>
		</button>
		<span class="askora-spinner" style="display:none;"></span>
	</div>
	<?php endif; ?>
</div>
