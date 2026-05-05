<?php
/**
 * Questions archive template handler.
 * Intercepts the CPT archive URL and renders our styled template.
 *
 * @package QuestionHub\Frontend\Inc\Archive
 * @since   1.0.0
 */

namespace QuestionHub\Frontend\Inc\Archive;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class QuestionArchive
 *
 * Hooks into template_include to serve our custom archive template
 * when WordPress tries to display the `questions` CPT archive.
 * This prevents the theme's generic archive.php from overriding the design.
 */
class QuestionArchive {

	public function __construct() {
		add_filter( 'template_include', [ $this, 'override_archive_template' ] );
	}

	/**
	 * Returns our custom template for the questions CPT (archive, taxonomy, and single).
	 *
	 * @param  string $template Current template path.
	 * @return string
	 * @since  1.0.0
	 */
	public function override_archive_template( string $template ): string {

		// Single question page.
		if ( is_singular( 'questions' ) ) {
			$theme_file  = get_stylesheet_directory() . '/questionhub/single-question.php';
			$plugin_file = QUESTIONHUB_PATH . 'Frontend/Inc/Templates/single-question.php';
			if ( file_exists( $theme_file ) ) {
				return $theme_file;
			}
			if ( file_exists( $plugin_file ) ) {
				return $plugin_file;
			}
		}

		// CPT archive (/questions/) — render the same view as [questionhub_questions] shortcode.
		if ( is_post_type_archive( 'questions' ) ) {
			$theme_file  = get_stylesheet_directory() . '/questionhub/archive-shortcode.php';
			$plugin_file = QUESTIONHUB_PATH . 'Frontend/Inc/Templates/archive-shortcode.php';
			if ( file_exists( $theme_file ) ) {
				return $theme_file;
			}
			if ( file_exists( $plugin_file ) ) {
				return $plugin_file;
			}
		}

		// Taxonomy pages (category / tag) — keep the richer sidebar layout.
		if ( is_tax( 'question_category' ) || is_tax( 'question_tag' ) ) {
			$theme_file  = get_stylesheet_directory() . '/questionhub/archive-questions.php';
			$plugin_file = QUESTIONHUB_PATH . 'Frontend/Inc/Templates/archive-questions.php';
			if ( file_exists( $theme_file ) ) {
				return $theme_file;
			}
			if ( file_exists( $plugin_file ) ) {
				return $plugin_file;
			}
		}

		return $template;
	}
}
