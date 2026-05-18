<?php
/**
 * Question custom post type registration.
 *
 * @package ASKORA\Admin\Inc\PostTypes
 * @since   1.0.0
 */

namespace ASKORA\Admin\Inc\PostTypes;

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Class QuestionPostType
 *
 * Registers the `questions` custom post type.
 */
class QuestionPostType
{

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct()
	{
		add_action('init', [$this, 'register']);
	}

	/**
	 * Registers the questions CPT.
	 *
	 * @since 1.0.0
	 */
	public function register()
	{
		$labels = [
			'name' => esc_html_x('Questions', 'post type general name', 'askora-community-qa'),
			'singular_name' => esc_html_x('Question', 'post type singular name', 'askora-community-qa'),
			'menu_name' => esc_html_x('Questions', 'admin menu', 'askora-community-qa'),
			'add_new' => esc_html__('Add New', 'askora-community-qa'),
			'add_new_item' => esc_html__('Add New Question', 'askora-community-qa'),
			'new_item' => esc_html__('New Question', 'askora-community-qa'),
			'edit_item' => esc_html__('Edit Question', 'askora-community-qa'),
			'view_item' => esc_html__('View Question', 'askora-community-qa'),
			'all_items' => esc_html__('All Questions', 'askora-community-qa'),
			'search_items' => esc_html__('Search Questions', 'askora-community-qa'),
			'parent_item_colon' => esc_html__('Parent Questions:', 'askora-community-qa'),
			'not_found' => esc_html__('No questions found.', 'askora-community-qa'),
			'not_found_in_trash' => esc_html__('No questions found in Trash.', 'askora-community-qa'),
		];

		$args = [
			'labels' => $labels,
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true,
			'show_in_menu' => true,
			'query_var' => true,
			'rewrite' => ['slug' => 'question'],
			'capability_type' => 'post',
			'has_archive' => true,
			'hierarchical' => false,
			'menu_position' => 26,
			'menu_icon' => 'dashicons-editor-help',
			'show_in_rest' => true,
			'supports' => [
				'title',
				'editor',
				'author',
				'comments',
				'thumbnail',
				'custom-fields',
			],
		];

		register_post_type('questions', $args);
	}
}
