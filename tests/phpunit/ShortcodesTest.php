<?php
/**
 * @package ASKORA
 */

class ShortcodesTest extends AskoraTestCase {

	public function set_up() {
		parent::set_up();
		$this->set_settings( [ 'questions_per_page' => 10, 'enable_phone_auth' => 1, 'require_login_to_ask' => 1 ] );
	}

	public function test_all_documented_shortcodes_are_registered() {
		foreach ( [ 'askora_submit_form', 'askora_questions', 'askora_search', 'askora_login', 'askora_register', 'askora_auth', 'askora_popular_questions', 'askora_unanswered_questions', 'askora_dashboard' ] as $tag ) {
			$this->assertTrue( shortcode_exists( $tag ), "[$tag] is not registered." );
		}
	}

	public function test_tag_attribute_filters_the_first_page_of_results() {
		$tagged   = $this->make_question( [ 'post_title' => 'Tagged question' ] );
		$untagged = $this->make_question( [ 'post_title' => 'Untagged question' ] );
		wp_set_object_terms( $tagged, [ 'php' ], 'question_tag' );

		$html = do_shortcode( '[askora_questions tag="php"]' );

		$this->assertStringContainsString( 'Tagged question', $html );
		$this->assertStringNotContainsString( 'Untagged question', $html );
		$this->assertStringContainsString( 'data-tag="php"', $html, 'Load More / reload need the tag on the wrapper.' );
		unset( $untagged );
	}

	public function test_unanswered_shortcode_filters_and_flags_the_wrapper_for_ajax() {
		$answered   = $this->make_question( [ 'post_title' => 'Has an answer' ] );
		$unanswered = $this->make_question( [ 'post_title' => 'Nobody answered' ] );
		$this->make_answer( $answered );

		$html = do_shortcode( '[askora_unanswered_questions]' );

		$this->assertStringContainsString( 'Nobody answered', $html );
		$this->assertStringNotContainsString( 'Has an answer', $html );
		$this->assertStringContainsString( 'data-unanswered="1"', $html, 'Without this flag "Load More" drops the filter.' );
		unset( $unanswered );
	}

	public function test_regular_list_is_not_flagged_unanswered() {
		$this->make_question();
		$this->assertStringContainsString( 'data-unanswered="0"', do_shortcode( '[askora_questions]' ) );
	}

	public function test_popular_shortcode_orders_by_votes_and_includes_unvoted_questions() {
		$low  = $this->make_question( [ 'post_title' => 'Low votes' ] );
		$top  = $this->make_question( [ 'post_title' => 'Top votes' ] );
		$none = $this->make_question( [ 'post_title' => 'No counter row' ] );
		update_post_meta( $low, '_askora_votes', 1 );
		update_post_meta( $top, '_askora_votes', 9 );

		$html = do_shortcode( '[askora_popular_questions]' );

		$this->assertGreaterThan( strpos( $html, 'Top votes' ), strpos( $html, 'Low votes' ) );
		$this->assertStringContainsString( 'No counter row', $html );
		unset( $none );
	}

	public function test_orderby_attribute_rejects_unknown_values() {
		$this->make_question();
		$html = do_shortcode( '[askora_questions orderby="post_author; DROP TABLE"]' );
		$this->assertStringContainsString( 'data-orderby="date"', $html );
	}

	public function test_sort_dropdown_offers_most_voted() {
		$this->make_question();
		$this->assertStringContainsString( 'value="votes"', do_shortcode( '[askora_questions]' ) );
	}

	public function test_submit_form_shows_the_auth_prompt_to_guests_and_the_editor_to_members() {
		wp_set_current_user( 0 );
		$this->assertStringContainsString( 'askora-auth-prompt', do_shortcode( '[askora_submit_form]' ) );

		$this->login_as();
		$html = do_shortcode( '[askora_submit_form]' );
		$this->assertStringContainsString( 'askora-submit-form', $html );
		$this->assertStringContainsString( 'wp-editor-wrap', $html );
	}

	public function test_submit_form_is_open_to_guests_when_login_is_not_required() {
		wp_set_current_user( 0 );
		$this->set_settings( [ 'require_login_to_ask' => 0 ] );
		$this->assertStringContainsString( 'askora-submit-form', do_shortcode( '[askora_submit_form]' ) );
	}

	public function test_submit_form_uses_a_textarea_when_the_rich_editor_is_off() {
		$this->login_as();
		$this->set_settings( [ 'enable_rich_editor' => 0 ] );
		$html = do_shortcode( '[askora_submit_form]' );
		$this->assertStringNotContainsString( 'wp-editor-wrap', $html );
		$this->assertStringContainsString( '<textarea', $html );
	}

	public function test_auth_forms_show_a_notice_when_phone_auth_is_disabled() {
		wp_set_current_user( 0 );
		$this->set_settings( [ 'enable_phone_auth' => 0 ] );

		foreach ( [ 'askora_login', 'askora_register', 'askora_auth' ] as $tag ) {
			$html = do_shortcode( "[$tag]" );
			$this->assertStringContainsString( 'currently disabled', $html );
			$this->assertStringNotContainsString( '<form', $html );
		}
	}

	public function test_register_form_contains_the_honeypot_field() {
		wp_set_current_user( 0 );
		foreach ( [ 'askora_register', 'askora_auth' ] as $tag ) {
			$this->assertStringContainsString( 'name="askora_hp"', do_shortcode( "[$tag]" ), "[$tag] needs the honeypot." );
		}
	}

	public function test_login_prompt_links_to_the_plugins_own_auth_page_when_published() {
		$page = self::factory()->post->create( [ 'post_type' => 'page', 'post_status' => 'publish', 'post_content' => '[askora_auth]' ] );
		update_option( 'askora_pages', [ 'auth' => $page ] );
		wp_set_current_user( 0 );

		$html = do_shortcode( '[askora_submit_form]' );

		$this->assertStringContainsString( esc_url( get_permalink( $page ) ), $html );
		$this->assertStringNotContainsString( 'wp-login.php', $html );
	}

	public function test_login_prompt_falls_back_to_wp_login_when_the_auth_page_is_a_draft() {
		$page = self::factory()->post->create( [ 'post_type' => 'page', 'post_status' => 'draft' ] );
		update_option( 'askora_pages', [ 'auth' => $page ] );
		wp_set_current_user( 0 );

		$this->assertStringContainsString( 'wp-login.php', do_shortcode( '[askora_submit_form]' ) );
	}
}
