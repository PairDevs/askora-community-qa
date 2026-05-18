<?php
/**
 * Question repository — data access layer.
 *
 * @package ASKORA\Frontend\Inc\Questions
 * @since   1.0.0
 */

namespace ASKORA\Frontend\Inc\Questions;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class QuestionRepository {

	/**
	 * Fetch paginated questions.
	 *
	 * @param  array $args WP_Query args override.
	 * @return array { posts: WP_Post[], total: int, pages: int }
	 */
	public function get_questions( array $args = [] ): array {
		$settings = get_option( 'askora_settings', [] );
		$per_page = isset( $settings['questions_per_page'] ) ? absint( $settings['questions_per_page'] ) : 10;

		$defaults = [
			'post_type'      => 'questions',
			'post_status'    => 'publish',
			'posts_per_page' => $per_page,
			'paged'          => 1,
			'orderby'        => 'date',
			'order'          => 'DESC',
		];

		$query_args = wp_parse_args( $args, $defaults );

		/**
		 * Filter the query arguments used to fetch questions.
		 *
		 * @param array $query_args WP_Query arguments.
		 * @since 1.0.0
		 */
		$query_args = apply_filters( 'askora_questions_query_args', $query_args );

		$query = new \WP_Query( $query_args );

		return [
			'posts' => $query->posts,
			'total' => $query->found_posts,
			'pages' => $query->max_num_pages,
		];
	}

	/**
	 * Get a single question post.
	 *
	 * @param int $post_id Post ID.
	 * @return \WP_Post|null
	 */
	public function get_question( int $post_id ): ?\WP_Post {
		$post = get_post( $post_id );
		return ( $post && 'questions' === $post->post_type ) ? $post : null;
	}

	/**
	 * Create a new question post.
	 *
	 * @param array $data { title, content, categories, tags, author_id }
	 * @return int|\WP_Error Post ID on success.
	 */
	public function create_question( array $data ) {
		$settings       = get_option( 'askora_settings', [] );
		$default_status = isset( $settings['question_status'] ) ? $settings['question_status'] : 'pending';
		$status         = apply_filters( 'askora_question_status', $default_status );

		$post_data = [
			'post_title'   => sanitize_text_field( $data['title'] ?? '' ),
			'post_content' => wp_kses_post( $data['content'] ?? '' ),
			'post_type'    => 'questions',
			'post_status'  => $status,
			'post_author'  => absint( $data['author_id'] ?? get_current_user_id() ),
		];

		$post_id = wp_insert_post( $post_data, true );

		if ( is_wp_error( $post_id ) ) {
			return $post_id;
		}

		// Assign categories.
		if ( ! empty( $data['categories'] ) ) {
			$cat_ids = array_map( 'absint', (array) $data['categories'] );
			wp_set_object_terms( $post_id, $cat_ids, 'question_category' );
		}

		// Assign tags (comma-separated string or array).
		if ( ! empty( $data['tags'] ) ) {
			$tags = is_array( $data['tags'] ) ? $data['tags'] : explode( ',', $data['tags'] );
			$tags = array_map( 'trim', $tags );
			wp_set_object_terms( $post_id, $tags, 'question_tag' );
		}

		return $post_id;
	}

	/**
	 * Increment the view count for a question.
	 *
	 * @param int $post_id Post ID.
	 * @since 1.0.0
	 */
	public function increment_views( int $post_id ): void {
		$current = (int) get_post_meta( $post_id, '_askora_views', true );
		update_post_meta( $post_id, '_askora_views', $current + 1 );
	}

	/**
	 * Get view count.
	 *
	 * @param int $post_id Post ID.
	 * @return int
	 */
	public function get_views( int $post_id ): int {
		return (int) get_post_meta( $post_id, '_askora_views', true );
	}

	/**
	 * Get vote count for a question.
	 *
	 * @param int $post_id Post ID.
	 * @return int
	 */
	public function get_votes( int $post_id ): int {
		return (int) get_post_meta( $post_id, '_askora_votes', true );
	}
}
