<?php
/**
 * @package ASKORA
 */

use ASKORA\Frontend\Inc\Questions\VoteManager;

class VoteManagerTest extends AskoraTestCase {

	/** @var VoteManager */
	private $votes;

	public function set_up() {
		parent::set_up();
		$this->votes = new VoteManager();
	}

	public function test_votes_are_counted_per_user_once() {
		$q = $this->make_question();

		$this->assertSame( 1, $this->votes->vote_question( $q, 11 ) );
		$this->assertSame( 2, $this->votes->vote_question( $q, 12 ) );
		$again = $this->votes->vote_question( $q, 11 );

		$this->assertWPError( $again );
		$this->assertSame( 'already_voted', $again->get_error_code() );
		$this->assertSame( 2, (int) get_post_meta( $q, '_askora_votes', true ) );
		$this->assertTrue( $this->votes->has_voted_question( $q, 11 ) );
		$this->assertFalse( $this->votes->has_voted_question( $q, 99 ) );
	}

	public function test_cannot_vote_on_things_that_are_not_published_questions() {
		$page    = self::factory()->post->create( [ 'post_type' => 'page' ] );
		$post    = self::factory()->post->create();
		$pending = $this->make_question( [ 'post_status' => 'pending' ] );

		foreach ( [ $page, $post, $pending, 987654 ] as $bad_id ) {
			$this->assertWPError( $this->votes->vote_question( $bad_id, 1 ), "Voting on #$bad_id must be refused." );
			$this->assertSame( '', (string) get_post_meta( $bad_id, '_askora_voted_users', true ) );
		}
	}

	public function test_answer_votes_need_an_approved_comment_on_a_published_question() {
		$q        = $this->make_question();
		$ok       = $this->make_answer( $q );
		$held     = $this->make_answer( $q, [ 'comment_approved' => 0 ] );
		$on_page  = $this->make_answer( self::factory()->post->create( [ 'post_type' => 'page' ] ) );
		$on_draft = $this->make_answer( $this->make_question( [ 'post_status' => 'pending' ] ) );

		$this->assertSame( 1, $this->votes->vote_answer( $ok, 1 ) );
		$this->assertWPError( $this->votes->vote_answer( $ok, 1 ), 'Second vote by the same user.' );
		foreach ( [ $held, $on_page, $on_draft, 987654 ] as $bad_id ) {
			$this->assertWPError( $this->votes->vote_answer( $bad_id, 1 ), "Voting on comment #$bad_id must be refused." );
		}
	}

	public function test_total_is_derived_from_the_voter_list() {
		$q = $this->make_question();
		update_post_meta( $q, '_askora_votes', 40 ); // Drifted counter.
		update_post_meta( $q, '_askora_voted_users', [ 1, 2 ] );

		$this->assertSame( 3, $this->votes->vote_question( $q, 3 ) );
	}
}
