<?php
/**
 * Template: archive-questions.php
 * Rendered when the /questions/ CPT archive URL is visited.
 *
 * @package QuestionHub
 */

defined( 'ABSPATH' ) || exit;

get_header();

$settings   = get_option( 'questionhub_settings', [] );
$per_page   = isset( $settings['questions_per_page'] ) ? absint( $settings['questions_per_page'] ) : 10;

// Detect taxonomy filtering from URL.
$tax_query  = [];
if ( is_tax( 'question_category' ) ) {
	$current_term = get_queried_object();
	$tax_query[]  = [
		'taxonomy' => 'question_category',
		'field'    => 'term_id',
		'terms'    => $current_term->term_id,
	];
} elseif ( is_tax( 'question_tag' ) ) {
	$current_term = get_queried_object();
	$tax_query[]  = [
		'taxonomy' => 'question_tag',
		'field'    => 'term_id',
		'terms'    => $current_term->term_id,
	];
}

$query_args = [
	'post_type'      => 'questions',
	'post_status'    => 'publish',
	'posts_per_page' => $per_page,
	'paged'          => max( 1, get_query_var( 'paged' ) ),
	'orderby'        => 'date',
	'order'          => 'DESC',
];

if ( ! empty( $tax_query ) ) {
	$query_args['tax_query'] = $tax_query; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
}

$query      = new WP_Query( $query_args );
$categories = get_terms( [ 'taxonomy' => 'question_category', 'hide_empty' => true ] );

/**
 * Resolve the "Ask a Question" URL.
 * Priority: 1. Admin-selected page, 2. Auto-detect page with shortcode, 3. home_url().
 */
$ask_url = '';
$submit_page_id = (int) ( $settings['submit_form_page_id'] ?? 0 );
if ( $submit_page_id > 0 && 'publish' === get_post_status( $submit_page_id ) ) {
	$ask_url = get_permalink( $submit_page_id );
}
if ( ! $ask_url ) {
	// Auto-detect: search all pages for the shortcode.
	global $wpdb;
	$detected = $wpdb->get_var( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$wpdb->prepare(
			"SELECT ID FROM {$wpdb->posts}
			 WHERE post_status = 'publish'
			 AND post_type = 'page'
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
					esc_html( _n( '%d question', '%d questions', $query->found_posts, 'questionhub' ) ),
					esc_html( $query->found_posts )
				);
				?>
			</p>
			<?php // Show button to all users — the form page handles login requirements. ?>
			<a href="<?php echo esc_url( $ask_url ); ?>" class="questionhub-button questionhub-button-primary questionhub-ask-hero-btn">
				<span class="dashicons dashicons-edit"></span>
				<?php esc_html_e( 'Ask a Question', 'questionhub' ); ?>
			</a>

		</div>
	</div>

	<div class="questionhub-archive-body">
		<!-- Sidebar filters -->
		<aside class="questionhub-archive-sidebar">
			<?php if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) : ?>
			<div class="questionhub-sidebar-widget">
				<h4 class="questionhub-sidebar-title"><?php esc_html_e( 'Categories', 'questionhub' ); ?></h4>
				<ul class="questionhub-cat-list">
					<li>
						<a href="<?php echo esc_url( get_post_type_archive_link( 'questions' ) ); ?>" class="questionhub-cat-link <?php echo ! is_tax() ? 'active' : ''; ?>">
							<?php esc_html_e( 'All Questions', 'questionhub' ); ?>
							<span class="questionhub-cat-count"><?php echo esc_html( wp_count_posts( 'questions' )->publish ); ?></span>
						</a>
					</li>
					<?php foreach ( $categories as $cat ) : ?>
					<li>
						<a href="<?php echo esc_url( get_term_link( $cat ) ); ?>" class="questionhub-cat-link <?php echo ( is_tax() && get_queried_object_id() === $cat->term_id ) ? 'active' : ''; ?>">
							<?php echo esc_html( $cat->name ); ?>
							<span class="questionhub-cat-count"><?php echo esc_html( $cat->count ); ?></span>
						</a>
					</li>
					<?php endforeach; ?>
				</ul>
			</div>
			<?php endif; ?>

			<!-- Popular Tags -->
			<?php
			$tags = get_terms( [ 'taxonomy' => 'question_tag', 'hide_empty' => true, 'number' => 20, 'orderby' => 'count', 'order' => 'DESC' ] );
			if ( ! empty( $tags ) && ! is_wp_error( $tags ) ) :
			?>
			<div class="questionhub-sidebar-widget">
				<h4 class="questionhub-sidebar-title"><?php esc_html_e( 'Popular Tags', 'questionhub' ); ?></h4>
				<div class="questionhub-tag-cloud">
					<?php foreach ( $tags as $tag ) : ?>
					<a href="<?php echo esc_url( get_term_link( $tag ) ); ?>" class="questionhub-tag <?php echo ( is_tax( 'question_tag' ) && get_queried_object_id() === $tag->term_id ) ? 'active' : ''; ?>">
						<?php echo esc_html( $tag->name ); ?>
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
					<div class="questionhub-archive-search-wrap">
						<span class="dashicons dashicons-search"></span>
						<input type="search" id="questionhub-search-input" class="questionhub-input questionhub-search-input questionhub-archive-search" placeholder="<?php esc_attr_e( 'Search questions…', 'questionhub' ); ?>">
					</div>
					<select class="questionhub-sort questionhub-select">
						<option value="date"><?php esc_html_e( 'Newest', 'questionhub' ); ?></option>
						<option value="comment_count"><?php esc_html_e( 'Most Answered', 'questionhub' ); ?></option>
						<option value="meta_value_num"><?php esc_html_e( 'Most Viewed', 'questionhub' ); ?></option>
					</select>
				</div>

				<!-- Questions list -->
				<div class="questionhub-questions-list">
					<?php
					if ( $query->have_posts() ) {
						while ( $query->have_posts() ) {
							$query->the_post();
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
				<?php if ( $query->max_num_pages > 1 ) : ?>
				<div class="questionhub-load-more-wrap">
					<button class="questionhub-button questionhub-load-more" data-page="2" data-max="<?php echo esc_attr( $query->max_num_pages ); ?>">
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
