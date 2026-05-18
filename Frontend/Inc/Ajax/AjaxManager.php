<?php
/**
 * AJAX manager — registers all wp_ajax_* actions.
 *
 * @package ASKORA\Frontend\Inc\Ajax
 * @since   1.0.0
 */

namespace ASKORA\Frontend\Inc\Ajax;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AjaxManager {

	protected $submit_question;
	protected $submit_answer;
	protected $search;
	protected $load_questions;
	protected $vote;
	protected $best_answer;
	protected $phone_login;
	protected $phone_register;
	protected $verify_answer;

	public function __construct() {
		$this->submit_question = new SubmitQuestion();
		$this->submit_answer   = new SubmitAnswer();
		$this->search          = new SearchQuestions();
		$this->load_questions  = new LoadQuestions();
		$this->vote            = new Vote();
		$this->best_answer     = new BestAnswer();
		$this->phone_login     = new PhoneLogin();
		$this->phone_register  = new PhoneRegister();
		$this->verify_answer   = new VerifyAnswer();
	}
}
