<?php

/**
 *  Add column `term_order` to WP_TERM table on plugin activation
 */
function add_column_to_wp_term_table(){
    global $wpdb;
    $result = $wpdb->query("DESCRIBE $wpdb->terms `term_order`");
    if (!$result) {
        $query = "ALTER TABLE $wpdb->terms ADD `term_order` INT( 4 ) NULL DEFAULT '0'";
        $wpdb->query($query);
    }
}
register_activation_hook( KE_PLUGIN_FILE_URL, 'add_column_to_wp_term_table' );


/**
 *  Remove column `term_order` to WP_TERM table on plugin deactivation
 */
function remove_column_from_wp_term_table(){
    global $wpdb;
    $result = $wpdb->query("DESCRIBE $wpdb->terms `term_order`");
    if ($result) {
        $query = "ALTER TABLE $wpdb->terms DROP `term_order`";
        $wpdb->query($query);
    }
}
register_deactivation_hook( KE_PLUGIN_FILE_URL, 'remove_column_from_wp_term_table' );


/**
 *  Register and enqueue assets to admin panel
 */
function uc_enqueue_assets($hook) {

    if ( $hook != 'edit.php' && $hook != 'edit-tags.php' ) {
        return;
    }
    wp_enqueue_script( 'jquery-ui-sortable');
    wp_register_script( 'kb-elementor-order-posts-tags', KE_PLUGIN_DIR_URL . 'assets/js/order-posts-tags.js', [ 'jquery' ], false, true );
    wp_enqueue_script( 'kb-elementor-order-posts-tags' );
}
add_action( 'admin_enqueue_scripts', 'uc_enqueue_assets' );


/**
 *  Update order of posts by ajax on trigger of drag and drop event
 */
function uc_sort_post_items(){

    $order = wp_parse_id_list(explode(',', $_POST['order']));

    global $wpdb;
    $list = join(', ', $order);
    $wpdb->query('SELECT @i:=0');
    $wpdb->query(
        "UPDATE wp_posts SET menu_order = ( @i:= @i+1 )
        WHERE ID IN ( $list ) ORDER BY FIELD( ID, $list );"
    );

    // Clear WP Rocket cache.
    // @toDo Find a better way to get the ajax refer page and all its parent category pages to clear cache
    $refer_url = wp_get_referer();
    if ( strpos($refer_url, "edit.php") !== false ){
        if ( function_exists( 'rocket_clean_domain' ) ) {
            rocket_clean_domain();
        }
    }
    else{
        if ( function_exists( 'rocket_clean_files' ) ) {
            rocket_clean_files( preg_replace('/(.+\..+?\/.+?\/).+/m', '$1', $refer_url ) );
        }
    }
}
add_action('wp_ajax_uc_sort_post_items', 'uc_sort_post_items');


/**
 *  Display sorted posts on dashboard
 */
function uc_pre_get_posts( $wp_query ){

    if ( is_admin() && basename($_SERVER['PHP_SELF']) == 'edit.php'){
        $wp_query->set('orderby', 'menu_order');
        $wp_query->set('order', 'ASC');
    }
}
add_action( 'pre_get_posts', 'uc_pre_get_posts', 1 );


/**
 *  Update order of terms by ajax on trigger of jquery ui sort event
 */
function uc_sort_term_items(){

    $order = wp_parse_id_list(explode(',', $_POST['order']));

    global $wpdb;
    $list = join(', ', $order);
    $wpdb->query('SELECT @i:=0');
    $wpdb->query(
        "UPDATE wp_terms SET term_order = ( @i:= @i+1 )
        WHERE term_id IN ( $list ) ORDER BY FIELD( term_id, $list );"
    );

    // Clear WP Rocket cache.
    // @toDo Find a better way to get the ajax refer page and all its parent category pages to clear cache
    $refer_url = wp_get_referer();
    if ( strpos($refer_url, "edit-tags.php") !== false ){
        if ( function_exists( 'rocket_clean_domain' ) ) {
            rocket_clean_domain();
        }
    }
    else{
        if ( function_exists( 'rocket_clean_files' ) ) {
            rocket_clean_files( preg_replace('/(.+\..+?\/.+?\/).+/m', '$1', $refer_url ) );
        }
    }

}
add_action('wp_ajax_uc_sort_term_items', 'uc_sort_term_items');


/**
 *  Display sorted terms
 */
function uc_get_terms_orderby($orderby, $query_vars){

    if ( is_admin() && basename($_SERVER['PHP_SELF']) == 'edit-tags.php') {
        return 't.term_order';
    }
    else{
        // https://wordpress.stackexchange.com/questions/92213/order-terms-by-term-order/338928#338928
        return $query_vars['orderby'] == 'term_order' ? 'term_order' : $orderby;
    }
}
add_filter('get_terms_orderby', 'uc_get_terms_orderby', 10, 2);


/**
 *  Clear Wp rocket cache of grand parent term of a post
 */
function rocket_cache_clear_term($post_id){
    $current_term_id = get_the_terms($post_id, 'category')[0]->term_id;
    $parent_term_list = get_term_parents_list($current_term_id, 'category', array('format' => 'name', 'separator' => ',', 'link' => true, 'inclusive' => false));
    if (!$parent_term_list)
       return;
    $grand_parent_term = strtok($parent_term_list, ",");
    $grand_parent_term_url = (string)( new SimpleXMLElement($grand_parent_term))['href'];
    if ( function_exists( 'rocket_clean_files' ) ) {
        rocket_clean_files($grand_parent_term_url);
    }
}


/**
 *  Set default menu order of a new post
 */
function uc_publish_post($post_id){

    remove_action('publish_post', 'uc_publish_post');
    if ( ! get_post_field( 'menu_order', $post_id)) {
        wp_update_post(array('ID' => $post_id, 'menu_order' => $post_id));
    }
    rocket_cache_clear_term($post_id);
    add_action('publish_post', 'uc_publish_post');    
}
add_action('publish_post', 'uc_publish_post');


/**
 *  Set default category of a post
 *
 *  @wp-hook pre_option_default_category
 *  @return  string Category slug
 */
function uc_get_default_cat_by_url()
{
    if ( ! isset( $_GET['post_cat'] ) )
        return FALSE;

    return array_map( 'sanitize_title', explode( ',', $_GET['post_cat'] ) );
}


/**
 *  Add category by URL parameter to auto-drafts.
 *
 *  @wp-hook wp_insert_post
 *  @param   int $post_ID
 *  @param   object $post
 *  @return  WP_Error|array An error object or term ID array.
 */
function uc_draft_category( $post_ID, $post ){
    if ( ! $cat = uc_get_default_cat_by_url()
        or 'auto-draft' !== $post->post_status )
        return;

    // return value will be used in unit tests only.
    return wp_set_object_terms( $post_ID, $cat, 'category' );
}
add_action( 'wp_insert_post', 'uc_draft_category', 10, 2 );
add_filter( 'pre_option_default_category', 'uc_get_default_cat_by_url' );


/**
 *  Write anything to debug.log file E.g write_log("My first log message")
 */
if (!function_exists('write_log')) {

    function write_log($log) {
        if (true === WP_DEBUG) {
            if (is_array($log) || is_object($log)) {
                error_log(print_r($log, true));
            } else {
                error_log($log);
            }
        }
    }
}

/**
 *  Add some custom Icons to Elementor Icon control list
 */
function ke_modify_icon_controls( $controls_registry ) {
    // Get existing icons
    $icons = $controls_registry->get_control( 'icon' )->get_settings( 'options' );
    // Append new icons
    $new_icons = array_merge(
        array(
            'ke-show-button' => '[Show] ke-show-button',
            'ke-hide-button' => '[Hide] ke-hide-button',
        ),
        $icons
    );
    // Then we set a new list of icons as the options of the icon control
    $controls_registry->get_control( 'icon' )->set_settings( 'options', $new_icons );
}
add_action( 'elementor/controls/controls_registered', 'ke_modify_icon_controls', 10, 1 );
