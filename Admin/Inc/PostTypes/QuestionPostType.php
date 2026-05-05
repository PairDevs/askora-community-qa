<?php
/**
 * Question custom post type registration.
 *
 * @package QuestionHub\Admin\Inc\PostTypes
 * @since   1.0.0
 */

namespace QuestionHub\Admin\Inc\PostTypes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class QuestionPostType
 *
 * Registers the `questions` custom post type.
 */
class QuestionPostType {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'init', [ $this, 'register' ] );
	}

	/**
	 * Registers the questions CPT.
	 *
	 * @since 1.0.0
	 */
	public function register() {
		$labels = [
			'name'               => esc_html_x( 'Questions', 'post type general name', 'questionhub' ),
			'singular_name'      => esc_html_x( 'Question', 'post type singular name', 'questionhub' ),
			'menu_name'          => esc_html_x( 'Questions', 'admin menu', 'questionhub' ),
			'add_new'            => esc_html__( 'Add New', 'questionhub' ),
			'add_new_item'       => esc_html__( 'Add New Question', 'questionhub' ),
			'new_item'           => esc_html__( 'New Question', 'questionhub' ),
			'edit_item'          => esc_html__( 'Edit Question', 'questionhub' ),
			'view_item'          => esc_html__( 'View Question', 'questionhub' ),
			'all_items'          => esc_html__( 'All Questions', 'questionhub' ),
			'search_items'       => esc_html__( 'Search Questions', 'questionhub' ),
			'parent_item_colon'  => esc_html__( 'Parent Questions:', 'questionhub' ),
			'not_found'          => esc_html__( 'No questions found.', 'questionhub' ),
			'not_found_in_trash' => esc_html__( 'No questions found in Trash.', 'questionhub' ),
		];

		$args = [
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => [ 'slug' => 'question' ],
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => 26,
			'menu_icon'          => 'dashicons-editor-help',
			'show_in_rest'       => true,
			'supports'           => [
				'title',
				'editor',
				'author',
				'comments',
				'thumbnail',
				'custom-fields',
			],
		];

		register_post_type( 'questions', $args );
	}
}
