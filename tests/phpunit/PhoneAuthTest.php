<?php
/**
 * @package ASKORA
 */

use ASKORA\Frontend\Inc\Auth\PhoneLogin;
use ASKORA\Frontend\Inc\Auth\PhoneRegister;
use ASKORA\Frontend\Inc\Auth\UserMeta;

class PhoneAuthTest extends AskoraTestCase {

	private function register( array $overrides = [] ) {
		return ( new PhoneRegister() )->register(
			array_merge(
				[
					'name'     => 'Zed Tester',
					'phone'    => '+15550' . wp_rand( 100000, 999999 ),
					'password' => 'secret-pass-1',
					'email'    => '',
				],
				$overrides
			)
		);
	}

	public function test_username_is_random_and_not_derived_from_the_phone_number() {
		$phone = '+15551234567';
		$id    = $this->register( [ 'phone' => $phone ] );
		$this->assertIsInt( $id );

		$login = get_userdata( $id )->user_login;
		$this->assertMatchesRegularExpression( '/^askora_[a-z0-9]{12}$/', $login );
		$this->assertStringNotContainsString( substr( md5( $phone ), 0, 10 ), $login, 'A phone hash in the username leaks the number.' );
		$this->assertSame( $phone, UserMeta::get_phone( $id ) );
	}

	public function test_two_registrations_never_share_a_username() {
		$a = get_userdata( $this->register() )->user_login;
		$b = get_userdata( $this->register() )->user_login;
		$this->assertNotSame( $a, $b );
	}

	public function test_passwords_are_stored_untouched() {
		$password = 'p<a>ss%41 w\\d "x"\'';
		$id       = $this->register( [ 'password' => $password ] );
		$this->assertIsInt( $id );

		$user = wp_authenticate( get_userdata( $id )->user_login, $password );
		$this->assertNotWPError( $user, 'A password with tags, %xx, quotes and backslashes must authenticate unchanged.' );
	}

	public function test_display_name_keeps_apostrophes_and_backslashes() {
		$id = $this->register( [ 'name' => "Zed O'Brien\\" ] );
		$this->assertSame( "Zed O'Brien\\", get_userdata( $id )->display_name );
	}

	public function test_short_and_blank_passwords_are_rejected() {
		foreach ( [ '12345', '      ', '' ] as $weak ) {
			$result = $this->register( [ 'password' => $weak ] );
			$this->assertWPError( $result );
			$this->assertSame( 'weak_password', $result->get_error_code() );
		}
	}

	public function test_required_fields_are_validated() {
		$this->assertSame( 'invalid_name', $this->register( [ 'name' => '' ] )->get_error_code() );
		$this->assertSame( 'invalid_phone', $this->register( [ 'phone' => '12' ] )->get_error_code() );
	}

	public function test_duplicate_phone_is_rejected() {
		$this->register( [ 'phone' => '+15559990000' ] );
		$dup = $this->register( [ 'phone' => '+15559990000' ] );
		$this->assertSame( 'phone_taken', $dup->get_error_code() );
	}

	public function test_email_is_enforced_when_required() {
		$this->set_settings( [ 'email_required' => 1 ] );
		$this->assertSame( 'email_required', $this->register()->get_error_code() );
		$this->assertIsInt( $this->register( [ 'email' => 'real@example.test' ] ) );

		$this->set_settings( [ 'email_required' => 0 ] );
		$id = $this->register();
		$this->assertStringEndsWith( '.local', get_userdata( $id )->user_email, 'A placeholder email is generated when none is needed.' );
	}

	public function test_login_works_with_the_phone_number_and_locks_out_after_repeated_failures() {
		$phone = '+15558887777';
		$id    = $this->register( [ 'phone' => $phone, 'password' => 'right-pass-1' ] );

		$login = new PhoneLogin();
		for ( $i = 0; $i < PhoneLogin::MAX_ATTEMPTS; $i++ ) {
			$this->assertWPError( $login->login( $phone, 'wrong' ) );
		}

		$locked = $login->login( $phone, 'right-pass-1' );
		$this->assertWPError( $locked );
		$this->assertSame( 'too_many_attempts', $locked->get_error_code() );

		delete_transient( PhoneLogin::ATTEMPTS_TRANSIENT . md5( $phone ) );
		$this->assertSame( $id, $login->login( $phone, 'right-pass-1' ) );
	}

	public function test_unknown_phone_gives_the_same_error_as_a_wrong_password() {
		$phone = '+15556665555';
		$this->register( [ 'phone' => $phone ] );

		$unknown = ( new PhoneLogin() )->login( '+15550000000', 'whatever1' );
		$wrong   = ( new PhoneLogin() )->login( $phone, 'whatever1' );

		$this->assertSame( $unknown->get_error_message(), $wrong->get_error_message(), 'Errors must not reveal which phone numbers exist.' );
	}
}
