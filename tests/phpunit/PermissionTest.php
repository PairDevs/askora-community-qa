<?php
/**
 * @package ASKORA
 */

use ASKORA\Frontend\Inc\Helpers\Permission;

class PermissionTest extends AskoraTestCase {

	public function test_asking_requires_login_only_when_the_setting_is_on() {
		$this->set_settings( [ 'require_login_to_ask' => 1 ] );
		wp_set_current_user( 0 );
		$this->assertFalse( Permission::can_submit_question() );

		$this->set_settings( [ 'require_login_to_ask' => 0 ] );
		$this->assertTrue( Permission::can_submit_question() );
	}

	public function test_guest_replies_need_both_settings() {
		wp_set_current_user( 0 );

		$this->set_settings( [ 'require_login_to_reply' => 1, 'allow_guest_replies' => 1 ] );
		$this->assertFalse( Permission::can_reply() );

		$this->set_settings( [ 'require_login_to_reply' => 0, 'allow_guest_replies' => 0 ] );
		$this->assertFalse( Permission::can_reply() );

		$this->set_settings( [ 'require_login_to_reply' => 0, 'allow_guest_replies' => 1 ] );
		$this->assertTrue( Permission::can_reply() );

		$this->login_as();
		$this->set_settings( [ 'require_login_to_reply' => 1 ] );
		$this->assertTrue( Permission::can_reply() );
	}

	public function test_feature_toggles_default_to_enabled_and_respect_the_setting() {
		delete_option( 'askora_settings' );
		$this->assertTrue( Permission::phone_auth_enabled() );
		$this->assertTrue( Permission::voting_enabled() );
		$this->assertTrue( Permission::best_answer_enabled() );

		$this->set_settings( [ 'enable_phone_auth' => 0, 'enable_voting' => 0, 'enable_best_answer' => 0 ] );
		$this->assertFalse( Permission::phone_auth_enabled() );
		$this->assertFalse( Permission::voting_enabled() );
		$this->assertFalse( Permission::best_answer_enabled() );
	}

	public function test_best_answer_is_limited_to_the_question_author_and_admins() {
		$author   = self::factory()->user->create( [ 'role' => 'subscriber' ] );
		$stranger = self::factory()->user->create( [ 'role' => 'subscriber' ] );
		$admin    = self::factory()->user->create( [ 'role' => 'administrator' ] );
		$q        = $this->make_question( [ 'post_author' => $author ] );

		wp_set_current_user( 0 );
		$this->assertFalse( Permission::can_mark_best_answer( $q ) );
		wp_set_current_user( $stranger );
		$this->assertFalse( Permission::can_mark_best_answer( $q ) );
		wp_set_current_user( $author );
		$this->assertTrue( Permission::can_mark_best_answer( $q ) );
		wp_set_current_user( $admin );
		$this->assertTrue( Permission::can_mark_best_answer( $q ) );
	}

	public function test_best_answer_is_refused_for_anything_that_is_not_a_question() {
		$admin = $this->login_as( 'administrator' );
		$post  = self::factory()->post->create( [ 'post_author' => $admin ] );

		$this->assertFalse( Permission::can_mark_best_answer( $post ) );
		$this->assertFalse( Permission::can_mark_best_answer( 987654 ) );
	}
}
