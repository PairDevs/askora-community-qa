<?php
/**
 * @package ASKORA
 */

use ASKORA\Frontend\Inc\Helpers\Editor;

class EditorTest extends AskoraTestCase {

	private function render( string $id = 'askora-test-editor', string $content = '' ): string {
		ob_start();
		Editor::render( $id, 'content', $content, [ 'placeholder' => 'Write here' ] );
		return (string) ob_get_clean();
	}

	public function test_rich_editor_is_on_by_default() {
		delete_option( 'askora_settings' );
		$this->assertTrue( Editor::is_enabled() );

		$html = $this->render();
		$this->assertStringContainsString( 'wp-editor-wrap', $html );
		$this->assertStringContainsString( 'askora-editor-wrap', $html );
		$this->assertStringContainsString( 'name="content"', $html );
	}

	public function test_setting_switches_to_a_plain_textarea() {
		$this->set_settings( [ 'enable_rich_editor' => 0 ] );
		$this->assertFalse( Editor::is_enabled() );

		$html = $this->render( 'plain-editor', 'a <b>b</b>' );
		$this->assertStringNotContainsString( 'wp-editor-wrap', $html );
		$this->assertStringContainsString( '<textarea', $html );
		$this->assertStringContainsString( 'placeholder="Write here"', $html );
		$this->assertStringContainsString( 'a &lt;b&gt;b&lt;/b&gt;', $html, 'Content must be escaped in the fallback textarea.' );
	}

	public function test_filter_can_override_the_setting() {
		$this->set_settings( [ 'enable_rich_editor' => 1 ] );
		add_filter( 'askora_enable_rich_editor', '__return_false' );
		$this->assertFalse( Editor::is_enabled() );
	}

	public function test_editor_settings_filter_is_applied() {
		$seen = null;
		add_filter(
			'askora_editor_settings',
			static function ( $settings, $id ) use ( &$seen ) {
				$seen = $id;
				$settings['media_buttons'] = true;
				return $settings;
			},
			10,
			2
		);

		$this->render( 'filtered-editor' );
		$this->assertSame( 'filtered-editor', $seen );
	}

	public function test_media_buttons_are_off_by_default() {
		$captured = null;
		add_filter(
			'askora_editor_settings',
			static function ( $settings ) use ( &$captured ) {
				$captured = $settings;
				return $settings;
			}
		);
		$this->render();

		$this->assertFalse( $captured['media_buttons'], 'Front-end visitors must not get the media library.' );
	}
}
