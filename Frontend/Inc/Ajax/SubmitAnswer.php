<?php
/**
 * AJAX: Submit Answer.
 *
 * @package ASKORA\Frontend\Inc\Ajax
 * @since   1.0.0
 */

namespace ASKORA\Frontend\Inc\Ajax;

use ASKORA\Frontend\Inc\Helpers\Permission;
use ASKORA\Frontend\Inc\Helpers\Response;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SubmitAnswer {

	public function __construct() {
		add_action( 'wp_ajax_askora_submit_answer',        [ $this, 'handle' ] );
		add_action( 'wp_ajax_nopriv_askora_submit_answer', [ $this, 'handle' ] );
	}

	/**
	 * Widen the comment kses context to the post-content tag set.
	 *
	 * @param array  $tags    Allowed tags for the context.
	 * @param string $context kses context.
	 * @return array
	 */
	public function allow_answer_html( $tags, $context ) {
		if ( 'pre_comment_content' === $context ) {
			return wp_kses_allowed_html( 'post' );
		}
		return $tags;
	}

	public function handle(): void {
		check_ajax_referer( 'askora_nonce', 'nonce' );

		if ( ! Permission::can_reply() ) {
			Response::die_error( __( 'You must be logged in to submit an answer.', 'askora-community-qa' ) );
		}

		$post_id = isset( $_POST['post_id'] ) ? absint( wp_unslash( $_POST['post_id'] ) ) : 0;
		$content = isset( $_POST['content'] ) ? wp_unslash( $_POST['content'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized by the core comment pipeline (kses) in wp_handle_comment_submission().

		// An empty rich-text editor submits markup such as "<p></p>", so test the visible text.
		if ( ! $post_id || '' === trim( wp_strip_all_tags( $content ) ) ) {
			Response::die_error( __( 'Answer content is required.', 'askora-community-qa' ) );
		}

		$post = get_post( $post_id );
		if ( ! $post || 'questions' !== $post->post_type ) {
			Response::die_error( __( 'Invalid question.', 'askora-community-qa' ) );
		}

		do_action( 'askora_before_answer_form' );

		// Hand off to core so answers get the same protections as normal comments:
		// post status/password checks, comments_open(), flood control, duplicate
		// detection, required guest name/email, moderation, and IP/user-agent capture.
		// Core limits comment HTML to a handful of inline tags for people without the
		// unfiltered_html capability. Answers are written in a rich-text editor, so allow
		// the same safe set as post content (paragraphs, lists, quotes, code) for this
		// one call only. Everything is still run through kses.
		add_filter( 'wp_kses_allowed_html', [ $this, 'allow_answer_html' ], 10, 2 );

		$comment = wp_handle_comment_submission(
			[
				'comment_post_ID' => $post_id,
				'comment'         => $content,
				'author'          => isset( $_POST['author'] ) ? sanitize_text_field( wp_unslash( $_POST['author'] ) ) : '',
				'email'           => isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '',
				'url'             => '',
				'comment_parent'  => 0,
			]
		);

		remove_filter( 'wp_kses_allowed_html', [ $this, 'allow_answer_html' ], 10 );

		do_action( 'askora_after_answer_form' );

		if ( is_wp_error( $comment ) ) {
			// Some core errors (e.g. answering a draft question) carry no message.
			$message = wp_strip_all_tags( $comment->get_error_message() );
			Response::die_error( '' !== $message ? $message : __( 'Answers are not open for this question.', 'askora-community-qa' ) );
		}

		if ( 'spam' === $comment->comment_approved ) {
			Response::die_error( __( 'Your answer could not be submitted.', 'askora-community-qa' ) );
		}

		$is_held = '1' !== (string) $comment->comment_approved;

		Response::success(
			$is_held
				? __( 'Your answer has been submitted and is awaiting moderation.', 'askora-community-qa' )
				: __( 'Your answer has been submitted.', 'askora-community-qa' ),
			[
				'comment_id' => (int) $comment->comment_ID,
				'held'       => $is_held,
			]
		);
	}
}
