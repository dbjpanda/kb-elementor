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

define('PLUGIN_FILE_URL', __FILE__);

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
     * Load widgets files
     *
     * @since 1.2.0
     * @access private
     */
    private function include_widgets_files() {
        require_once( __DIR__ . '/widgets/toc.php' );
    }

    /**
     * widget_scripts
     *
     * Load required plugin core files.
     *
     * @since 1.2.0
     * @access public
     */
    public function register_scripts() {

        wp_register_style( 'jquery-simpleTreeMenu.css', plugins_url( '/assets/css/jquery-simpleTreeMenu.css', __FILE__ ));
        wp_enqueue_style( 'jquery-simpleTreeMenu.css' );
        wp_register_script( 'jquery-simpleTreeMenu.js', plugins_url( '/assets/js/jquery-simpleTreeMenu.js', __FILE__ ), [ 'jquery' ], false, true );
        wp_enqueue_script( 'jquery-simpleTreeMenu.js' );

        wp_register_style( 'kb-elementor-toc.css', plugins_url( '/assets/css/toc.css', __FILE__ ));
        wp_enqueue_style( 'kb-elementor-toc.css' );
        wp_register_script( 'kb-elementor-toc.js', plugins_url( '/assets/js/toc.js', __FILE__ ), [ 'jquery' ], false, true );
        wp_enqueue_script( 'kb-elementor-toc.js' );


//        wp_localize_script( 'kb-elementor-toc-js', 'kbElementorToc',array(
//            \KbElementor\Widgets\Toc::settings()
//        ) );

    }

    /**
     * Register Widgets
     *
     * Register new Elementor widgets.
     *
     * @since 1.2.0
     * @access public
     */
    public function register_widgets() {
        // Its is now safe to include Widgets files
        $this->include_widgets_files();

        // Register Widgets
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Toc() );
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


        // Register widget scripts
        add_action( 'elementor/frontend/after_register_scripts', [ $this, 'register_scripts' ] );

        // Register widgets
        add_action( 'elementor/widgets/widgets_registered', [ $this, 'register_widgets' ] );



    }
}


require_once( __DIR__ . '/functions.php' );

// Instantiate Plugin Class
Plugin::instance();
