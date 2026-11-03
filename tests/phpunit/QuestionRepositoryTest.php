<?php
/**
 * @package ASKORA
 */

use ASKORA\Frontend\Inc\Questions\QuestionRepository;

class QuestionRepositoryTest extends AskoraTestCase {

	public function test_backslashes_in_title_and_content_are_preserved() {
		$id = ( new QuestionRepository() )->create_question(
			[
				'title'     => 'Path C:\\Temp',
				'content'   => '<p>Open C:\\Users\\bob and "quote"</p>',
				'author_id' => $this->login_as( 'subscriber' ),
			]
		);

		$post = get_post( $id );
		$this->assertSame( 'Path C:\\Temp', $post->post_title );
		$this->assertStringContainsString( 'C:\\Users\\bob', $post->post_content );
	}

	public function test_new_questions_accept_answers_and_start_with_counters() {
		update_option( 'default_comment_status', 'closed' );
		$id = ( new QuestionRepository() )->create_question( [ 'title' => 'Q', 'content' => '', 'author_id' => $this->login_as() ] );

		$this->assertSame( 'open', get_post( $id )->comment_status, 'Answers must not inherit a closed site default.' );
		$this->assertSame( '0', (string) get_post_meta( $id, '_askora_views', true ) );
		$this->assertSame( '0', (string) get_post_meta( $id, '_askora_votes', true ) );
	}

	public function test_status_follows_the_setting() {
		$this->set_settings( [ 'question_status' => 'draft' ] );
		$id = ( new QuestionRepository() )->create_question( [ 'title' => 'Q', 'content' => '', 'author_id' => $this->login_as() ] );
		$this->assertSame( 'draft', get_post_status( $id ) );
	}

	public function test_only_existing_categories_are_assigned() {
		$cat = self::factory()->term->create( [ 'taxonomy' => 'question_category' ] );
		$id  = ( new QuestionRepository() )->create_question(
			[
				'title'      => 'Q',
				'content'    => '',
				'author_id'  => $this->login_as(),
				'categories' => [ $cat, 987654, 0 ],
			]
		);

		$this->assertSame( [ $cat ], wp_get_object_terms( $id, 'question_category', [ 'fields' => 'ids' ] ) );
	}

	public function test_tags_are_deduplicated_blank_filtered_and_capped() {
		$id = ( new QuestionRepository() )->create_question(
			[
				'title'     => 'Q',
				'content'   => '',
				'author_id' => $this->login_as(),
				'tags'      => 'one, two, two, , three, four, five, six, seven',
			]
		);
		$this->assertCount( 5, wp_get_object_terms( $id, 'question_tag' ) );

		add_filter( 'askora_max_tags', static fn() => 2 );
		$id2 = ( new QuestionRepository() )->create_question( [ 'title' => 'Q2', 'content' => '', 'author_id' => 1, 'tags' => 'a,b,c,d' ] );
		$this->assertCount( 2, wp_get_object_terms( $id2, 'question_tag' ) );
	}

	public function test_meta_ordering_keeps_questions_that_have_no_counter_row() {
		$with_votes = $this->make_question( [ 'post_title' => 'voted' ] );
		update_post_meta( $with_votes, '_askora_votes', 5 );
		update_post_meta( $with_votes, '_askora_views', 9 );
		$legacy = $this->make_question( [ 'post_title' => 'legacy' ] ); // No meta rows at all.

		foreach ( [ 'votes', 'meta_value_num' ] as $orderby ) {
			$args = QuestionRepository::apply_orderby(
				[ 'post_type' => 'questions', 'post_status' => 'publish', 'fields' => 'ids', 'posts_per_page' => -1 ],
				$orderby
			);
			$ids = ( new WP_Query( $args ) )->posts;

			$this->assertContains( $legacy, $ids, "orderby=$orderby must not drop questions without a counter row." );
			$this->assertLessThan( array_search( $legacy, $ids, true ), array_search( $with_votes, $ids, true ), "orderby=$orderby ranks the counted question first." );
		}
	}

	public function test_unknown_orderby_falls_back_to_date() {
		$args = QuestionRepository::apply_orderby( [], 'something_else' );
		$this->assertSame( 'date', $args['orderby'] );
		$this->assertSame( 'comment_count', QuestionRepository::apply_orderby( [], 'comment_count' )['orderby'] );
	}
}
