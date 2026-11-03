<?php
/**
 * @group ajax
 * @package ASKORA
 */

class SubmitQuestionAjaxTest extends AskoraAjaxTestCase {

	public function test_guests_are_refused_when_login_is_required() {
		wp_set_current_user( 0 );
		$res = $this->ajax( 'askora_submit_question', [ 'title' => 'Guest question' ] );

		$this->assertFalse( $res['success'] );
		$this->assertStringContainsString( 'logged in', $this->message( $res ) );
	}

	public function test_guests_can_ask_when_login_is_not_required() {
		$this->set_settings( [ 'require_login_to_ask' => 0 ] );
		wp_set_current_user( 0 );
		$res = $this->ajax( 'askora_submit_question', [ 'title' => 'Guest question', 'content' => '<p>C:\\temp</p>' ] );

		$this->assertTrue( $res['success'], $this->message( $res ) );
		$post = get_post( $res['data']['post_id'] );
		$this->assertSame( 'questions', $post->post_type );
		$this->assertSame( '0', (string) $post->post_author );
		$this->assertSame( 'pending', $post->post_status, 'Guest questions still honour the default status.' );
		$this->assertStringContainsString( 'C:\\temp', $post->post_content );
	}

	public function test_member_question_with_category_and_tags() {
		$user = $this->login_as( 'subscriber' );
		$cat  = self::factory()->term->create( [ 'taxonomy' => 'question_category' ] );

		$res = $this->ajax(
			'askora_submit_question',
			[
				'title'      => 'How do I <b>test</b>?',
				'content'    => '<p>Details</p><script>alert(1)</script>',
				'categories' => [ (string) $cat ],
				'tags'       => 'php, wordpress',
			]
		);

		$this->assertTrue( $res['success'], $this->message( $res ) );
		$post = get_post( $res['data']['post_id'] );
		$this->assertSame( $user, (int) $post->post_author );
		$this->assertSame( 'How do I test?', $post->post_title, 'Titles are plain text.' );
		$this->assertStringNotContainsString( '<script', $post->post_content );
		$this->assertSame( [ $cat ], wp_get_object_terms( $post->ID, 'question_category', [ 'fields' => 'ids' ] ) );
		$this->assertEqualSets( [ 'php', 'wordpress' ], wp_list_pluck( wp_get_object_terms( $post->ID, 'question_tag' ), 'name' ) );
	}

	public function test_title_is_required() {
		$this->login_as();
		$res = $this->ajax( 'askora_submit_question', [ 'title' => '   ', 'content' => 'x' ] );

		$this->assertFalse( $res['success'] );
		$this->assertStringContainsString( 'required', $this->message( $res ) );
	}

	public function test_empty_category_selection_is_harmless() {
		$this->login_as();
		$res = $this->ajax( 'askora_submit_question', [ 'title' => 'No category', 'categories' => [ '' ] ] );

		$this->assertTrue( $res['success'], $this->message( $res ) );
		$this->assertSame( [], wp_get_object_terms( $res['data']['post_id'], 'question_category' ) );
	}
}
