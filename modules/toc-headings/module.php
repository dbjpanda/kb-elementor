<?php
namespace KbElementor\Modules\TocHeadings;

define('TOC_HEADINGS_FILE_URL', plugins_url(__FILE__));
define('TOC_HEADINGS_DIR_URL', plugin_dir_url( __FILE__ ));
define('TOC_HEADINGS_DIR_PATH', plugin_dir_path( __FILE__ ) );

use KbElementor\Base\Module_Base;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class Module extends Module_Base {

    public function __construct()
    {
        parent::__construct();
        require_once( TOC_HEADINGS_DIR_PATH . 'includes/functions.php' );
    }

    public function get_widgets() {
        return [
            'Toc_Headings',
        ];
    }

    public function get_name() {
        return 'toc-headings';
    }
}
