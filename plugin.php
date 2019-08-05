<?php

/**
 * Plugin Name: KB Elementor
 * Plugin URI: https://github.com/dbjpanda/kb-elementor
 * Description: Elementor Plugin for creating wiki like knowledge based site |  Learning Management Site  | Category-Post tree | Series of Posts | Table of Content
 * Version:     1.0.4
 * Author:      dbjpanda
 * Author URI:  https://dbjpanda.me
 *
 * License: GPLv2 or later
 * Text Domain: kb-elementor
 */

namespace KbElementor;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

define('KE_PLUGIN_FILE_URL', __FILE__);
define('KE_PLUGIN_DIR_URL', plugin_dir_url( __FILE__ ));
define('KE_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Main Plugin class
 */
class Plugin {

    /**
     * @var Plugin
     */
    private static $_instance;

    /**
     * @var Manager
     */
    public $modules_manager;


    private $classes_aliases = [
        'ElementorPro\Modules\PanelPostsControl\Module' => 'ElementorPro\Modules\QueryControl\Module',
        'ElementorPro\Modules\PanelPostsControl\Controls\Group_Control_Posts' => 'ElementorPro\Modules\QueryControl\Controls\Group_Control_Posts',
        'ElementorPro\Modules\PanelPostsControl\Controls\Query' => 'ElementorPro\Modules\QueryControl\Controls\Query',
    ];

    /**
     * Throw error on object clone
     *
     * The whole idea of the singleton design pattern is that there is a single
     * object therefore, we don't want the object to be cloned.
     *
     * @since 1.0.1
     * @return void
     */
    public function __clone() {
        // Cloning instances of the class is forbidden
        _doing_it_wrong( __FUNCTION__, __( 'Something went wrong.', 'kb-elementor' ), '1.0.1' );
    }

    /**
     * Disable unserializing of the class
     *
     * @since 1.0.1
     * @return void
     */
    public function __wakeup() {
        // Unserializing instances of the class is forbidden
        _doing_it_wrong( __FUNCTION__, __( 'Something went wrong.', 'kb-elementor' ), '1.0.1' );
    }

    /**
     * @return \Elementor\Plugin
     */
    public static function elementor() {
        return \Elementor\Plugin::$instance;
    }

    /**
     * Instance
     *
     * Ensures only one instance of the class is loaded or can be loaded.
     *
     * @access public
     *
     * @return Plugin An instance of the class.
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    private function includes() {
        // Main plugin functions
        require_once( KE_PLUGIN_DIR_PATH . 'includes/functions.php' );

        // Elementor module manger which instantiate the elementor widgets
        require KE_PLUGIN_DIR_PATH . 'includes/modules-manager.php';
    }

    /**
     * Autoloader function for all classes files
     */
    public function autoload( $class ) {
        if ( 0 !== strpos( $class, __NAMESPACE__ ) ) {
            return;
        }

        $has_class_alias = isset( $this->classes_aliases[ $class ] );

        // Backward Compatibility: Save old class name for set an alias after the new class is loaded
        if ( $has_class_alias ) {
            $class_alias_name = $this->classes_aliases[ $class ];
            $class_to_load = $class_alias_name;
        } else {
            $class_to_load = $class;
        }

        if ( ! class_exists( $class_to_load ) ) {
            $filename = strtolower(
                preg_replace(
                    [ '/^' . __NAMESPACE__ . '\\\/', '/([a-z])([A-Z])/', '/_/', '/\\\/' ],
                    [ '', '$1-$2', '-', DIRECTORY_SEPARATOR ],
                    $class_to_load
                )
            );
            $filename = KE_PLUGIN_DIR_PATH . $filename . '.php';

            if ( is_readable( $filename ) ) {
                include( $filename );
            }
        }

        if ( $has_class_alias ) {
            class_alias( $class_alias_name, $class );
        }
    }

    /**
     * Register all css assets
     *
     * @since 1.0.1
     * @access public
     */
    public function register_widget_styles() {

        // Toc Posts
        wp_register_style( 'vendor-simpleTreeMenu', KE_PLUGIN_DIR_URL . 'assets/vendor/simpleTreeMenu/jquery-simpleTreeMenu.css');
        wp_register_style( 'ke-toc-posts', KE_PLUGIN_DIR_URL . 'assets/css/toc-posts.css');

        // TOC Headings
        wp_register_style( 'ke-toc-headings', KE_PLUGIN_DIR_URL . 'assets/css/toc-headings.css');

    }

    /**
     * Register all js assets
     *
     * @since 1.0.0
     * @access public
     */
    public function register_widget_scripts() {

        // Toc Posts
        wp_register_script( 'vendor-simpleTreeMenu', KE_PLUGIN_DIR_URL . 'assets/vendor/simpleTreeMenu/jquery-simpleTreeMenu.js',  [ 'jquery' ], false, true );
        wp_register_script( 'ke-toc-posts', KE_PLUGIN_DIR_URL . 'assets/js/toc-posts.js', [ 'jquery' ], false, true );
        wp_register_script( 'ke-order-posts-tags', KE_PLUGIN_DIR_URL . 'assets/js/order-posts-tags.js', [ 'jquery' ], false, true );

        // TOC Headings
        wp_register_script( 'vendor-tocbot', KE_PLUGIN_DIR_URL . 'assets/vendor/tocbot/tocbot.js', [ 'jquery' ], false, true );
        wp_register_script( 'ke-toc-headings', KE_PLUGIN_DIR_URL . 'assets/js/toc-headings.js',  [ 'jquery' ], false, true );

    }

    public function on_elementor_init()
    {
        $this->modules_manager = new Manager();

        /**
         * Kb Elementor init.
         *
         * Fires on Kb Elementor, after Elementor has finished loading but
         * before any headers are sent.
         *
         * @since 1.0.1
         */
        do_action('kb_elementor/init');
    }

    /**
     * Register a custom category
     *
     * @since 1.0.1
     * @access public
     */
    public function register_widget_category( $elements_manager ) {

        $elements_manager->add_category('kb-elementor', [
            'title' => __( 'KB ELEMENTOR', 'kb-elementor' ),
            'icon' => 'fa fa-plug'
        ]);
    }

    /**
     *  Plugin class constructor
     *
     * Register plugin action hooks and filters
     *
     * @since 1.0.1
     * @access public
     */
    private function __construct() {

        // Register autoload function
        spl_autoload_register( [ $this, 'autoload' ] );

        // Include some backend files like functions.php module-manger.php etc
        $this->includes();

        // Instantiate your module manager on elementor init which results in instantiate your widget too
        add_action( 'elementor/init', [ $this, 'on_elementor_init' ] );

        // Register a custom category in editor's panel widgets
        add_action( 'elementor/elements/categories_registered', [ $this, 'register_widget_category'] );

        // Register all widget styles
        add_action( 'elementor/frontend/after_enqueue_styles', [ $this, 'register_widget_styles' ] );

        // Register all widget scripts
        add_action( 'elementor/frontend/after_register_scripts', [ $this, 'register_widget_scripts' ] );

    }
}

// Instantiate `Plugin` Class
Plugin::instance();
