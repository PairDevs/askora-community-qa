<?php
/**
 * Voting, best answer, verification, list loading and search.
 *
 * @group ajax
 * @package ASKORA
 */

class InteractionAjaxTest extends AskoraAjaxTestCase {

	// ── Vote ───────────────────────────────────────────────────────────────

	public function test_voting_requires_login() {
		wp_set_current_user( 0 );
		$res = $this->ajax( 'askora_vote', [ 'type' => 'question', 'id' => $this->make_question() ] );
		$this->assertFalse( $res['success'] );
	}

	public function test_question_vote_counts_and_cannot_be_repeated() {
		$this->login_as();
		$q = $this->make_question();

		$first = $this->ajax( 'askora_vote', [ 'type' => 'question', 'id' => $q ] );
		$again = $this->ajax( 'askora_vote', [ 'type' => 'question', 'id' => $q ] );

		$this->assertTrue( $first['success'] );
		$this->assertSame( 1, $first['data']['votes'] );
		$this->assertFalse( $again['success'] );
		$this->assertSame( 1, (int) get_post_meta( $q, '_askora_votes', true ) );
	}

	public function test_answer_vote_works() {
		$this->login_as();
		$res = $this->ajax( 'askora_vote', [ 'type' => 'answer', 'id' => $this->make_answer( $this->make_question() ) ] );
		$this->assertTrue( $res['success'] );
		$this->assertSame( 1, $res['data']['votes'] );
	}

	public function test_votes_on_unrelated_posts_and_comments_are_refused_and_leave_no_meta() {
		$this->login_as();
		$post    = self::factory()->post->create();
		$comment = $this->make_answer( $post );

		$this->assertFalse( $this->ajax( 'askora_vote', [ 'type' => 'question', 'id' => $post ] )['success'] );
		$this->assertFalse( $this->ajax( 'askora_vote', [ 'type' => 'answer', 'id' => $comment ] )['success'] );
		$this->assertSame( '', (string) get_post_meta( $post, '_askora_voted_users', true ) );
		$this->assertSame( '', (string) get_comment_meta( $comment, '_askora_answer_voted_users', true ) );
	}

	public function test_unknown_vote_type_is_refused_instead_of_treated_as_an_answer() {
		$this->login_as();
		$q       = $this->make_question();
		$comment = $this->make_answer( $q );

		$res = $this->ajax( 'askora_vote', [ 'type' => 'banana', 'id' => $comment ] );
		$this->assertFalse( $res['success'] );
		$this->assertSame( '', (string) get_comment_meta( $comment, '_askora_answer_votes', true ) );
	}

	public function test_voting_switch_is_enforced_by_the_server() {
		$this->set_settings( [ 'enable_voting' => 0 ] );
		$this->login_as();
		$q = $this->make_question();

		$res = $this->ajax( 'askora_vote', [ 'type' => 'question', 'id' => $q ] );
		$this->assertFalse( $res['success'] );
		$this->assertSame( '0', (string) ( get_post_meta( $q, '_askora_votes', true ) ?: 0 ) );
	}

	// ── Best answer ────────────────────────────────────────────────────────

	public function test_question_author_can_accept_an_answer_and_it_replaces_the_previous_one() {
		$author = $this->login_as();
		$q      = $this->make_question( [ 'post_author' => $author ] );
		$a1     = $this->make_answer( $q );
		$a2     = $this->make_answer( $q );

		$this->assertTrue( $this->ajax( 'askora_best_answer', [ 'post_id' => $q, 'comment_id' => $a1 ] )['success'] );
		$this->assertTrue( $this->ajax( 'askora_best_answer', [ 'post_id' => $q, 'comment_id' => $a2 ] )['success'] );

		$this->assertSame( $a2, (int) get_post_meta( $q, '_askora_best_answer', true ) );
		$this->assertSame( '', (string) get_comment_meta( $a1, '_askora_is_best_answer', true ) );
		$this->assertSame( '1', (string) get_comment_meta( $a2, '_askora_is_best_answer', true ) );
	}

	public function test_other_members_cannot_accept_answers() {
		$q = $this->make_question( [ 'post_author' => self::factory()->user->create() ] );
		$a = $this->make_answer( $q );
		$this->login_as( 'subscriber' );

		$this->assertFalse( $this->ajax( 'askora_best_answer', [ 'post_id' => $q, 'comment_id' => $a ] )['success'] );
		$this->assertSame( '', (string) get_post_meta( $q, '_askora_best_answer', true ) );
	}

	public function test_an_answer_from_another_question_cannot_be_accepted() {
		$admin = $this->login_as( 'administrator' );
		$q     = $this->make_question( [ 'post_author' => $admin ] );
		$other = $this->make_answer( $this->make_question() );

		$res = $this->ajax( 'askora_best_answer', [ 'post_id' => $q, 'comment_id' => $other ] );
		$this->assertFalse( $res['success'] );
		$this->assertSame( '', (string) get_comment_meta( $other, '_askora_is_best_answer', true ) );
	}

	public function test_unapproved_and_missing_answers_cannot_be_accepted() {
		$admin = $this->login_as( 'administrator' );
		$q     = $this->make_question( [ 'post_author' => $admin ] );
		$held  = $this->make_answer( $q, [ 'comment_approved' => 0 ] );

		$this->assertFalse( $this->ajax( 'askora_best_answer', [ 'post_id' => $q, 'comment_id' => $held ] )['success'] );
		$this->assertFalse( $this->ajax( 'askora_best_answer', [ 'post_id' => $q, 'comment_id' => 987654 ] )['success'] );
		$this->assertFalse( $this->ajax( 'askora_best_answer', [ 'post_id' => $q, 'comment_id' => 0 ] )['success'] );
	}

	public function test_best_answer_cannot_be_set_on_a_regular_post() {
		$admin   = $this->login_as( 'administrator' );
		$post    = self::factory()->post->create( [ 'post_author' => $admin ] );
		$comment = $this->make_answer( $post );

		$this->assertFalse( $this->ajax( 'askora_best_answer', [ 'post_id' => $post, 'comment_id' => $comment ] )['success'] );
		$this->assertSame( '', (string) get_post_meta( $post, '_askora_best_answer', true ) );
	}

	public function test_best_answer_switch_is_enforced_by_the_server() {
		$this->set_settings( [ 'enable_best_answer' => 0 ] );
		$admin = $this->login_as( 'administrator' );
		$q     = $this->make_question( [ 'post_author' => $admin ] );
		$a     = $this->make_answer( $q );

		$this->assertFalse( $this->ajax( 'askora_best_answer', [ 'post_id' => $q, 'comment_id' => $a ] )['success'] );
	}

	// ── Verify ─────────────────────────────────────────────────────────────

	public function test_only_admins_can_verify_answers() {
		$a = $this->make_answer( $this->make_question() );

		$this->login_as( 'editor' );
		$this->assertFalse( $this->ajax( 'askora_verify_answer', [ 'comment_id' => $a ] )['success'] );

		$this->login_as( 'administrator' );
		$this->assertTrue( $this->ajax( 'askora_verify_answer', [ 'comment_id' => $a ] )['success'] );
	}

	public function test_verify_toggles_on_and_off_and_records_who_and_when() {
		$admin = $this->login_as( 'administrator' );
		$a     = $this->make_answer( $this->make_question() );

		$on = $this->ajax( 'askora_verify_answer', [ 'comment_id' => $a ] );
		$this->assertTrue( $on['data']['verified'] );
		$this->assertSame( $admin, (int) get_comment_meta( $a, '_askora_verified_by', true ) );
		$this->assertNotSame( '', (string) get_comment_meta( $a, '_askora_verified_at', true ) );

		$off = $this->ajax( 'askora_verify_answer', [ 'comment_id' => $a ] );
		$this->assertFalse( $off['data']['verified'] );
		$this->assertSame( '', (string) get_comment_meta( $a, '_askora_verified', true ) );
	}

	public function test_verify_refuses_comments_that_are_not_answers_to_questions() {
		$this->login_as( 'administrator' );
		$blog_comment = $this->make_answer( self::factory()->post->create() );

		$this->assertFalse( $this->ajax( 'askora_verify_answer', [ 'comment_id' => $blog_comment ] )['success'] );
		$this->assertSame( '', (string) get_comment_meta( $blog_comment, '_askora_verified', true ) );
		$this->assertFalse( $this->ajax( 'askora_verify_answer', [ 'comment_id' => 987654 ] )['success'] );
	}

	// ── Lists and search ───────────────────────────────────────────────────

	public function test_load_questions_paginates_and_reports_more() {
		$this->set_settings( [ 'questions_per_page' => 2 ] );
		for ( $i = 1; $i <= 3; $i++ ) {
			$this->make_question( [ 'post_title' => "Question $i" ] );
		}
		wp_set_current_user( 0 );

		$page1 = $this->ajax( 'askora_load_questions', [ 'page' => 1 ] );
		$page2 = $this->ajax( 'askora_load_questions', [ 'page' => 2 ] );

		$this->assertTrue( $page1['data']['has_more'] );
		$this->assertFalse( $page2['data']['has_more'] );
		$this->assertSame( 3, $page1['data']['total'] );
		$this->assertSame( 1, substr_count( $page2['data']['html'], 'askora-question-card"' ) );
	}

	public function test_load_questions_applies_tag_category_and_unanswered_filters() {
		$cat      = self::factory()->term->create( [ 'taxonomy' => 'question_category' ] );
		$tagged   = $this->make_question( [ 'post_title' => 'Tagged one' ] );
		$answered = $this->make_question( [ 'post_title' => 'Answered one' ] );
		$other    = $this->make_question( [ 'post_title' => 'Other one' ] );
		wp_set_object_terms( $tagged, [ 'php' ], 'question_tag' );
		wp_set_object_terms( $tagged, [ $cat ], 'question_category' );
		$this->make_answer( $answered );
		wp_set_current_user( 0 );

		$by_tag = $this->ajax( 'askora_load_questions', [ 'tag' => 'php' ] )['data']['html'];
		$this->assertStringContainsString( 'Tagged one', $by_tag );
		$this->assertStringNotContainsString( 'Other one', $by_tag );

		$by_cat = $this->ajax( 'askora_load_questions', [ 'category' => $cat ] )['data']['html'];
		$this->assertStringContainsString( 'Tagged one', $by_cat );
		$this->assertStringNotContainsString( 'Other one', $by_cat );

		$both = $this->ajax( 'askora_load_questions', [ 'category' => $cat, 'tag' => 'php' ] )['data']['html'];
		$this->assertStringContainsString( 'Tagged one', $both );

		$unanswered = $this->ajax( 'askora_load_questions', [ 'unanswered' => 1 ] )['data']['html'];
		$this->assertStringNotContainsString( 'Answered one', $unanswered );
		$this->assertStringContainsString( 'Other one', $unanswered );
	}

	public function test_load_questions_ordering_includes_questions_without_counters_and_ignores_bad_values() {
		$top    = $this->make_question( [ 'post_title' => 'Top voted' ] );
		$legacy = $this->make_question( [ 'post_title' => 'No counters' ] );
		update_post_meta( $top, '_askora_votes', 4 );
		wp_set_current_user( 0 );

		foreach ( [ 'votes', 'meta_value_num', 'comment_count', 'date', "date; DROP TABLE wp_posts" ] as $orderby ) {
			$html = $this->ajax( 'askora_load_questions', [ 'orderby' => $orderby ] )['data']['html'];
			$this->assertStringContainsString( 'No counters', $html, "orderby=$orderby must list every published question." );
		}

		$by_votes = $this->ajax( 'askora_load_questions', [ 'orderby' => 'votes' ] )['data']['html'];
		$this->assertLessThan( strpos( $by_votes, 'No counters' ), strpos( $by_votes, 'Top voted' ) );
		unset( $legacy );
	}

	public function test_lists_only_show_published_questions() {
		$this->make_question( [ 'post_title' => 'Visible one' ] );
		$this->make_question( [ 'post_title' => 'Pending one', 'post_status' => 'pending' ] );
		$this->make_question( [ 'post_title' => 'Private one', 'post_status' => 'private' ] );
		wp_set_current_user( 0 );

		foreach ( [ 'askora_load_questions' => [], 'askora_search_questions' => [ 'keyword' => 'one' ] ] as $action => $post ) {
			$html = $this->ajax( $action, $post )['data']['html'];
			$this->assertStringContainsString( 'Visible one', $html, $action );
			$this->assertStringNotContainsString( 'Pending one', $html, $action );
			$this->assertStringNotContainsString( 'Private one', $html, $action );
		}
	}

	public function test_search_finds_matches_and_respects_the_category() {
		$cat   = self::factory()->term->create( [ 'taxonomy' => 'question_category' ] );
		$match = $this->make_question( [ 'post_title' => 'Gutenberg block question' ] );
		$this->make_question( [ 'post_title' => 'Gutenberg elsewhere' ] );
		wp_set_object_terms( $match, [ $cat ], 'question_category' );
		wp_set_current_user( 0 );

		$all = $this->ajax( 'askora_search_questions', [ 'keyword' => 'Gutenberg' ] )['data'];
		$this->assertSame( 2, $all['total'] );

		$scoped = $this->ajax( 'askora_search_questions', [ 'keyword' => 'Gutenberg', 'category' => $cat ] )['data'];
		$this->assertSame( 1, $scoped['total'] );
		$this->assertStringContainsString( 'block question', $scoped['html'] );
	}

	public function test_question_cards_escape_titles() {
		$this->make_question( [ 'post_title' => 'Tom & "Jerry" <i>rule</i>' ] );
		wp_set_current_user( 0 );

		$html = $this->ajax( 'askora_load_questions' )['data']['html'];
		$this->assertStringNotContainsString( '<i>rule</i>', $html );
	}
}
