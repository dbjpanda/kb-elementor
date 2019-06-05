<?php
/**
 * Plugin Name: Kb Elmentor
 * Plugin URI: https://dbjpanda.me
 * Description: knowledge Base plugin for Elementor
 * Version:     1.0.1
 * Author:      dbjpanda
 * Author URI:  https://dbjpanda.me
 */

namespace KbElementor;

define('KE_PLUGIN_FILE_URL', plugins_url(__FILE__));
define('KE_PLUGIN_DIR_URL', plugin_dir_url( __FILE__ ));

use KbElementor\Widgets;

/**
 * Class Plugin
 *
 * Main Plugin class
 * @since 1.2.0
 */
class Plugin {

    /**
     * Instance
     *
     * @since 1.2.0
     * @access private
     * @static
     *
     * @var Plugin The single instance of the class.
     */
    private static $_instance = null;

    /**
     * Instance
     *
     * Ensures only one instance of the class is loaded or can be loaded.
     *
     * @since 1.2.0
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

    /**
     * Include Widgets files
     *
     * @since 1.2.0
     * @access private
     */
    private function include_widgets_files() {

        require_once( __DIR__ . '/widgets/toc-posts/toc-posts.php' );
        require_once( __DIR__ . '/widgets/toc-headings/toc-headings.php' );
    }

    /**
     * Register Widgets type
     *
     * @since 1.2.0
     * @access public
     */
    public function register_widgets_type(){

        \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\TocPosts() );
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\TocHeadings() );

    }

    /**
     * Register css and js assets for elementor
     *
     * @since 1.2.0
     * @access public
     */
    public function register_widget_assets() {

        wp_register_style( 'jquery-simpleTreeMenu', KE_PLUGIN_DIR_URL . 'assets/css/jquery-simpleTreeMenu.css');
        wp_register_style( 'kb-elementor-toc-posts', KE_PLUGIN_DIR_URL . 'assets/css/toc-posts.css');
        wp_register_style( 'jquery-simpleTreeMenu', KE_PLUGIN_DIR_URL . 'assets/css/jquery-simpleTreeMenu.css');
        wp_register_style( 'kb-elementor-toc-headings', KE_PLUGIN_DIR_URL . 'assets/css/toc-headings.css');

        wp_register_script( 'jquery-simpleTreeMenu', KE_PLUGIN_DIR_URL . 'assets/js/jquery-simpleTreeMenu.js',  [ 'jquery' ], false, true );
        wp_register_script( 'kb-elementor-toc-posts', KE_PLUGIN_DIR_URL . 'assets/js/toc-posts.js', [ 'jquery' ], false, true );
        wp_register_script( 'kb-elementor-toc-headings', KE_PLUGIN_DIR_URL . 'assets/js/toc-headings.js', [ 'jquery' ], false, true );
        wp_register_script( 'kb-elementor-order-posts-tags', KE_PLUGIN_DIR_URL . 'assets/js/order-posts-tags.js', [ 'jquery' ], false, true );
    }

    /**
     * Register Widgets
     *
     * Register new Elementor widgets.
     *
     * @since 1.2.0
     * @access public
     */
    public function register_elementor_widgets() {

        $this->include_widgets_files();
        $this->register_widgets_type();
    }

    /**
     * Register a custom category
     *
     * @since 1.2.0
     * @access public
     */
    public function add_widget_category( $elements_manager ) {

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
     * @since 1.2.0
     * @access public
     */
    public function __construct() {

        // Include main plugin functions
        require_once( __DIR__ . '/functions.php' );

        // Register all widget scripts
        add_action( 'elementor/frontend/after_register_scripts', [ $this, 'register_widget_assets' ] );

        // Register all widgets
        add_action( 'elementor/widgets/widgets_registered', [ $this, 'register_elementor_widgets' ] );

        // // Add a custom category in editor's panel widgets
        add_action( 'elementor/elements/categories_registered', [ $this, 'add_widget_category'] );
    }
}

// Instantiate Plugin Class
Plugin::instance();
