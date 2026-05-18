<?php
/**
 * Answer renderer — renders answers and the answer form.
 *
 * @package ASKORA\Frontend\Inc\Comments
 * @since   1.0.0
 */

namespace ASKORA\Frontend\Inc\Comments;

use ASKORA\Frontend\Inc\Helpers\Template;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AnswerRenderer {

	public function __construct() {
		// Override comment template on questions CPT.
		add_filter( 'comments_template', [ $this, 'override_comments_template' ] );
	}

	/**
	 * Loads our custom answers template for questions CPT.
	 *
	 * @param  string $template Default comments template path.
	 * @return string
	 * @since  1.0.0
	 */
	public function override_comments_template( $template ) {
		if ( is_singular( 'questions' ) ) {
			$custom = get_stylesheet_directory() . '/askora-community-qa/answers-template.php';
			if ( file_exists( $custom ) ) {
				return $custom;
			}
			$plugin = ASKORA_PATH . 'Frontend/Inc/Templates/answers-template.php';
			if ( file_exists( $plugin ) ) {
				return $plugin;
			}
		}
		return $template;
	}

	/**
	 * Renders the answers list for a question.
	 *
	 * @param int $post_id Post ID.
	 * @return string HTML.
	 * @since 1.0.0
	 */
	public static function render_list( int $post_id ): string {
		$settings  = get_option( 'askora_settings', [] );
		$best_id   = (int) get_post_meta( $post_id, '_askora_best_answer', true );
		$comments  = get_comments( [
			'post_id' => $post_id,
			'status'  => 'approve',
			'orderby' => 'comment_date',
			'order'   => 'ASC',
		] );

		ob_start();
		if ( ! empty( $comments ) ) {
			echo '<div class="askora-answer-list">';
			foreach ( $comments as $comment ) {
				Template::load( 'answer-item.php', [
					'comment'    => $comment,
					'post_id'    => $post_id,
					'best_id'    => $best_id,
					'settings'   => $settings,
				] );
			}
			echo '</div>';
		}
		return ob_get_clean();
	}

	/**
	 * Renders the answer submission form.
	 *
	 * @param int $post_id Post ID.
	 * @return string HTML.
	 * @since 1.0.0
	 */
	public static function render_form( int $post_id ): string {
		return Template::get( 'answer-form.php', [ 'post_id' => $post_id ] );
	}
}
