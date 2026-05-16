<?php
/**
 * Template: question-list.php
 * Shortcode: [questionhub_questions]
 *
 * @package QuestionHub
 * @var array $questionhub_atts Shortcode attributes.
 */

defined( 'ABSPATH' ) || exit;

$questionhub_settings   = get_option( 'questionhub_settings', [] );
$questionhub_per_page       = ! empty( $questionhub_atts['per_page'] ) ? absint( $questionhub_atts['per_page'] ) : ( isset( $questionhub_settings['questions_per_page'] ) ? absint( $questionhub_settings['questions_per_page'] ) : 10 );
$questionhub_category    = ! empty( $questionhub_atts['category'] ) ? absint( $questionhub_atts['category'] ) : 0;
$questionhub_tag            = ! empty( $questionhub_atts['tag'] ) ? sanitize_text_field( $questionhub_atts['tag'] ) : '';
$questionhub_orderby        = ! empty( $questionhub_atts['orderby'] ) ? sanitize_key( $questionhub_atts['orderby'] ) : 'date';
$questionhub_unanswered  = ! empty( $questionhub_atts['unanswered'] );

// Attr flags — accept 'true'/'false' strings or 1/0.
$questionhub_show_ask_btn = ! in_array( strtolower( (string) ( $questionhub_atts['show_ask_btn'] ?? 'true' ) ), [ 'false', '0', 'no' ], true );
$questionhub_show_search  = ! in_array( strtolower( (string) ( $questionhub_atts['show_search'] ?? 'true' ) ), [ 'false', '0', 'no' ], true );

// ── Ask a Question URL ────────────────────────────────────────────────────────
$questionhub_ask_url = '';
if ( $questionhub_show_ask_btn ) {
	$questionhub_submit_page_id = (int) ( $questionhub_settings['submit_form_page_id'] ?? 0 );
	if ( $questionhub_submit_page_id > 0 && 'publish' === get_post_status( $questionhub_submit_page_id ) ) {
		$questionhub_ask_url = get_permalink( $questionhub_submit_page_id );
	}
	if ( ! $questionhub_ask_url ) {
		$questionhub_detected_pages = get_posts( [
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			's'              => 'questionhub_submit_form',
			'fields'         => 'ids',
		] );

		if ( ! empty( $questionhub_detected_pages ) ) {
			$questionhub_ask_url = get_permalink( (int) $questionhub_detected_pages[0] );
		}
	}
	if ( ! $questionhub_ask_url ) {
		$questionhub_ask_url = apply_filters( 'questionhub_ask_url', home_url() );
	}
}

// ── Query ─────────────────────────────────────────────────────────────────────
$questionhub_query_args = [
	'post_type'      => 'questions',
	'post_status'    => 'publish',
	'posts_per_page' => $questionhub_per_page,
	'orderby'        => $questionhub_orderby,
	'order'          => 'DESC',
];
if ( 'meta_value_num' === $questionhub_orderby ) {
	$questionhub_query_args['meta_key'] = '_questionhub_views'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
}
if ( $questionhub_category ) {
	$questionhub_query_args['tax_query'] = [ [ 'taxonomy' => 'question_category', 'field' => 'term_id', 'terms' => $questionhub_category ] ]; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
}
if ( $questionhub_unanswered ) {
	$questionhub_query_args['comment_count'] = 0;
}

$questionhub_query      = new WP_Query( $questionhub_query_args );
$questionhub_categories = get_terms( [ 'taxonomy' => 'question_category', 'hide_empty' => true ] );
?>
<div class="questionhub-wrapper questionhub-list-wrapper" data-page="1" data-category="<?php echo esc_attr( $questionhub_category ); ?>" data-tag="<?php echo esc_attr( $questionhub_tag ); ?>" data-orderby="<?php echo esc_attr( $questionhub_orderby ); ?>">

	<?php if ( $questionhub_show_search ) : ?>
	<div class="questionhub-search-wrap questionhub-list-search">
		<span class="dashicons dashicons-search"></span>
		<input type="search"
			   id="questionhub-search-input"
			   class="questionhub-input questionhub-search-input"
			   placeholder="<?php esc_attr_e( 'Search questions…', 'questionhub' ); ?>">
	</div>
	<?php endif; ?>

	<div class="questionhub-list-controls">
		<?php if ( ! empty( $questionhub_categories ) && ! is_wp_error( $questionhub_categories ) ) : ?>
		<select class="questionhub-filter-category questionhub-select">
			<option value=""><?php esc_html_e( 'All Categories', 'questionhub' ); ?></option>
			<?php foreach ( $questionhub_categories as $questionhub_cat ) : ?>
				<option value="<?php echo esc_attr( $questionhub_cat->term_id ); ?>" <?php selected( $questionhub_category, $questionhub_cat->term_id ); ?>><?php echo esc_html( $questionhub_cat->name ); ?></option>
			<?php endforeach; ?>
		</select>
		<?php endif; ?>

		<select class="questionhub-sort questionhub-select">
			<option value="date"           <?php selected( $questionhub_orderby, 'date' ); ?>><?php esc_html_e( 'Newest', 'questionhub' ); ?></option>
			<option value="comment_count"  <?php selected( $questionhub_orderby, 'comment_count' ); ?>><?php esc_html_e( 'Most Answered', 'questionhub' ); ?></option>
			<option value="meta_value_num" <?php selected( $questionhub_orderby, 'meta_value_num' ); ?>><?php esc_html_e( 'Most Viewed', 'questionhub' ); ?></option>
		</select>

		<?php if ( $questionhub_show_ask_btn && $questionhub_ask_url ) : ?>
		<a href="<?php echo esc_url( $questionhub_ask_url ); ?>" class="questionhub-button questionhub-button-primary questionhub-ask-btn">
			<span class="dashicons dashicons-edit"></span>
			<?php esc_html_e( 'Ask a Question', 'questionhub' ); ?>
		</a>
		<?php endif; ?>
	</div>

	<div class="questionhub-questions-list">
		<?php
		if ( $questionhub_query->have_posts() ) {
			while ( $questionhub_query->have_posts() ) {
				$questionhub_query->the_post();
				\QuestionHub\Frontend\Inc\Helpers\Template::load( 'question-card.php', [ 'post_id' => get_the_ID() ] );
			}
			wp_reset_postdata();
		} else {
			echo '<p class="questionhub-no-results">' . esc_html__( 'No questions found.', 'questionhub' ) . '</p>';
		}
		?>
	</div>

	<?php if ( $questionhub_query->max_num_pages > 1 ) : ?>
	<div class="questionhub-load-more-wrap">
		<button class="questionhub-button questionhub-load-more" data-page="2" data-max="<?php echo esc_attr( $questionhub_query->max_num_pages ); ?>">
			<?php esc_html_e( 'Load More', 'questionhub' ); ?>
		</button>
		<span class="questionhub-spinner" style="display:none;"></span>
	</div>
	<?php endif; ?>
</div>
