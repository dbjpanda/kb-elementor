<?php

/**
 *  Enqueue assets to elementor preview panel
 *  @toDo Remove this action when the issue https://github.com/elementor/elementor/issues/7907 gets resolved
 */
add_action( 'elementor/preview/enqueue_styles', function() {
    wp_enqueue_style( 'ke-toc-headings' );
} );


//add_filter( 'the_content', function ($content){
//    $toc = do_shortcode('[elementor-template id="7076"]');
//    return $toc . $content;
//} );


//add_filter( 'the_content', function ($content){
//    $toc_space = '<div id = "toc-space" style="height: 132px;width: 191px;background-color: red;float: left;margin-right: 10px;"></div>';
//    write_log('content called');
//    return $toc_space . $content;
//} );


//add_filter( 'the_content', function ($content){
//    $toc_space = '<div id="toc-space"></div>';
//    return $toc_space . $content;
//} );



/**
 *  Add ID attribute to each headings tag
 *
 *  You can use this function instead of doing it in clientside using JAvascript.
 *
 *  @toDo Use PHP DOM extension instead of regex
 *  https://stackoverflow.com/questions/3577641/how-do-you-parse-and-process-html-xml-in-php
 *  https://pento.net/2013/12/19/dont-do-regular-expressions-use-the-dom
 *
 */
//function ke_add_ids_to_header_tags( $content ) {
//    $content = preg_replace_callback( '/(\<h[1-6](.*?))\>(.*)(<\/h[1-6]>)/i', function( $matches ) {
//        if ( ! stripos( $matches[0], 'id=' ) ) :
//            $matches[0] = $matches[1] . $matches[2] . ' id="' . sanitize_title( $matches[3] ) . '">' . $matches[3] . $matches[4];
//        endif;
//        return $matches[0];
//    }, $content );
//    return $content;
//}
//add_filter( 'the_content', 'ke_add_ids_to_header_tags' );





//function my_plugin_class($classes) {
//    $classes[] = 'my-plugin-class';
//    return $classes;
//}
//add_filter('post_class', 'my_plugin_class');


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
