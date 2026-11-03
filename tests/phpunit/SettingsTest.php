<?php
/**
 * @package ASKORA
 */

use ASKORA\Admin\Inc\Dashboard\Settings\Settings;
use ASKORA\Admin\Inc\Dashboard\Settings\SettingsTabs\Advanced;

class SettingsTest extends AskoraTestCase {

	private function submit_advanced( array $input ): array {
		$this->login_as( 'administrator' );
		$_POST['askora_nonce'] = wp_create_nonce( 'askora_save_settings' );
		$_POST['option_page']  = 'askora_advanced_group';
		return ( new Advanced() )->sanitize( $input );
	}

	public function test_valid_colors_are_saved_and_invalid_ones_fall_back_to_defaults() {
		$saved = $this->submit_advanced( [ 'primary_color' => 'not-a-color', 'category_color' => '#ABCDEF', 'tag_color' => '' ] );

		$this->assertSame( '#6C63FF', $saved['primary_color'] );
		$this->assertSame( '#ABCDEF', $saved['category_color'] );
		$this->assertSame( '#0E9F8E', $saved['tag_color'] );
	}

	public function test_rich_editor_toggle_is_saved_as_a_boolean_flag() {
		$this->assertSame( 1, $this->submit_advanced( [ 'enable_rich_editor' => '1' ] )['enable_rich_editor'] );
		$this->assertSame( 0, $this->submit_advanced( [] )['enable_rich_editor'] );
	}

	public function test_saving_without_a_valid_nonce_changes_nothing() {
		update_option( 'askora_settings', [ 'primary_color' => '#111111' ] );
		$this->login_as( 'administrator' );
		$_POST['askora_nonce'] = 'bad';
		$_POST['option_page']  = 'askora_advanced_group';

		$result = ( new Advanced() )->sanitize( [ 'primary_color' => '#999999' ] );
		$this->assertSame( '#111111', $result['primary_color'] );
	}

	public function test_advanced_tab_renders_the_new_fields() {
		$this->login_as( 'administrator' );
		$settings = new Settings();
		$method   = new ReflectionMethod( $settings, 'render_advanced_fields' );
		$method->setAccessible( true );

		ob_start();
		$method->invoke( $settings );
		$html = ob_get_clean();

		foreach ( [ 'askora_category_color', 'askora_tag_color', 'askora_enable_rich_editor', 'askora_primary_color' ] as $field_id ) {
			$this->assertStringContainsString( 'id="' . $field_id . '"', $html );
		}
	}
}
