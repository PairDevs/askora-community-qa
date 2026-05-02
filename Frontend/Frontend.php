<?php
/**
 * Frontend orchestrator — mirrors PrimeKit's Frontend.php exactly.
 *
 * @package QuestionHub\Frontend
 * @since   1.0.0
 */

namespace QuestionHub\Frontend;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use QuestionHub\Frontend\Inc\Assets\Assets;
use QuestionHub\Frontend\Inc\Shortcodes\Shortcodes;
use QuestionHub\Frontend\Inc\Ajax\AjaxManager;
use QuestionHub\Frontend\Inc\Auth\AuthForms;
use QuestionHub\Frontend\Inc\Questions\QuestionService;
use QuestionHub\Frontend\Inc\Questions\ViewCounter;
use QuestionHub\Frontend\Inc\Questions\VoteManager;
use QuestionHub\Frontend\Inc\Comments\AnswerRenderer;
use QuestionHub\Frontend\Inc\Archive\QuestionArchive;

/**
 * Class Frontend
 *
 * Instantiates all frontend sub-classes in initialize().
 */
class Frontend {

	protected $assets;
	protected $shortcodes;
	protected $ajax;
	protected $auth;
	protected $question_service;
	protected $view_counter;
	protected $vote_manager;
	protected $answer_renderer;
	protected $archive;

	public function __construct() {
		$this->initialize();
	}

	public function initialize(): void {
		$this->assets           = new Assets();
		$this->shortcodes       = new Shortcodes();
		$this->ajax             = new AjaxManager();
		$this->auth             = new AuthForms();
		$this->question_service = new QuestionService();
		$this->view_counter     = new ViewCounter();
		$this->vote_manager     = new VoteManager();
		$this->answer_renderer  = new AnswerRenderer();
		$this->archive          = new QuestionArchive();
	}
}
