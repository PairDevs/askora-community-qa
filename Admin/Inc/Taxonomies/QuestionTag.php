<?php
/**
 * Question Tag taxonomy registration.
 *
 * @package ASKORA\Admin\Inc\Taxonomies
 * @since   1.0.0
 */

namespace ASKORA\Admin\Inc\Taxonomies;

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
			'name'                       => esc_html_x( 'Question Tags', 'taxonomy general name', 'askora-community-qa' ),
			'singular_name'              => esc_html_x( 'Question Tag', 'taxonomy singular name', 'askora-community-qa' ),
			'search_items'               => esc_html__( 'Search Tags', 'askora-community-qa' ),
			'popular_items'              => esc_html__( 'Popular Tags', 'askora-community-qa' ),
			'all_items'                  => esc_html__( 'All Tags', 'askora-community-qa' ),
			'edit_item'                  => esc_html__( 'Edit Tag', 'askora-community-qa' ),
			'update_item'                => esc_html__( 'Update Tag', 'askora-community-qa' ),
			'add_new_item'               => esc_html__( 'Add New Tag', 'askora-community-qa' ),
			'new_item_name'              => esc_html__( 'New Tag Name', 'askora-community-qa' ),
			'separate_items_with_commas' => esc_html__( 'Separate tags with commas', 'askora-community-qa' ),
			'add_or_remove_items'        => esc_html__( 'Add or remove tags', 'askora-community-qa' ),
			'choose_from_most_used'      => esc_html__( 'Choose from the most used tags', 'askora-community-qa' ),
			'menu_name'                  => esc_html__( 'Tags', 'askora-community-qa' ),
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
