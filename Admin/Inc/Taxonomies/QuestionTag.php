<?php
/**
 * Question Tag taxonomy registration.
 *
 * @package QuestionHub\Admin\Inc\Taxonomies
 * @since   1.0.0
 */

namespace QuestionHub\Admin\Inc\Taxonomies;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class QuestionTag
 *
 * Registers the `question_tag` flat taxonomy.
 */
class QuestionTag {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'init', [ $this, 'register' ] );
	}

	/**
	 * Registers the question_tag taxonomy.
	 *
	 * @since 1.0.0
	 */
	public function register() {
		$labels = [
			'name'                       => esc_html_x( 'Question Tags', 'taxonomy general name', 'questionhub' ),
			'singular_name'              => esc_html_x( 'Question Tag', 'taxonomy singular name', 'questionhub' ),
			'search_items'               => esc_html__( 'Search Tags', 'questionhub' ),
			'popular_items'              => esc_html__( 'Popular Tags', 'questionhub' ),
			'all_items'                  => esc_html__( 'All Tags', 'questionhub' ),
			'edit_item'                  => esc_html__( 'Edit Tag', 'questionhub' ),
			'update_item'                => esc_html__( 'Update Tag', 'questionhub' ),
			'add_new_item'               => esc_html__( 'Add New Tag', 'questionhub' ),
			'new_item_name'              => esc_html__( 'New Tag Name', 'questionhub' ),
			'separate_items_with_commas' => esc_html__( 'Separate tags with commas', 'questionhub' ),
			'add_or_remove_items'        => esc_html__( 'Add or remove tags', 'questionhub' ),
			'choose_from_most_used'      => esc_html__( 'Choose from the most used tags', 'questionhub' ),
			'menu_name'                  => esc_html__( 'Tags', 'questionhub' ),
		];

		$args = [
			'hierarchical'          => false,
			'labels'                => $labels,
			'show_ui'               => true,
			'show_admin_column'     => true,
			'update_count_callback' => '_update_post_term_count',
			'query_var'             => true,
			'show_in_rest'          => true,
			'rewrite'               => [ 'slug' => 'question-tag' ],
		];

		register_taxonomy( 'question_tag', 'questions', $args );
	}
}
