<?php
/**
 * @package ASKORA
 */

use ASKORA\Admin\Inc\Users\Profile;
use ASKORA\Frontend\Inc\Auth\UserMeta;

class ProfileTest extends AskoraTestCase {

	public function test_profile_form_outputs_the_nonce_field_that_save_requires() {
		$user = get_userdata( $this->login_as( 'subscriber' ) );

		ob_start();
		( new Profile() )->render_phone_field( $user );
		$html = ob_get_clean();

		$this->assertStringContainsString( 'name="askora_user_phone_nonce"', $html );
		$this->assertStringContainsString( 'name="askora_phone"', $html );
	}

	public function test_phone_is_saved_with_a_valid_nonce() {
		$user_id = $this->login_as( 'subscriber' );
		$_POST   = [
			'askora_user_phone_nonce' => wp_create_nonce( 'askora_save_user_phone' ),
			'askora_phone'            => '+1 (555) 000-1234abc',
		];

		( new Profile() )->save_phone_field( $user_id );

		$this->assertSame( '+1 (555) 000-1234', UserMeta::get_phone( $user_id ), 'Non-phone characters are stripped.' );
	}

	public function test_phone_is_not_saved_without_a_nonce() {
		$user_id = $this->login_as( 'subscriber' );
		$_POST   = [ 'askora_phone' => '+15550001111' ];

		( new Profile() )->save_phone_field( $user_id );

		$this->assertSame( '', UserMeta::get_phone( $user_id ) );
	}

	public function test_phone_already_used_by_someone_else_is_not_taken() {
		$owner = self::factory()->user->create();
		UserMeta::set_phone( $owner, '+15557770000' );

		$user_id = $this->login_as( 'subscriber' );
		$_POST   = [
			'askora_user_phone_nonce' => wp_create_nonce( 'askora_save_user_phone' ),
			'askora_phone'            => '+15557770000',
		];
		( new Profile() )->save_phone_field( $user_id );

		$this->assertSame( '', UserMeta::get_phone( $user_id ) );
	}

	public function test_users_cannot_edit_other_users_phone_numbers() {
		$victim = self::factory()->user->create();
		$this->login_as( 'subscriber' );
		$_POST = [
			'askora_user_phone_nonce' => wp_create_nonce( 'askora_save_user_phone' ),
			'askora_phone'            => '+15558889999',
		];

		( new Profile() )->save_phone_field( $victim );

		$this->assertSame( '', UserMeta::get_phone( $victim ) );
	}
}
