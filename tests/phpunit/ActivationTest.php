<?php
/**
 * @package ASKORA
 */

use ASKORA\Activate;

class ActivationTest extends AskoraTestCase {

	public function test_activation_stores_version_and_default_settings() {
		delete_option( 'askora_settings' );
		delete_option( 'askora_pages' );
		Activate::activate();

		$this->assertSame( ASKORA_VERSION, get_option( 'askora_version' ) );
		$settings = get_option( 'askora_settings' );
		foreach ( [ 'question_status', 'require_login_to_ask', 'enable_voting', 'primary_color', 'category_color', 'tag_color', 'enable_rich_editor' ] as $key ) {
			$this->assertArrayHasKey( $key, $settings, "Default for $key is missing." );
		}
		$this->assertSame( 1, $settings['enable_rich_editor'] );
		$this->assertNotSame( $settings['category_color'], $settings['tag_color'], 'Categories and tags must default to different colors.' );
	}

	public function test_activation_does_not_overwrite_existing_settings() {
		update_option( 'askora_settings', [ 'questions_per_page' => 33 ] );
		Activate::activate();
		$this->assertSame( 33, get_option( 'askora_settings' )['questions_per_page'] );
	}

	public function test_question_rewrite_rules_exist_straight_after_activation() {
		$this->set_permalink_structure( '/%postname%/' );

		// On a real activation the plugin has not booted yet, so the post type is unregistered.
		unregister_post_type( 'questions' );
		unregister_taxonomy( 'question_category' );
		unregister_taxonomy( 'question_tag' );
		flush_rewrite_rules();
		$this->assertStringNotContainsString( 'post_type=questions', wp_json_encode( get_option( 'rewrite_rules' ) ) );

		Activate::activate();

		$rules = wp_json_encode( get_option( 'rewrite_rules' ) );
		$this->assertStringContainsString( 'post_type=questions', $rules, 'Single question URLs would 404 until permalinks are re-saved.' );
		$this->assertStringContainsString( 'question_category=', $rules );
		$this->assertStringContainsString( 'question_tag=', $rules );
	}

	public function test_activation_creates_draft_pages_once() {
		delete_option( 'askora_pages' );
		Activate::activate();

		$pages = get_option( 'askora_pages' );
		$this->assertIsArray( $pages );
		$this->assertArrayHasKey( 'auth', $pages );
		$this->assertSame( 'draft', get_post_status( $pages['auth'] ) );
		$this->assertStringContainsString( '[askora_auth]', get_post( $pages['auth'] )->post_content );
	}
}
