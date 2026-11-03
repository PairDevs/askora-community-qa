<?php
/**
 * @package ASKORA
 */

class AssetsTest extends AskoraTestCase {

	private function inline_css(): string {
		wp_dequeue_style( 'askora-frontend' );
		wp_deregister_style( 'askora-frontend' );
		do_action( 'wp_enqueue_scripts' );
		return (string) wp_styles()->get_data( 'askora-frontend', 'after' )[0] ?? '';
	}

	public function test_css_variables_use_the_configured_colors() {
		$this->set_settings( [ 'primary_color' => '#112233', 'category_color' => '#AA0000', 'tag_color' => '#00AA00' ] );
		$css = $this->inline_css();

		$this->assertStringContainsString( '--askora-primary: #112233', $css );
		$this->assertStringContainsString( '--askora-category: #AA0000', $css );
		$this->assertStringContainsString( '--askora-tag: #00AA00', $css );
	}

	public function test_backgrounds_are_light_tints_of_the_color() {
		$this->set_settings( [ 'category_color' => '#000000', 'tag_color' => '#FF0000' ] );
		$css = $this->inline_css();

		// 88% towards white.
		$this->assertStringContainsString( '--askora-category-bg: #e0e0e0', $css );
		$this->assertStringContainsString( '--askora-tag-bg: #ffe0e0', $css );
	}

	public function test_invalid_or_missing_colors_fall_back_to_defaults() {
		$this->set_settings( [ 'primary_color' => null, 'category_color' => 'javascript:alert(1)', 'tag_color' => '' ] );
		$css = $this->inline_css();

		$this->assertStringContainsString( '--askora-primary: #6C63FF', $css );
		$this->assertStringContainsString( '--askora-category: #6C63FF', $css );
		$this->assertStringContainsString( '--askora-tag: #0E9F8E', $css );
		$this->assertStringNotContainsString( 'javascript', $css );
		$this->assertStringNotContainsString( ': ;', $css, 'No empty custom property values.' );
	}

	public function test_auth_script_depends_on_the_script_that_defines_askora_data() {
		do_action( 'wp_enqueue_scripts' );
		$this->assertContains( 'askora-frontend', wp_scripts()->registered['askora-auth']->deps );
	}

	public function test_nonce_and_ajax_url_are_localized() {
		do_action( 'wp_enqueue_scripts' );
		$data = (string) wp_scripts()->get_data( 'askora-frontend', 'data' );
		$this->assertStringContainsString( 'AskoraData', $data );
		$this->assertStringContainsString( 'admin-ajax.php', $data );
		$this->assertStringContainsString( '"nonce"', $data );
	}
}
