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
	 * Returns our custom archive template for the questions CPT.
	 *
	 * @param  string $template Current template path.
	 * @return string
	 * @since  1.0.0
	 */
	public function override_archive_template( string $template ): string {
		if ( is_post_type_archive( 'questions' ) || is_tax( 'question_category' ) || is_tax( 'question_tag' ) ) {
			// Check for theme override first: theme/questionhub/archive-questions.php
			$theme_file = get_stylesheet_directory() . '/questionhub/archive-questions.php';
			if ( file_exists( $theme_file ) ) {
				return $theme_file;
			}

			$plugin_file = QUESTIONHUB_PATH . 'Frontend/Inc/Templates/archive-questions.php';
			if ( file_exists( $plugin_file ) ) {
				return $plugin_file;
			}
		}
		return $template;
	}
}
