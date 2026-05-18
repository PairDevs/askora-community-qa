<?php
/**
 * Shortcodes registration.
 *
 * @package ASKORA\Frontend\Inc\Shortcodes
 * @since   1.0.0
 */

namespace ASKORA\Frontend\Inc\Shortcodes;

use ASKORA\Frontend\Inc\Helpers\Template;
use ASKORA\Frontend\Inc\Auth\AuthForms;
use ASKORA\Frontend\Inc\Questions\QuestionRepository;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Shortcodes {

	public function __construct() {
		add_action( 'init', [ $this, 'register' ] );
	}

	public function register(): void {
		$shortcodes = [
			'askora_submit_form'        => 'render_submit_form',
			'askora_questions'          => 'render_question_list',
			'askora_search'             => 'render_search',
			'askora_login'              => 'render_login',
			'askora_register'           => 'render_register',
			'askora_auth'               => 'render_auth',
			'askora_popular_questions'  => 'render_popular',
			'askora_unanswered_questions' => 'render_unanswered',
			'askora_dashboard'          => 'render_dashboard',
		];

		foreach ( $shortcodes as $tag => $method ) {
			add_shortcode( $tag, [ $this, $method ] );
		}
	}

	/** [askora_submit_form] */
	public function render_submit_form( $atts ): string {
		if ( ! is_user_logged_in() ) {
			return Template::get( 'auth-prompt.php' );
		}
		return Template::get( 'question-form.php' );
	}

	/** [askora_questions] */
	public function render_question_list( $atts ): string {
		$atts = shortcode_atts( [
			'category'     => '',
			'tag'          => '',
			'orderby'      => 'date',
			'per_page'     => '',
			'show_ask_btn' => 'true',
			'show_search'  => 'true',
		], $atts, 'askora_questions' );

		return Template::get( 'question-list.php', [ 'atts' => $atts ] );
	}

	/** [askora_search] */
	public function render_search( $atts ): string {
		return Template::get( 'search-form.php' );
	}

	/** [askora_login] */
	public function render_login( $atts ): string {
		if ( is_user_logged_in() ) {
			$logout_url = wp_logout_url( get_permalink() );
			return '<p class="askora-alert askora-alert-info">' . esc_html__( 'You are already logged in.', 'askora-community-qa' ) . ' <a href="' . esc_url( $logout_url ) . '">' . esc_html__( 'Log out', 'askora-community-qa' ) . '</a></p>';
		}
		$auth = new AuthForms();
		return $auth->render_login();
	}

	/** [askora_register] */
	public function render_register( $atts ): string {
		if ( is_user_logged_in() ) {
			$logout_url = wp_logout_url( get_permalink() );
			return '<p class="askora-alert askora-alert-info">' . esc_html__( 'You already have an account.', 'askora-community-qa' ) . ' <a href="' . esc_url( $logout_url ) . '">' . esc_html__( 'Log out', 'askora-community-qa' ) . '</a></p>';
		}
		$auth = new AuthForms();
		return $auth->render_register();
	}

	/** [askora_auth] */
	public function render_auth( $atts ): string {
		if ( is_user_logged_in() ) {
			$logout_url = wp_logout_url( get_permalink() );
			return '<p class="askora-alert askora-alert-info">' . esc_html__( 'You are already logged in.', 'askora-community-qa' ) . ' <a href="' . esc_url( $logout_url ) . '">' . esc_html__( 'Log out', 'askora-community-qa' ) . '</a></p>';
		}
		$auth = new AuthForms();
		return $auth->render_combined();
	}

	/** [askora_popular_questions] */
	public function render_popular( $atts ): string {
		return Template::get( 'question-list.php', [
			'atts' => [ 'orderby' => 'meta_value_num', 'category' => '', 'tag' => '', 'per_page' => 5 ],
		] );
	}

	/** [askora_unanswered_questions] */
	public function render_unanswered( $atts ): string {
		return Template::get( 'question-list.php', [
			'atts' => [ 'unanswered' => true, 'category' => '', 'tag' => '', 'orderby' => 'date', 'per_page' => '' ],
		] );
	}

	/** [askora_dashboard] */
	public function render_dashboard( $atts ): string {
		if ( ! is_user_logged_in() ) {
			return Template::get( 'auth-prompt.php' );
		}
		return Template::get( 'dashboard.php' );
	}
}
