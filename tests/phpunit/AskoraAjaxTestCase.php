<?php
/**
 * Base test case for the plugin's admin-ajax handlers.
 *
 * @package ASKORA
 */

/**
 * Runs a registered AJAX action and returns the decoded JSON response.
 */
abstract class AskoraAjaxTestCase extends WP_Ajax_UnitTestCase {

	/**
	 * Reset plugin-visible state before each test.
	 *
	 * @return void
	 */
	public function set_up() {
		parent::set_up();
		$this->go_offline();
		// Flood control is covered by its own test; keep unrelated tests independent.
		add_filter( 'comment_flood_filter', '__return_false' );
		$this->set_settings(
			[
				'require_login_to_ask'   => 1,
				'require_login_to_reply' => 1,
				'allow_guest_replies'    => 0,
				'enable_voting'          => 1,
				'enable_best_answer'     => 1,
				'enable_phone_auth'      => 1,
				'email_required'         => 0,
				'question_status'        => 'pending',
			]
		);
	}

	/**
	 * The plugin makes no external requests, so tests must never need the network.
	 *
	 * Firing admin_init (which the AJAX harness does) otherwise triggers a real
	 * wp_version_check() call to api.wordpress.org before every test, making the
	 * suite slow and dependent on connectivity.
	 *
	 * @return void
	 */
	protected function go_offline(): void {
		remove_action( 'admin_init', '_maybe_update_core' );
		remove_action( 'admin_init', '_maybe_update_plugins' );
		remove_action( 'admin_init', '_maybe_update_themes' );
		add_filter(
			'pre_http_request',
			static function () {
				return new WP_Error( 'askora_tests_offline', 'External HTTP requests are blocked in the Askora test suite.' );
			}
		);
	}

	/**
	 * Merge values into the plugin settings option.
	 *
	 * @param array $values Settings to set.
	 * @return void
	 */
	protected function set_settings( array $values ): void {
		update_option( 'askora_settings', array_merge( (array) get_option( 'askora_settings', [] ), $values ) );
	}

	/**
	 * Create a published question.
	 *
	 * @param array $args Overrides.
	 * @return int
	 */
	protected function make_question( array $args = [] ): int {
		return (int) self::factory()->post->create(
			array_merge(
				[
					'post_type'      => 'questions',
					'post_status'    => 'publish',
					'post_title'     => 'Test question',
					'comment_status' => 'open',
				],
				$args
			)
		);
	}

	/**
	 * Create an approved answer.
	 *
	 * @param int   $post_id Post ID.
	 * @param array $args    Overrides.
	 * @return int
	 */
	protected function make_answer( int $post_id, array $args = [] ): int {
		return (int) self::factory()->comment->create(
			array_merge(
				[
					'comment_post_ID'  => $post_id,
					'comment_content'  => 'An answer',
					'comment_approved' => 1,
				],
				$args
			)
		);
	}

	/**
	 * Log in as a fresh user with the given role.
	 *
	 * @param string $role Role slug.
	 * @return int
	 */
	protected function login_as( string $role = 'subscriber' ): int {
		$user_id = self::factory()->user->create( [ 'role' => $role ] );
		wp_set_current_user( $user_id );
		return $user_id;
	}

	/**
	 * Fire an AJAX action with the given POST data and return the decoded response.
	 *
	 * @param string $action Action name without the wp_ajax_ prefix.
	 * @param array  $post   POST fields. A valid nonce is added unless one is given.
	 * @return array Decoded JSON ({success, data}).
	 */
	protected function ajax( string $action, array $post = [] ): array {
		$_POST           = array_merge( [ 'nonce' => wp_create_nonce( 'askora_nonce' ) ], $post );
		$_POST['action'] = $action;
		// WordPress adds magic-quote slashes to $_POST on every real request (wp_magic_quotes()),
		// and handlers are written to wp_unslash() it. Emulate that or backslashes are lost.
		$_POST = wp_slash( $_POST );
		$this->_last_response = '';

		try {
			$this->_handleAjax( $action );
		} catch ( WPAjaxDieContinueException $e ) {
			// Expected: wp_send_json_* ends the request.
			unset( $e );
		}

		$decoded = json_decode( $this->_last_response, true );
		$this->assertIsArray( $decoded, 'AJAX handler did not return JSON: ' . $this->_last_response );
		return $decoded;
	}

	/**
	 * Convenience: message from an AJAX response.
	 *
	 * @param array $response Decoded response.
	 * @return string
	 */
	protected function message( array $response ): string {
		return (string) ( $response['data']['message'] ?? '' );
	}
}
