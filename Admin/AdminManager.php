<?php
/**
 * Admin orchestrator — mirrors PrimeKit's AdminManager.php exactly.
 *
 * @package ASKORA\Admin
 * @since   1.0.0
 */

namespace ASKORA\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use ASKORA\Admin\Assets\Assets;
use ASKORA\Admin\Inc\Dashboard\Menu\Askora as MenuPage;
use ASKORA\Admin\Inc\Dashboard\Settings\Settings;
use ASKORA\Admin\Inc\Hooks\ActionHooks;
use ASKORA\Admin\Inc\Hooks\FilterHooks;
use ASKORA\Admin\Inc\PostTypes\QuestionPostType;
use ASKORA\Admin\Inc\Taxonomies\QuestionCategory;
use ASKORA\Admin\Inc\Taxonomies\QuestionTag;
use ASKORA\Admin\Inc\Columns\QuestionColumns;
use ASKORA\Admin\Inc\Notices\AdminNotices;
use ASKORA\Admin\Inc\Users\Profile;

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
	protected $profile;

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
		define( 'ASKORA_ADMIN_ASSETS', plugin_dir_url( __FILE__ ) . 'Assets' );
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
		$this->profile      = new Profile();
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
