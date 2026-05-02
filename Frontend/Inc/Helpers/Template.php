<?php
/**
 * Template loader helper.
 *
 * @package QuestionHub\Frontend\Inc\Helpers
 * @since   1.0.0
 */

namespace QuestionHub\Frontend\Inc\Helpers;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Template
 *
 * Loads plugin templates with theme override support.
 * Override in theme: {theme}/questionhub/{template}.php
 */
class Template {

	/**
	 * Loads a template file, checking the active theme first.
	 *
	 * @param string $template Template filename (e.g. 'question-card.php').
	 * @param array  $data     Variables to extract into template scope.
	 * @since 1.0.0
	 */
	public static function load( $template, $data = [] ) {
		$theme_file  = get_stylesheet_directory() . '/questionhub/' . $template;
		$plugin_file = QUESTIONHUB_PATH . 'Frontend/Inc/Templates/' . $template;

		$file = file_exists( $theme_file ) ? $theme_file : $plugin_file;

		if ( ! file_exists( $file ) ) {
			return;
		}

		if ( ! empty( $data ) ) {
			// phpcs:ignore WordPress.PHP.DontExtract.extract_extract
			extract( $data, EXTR_SKIP );
		}

		include $file;
	}

	/**
	 * Loads a template and returns its output as a string.
	 *
	 * @param string $template Template filename.
	 * @param array  $data     Variables to extract into template scope.
	 * @return string
	 * @since 1.0.0
	 */
	public static function get( $template, $data = [] ) {
		ob_start();
		self::load( $template, $data );
		return ob_get_clean();
	}
}
