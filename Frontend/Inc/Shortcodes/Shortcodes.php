<?php
/**
 * Shortcodes registration.
 *
 * @package QuestionHub\Frontend\Inc\Shortcodes
 * @since   1.0.0
 */

namespace QuestionHub\Frontend\Inc\Shortcodes;

use QuestionHub\Frontend\Inc\Helpers\Template;
use QuestionHub\Frontend\Inc\Auth\AuthForms;
use QuestionHub\Frontend\Inc\Questions\QuestionRepository;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Shortcodes {

	public function __construct() {
		add_action( 'init', [ $this, 'register' ] );
	}

	public function register(): void {
		$shortcodes = [
			'questionhub_submit_form'        => 'render_submit_form',
			'questionhub_questions'          => 'render_question_list',
			'questionhub_search'             => 'render_search',
			'questionhub_login'              => 'render_login',
			'questionhub_register'           => 'render_register',
			'questionhub_auth'               => 'render_auth',
			'questionhub_popular_questions'  => 'render_popular',
			'questionhub_unanswered_questions' => 'render_unanswered',
			'questionhub_dashboard'          => 'render_dashboard',
		];

		foreach ( $shortcodes as $tag => $method ) {
			add_shortcode( $tag, [ $this, $method ] );
		}
	}

	/** [questionhub_submit_form] */
	public function render_submit_form( $atts ): string {
		if ( ! is_user_logged_in() ) {
			return Template::get( 'auth-prompt.php' );
		}
		return Template::get( 'question-form.php' );
	}

	/** [questionhub_questions] */
	public function render_question_list( $atts ): string {
		$atts = shortcode_atts( [
			'category' => '',
			'tag'      => '',
			'orderby'  => 'date',
			'per_page' => '',
		], $atts, 'questionhub_questions' );

		return Template::get( 'question-list.php', [ 'atts' => $atts ] );
	}

	/** [questionhub_search] */
	public function render_search( $atts ): string {
		return Template::get( 'search-form.php' );
	}

	/** [questionhub_login] */
	public function render_login( $atts ): string {
		if ( is_user_logged_in() ) {
			return '<p class="questionhub-alert questionhub-alert-info">' . esc_html__( 'You are already logged in.', 'questionhub' ) . '</p>';
		}
		$auth = new AuthForms();
		return $auth->render_login();
	}

	/** [questionhub_register] */
	public function render_register( $atts ): string {
		if ( is_user_logged_in() ) {
			return '<p class="questionhub-alert questionhub-alert-info">' . esc_html__( 'You already have an account.', 'questionhub' ) . '</p>';
		}
		$auth = new AuthForms();
		return $auth->render_register();
	}

	/** [questionhub_auth] */
	public function render_auth( $atts ): string {
		if ( is_user_logged_in() ) {
			return '<p class="questionhub-alert questionhub-alert-info">' . esc_html__( 'You are already logged in.', 'questionhub' ) . '</p>';
		}
		$auth = new AuthForms();
		return $auth->render_combined();
	}

	/** [questionhub_popular_questions] */
	public function render_popular( $atts ): string {
		return Template::get( 'question-list.php', [
			'atts' => [ 'orderby' => 'meta_value_num', 'category' => '', 'tag' => '', 'per_page' => 5 ],
		] );
	}

	/** [questionhub_unanswered_questions] */
	public function render_unanswered( $atts ): string {
		return Template::get( 'question-list.php', [
			'atts' => [ 'unanswered' => true, 'category' => '', 'tag' => '', 'orderby' => 'date', 'per_page' => '' ],
		] );
	}

	/** [questionhub_dashboard] */
	public function render_dashboard( $atts ): string {
		if ( ! is_user_logged_in() ) {
			return Template::get( 'auth-prompt.php' );
		}
		return Template::get( 'dashboard.php' );
	}
}
