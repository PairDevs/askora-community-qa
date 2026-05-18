<?php
/**
 * Custom admin columns for the Questions CPT.
 *
 * @package ASKORA\Admin\Inc\Columns
 * @since   1.0.0
 */

namespace ASKORA\Admin\Inc\Columns;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class QuestionColumns
 *
 * Adds Views, Replies, and Votes columns to the Questions admin list.
 */
class QuestionColumns {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( 'manage_questions_posts_columns',       [ $this, 'add_columns' ] );
		add_action( 'manage_questions_posts_custom_column', [ $this, 'render_column' ], 10, 2 );
		add_filter( 'manage_edit-questions_sortable_columns', [ $this, 'sortable_columns' ] );
	}

	/**
	 * Adds custom columns.
	 *
	 * @param  array $columns Existing columns.
	 * @return array
	 * @since  1.0.0
	 */
	public function add_columns( $columns ) {
		$new = [];
		foreach ( $columns as $key => $label ) {
			$new[ $key ] = $label;
			if ( 'author' === $key ) {
				$new['askora_views']   = esc_html__( 'Views', 'askora-community-qa' );
				$new['askora_replies'] = esc_html__( 'Replies', 'askora-community-qa' );
				$new['askora_votes']   = esc_html__( 'Votes', 'askora-community-qa' );
			}
		}
		return $new;
	}

	/**
	 * Renders column content.
	 *
	 * @param string $column  Column key.
	 * @param int    $post_id Post ID.
	 * @since 1.0.0
	 */
	public function render_column( $column, $post_id ) {
		switch ( $column ) {
			case 'askora_views':
				$views = (int) get_post_meta( $post_id, '_askora_views', true );
				echo '<strong>' . esc_html( $views ) . '</strong>';
				break;

			case 'askora_replies':
				$count = get_comments_number( $post_id );
				echo '<strong>' . esc_html( $count ) . '</strong>';
				break;

			case 'askora_votes':
				$votes = (int) get_post_meta( $post_id, '_askora_votes', true );
				echo '<strong>' . esc_html( $votes ) . '</strong>';
				break;
		}
	}

	/**
	 * Makes columns sortable.
	 *
	 * @param  array $columns Sortable columns.
	 * @return array
	 * @since  1.0.0
	 */
	public function sortable_columns( $columns ) {
		$columns['askora_views'] = 'askora_views';
		$columns['askora_votes'] = 'askora_votes';
		return $columns;
	}
}
