<?php
namespace KbElementor\Modules\TocPosts;

use KbElementor\Base\Module_Base;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class Module extends Module_Base {

    public function get_widgets() {
        return [
            'Toc_Posts',
        ];
    }

    public function get_name() {
        return 'toc-posts';
    }
}
