<?php

namespace ZionBuilder;

use ZionBuilder\CommonJS;
use ZionBuilder\Shortcodes;
use ZionBuilder\Renderer;
use ZionBuilder\BulkActionsData;
use ZionBuilder\Admin\Admin;
use ZionBuilder\Editor\Editor;
use ZionBuilder\Post\PostManager;
use ZionBuilder\Elements\Manager as ElementsManager;
use ZionBuilder\PageTemplates\PageTemplates;
use ZionBuilder\FontsManager\FontsManager;
use ZionBuilder\Api\RestApi;
use ZionBuilder\Upgrade\Upgrader;
use ZionBuilder\MaintenanceMode;
use ZionBuilder\CustomCode;
use ZionBuilder\Screenshot;
use ZionBuilder\Library\Library;
use ZionBuilder\Templates;
use ZionBuilder\Performance;
use ZionBuilder\Responsive;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Plugin
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @since 1.0.0
 *
 * @package ZionBuilder
 */
class Plugin {
	/**
	 * ZionBuilder instance.
	 *
	 * Holds the plugin instance.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @var Plugin
	 */
	public static $instance = null;

	/**
	 * Renderer class
	 *
	 * @var Renderer
	 */
	public $renderer = null;

	/**
	 * Plugin data from header comments
	 *
	 * @var string
	 */
	private $version = null;

	/**
	 * Project root path
	 *
	 * @var string
	 */
	private $project_root_path = null;

	/**
	 * Project root url
	 *
	 * @var string
	 */
	private $project_root_url = null;

	/**
	 * Holds the reference to the instance of the \ZionBuilder\Whitelabel class
	 *
	 * @var Whitelabel
	 *
	 * @see Plugin::init()
	 */
	public $whitelabel = null;
	/**
	 * Holds the reference to the instance of the \ZionBuilder\Permissions class
	 *
	 * @var Permissions
	 *
	 * @see Plugin::init()
	 */
	public $permissions = null;
	/**
	 * Holds the reference to the instance of the \ZionBuilder\Elements\Manager class
	 *
	 * @var ElementsManager
	 *
	 * @see Plugin::init()
	 */
	public $elements_manager = null;
	/**
	 * Holds the reference to the instance of the \ZionBuilder\Post\PostManager class
	 *
	 * @var PostManager
	 *
	 * @see Plugin::init()
	 */
	public $post_manager = null;
	/**
	 * Zion builder admin instance
	 * Holds the reference to the ZionBuilder\Admin\Admin class
	 *
	 * @var Admin
	 *
	 * @see Plugin::init()
	 */
	public $admin = null;
	/**
	 * Zion builder editor instance
	 * Holds the reference to the ZionBuilder\Editor\Editor class
	 *
	 * @var Editor
	 *
	 * @see Plugin::init()
	 */
	public $editor = null;
	/**
	 * Holds the reference to the instance of the \ZionBuilder\Frontend class
	 *
	 * @var Frontend
	 *
	 * @see Plugin::init()
	 */
	public $frontend = null;
	/**
	 * Holds the reference to the instance of the \ZionBuilder\Cache class
	 *
	 * @var Cache
	 *
	 * @see Plugin::init()
	 */
	public $cache = null;
	/**
	 * Holds the reference to the instance of the \ZionBuilder\PageTemplates\PageTemplates class
	 *
	 * @var PageTemplates
	 *
	 * @see Plugin::init()
	 */
	public $page_templates = null;

	/**
	 * Holds the reference to the instance of the \ZionBuilder\Api\RestApi class
	 *
	 * @var RestApi
	 *
	 * @see Plugin::init()
	 */
	public $api = null;
	/**
	 * Holds the reference to the instance of the \ZionBuilder\FontsManager\FontsManager class
	 *
	 * @var FontsManager
	 *
	 * @see Plugin::init()
	 */
	public $fonts_manager = null;
	/**
	 * Holds the reference to the instance of the \ZionBuilder\ImportExport class
	 *
	 * @var ImportExport
	 *
	 * @see Plugin::init()
	 */
	public $import_export = null;


	/**
	 * Holds the reference to the instance of the \ZionBuilder\Icons class
	 *
	 * @var Icons
	 *
	 * @see Plugin::init()
	 */
	public $icons = null;


	/**
	 * Holds the reference to the instance of the \ZionBuilder\Icons class
	 *
	 * @var Scripts
	 *
	 * @see Plugin::init()
	 */
	public $scripts = null;


	/**
	 * Holds the reference to the instance of the \ZionBuilder\Icons class
	 *
	 * @var Library
	 *
	 * @see Plugin::init()
	 */
	public $library = null;

	/**
	 * Holds the reference to the instance of the \ZionBuilder\Icons class
	 *
	 * @var Templates
	 *
	 * @see Plugin::init()
	 */
	public $templates = null;

	/**
	 * Main class init
	 *
	 * Will hook into WP actions to add the plugin functionality
	 *
	 * @return void
	 */
	public function init() {
		do_action( 'zionbuilder/before_init' );

		$this->load_libraries();

		// initiate permissions
		$this->whitelabel       = new Whitelabel();
		$this->cache            = new Cache();
		$this->renderer         = new Renderer();
		$this->scripts          = new Scripts();
		$this->permissions      = new Permissions();
		$this->fonts_manager    = new FontsManager();
		$this->elements_manager = new ElementsManager();
		$this->post_manager     = new PostManager();
		$this->admin            = new Admin();
		$this->editor           = new Editor();
		$this->frontend         = new Frontend();
		$this->page_templates   = new PageTemplates();
		$this->api              = new RestApi();
		$this->import_export    = new ImportExport();
		$this->icons            = new Icons();
		$this->templates        = new Templates();
		$this->library          = new Library();

		new Shortcodes();
		new CommonJS();
		new Integrations();
		new AdminBar();
		new Upgrader();
		new BulkActionsData();
		new MaintenanceMode();
		new CustomCode();
		new Screenshot();
		new Performance();
		new Responsive();

		/*
		 * ZionBuilder loaded.
		 *
		 * Allow others to hook when the plugin loads
		 *
		 * @since 1.0.0
		 */
		do_action( 'zionbuilder/loaded' );
	}

	/**
	 * Loads extra library files required for the plugin
	 *
	 * @return void
	 */
	public function load_libraries() {
		require_once $this->get_root_path() . 'vendor/woocommerce/action-scheduler/action-scheduler.php';
	}

	/**
	 * Instance.
	 *
	 * Always load a single instance of the Plugin class
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @return Plugin an instance of the class
	 */
	public static function instance() {
		return self::$instance;
	}

	/**
	 * Plugin constructor.
	 * Create an instance of the Plugin class
	 *
	 * @param string $project_path
	 * @param string $project_url
	 * @param string $version
	 *
	 * @since 1.0.0
	 */
	public function __construct( $project_path, $project_url, $version ) {
		$this->project_root_path = $project_path;
		$this->project_root_url  = $project_url;
		$this->version           = $version;

		self::$instance = $this;

		add_action( 'after_setup_theme', [ $this, 'init' ] );

		do_action( 'zionbuilder/main' );
	}


	/**
	 * Retrieve the project root path
	 *
	 * @return string
	 */
	public function get_root_path() {
		return $this->project_root_path;
	}


	/**
	 * Retrieve the project root url
	 *
	 * @return string
	 */
	public function get_root_url() {
		return $this->project_root_url;
	}

	/**
	 * Retrieve the project version
	 *
	 * @return string
	 */
	public function get_version() {
		return $this->version;
	}
}
