<?php
/**
 * Question Category taxonomy registration.
 *
 * @package QuestionHub\Admin\Inc\Taxonomies
 * @since   1.0.0
 */

namespace QuestionHub\Admin\Inc\Taxonomies;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class QuestionCategory
 *
 * Registers the `question_category` hierarchical taxonomy.
 */
class QuestionCategory {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'init', [ $this, 'register' ] );
	}

	/**
	 * Registers the question_category taxonomy.
	 *
	 * @since 1.0.0
	 */
	public function register() {
		$labels = [
			'name'              => esc_html_x( 'Question Categories', 'taxonomy general name', 'questionhub' ),
			'singular_name'     => esc_html_x( 'Question Category', 'taxonomy singular name', 'questionhub' ),
			'search_items'      => esc_html__( 'Search Categories', 'questionhub' ),
			'all_items'         => esc_html__( 'All Categories', 'questionhub' ),
			'parent_item'       => esc_html__( 'Parent Category', 'questionhub' ),
			'parent_item_colon' => esc_html__( 'Parent Category:', 'questionhub' ),
			'edit_item'         => esc_html__( 'Edit Category', 'questionhub' ),
			'update_item'       => esc_html__( 'Update Category', 'questionhub' ),
			'add_new_item'      => esc_html__( 'Add New Category', 'questionhub' ),
			'new_item_name'     => esc_html__( 'New Category Name', 'questionhub' ),
			'menu_name'         => esc_html__( 'Categories', 'questionhub' ),
		];

		$args = [
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'show_in_rest'      => true,
			'rewrite'           => [ 'slug' => 'question-category' ],
		];

		register_taxonomy( 'question_category', 'questions', $args );
	}
}
