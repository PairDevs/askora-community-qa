<?php
/**
 * Editor helper.
 *
 * Renders the WordPress visual editor (TinyMCE) for the question and answer
 * forms, or a plain textarea when the rich editor is switched off.
 *
 * @package ASKORA\Frontend\Inc\Helpers
 * @since   1.1.0
 */

namespace ASKORA\Frontend\Inc\Helpers;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Editor
 */
class Editor {

	/**
	 * Whether the rich text editor is enabled in the plugin settings.
	 *
	 * @return bool
	 */
	public static function is_enabled(): bool {
		$settings = get_option( 'askora_settings', [] );
		$enabled  = ! isset( $settings['enable_rich_editor'] ) || ! empty( $settings['enable_rich_editor'] );

		/**
		 * Filters whether the rich text editor is used on the front-end forms.
		 *
		 * @param bool $enabled Whether the editor is enabled.
		 * @since 1.1.0
		 */
		return (bool) apply_filters( 'askora_enable_rich_editor', $enabled );
	}

	/**
	 * Output the editor (or a textarea fallback).
	 *
	 * @param string $editor_id  Unique HTML id; letters, numbers, dashes and underscores only.
	 * @param string $name       Field name posted to the server.
	 * @param string $content    Initial content.
	 * @param array  $args       Optional. rows, placeholder, class.
	 * @return void
	 */
	public static function render( string $editor_id, string $name, string $content = '', array $args = [] ): void {
		$args = wp_parse_args(
			$args,
			[
				'rows'        => 7,
				'placeholder' => '',
				'class'       => 'askora-textarea',
			]
		);

		if ( ! self::is_enabled() ) {
			printf(
				'<textarea id="%1$s" name="%2$s" class="%3$s" rows="%4$d" placeholder="%5$s">%6$s</textarea>',
				esc_attr( $editor_id ),
				esc_attr( $name ),
				esc_attr( $args['class'] ),
				absint( $args['rows'] ),
				esc_attr( $args['placeholder'] ),
				esc_textarea( $content )
			);
			return;
		}

		/**
		 * Filters the wp_editor() settings used on the front-end forms.
		 *
		 * @param array  $settings  wp_editor() settings.
		 * @param string $editor_id Editor id.
		 * @since 1.1.0
		 */
		$settings = apply_filters(
			'askora_editor_settings',
			[
				'textarea_name' => $name,
				'textarea_rows' => absint( $args['rows'] ),
				'media_buttons' => false,
				'drag_drop_upload' => false,
				'wpautop'       => true,
				'editor_class'  => 'askora-editor-textarea',
				'tinymce'       => [
					'toolbar1'  => 'bold,italic,underline,strikethrough,bullist,numlist,blockquote,link,unlink,removeformat,undo,redo',
					'toolbar2'  => '',
					'statusbar' => false,
					'placeholder' => $args['placeholder'],
				],
				'quicktags'     => [
					'buttons' => 'strong,em,link,block,ul,ol,li,code,close',
				],
			],
			$editor_id
		);

		echo '<div class="askora-editor-wrap">';
		wp_editor( $content, $editor_id, $settings );
		echo '</div>';
	}
}
