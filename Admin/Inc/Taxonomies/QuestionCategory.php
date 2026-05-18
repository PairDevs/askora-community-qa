<?php
/**
 * Question Category taxonomy registration.
 *
 * @package ASKORA\Admin\Inc\Taxonomies
 * @since   1.0.0
 */

namespace ASKORA\Admin\Inc\Taxonomies;

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
			'name'              => esc_html_x( 'Question Categories', 'taxonomy general name', 'askora-community-qa' ),
			'singular_name'     => esc_html_x( 'Question Category', 'taxonomy singular name', 'askora-community-qa' ),
			'search_items'      => esc_html__( 'Search Categories', 'askora-community-qa' ),
			'all_items'         => esc_html__( 'All Categories', 'askora-community-qa' ),
			'parent_item'       => esc_html__( 'Parent Category', 'askora-community-qa' ),
			'parent_item_colon' => esc_html__( 'Parent Category:', 'askora-community-qa' ),
			'edit_item'         => esc_html__( 'Edit Category', 'askora-community-qa' ),
			'update_item'       => esc_html__( 'Update Category', 'askora-community-qa' ),
			'add_new_item'      => esc_html__( 'Add New Category', 'askora-community-qa' ),
			'new_item_name'     => esc_html__( 'New Category Name', 'askora-community-qa' ),
			'menu_name'         => esc_html__( 'Categories', 'askora-community-qa' ),
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
