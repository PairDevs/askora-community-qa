<?php
/**
 * Template: archive-questions.php
 * Rendered when the /questions/ CPT archive URL is visited.
 *
 * @package ASKORA
 */

defined( 'ABSPATH' ) || exit;

get_header();

$askora_settings   = get_option( 'askora_settings', [] );
$askora_per_page   = isset( $askora_settings['questions_per_page'] ) ? absint( $askora_settings['questions_per_page'] ) : 10;

// Detect taxonomy filtering from URL.
$askora_tax_query  = [];
if ( is_tax( 'question_category' ) ) {
	$askora_current_term = get_queried_object();
	$askora_tax_query[]  = [
		'taxonomy' => 'question_category',
		'field'    => 'term_id',
		'terms'    => $askora_current_term->term_id,
	];
} elseif ( is_tax( 'question_tag' ) ) {
	$askora_current_term = get_queried_object();
	$askora_tax_query[]  = [
		'taxonomy' => 'question_tag',
		'field'    => 'term_id',
		'terms'    => $askora_current_term->term_id,
	];
}

$askora_query_args = [
	'post_type'      => 'questions',
	'post_status'    => 'publish',
	'posts_per_page' => $askora_per_page,
	'paged'          => max( 1, get_query_var( 'paged' ) ),
	'orderby'        => 'date',
	'order'          => 'DESC',
];

if ( ! empty( $askora_tax_query ) ) {
	$askora_query_args['tax_query'] = $askora_tax_query; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
}

$askora_query      = new WP_Query( $askora_query_args );
$askora_categories = get_terms( [ 'taxonomy' => 'question_category', 'hide_empty' => true ] );

/**
 * Resolve the "Ask a Question" URL.
 * Priority: 1. Admin-selected page, 2. Auto-detect page with shortcode, 3. home_url().
 */
$askora_ask_url = '';
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

?>
<div class="askora-archive-page">
	<!-- Page title bar -->
	<div class="askora-archive-hero">
		<div class="askora-archive-hero-inner">
			<h1 class="askora-archive-title">
				<?php
				if ( is_tax() ) {
					echo esc_html( single_term_title( '', false ) );
				} else {
					esc_html_e( 'All Questions', 'askora-community-qa' );
				}
				?>
			</h1>
			<p class="askora-archive-subtitle">
				<?php
				printf(
					/* translators: %d: total number of questions */
					esc_html( _n( '%d question', '%d questions', $askora_query->found_posts, 'askora-community-qa' ) ),
					esc_html( $askora_query->found_posts )
				);
				?>
			</p>
			<?php // Show button to all users — the form page handles login requirements. ?>
			<a href="<?php echo esc_url( $askora_ask_url ); ?>" class="askora-button askora-button-primary askora-ask-hero-btn">
				<span class="dashicons dashicons-edit"></span>
				<?php esc_html_e( 'Ask a Question', 'askora-community-qa' ); ?>
			</a>
		</div>
	</div>

	<div class="askora-archive-body">
		<!-- Sidebar filters -->
		<aside class="askora-archive-sidebar">
			<?php if ( ! empty( $askora_categories ) && ! is_wp_error( $askora_categories ) ) : ?>
			<div class="askora-sidebar-widget">
				<h4 class="askora-sidebar-title"><?php esc_html_e( 'Categories', 'askora-community-qa' ); ?></h4>
				<ul class="askora-cat-list">
					<li>
						<a href="<?php echo esc_url( get_post_type_archive_link( 'questions' ) ); ?>" class="askora-cat-link <?php echo ! is_tax() ? 'active' : ''; ?>">
							<?php esc_html_e( 'All Questions', 'askora-community-qa' ); ?>
							<span class="askora-cat-count"><?php echo esc_html( wp_count_posts( 'questions' )->publish ); ?></span>
						</a>
					</li>
					<?php foreach ( $askora_categories as $askora_cat ) : ?>
					<li>
						<a href="<?php echo esc_url( get_term_link( $askora_cat ) ); ?>" class="askora-cat-link <?php echo ( is_tax() && get_queried_object_id() === $askora_cat->term_id ) ? 'active' : ''; ?>">
							<?php echo esc_html( $askora_cat->name ); ?>
							<span class="askora-cat-count"><?php echo esc_html( $askora_cat->count ); ?></span>
						</a>
					</li>
					<?php endforeach; ?>
				</ul>
			</div>
			<?php endif; ?>

			<!-- Popular Tags -->
			<?php
			$askora_tags = get_terms( [ 'taxonomy' => 'question_tag', 'hide_empty' => true, 'number' => 20, 'orderby' => 'count', 'order' => 'DESC' ] );
			if ( ! empty( $askora_tags ) && ! is_wp_error( $askora_tags ) ) :
			?>
			<div class="askora-sidebar-widget">
				<h4 class="askora-sidebar-title"><?php esc_html_e( 'Popular Tags', 'askora-community-qa' ); ?></h4>
				<div class="askora-tag-cloud">
					<?php foreach ( $askora_tags as $askora_tag ) : ?>
					<a href="<?php echo esc_url( get_term_link( $askora_tag ) ); ?>" class="askora-tag <?php echo ( is_tax( 'question_tag' ) && get_queried_object_id() === $askora_tag->term_id ) ? 'active' : ''; ?>">
						<?php echo esc_html( $askora_tag->name ); ?>
					</a>
					<?php endforeach; ?>
				</div>
			</div>
			<?php endif; ?>
		</aside>

		<!-- Main content -->
		<main class="askora-archive-main">
			<!-- Sort & Filter Bar -->
			<div class="askora-wrapper askora-list-wrapper"
				 data-page="1"
				 data-category=""
				 data-tag=""
				 data-orderby="date">

				<div class="askora-list-controls askora-archive-controls">
					<div class="askora-list-search askora-archive-search-wrap">
						<span class="dashicons dashicons-search"></span>
						<input type="search" class="askora-input askora-search-input askora-archive-search" placeholder="<?php esc_attr_e( 'Search questions…', 'askora-community-qa' ); ?>">
					</div>
					<select class="askora-sort askora-select">
						<option value="date"><?php esc_html_e( 'Newest', 'askora-community-qa' ); ?></option>
						<option value="comment_count"><?php esc_html_e( 'Most Answered', 'askora-community-qa' ); ?></option>
						<option value="meta_value_num"><?php esc_html_e( 'Most Viewed', 'askora-community-qa' ); ?></option>
						<option value="unanswered"><?php esc_html_e( 'Unanswered', 'askora-community-qa' ); ?></option>
					</select>
				</div>

				<!-- Questions list -->
				<div class="askora-questions-list">
					<?php
					if ( $askora_query->have_posts() ) {
						while ( $askora_query->have_posts() ) {
							$askora_query->the_post();
							\ASKORA\Frontend\Inc\Helpers\Template::load( 'question-card.php', [ 'post_id' => get_the_ID() ] );
						}
						wp_reset_postdata();
					} else {
						echo '<div class="askora-empty-state">';
						echo '<span class="dashicons dashicons-editor-help askora-empty-icon"></span>';
						echo '<h3>' . esc_html__( 'No questions yet', 'askora-community-qa' ) . '</h3>';
						echo '<p>' . esc_html__( 'Be the first to ask a question!', 'askora-community-qa' ) . '</p>';
						echo '</div>';
					}
					?>
				</div>

				<!-- Load more / pagination -->
				<?php if ( $askora_query->max_num_pages > 1 ) : ?>
				<div class="askora-load-more-wrap">
					<button class="askora-button askora-load-more" data-page="2" data-max="<?php echo esc_attr( $askora_query->max_num_pages ); ?>">
						<?php esc_html_e( 'Load More', 'askora-community-qa' ); ?>
					</button>
					<span class="askora-spinner" style="display:none;"></span>
				</div>
				<?php endif; ?>
			</div>
		</main>
	</div>
</div>

<?php get_footer(); ?>
