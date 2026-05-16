<?php
/**
 * AJAX: Load Questions (pagination / load-more / filter).
 *
 * @package QuestionHub\Frontend\Inc\Ajax
 * @since   1.0.0
 */

namespace QuestionHub\Frontend\Inc\Ajax;

use QuestionHub\Frontend\Inc\Helpers\Response;
use QuestionHub\Frontend\Inc\Helpers\Template;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class LoadQuestions {

	public function __construct() {
		add_action( 'wp_ajax_questionhub_load_questions',        [ $this, 'handle' ] );
		add_action( 'wp_ajax_nopriv_questionhub_load_questions', [ $this, 'handle' ] );
	}

	public function handle(): void {
		check_ajax_referer( 'questionhub_nonce', 'nonce' );

		$settings = get_option( 'questionhub_settings', [] );
		$per_page = isset( $settings['questions_per_page'] ) ? absint( $settings['questions_per_page'] ) : 10;

		$page     = isset( $_POST['page'] ) ? absint( wp_unslash( $_POST['page'] ) ) : 1;
		$category = isset( $_POST['category'] ) ? absint( wp_unslash( $_POST['category'] ) ) : 0;
		$tag      = isset( $_POST['tag'] ) ? sanitize_text_field( wp_unslash( $_POST['tag'] ) ) : '';
		$orderby  = isset( $_POST['orderby'] ) ? sanitize_text_field( wp_unslash( $_POST['orderby'] ) ) : 'date';
		$keyword  = isset( $_POST['keyword'] ) ? sanitize_text_field( wp_unslash( $_POST['keyword'] ) ) : '';

		// Whitelist orderby values.
		$allowed_orderby = [ 'date', 'comment_count', 'meta_value_num' ];
		if ( ! in_array( $orderby, $allowed_orderby, true ) ) {
			$orderby = 'date';
		}

		$args = [
			'post_type'      => 'questions',
			'post_status'    => 'publish',
			'posts_per_page' => apply_filters( 'questionhub_questions_per_page', $per_page ),
			'paged'          => $page,
			'orderby'        => $orderby,
			'order'          => 'DESC',
		];

		if ( 'meta_value_num' === $orderby ) {
			$args['meta_key'] = '_questionhub_views'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
		}

		if ( $category ) {
			$args['tax_query'] = [ // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
				[
					'taxonomy' => 'question_category',
					'field'    => 'term_id',
					'terms'    => $category,
				],
			];
		}

		if ( $tag ) {
			$args['tax_query'][] = [ // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
				'taxonomy' => 'question_tag',
				'field'    => 'slug',
				'terms'    => $tag,
			];
		}

		// Keyword search.
		if ( $keyword ) {
			$args['s'] = $keyword;
		}

		// Unanswered filter.
		if ( isset( $_POST['unanswered'] ) && absint( wp_unslash( $_POST['unanswered'] ) ) ) {
			$args['comment_count'] = 0;
		}

		$query = new \WP_Query( $args );
		$html  = '';

		if ( $query->have_posts() ) {
			ob_start();
			while ( $query->have_posts() ) {
				$query->the_post();
				Template::load( 'question-card.php', [ 'post_id' => get_the_ID() ] );
			}
			wp_reset_postdata();
			$html = ob_get_clean();
		}

		Response::success( '', [
			'html'       => $html,
			'total'      => $query->found_posts,
			'pages'      => $query->max_num_pages,
			'current'    => $page,
			'has_more'   => $page < $query->max_num_pages,
		] );
	}
}
