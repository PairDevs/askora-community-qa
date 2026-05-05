<?php
/**
 * Template: question-list.php
 * Shortcode: [questionhub_questions]
 *
 * @package QuestionHub
 * @var array $atts Shortcode attributes.
 */

defined( 'ABSPATH' ) || exit;

$settings   = get_option( 'questionhub_settings', [] );
$per_page   = ! empty( $atts['per_page'] ) ? absint( $atts['per_page'] ) : ( isset( $settings['questions_per_page'] ) ? absint( $settings['questions_per_page'] ) : 10 );
$category   = ! empty( $atts['category'] ) ? absint( $atts['category'] ) : 0;
$tag        = ! empty( $atts['tag'] ) ? sanitize_text_field( $atts['tag'] ) : '';
$orderby    = ! empty( $atts['orderby'] ) ? sanitize_key( $atts['orderby'] ) : 'date';
$unanswered = ! empty( $atts['unanswered'] );

// Attr flags — accept 'true'/'false' strings or 1/0.
$show_ask_btn = ! in_array( strtolower( (string) ( $atts['show_ask_btn'] ?? 'true' ) ), [ 'false', '0', 'no' ], true );
$show_search  = ! in_array( strtolower( (string) ( $atts['show_search'] ?? 'true' ) ), [ 'false', '0', 'no' ], true );

// ── Ask a Question URL ────────────────────────────────────────────────────────
$ask_url = '';
if ( $show_ask_btn ) {
	$submit_page_id = (int) ( $settings['submit_form_page_id'] ?? 0 );
	if ( $submit_page_id > 0 && 'publish' === get_post_status( $submit_page_id ) ) {
		$ask_url = get_permalink( $submit_page_id );
	}
	if ( ! $ask_url ) {
		global $wpdb;
		$detected = $wpdb->get_var( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$wpdb->prepare(
				"SELECT ID FROM {$wpdb->posts}
				 WHERE post_status = 'publish'
				 AND post_type   = 'page'
				 AND post_content LIKE %s
				 LIMIT 1",
				'%[questionhub_submit_form%'
			)
		);
		if ( $detected ) {
			$ask_url = get_permalink( (int) $detected );
		}
	}
	if ( ! $ask_url ) {
		$ask_url = apply_filters( 'questionhub_ask_url', home_url() );
	}
}

// ── Query ─────────────────────────────────────────────────────────────────────
$query_args = [
	'post_type'      => 'questions',
	'post_status'    => 'publish',
	'posts_per_page' => $per_page,
	'orderby'        => $orderby,
	'order'          => 'DESC',
];
if ( 'meta_value_num' === $orderby ) {
	$query_args['meta_key'] = '_questionhub_views'; // phpcs:ignore
}
if ( $category ) {
	$query_args['tax_query'] = [ [ 'taxonomy' => 'question_category', 'field' => 'term_id', 'terms' => $category ] ]; // phpcs:ignore
}
if ( $unanswered ) {
	$query_args['comment_count'] = 0;
}

$query      = new WP_Query( $query_args );
$categories = get_terms( [ 'taxonomy' => 'question_category', 'hide_empty' => true ] );
?>
<div class="questionhub-wrapper questionhub-list-wrapper" data-page="1" data-category="<?php echo esc_attr( $category ); ?>" data-tag="<?php echo esc_attr( $tag ); ?>" data-orderby="<?php echo esc_attr( $orderby ); ?>">

	<?php if ( $show_search ) : ?>
	<div class="questionhub-search-wrap questionhub-list-search">
		<span class="dashicons dashicons-search"></span>
		<input type="search"
			   id="questionhub-search-input"
			   class="questionhub-input questionhub-search-input"
			   placeholder="<?php esc_attr_e( 'Search questions…', 'questionhub' ); ?>">
	</div>
	<?php endif; ?>

	<div class="questionhub-list-controls">
		<?php if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) : ?>
		<select class="questionhub-filter-category questionhub-select">
			<option value=""><?php esc_html_e( 'All Categories', 'questionhub' ); ?></option>
			<?php foreach ( $categories as $cat ) : ?>
				<option value="<?php echo esc_attr( $cat->term_id ); ?>" <?php selected( $category, $cat->term_id ); ?>><?php echo esc_html( $cat->name ); ?></option>
			<?php endforeach; ?>
		</select>
		<?php endif; ?>

		<select class="questionhub-sort questionhub-select">
			<option value="date"           <?php selected( $orderby, 'date' ); ?>><?php esc_html_e( 'Newest', 'questionhub' ); ?></option>
			<option value="comment_count"  <?php selected( $orderby, 'comment_count' ); ?>><?php esc_html_e( 'Most Answered', 'questionhub' ); ?></option>
			<option value="meta_value_num" <?php selected( $orderby, 'meta_value_num' ); ?>><?php esc_html_e( 'Most Viewed', 'questionhub' ); ?></option>
		</select>

		<?php if ( $show_ask_btn && $ask_url ) : ?>
		<a href="<?php echo esc_url( $ask_url ); ?>" class="questionhub-button questionhub-button-primary questionhub-ask-btn">
			<span class="dashicons dashicons-edit"></span>
			<?php esc_html_e( 'Ask a Question', 'questionhub' ); ?>
		</a>
		<?php endif; ?>
	</div>

	<div class="questionhub-questions-list">
		<?php
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				\QuestionHub\Frontend\Inc\Helpers\Template::load( 'question-card.php', [ 'post_id' => get_the_ID() ] );
			}
			wp_reset_postdata();
		} else {
			echo '<p class="questionhub-no-results">' . esc_html__( 'No questions found.', 'questionhub' ) . '</p>';
		}
		?>
	</div>

	<?php if ( $query->max_num_pages > 1 ) : ?>
	<div class="questionhub-load-more-wrap">
		<button class="questionhub-button questionhub-load-more" data-page="2" data-max="<?php echo esc_attr( $query->max_num_pages ); ?>">
			<?php esc_html_e( 'Load More', 'questionhub' ); ?>
		</button>
		<span class="questionhub-spinner" style="display:none;"></span>
	</div>
	<?php endif; ?>
</div>
