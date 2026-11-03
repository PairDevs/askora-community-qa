<?php
/**
 * Shared helpers for the Askora test suite.
 *
 * @package ASKORA
 */

/**
 * Base test case with small factories for questions, answers and settings.
 */
abstract class AskoraTestCase extends WP_UnitTestCase {

	/**
	 * Block external HTTP: the plugin makes no external requests, so a test that
	 * tries one is a bug (and would make the suite slow and flaky offline).
	 *
	 * @return void
	 */
	public function set_up() {
		parent::set_up();
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
	 * Create a published question (or one with a custom status).
	 *
	 * @param array $args wp_insert_post style overrides.
	 * @return int Post ID.
	 */
	protected function make_question( array $args = [] ): int {
		return (int) self::factory()->post->create(
			array_merge(
				[
					'post_type'      => 'questions',
					'post_status'    => 'publish',
					'post_title'     => 'Test question',
					'post_content'   => '<p>Body</p>',
					'comment_status' => 'open',
				],
				$args
			)
		);
	}

	/**
	 * Create an approved answer on a post.
	 *
	 * @param int   $post_id Post ID.
	 * @param array $args    Comment overrides.
	 * @return int Comment ID.
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
	 * @return int User ID.
	 */
	protected function login_as( string $role = 'subscriber' ): int {
		$user_id = self::factory()->user->create( [ 'role' => $role ] );
		wp_set_current_user( $user_id );
		return $user_id;
	}
}
