<?php
/**
 * Template: archive-questions.php
 * Rendered when the /questions/ CPT archive URL is visited.
 *
 * @package QuestionHub
 */

defined( 'ABSPATH' ) || exit;

get_header();

$questionhub_settings   = get_option( 'questionhub_settings', [] );
$questionhub_per_page   = isset( $questionhub_settings['questions_per_page'] ) ? absint( $questionhub_settings['questions_per_page'] ) : 10;

// Detect taxonomy filtering from URL.
$questionhub_tax_query  = [];
if ( is_tax( 'question_category' ) ) {
	$questionhub_current_term = get_queried_object();
	$questionhub_tax_query[]  = [
		'taxonomy' => 'question_category',
		'field'    => 'term_id',
		'terms'    => $questionhub_current_term->term_id,
	];
} elseif ( is_tax( 'question_tag' ) ) {
	$questionhub_current_term = get_queried_object();
	$questionhub_tax_query[]  = [
		'taxonomy' => 'question_tag',
		'field'    => 'term_id',
		'terms'    => $questionhub_current_term->term_id,
	];
}

$questionhub_query_args = [
	'post_type'      => 'questions',
	'post_status'    => 'publish',
	'posts_per_page' => $questionhub_per_page,
	'paged'          => max( 1, get_query_var( 'paged' ) ),
	'orderby'        => 'date',
	'order'          => 'DESC',
];

if ( ! empty( $questionhub_tax_query ) ) {
	$questionhub_query_args['tax_query'] = $questionhub_tax_query; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
}

$questionhub_query      = new WP_Query( $questionhub_query_args );
$questionhub_categories = get_terms( [ 'taxonomy' => 'question_category', 'hide_empty' => true ] );

/**
 * Resolve the "Ask a Question" URL.
 * Priority: 1. Admin-selected page, 2. Auto-detect page with shortcode, 3. home_url().
 */
$questionhub_ask_url = '';
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

?>
<div class="questionhub-archive-page">
	<!-- Page title bar -->
	<div class="questionhub-archive-hero">
		<div class="questionhub-archive-hero-inner">
			<h1 class="questionhub-archive-title">
				<?php
				if ( is_tax() ) {
					echo esc_html( single_term_title( '', false ) );
				} else {
					esc_html_e( 'All Questions', 'questionhub' );
				}
				?>
			</h1>
			<p class="questionhub-archive-subtitle">
				<?php
				printf(
					/* translators: %d: total number of questions */
					esc_html( _n( '%d question', '%d questions', $questionhub_query->found_posts, 'questionhub' ) ),
					esc_html( $questionhub_query->found_posts )
				);
				?>
			</p>
			<?php // Show button to all users — the form page handles login requirements. ?>
			<a href="<?php echo esc_url( $questionhub_ask_url ); ?>" class="questionhub-button questionhub-button-primary questionhub-ask-hero-btn">
				<span class="dashicons dashicons-edit"></span>
				<?php esc_html_e( 'Ask a Question', 'questionhub' ); ?>
			</a>
		</div>
	</div>

	<div class="questionhub-archive-body">
		<!-- Sidebar filters -->
		<aside class="questionhub-archive-sidebar">
			<?php if ( ! empty( $questionhub_categories ) && ! is_wp_error( $questionhub_categories ) ) : ?>
			<div class="questionhub-sidebar-widget">
				<h4 class="questionhub-sidebar-title"><?php esc_html_e( 'Categories', 'questionhub' ); ?></h4>
				<ul class="questionhub-cat-list">
					<li>
						<a href="<?php echo esc_url( get_post_type_archive_link( 'questions' ) ); ?>" class="questionhub-cat-link <?php echo ! is_tax() ? 'active' : ''; ?>">
							<?php esc_html_e( 'All Questions', 'questionhub' ); ?>
							<span class="questionhub-cat-count"><?php echo esc_html( wp_count_posts( 'questions' )->publish ); ?></span>
						</a>
					</li>
					<?php foreach ( $questionhub_categories as $questionhub_cat ) : ?>
					<li>
						<a href="<?php echo esc_url( get_term_link( $questionhub_cat ) ); ?>" class="questionhub-cat-link <?php echo ( is_tax() && get_queried_object_id() === $questionhub_cat->term_id ) ? 'active' : ''; ?>">
							<?php echo esc_html( $questionhub_cat->name ); ?>
							<span class="questionhub-cat-count"><?php echo esc_html( $questionhub_cat->count ); ?></span>
						</a>
					</li>
					<?php endforeach; ?>
				</ul>
			</div>
			<?php endif; ?>

			<!-- Popular Tags -->
			<?php
			$questionhub_tags = get_terms( [ 'taxonomy' => 'question_tag', 'hide_empty' => true, 'number' => 20, 'orderby' => 'count', 'order' => 'DESC' ] );
			if ( ! empty( $questionhub_tags ) && ! is_wp_error( $questionhub_tags ) ) :
			?>
			<div class="questionhub-sidebar-widget">
				<h4 class="questionhub-sidebar-title"><?php esc_html_e( 'Popular Tags', 'questionhub' ); ?></h4>
				<div class="questionhub-tag-cloud">
					<?php foreach ( $questionhub_tags as $questionhub_tag ) : ?>
					<a href="<?php echo esc_url( get_term_link( $questionhub_tag ) ); ?>" class="questionhub-tag <?php echo ( is_tax( 'question_tag' ) && get_queried_object_id() === $questionhub_tag->term_id ) ? 'active' : ''; ?>">
						<?php echo esc_html( $questionhub_tag->name ); ?>
					</a>
					<?php endforeach; ?>
				</div>
			</div>
			<?php endif; ?>
		</aside>

		<!-- Main content -->
		<main class="questionhub-archive-main">
			<!-- Sort & Filter Bar -->
			<div class="questionhub-wrapper questionhub-list-wrapper"
				 data-page="1"
				 data-category=""
				 data-tag=""
				 data-orderby="date">

				<div class="questionhub-list-controls questionhub-archive-controls">
					<div class="questionhub-list-search questionhub-archive-search-wrap">
						<span class="dashicons dashicons-search"></span>
						<input type="search" class="questionhub-input questionhub-search-input questionhub-archive-search" placeholder="<?php esc_attr_e( 'Search questions…', 'questionhub' ); ?>">
					</div>
					<select class="questionhub-sort questionhub-select">
						<option value="date"><?php esc_html_e( 'Newest', 'questionhub' ); ?></option>
						<option value="comment_count"><?php esc_html_e( 'Most Answered', 'questionhub' ); ?></option>
						<option value="meta_value_num"><?php esc_html_e( 'Most Viewed', 'questionhub' ); ?></option>
						<option value="unanswered"><?php esc_html_e( 'Unanswered', 'questionhub' ); ?></option>
					</select>
				</div>

				<!-- Questions list -->
				<div class="questionhub-questions-list">
					<?php
					if ( $questionhub_query->have_posts() ) {
						while ( $questionhub_query->have_posts() ) {
							$questionhub_query->the_post();
							\QuestionHub\Frontend\Inc\Helpers\Template::load( 'question-card.php', [ 'post_id' => get_the_ID() ] );
						}
						wp_reset_postdata();
					} else {
						echo '<div class="questionhub-empty-state">';
						echo '<span class="dashicons dashicons-editor-help questionhub-empty-icon"></span>';
						echo '<h3>' . esc_html__( 'No questions yet', 'questionhub' ) . '</h3>';
						echo '<p>' . esc_html__( 'Be the first to ask a question!', 'questionhub' ) . '</p>';
						echo '</div>';
					}
					?>
				</div>

				<!-- Load more / pagination -->
				<?php if ( $questionhub_query->max_num_pages > 1 ) : ?>
				<div class="questionhub-load-more-wrap">
					<button class="questionhub-button questionhub-load-more" data-page="2" data-max="<?php echo esc_attr( $questionhub_query->max_num_pages ); ?>">
						<?php esc_html_e( 'Load More', 'questionhub' ); ?>
					</button>
					<span class="questionhub-spinner" style="display:none;"></span>
				</div>
				<?php endif; ?>
			</div>
		</main>
	</div>
</div>

<?php get_footer(); ?>
