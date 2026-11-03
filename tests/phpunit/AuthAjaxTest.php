<?php
/**
 * @group ajax
 * @package ASKORA
 */

use ASKORA\Frontend\Inc\Auth\UserMeta;

class AuthAjaxTest extends AskoraAjaxTestCase {

	private function register_payload( array $overrides = [] ): array {
		return array_merge(
			[
				'name'             => 'Nina New',
				'phone'            => '+15550' . wp_rand( 100000, 999999 ),
				'email'            => '',
				'password'         => 'a-good-pass-1',
				'confirm_password' => 'a-good-pass-1',
				'askora_hp'        => '',
			],
			$overrides
		);
	}

	public function set_up() {
		parent::set_up();
		wp_set_current_user( 0 );
	}

	public function test_register_creates_an_account_and_logs_in() {
		$payload = $this->register_payload();
		$res     = $this->ajax( 'askora_phone_register', $payload );

		$this->assertTrue( $res['success'], $this->message( $res ) );
		$user_id = UserMeta::find_user_by_phone( $payload['phone'] );
		$this->assertNotNull( $user_id );
		$this->assertSame( 'Nina New', get_userdata( $user_id )->display_name );
		$this->assertArrayHasKey( 'redirect', $res['data'] );
	}

	public function test_password_with_special_characters_survives_the_ajax_layer() {
		$password = 'p<a>ss%41 w\\d "x"';
		$payload  = $this->register_payload( [ 'password' => $password, 'confirm_password' => $password ] );
		$this->ajax( 'askora_phone_register', $payload );

		$user = get_userdata( UserMeta::find_user_by_phone( $payload['phone'] ) );
		$this->assertNotWPError( wp_authenticate( $user->user_login, $password ), 'The exact password the user typed must be accepted.' );
	}

	public function test_login_accepts_the_same_special_character_password() {
		$password = 'p<a>ss%41 w\\d "x"';
		$payload  = $this->register_payload( [ 'password' => $password, 'confirm_password' => $password ] );
		$this->ajax( 'askora_phone_register', $payload );
		wp_set_current_user( 0 );

		$res = $this->ajax( 'askora_phone_login', [ 'phone' => $payload['phone'], 'password' => $password ] );
		$this->assertTrue( $res['success'], $this->message( $res ) );
	}

	public function test_login_rejects_a_wrong_password_with_a_generic_message() {
		$payload = $this->register_payload();
		$this->ajax( 'askora_phone_register', $payload );
		wp_set_current_user( 0 );

		$res = $this->ajax( 'askora_phone_login', [ 'phone' => $payload['phone'], 'password' => 'wrong-pass' ] );
		$this->assertFalse( $res['success'] );
		$this->assertStringContainsString( 'Invalid phone number or password', $this->message( $res ) );
	}

	public function test_mismatched_passwords_are_rejected() {
		$res = $this->ajax( 'askora_phone_register', $this->register_payload( [ 'confirm_password' => 'different-pass' ] ) );
		$this->assertFalse( $res['success'] );
		$this->assertStringContainsString( 'do not match', $this->message( $res ) );
	}

	public function test_honeypot_blocks_bots_without_creating_an_account() {
		$payload = $this->register_payload( [ 'askora_hp' => 'http://spam.example' ] );
		$res     = $this->ajax( 'askora_phone_register', $payload );

		$this->assertFalse( $res['success'] );
		$this->assertNull( UserMeta::find_user_by_phone( $payload['phone'] ) );
	}

	public function test_registration_is_rate_limited_per_ip() {
		$_SERVER['REMOTE_ADDR'] = '192.0.2.50';
		add_filter( 'askora_registrations_per_hour', static fn() => 2 );

		$this->assertTrue( $this->ajax( 'askora_phone_register', $this->register_payload() )['success'] );
		wp_set_current_user( 0 );
		$this->assertTrue( $this->ajax( 'askora_phone_register', $this->register_payload() )['success'] );
		wp_set_current_user( 0 );

		$third = $this->ajax( 'askora_phone_register', $this->register_payload() );
		$this->assertFalse( $third['success'] );
		$this->assertStringContainsString( 'Too many registrations', $this->message( $third ) );

		$_SERVER['REMOTE_ADDR'] = '192.0.2.51';
		wp_set_current_user( 0 );
		$this->assertTrue( $this->ajax( 'askora_phone_register', $this->register_payload() )['success'], 'Other IP addresses are unaffected.' );
	}

	public function test_failed_registrations_do_not_use_up_the_rate_limit() {
		$_SERVER['REMOTE_ADDR'] = '192.0.2.60';
		add_filter( 'askora_registrations_per_hour', static fn() => 1 );

		$this->ajax( 'askora_phone_register', $this->register_payload( [ 'confirm_password' => 'mismatch' ] ) );
		wp_set_current_user( 0 );
		$this->assertTrue( $this->ajax( 'askora_phone_register', $this->register_payload() )['success'] );
	}

	public function test_registration_can_be_closed_with_a_filter() {
		add_filter( 'askora_allow_registration', '__return_false' );
		$res = $this->ajax( 'askora_phone_register', $this->register_payload() );

		$this->assertFalse( $res['success'] );
		$this->assertStringContainsString( 'closed', $this->message( $res ) );
	}

	public function test_email_required_is_enforced_by_the_server() {
		$this->set_settings( [ 'email_required' => 1 ] );
		$res = $this->ajax( 'askora_phone_register', $this->register_payload() );

		$this->assertFalse( $res['success'] );
		$this->assertStringContainsString( 'email', strtolower( $this->message( $res ) ) );
	}

	public function test_invalid_email_is_rejected_rather_than_silently_dropped() {
		$res = $this->ajax( 'askora_phone_register', $this->register_payload( [ 'email' => 'not-an-email' ] ) );
		$this->assertFalse( $res['success'] );
	}

	public function test_phone_auth_switch_disables_both_endpoints() {
		$this->set_settings( [ 'enable_phone_auth' => 0 ] );

		$login = $this->ajax( 'askora_phone_login', [ 'phone' => '+15550001111', 'password' => 'abcdef1' ] );
		$reg   = $this->ajax( 'askora_phone_register', $this->register_payload() );

		$this->assertFalse( $login['success'] );
		$this->assertStringContainsString( 'disabled', $this->message( $login ) );
		$this->assertFalse( $reg['success'] );
		$this->assertStringContainsString( 'disabled', $this->message( $reg ) );
	}
}
