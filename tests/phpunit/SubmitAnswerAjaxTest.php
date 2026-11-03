<?php
/**
 * @group ajax
 * @package ASKORA
 */

class SubmitAnswerAjaxTest extends AskoraAjaxTestCase {

	private function answer( array $post ): array {
		return $this->ajax( 'askora_submit_answer', $post );
	}

	public function test_nonce_is_required() {
		$this->expectException( WPAjaxDieStopException::class );
		$_POST = [ 'nonce' => 'bad', 'post_id' => $this->make_question(), 'content' => 'x' ];
		$this->_handleAjax( 'askora_submit_answer' );
	}

	public function test_guests_are_rejected_while_login_is_required() {
		wp_set_current_user( 0 );
		$res = $this->answer( [ 'post_id' => $this->make_question(), 'content' => '<p>hi</p>', 'author' => 'G', 'email' => 'g@example.test' ] );

		$this->assertFalse( $res['success'] );
		$this->assertStringContainsString( 'logged in', $this->message( $res ) );
	}

	public function test_member_answer_is_saved_with_ip_and_agent() {
		$user = $this->login_as( 'subscriber' );
		$_SERVER['REMOTE_ADDR']     = '203.0.113.9';
		$_SERVER['HTTP_USER_AGENT'] = 'PHPUnit';
		$q = $this->make_question();

		$res = $this->answer( [ 'post_id' => $q, 'content' => '<p>My answer</p>' ] );

		$this->assertTrue( $res['success'], $this->message( $res ) );
		$comment = get_comment( $res['data']['comment_id'] );
		$this->assertSame( $q, (int) $comment->comment_post_ID );
		$this->assertSame( $user, (int) $comment->user_id );
		$this->assertSame( '203.0.113.9', $comment->comment_author_IP, 'IP is recorded like a normal comment.' );
		$this->assertSame( 'PHPUnit', $comment->comment_agent );
	}

	public function test_backslashes_in_answers_are_preserved() {
		$this->login_as( 'administrator' );
		$res = $this->answer( [ 'post_id' => $this->make_question(), 'content' => '<p>Use C:\\Users\\bob</p>' ] );

		$this->assertTrue( $res['success'], $this->message( $res ) );
		$this->assertStringContainsString( 'C:\\Users\\bob', get_comment( $res['data']['comment_id'] )->comment_content );
	}

	public function test_empty_rich_editor_markup_counts_as_empty() {
		$this->login_as();
		foreach ( [ '', '   ', '<p></p>', '<p> </p>' ] as $empty ) {
			$res = $this->answer( [ 'post_id' => $this->make_question(), 'content' => $empty ] );
			$this->assertFalse( $res['success'], 'Empty content must be refused: ' . $empty );
			$this->assertStringContainsString( 'required', $this->message( $res ) );
		}
	}

	public function test_rich_html_is_kept_and_scripts_are_stripped() {
		$this->login_as( 'subscriber' );
		$res = $this->answer(
			[
				'post_id' => $this->make_question(),
				'content' => '<p>Intro</p><ul><li>one</li></ul><blockquote>q</blockquote><script>alert(1)</script><a href="javascript:alert(2)">x</a>',
			]
		);

		$this->assertTrue( $res['success'], $this->message( $res ) );
		$body = get_comment( $res['data']['comment_id'] )->comment_content;
		$this->assertStringContainsString( '<ul><li>one</li></ul>', $body );
		$this->assertStringContainsString( '<blockquote>', $body );
		$this->assertStringNotContainsString( '<script', $body );
		$this->assertStringNotContainsString( 'javascript:', $body );
	}

	public function test_comment_html_whitelist_widening_does_not_leak_to_later_code() {
		$this->login_as();
		$this->answer( [ 'post_id' => $this->make_question(), 'content' => '<p>x</p>' ] );

		$this->assertFalse( has_filter( 'wp_kses_allowed_html', [ new ASKORA\Frontend\Inc\Ajax\SubmitAnswer(), 'allow_answer_html' ] ) );
		$tags = wp_kses_allowed_html( 'pre_comment_content' );
		$this->assertArrayNotHasKey( 'table', $tags, 'Normal comments must keep core\'s strict tag list.' );
	}

	public function test_answers_to_unpublished_or_non_question_posts_are_refused() {
		$this->login_as();
		foreach ( [ $this->make_question( [ 'post_status' => 'draft' ] ), $this->make_question( [ 'post_status' => 'pending' ] ), self::factory()->post->create(), 987654 ] as $bad ) {
			$res = $this->answer( [ 'post_id' => $bad, 'content' => '<p>x</p>' ] );
			$this->assertFalse( $res['success'], "Answering post #$bad must fail." );
			$this->assertNotSame( '', $this->message( $res ), 'Errors always carry a message.' );
		}
	}

	public function test_closed_questions_refuse_answers() {
		$this->login_as();
		$res = $this->answer( [ 'post_id' => $this->make_question( [ 'comment_status' => 'closed' ] ), 'content' => '<p>x</p>' ] );
		$this->assertFalse( $res['success'] );
	}

	public function test_guest_must_give_name_and_email_when_guest_replies_are_on() {
		$this->set_settings( [ 'require_login_to_reply' => 0, 'allow_guest_replies' => 1 ] );
		wp_set_current_user( 0 );
		$q = $this->make_question();

		$missing = $this->answer( [ 'post_id' => $q, 'content' => '<p>hello</p>' ] );
		$this->assertFalse( $missing['success'] );

		$ok = $this->answer( [ 'post_id' => $q, 'content' => '<p>hello</p>', 'author' => 'Gina Guest', 'email' => 'gina@example.test' ] );
		$this->assertTrue( $ok['success'], $this->message( $ok ) );
		$comment = get_comment( $ok['data']['comment_id'] );
		$this->assertSame( 'Gina Guest', $comment->comment_author );
		$this->assertSame( 'gina@example.test', $comment->comment_author_email );
		$this->assertSame( '0', (string) $comment->user_id );
	}

	public function test_moderation_setting_holds_answers_and_says_so() {
		update_option( 'comment_moderation', 1 );
		$asker = self::factory()->user->create();
		$this->login_as( 'subscriber' ); // Core always approves a question's own author, so answer someone else's question.
		$res = $this->answer( [ 'post_id' => $this->make_question( [ 'post_author' => $asker ] ), 'content' => '<p>needs review</p>' ] );

		$this->assertTrue( $res['success'], $this->message( $res ) );
		$this->assertTrue( $res['data']['held'] );
		$this->assertStringContainsString( 'moderation', $this->message( $res ) );
		$this->assertSame( '0', (string) get_comment( $res['data']['comment_id'] )->comment_approved );
	}

	public function test_admins_bypass_moderation_and_are_published_immediately() {
		update_option( 'comment_moderation', 1 );
		$this->login_as( 'administrator' );
		$res = $this->answer( [ 'post_id' => $this->make_question(), 'content' => '<p>admin answer</p>' ] );

		$this->assertTrue( $res['success'], $this->message( $res ) );
		$this->assertFalse( $res['data']['held'] );
	}

	public function test_disallowed_words_are_honoured() {
		update_option( 'disallowed_keys', 'forbiddenword' );
		$asker = self::factory()->user->create();
		$this->login_as( 'subscriber' );
		$res = $this->answer( [ 'post_id' => $this->make_question( [ 'post_author' => $asker ] ), 'content' => '<p>contains forbiddenword here</p>' ] );

		$published = $res['success'] && '1' === (string) get_comment( $res['data']['comment_id'] )->comment_approved;
		$this->assertFalse( $published, 'A comment containing a disallowed word must never be published.' );
	}

	public function test_duplicate_answers_are_refused() {
		$this->login_as( 'subscriber' );
		$q = $this->make_question();
		$first = $this->answer( [ 'post_id' => $q, 'content' => '<p>Same words</p>' ] );
		$dupe  = $this->answer( [ 'post_id' => $q, 'content' => '<p>Same words</p>' ] );

		$this->assertTrue( $first['success'] );
		$this->assertFalse( $dupe['success'], 'Core duplicate-comment detection applies.' );
	}

	public function test_flood_control_applies() {
		remove_filter( 'comment_flood_filter', '__return_false' );
		$this->login_as( 'subscriber' );
		$q = $this->make_question();
		$_SERVER['REMOTE_ADDR'] = '198.51.100.7';

		$first = $this->answer( [ 'post_id' => $q, 'content' => '<p>first answer</p>' ] );
		$second = $this->answer( [ 'post_id' => $q, 'content' => '<p>a different second answer</p>' ] );

		$this->assertTrue( $first['success'] );
		$this->assertFalse( $second['success'], 'Posting twice immediately must hit core flood control.' );
	}

	public function test_hooks_fire_around_submission() {
		$fired = [];
		add_action( 'askora_before_answer_form', static function () use ( &$fired ) { $fired[] = 'before'; } );
		add_action( 'askora_after_answer_form', static function () use ( &$fired ) { $fired[] = 'after'; } );
		$this->login_as();
		$this->answer( [ 'post_id' => $this->make_question(), 'content' => '<p>x</p>' ] );

		$this->assertSame( [ 'before', 'after' ], $fired );
	}
}
