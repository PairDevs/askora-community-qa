<?php
/**
 * @package ASKORA
 */

use ASKORA\Frontend\Inc\Auth\UserMeta;

class UninstallTest extends AskoraTestCase {

	private function run_uninstall(): void {
		if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
			define( 'WP_UNINSTALL_PLUGIN', 'askora-community-qa/askora-community-qa.php' );
		}
		include dirname( __DIR__, 2 ) . '/uninstall.php';
	}

	public function test_nothing_is_removed_unless_delete_data_is_enabled() {
		$q = $this->make_question();
		$this->set_settings( [ 'delete_data_on_uninstall' => 0 ] );

		$this->run_uninstall();

		$this->assertSame( 'publish', get_post_status( $q ) );
		$this->assertNotFalse( get_option( 'askora_settings' ) );
	}

	public function test_enabling_delete_data_removes_questions_answers_terms_options_and_meta() {
		$q      = $this->make_question();
		$answer = $this->make_answer( $q );
		$term   = self::factory()->term->create( [ 'taxonomy' => 'question_category' ] );
		wp_set_object_terms( $q, [ $term ], 'question_category' );
		update_comment_meta( $answer, '_askora_verified', 1 );
		update_comment_meta( $answer, '_askora_verified_by', 1 );
		update_comment_meta( $answer, '_askora_answer_votes', 3 );
		$user = self::factory()->user->create();
		UserMeta::set_phone( $user, '+15550001234' );
		update_option( 'askora_pages', [ 'auth' => 1 ] );
		update_option( 'askora_welcome_notice_dismissed', 1 );
		$this->set_settings( [ 'delete_data_on_uninstall' => 1 ] );

		$this->run_uninstall();

		$this->assertNull( get_post( $q ), 'The question is deleted.' );
		$this->assertNull( get_comment( $answer ), 'Its answers are deleted with it.' );
		$this->assertEmpty( term_exists( $term, 'question_category' ), 'Plugin terms are deleted.' );
		$this->assertFalse( get_option( 'askora_settings' ) );
		$this->assertFalse( get_option( 'askora_pages' ) );
		$this->assertFalse( get_option( 'askora_welcome_notice_dismissed' ) );
		$this->assertSame( '', UserMeta::get_phone( $user ) );
		$this->assertSame( '', (string) get_comment_meta( $answer, '_askora_verified', true ) );
	}

	public function test_unrelated_content_survives_a_full_uninstall() {
		$post = self::factory()->post->create();
		$page = self::factory()->post->create( [ 'post_type' => 'page' ] );
		$cat  = self::factory()->category->create();
		$this->set_settings( [ 'delete_data_on_uninstall' => 1 ] );

		$this->run_uninstall();

		$this->assertNotNull( get_post( $post ) );
		$this->assertNotNull( get_post( $page ) );
		$this->assertNotFalse( term_exists( $cat, 'category' ) );
	}
}
