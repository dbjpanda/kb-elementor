<?php

/**
 *  Enqueue assets to elementor preview panel
 *  @toDo Remove this action when the issue https://github.com/elementor/elementor/issues/7907 gets resolved
 */
add_action( 'elementor/preview/enqueue_styles', function() {
    wp_enqueue_style( 'ke-toc-headings' );
} );

/**
 *  Add css class attribute to content
 *  @toDo Currently add css class to all content available in a page. Make it configurable from within widget
 */
function add_css_class_to_content($content){
    $str = '<div class="ke-toc-headings-content">';
    $content = $str . $content . "</div>";
    return $content;
}
add_filter( 'the_content', 'add_css_class_to_content' );
