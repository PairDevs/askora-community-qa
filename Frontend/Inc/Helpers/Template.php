<?php
/**
 * Template loader helper.
 *
 * @package ASKORA\Frontend\Inc\Helpers
 * @since   1.0.0
 */

namespace ASKORA\Frontend\Inc\Helpers;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Template
 *
 * Loads plugin templates with theme override support.
 * Override in theme: {theme}/askora-community-qa/{template}.php
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
		$theme_file  = get_stylesheet_directory() . '/askora-community-qa/' . $template;
		$plugin_file = ASKORA_PATH . 'Frontend/Inc/Templates/' . $template;

		$file = file_exists( $theme_file ) ? $theme_file : $plugin_file;

		if ( ! file_exists( $file ) ) {
			return;
		}

		$askora_template_data = [];

		foreach ( (array) $data as $askora_key => $askora_value ) {
			$askora_template_data[ 'askora_' . ltrim( (string) $askora_key, '_' ) ] = $askora_value;
		}

		if ( ! empty( $askora_template_data ) ) {
			// phpcs:ignore WordPress.PHP.DontExtract.extract_extract
			extract( $askora_template_data, EXTR_SKIP );
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
