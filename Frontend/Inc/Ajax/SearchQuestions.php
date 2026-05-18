<?php
/**
 * AJAX: Search Questions.
 *
 * @package ASKORA\Frontend\Inc\Ajax
 * @since   1.0.0
 */

namespace ASKORA\Frontend\Inc\Ajax;

use ASKORA\Frontend\Inc\Helpers\Response;
use ASKORA\Frontend\Inc\Helpers\Template;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SearchQuestions {

	public function __construct() {
		add_action( 'wp_ajax_askora_search_questions',        [ $this, 'handle' ] );
		add_action( 'wp_ajax_nopriv_askora_search_questions', [ $this, 'handle' ] );
	}

	public function handle(): void {
		check_ajax_referer( 'askora_nonce', 'nonce' );

		$keyword  = isset( $_POST['keyword'] ) ? sanitize_text_field( wp_unslash( $_POST['keyword'] ) ) : '';
		$category = isset( $_POST['category'] ) ? absint( wp_unslash( $_POST['category'] ) ) : 0;

		$args = [
			'post_type'      => 'questions',
			'post_status'    => 'publish',
			'posts_per_page' => 10,
			's'              => $keyword,
		];

		if ( $category ) {
			$args['tax_query'] = [ // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
				[
					'taxonomy' => 'question_category',
					'field'    => 'term_id',
					'terms'    => $category,
				],
			];
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
			'html'  => $html,
			'total' => $query->found_posts,
		] );
	}
}
