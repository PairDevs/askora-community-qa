<?php
/**
 * Admin orchestrator — mirrors PrimeKit's AdminManager.php exactly.
 *
 * @package QuestionHub\Admin
 * @since   1.0.0
 */

namespace QuestionHub\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use QuestionHub\Admin\Assets\Assets;
use QuestionHub\Admin\Inc\Dashboard\Menu\QuestionHub as MenuPage;
use QuestionHub\Admin\Inc\Dashboard\Settings\Settings;
use QuestionHub\Admin\Inc\Hooks\ActionHooks;
use QuestionHub\Admin\Inc\Hooks\FilterHooks;
use QuestionHub\Admin\Inc\PostTypes\QuestionPostType;
use QuestionHub\Admin\Inc\Taxonomies\QuestionCategory;
use QuestionHub\Admin\Inc\Taxonomies\QuestionTag;
use QuestionHub\Admin\Inc\Columns\QuestionColumns;
use QuestionHub\Admin\Inc\Notices\AdminNotices;

/**
 * Class AdminManager
 *
 * Orchestrates all admin sub-classes.
 */
class AdminManager {

	protected $menu;
	protected $settings;
	protected $assets;
	protected $action_hooks;
	protected $filter_hooks;
	protected $post_type;
	protected $category;
	protected $tag;
	protected $columns;
	protected $notices;

	public function __construct() {
		$this->set_constants();
		$this->init();
	}

	/**
	 * Defines admin-specific constants.
	 *
	 * @since 1.0.0
	 */
	public function set_constants() {
		define( 'QUESTIONHUB_ADMIN_ASSETS', plugin_dir_url( __FILE__ ) . 'Assets' );
	}

	/**
	 * Instantiates all admin sub-classes.
	 *
	 * @since 1.0.0
	 */
	public function init() {
		$this->menu         = new MenuPage();
		$this->settings     = new Settings();
		$this->assets       = new Assets();
		$this->action_hooks = new ActionHooks();
		$this->filter_hooks = new FilterHooks();
		$this->post_type    = new QuestionPostType();
		$this->category     = new QuestionCategory();
		$this->tag          = new QuestionTag();
		$this->columns      = new QuestionColumns();
		$this->notices      = new AdminNotices();
	}

	/**
	 * Proxy for reading any plugin setting.
	 *
	 * @param  string $key     Option key.
	 * @param  mixed  $default Default value.
	 * @return mixed
	 * @since  1.0.0
	 */
	public function get_option( $key, $default = null ) {
		return $this->settings->get_option( $key, $default );
	}
}
