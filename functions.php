<?php

function add_column_to_wp_term_table(){
    global $wpdb;
    $result = $wpdb->query("DESCRIBE $wpdb->terms `term_order`");
    if (!$result) {
        $query = "ALTER TABLE $wpdb->terms ADD `term_order` INT( 4 ) NULL DEFAULT '0'";
        $wpdb->query($query);
    }
}
register_activation_hook( PLUGIN_FILE_URL, 'add_column_to_wp_term_table' );



function remove_column_from_wp_term_table(){
    global $wpdb;
    $result = $wpdb->query("DESCRIBE $wpdb->terms `term_order`");
    if ($result) {
        $query = "ALTER TABLE $wpdb->terms DROP `term_order`";
        $wpdb->query($query);
    }
}
register_deactivation_hook( PLUGIN_FILE_URL, 'remove_column_from_wp_term_table' );



function get_posts_per_taxterm($taxonomy, $term_id ){
    $posts = get_posts(
        array(
            'posts_per_page' => -1,
            'post_type' => 'post',
            'orderby'  => 'menu_order',
            'order'    => 'ASC',
            'tax_query' => array(
                array(
                    'taxonomy' => $taxonomy,
                    'field' => 'term_id',
                    'terms' => $term_id,
                    'include_children' => false
                )
            )
        )
    );

    // `first_post_id` is a global variable which contains the ID of the post which comes first inside TOC
    // @toDo Find a better way
    global $first_post_id;

    if(!isset($first_post_id)){
        if (!empty($posts)){
            $first_post_id = $posts[0]->ID;
        }
    }

    return $posts;
}




function get_array_of_posts_grouped_by_taxterms($terms, $parent_id = 0, $outputs = []) {

    foreach ($terms as $term) {

        if ($parent_id == $term->parent) {

            $outputs['<a data-id = "'.$term->term_id.'" class = "elementor-kb-term-href stm-content" href ='.get_term_link($term->term_id).'>'.$term->name.'</a>'] = get_array_of_posts_grouped_by_taxterms( $terms, $term->term_id);
            $posts= get_posts_per_taxterm('category',$term->term_id);

            foreach ($posts as $post){
                $outputs['<a data-id = "'.$term->term_id.'" class = "elementor-kb-term-href stm-content" href ='.get_term_link($term->term_id).'>'.$term->name.'</a>'][] = '<a class = "elementor-kb-post-href stm-content" href='.get_permalink($post->ID).' data-id="'.$post->ID.'">'.$post->post_title.'</a>';
            }
        }
    }

    return $outputs;
}




function convert_array_to_html_list(array $array){

    $html = '';
    foreach ($array as $key => $item)
    {
        if (is_array($item))
        {
            $html .= '<li><span class="stm-icon"></span>'.$key.'<ul class="elementor-kb">';
            $html .= convert_array_to_html_list($item);
            $html .= '</ul></li>';
        }
        else
        {
            $html .= '<li><span class="stm-icon"></span>'.$item.'</li>';
        }
    }
    return $html;
}



function get_archive_post_title(){

    global $first_post_id;
    $post_title = get_post_field('post_title', $first_post_id);
    return '<span id="kb-title">'.$post_title.'</span>';
}
add_shortcode( 'elementor-kb-archive-post-title', 'get_archive_post_title' );



function get_archive_post_content(){

    global $first_post_id;
    $post_content = get_post_field('post_content', $first_post_id);
    return '<article id="kb-content">'.$post_content.'</article>';
}
add_shortcode( 'elementor-kb-archive-post-content', 'get_archive_post_content' );



/**
 *  Enqueue javascript and css files for drag and drop sorting of posts and terms
 */
function uc_enqueue_my_assets(){

    wp_enqueue_script( 'jquery-ui-sortable');
    wp_register_script( 'order', plugins_url( '/assets/js/order.js', __FILE__ ), array( 'jquery' ) );
    wp_enqueue_script( 'order' );
}

function uc_is_user_logged_in(){

    if ( is_user_logged_in()) {
        add_action( 'wp_enqueue_scripts', 'uc_enqueue_my_assets' );
        add_action( 'admin_enqueue_scripts', 'uc_enqueue_my_assets' );
    }
}
add_action('init', 'uc_is_user_logged_in');



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
    $grand_parent_term = strtok($parent_term_list, ",");
    $grand_parent_term_url = (string)( new SimpleXMLElement($grand_parent_term))['href'];
    rocket_clean_files($grand_parent_term_url);
}

/**
 *  Set default menu order of a new post
 */
function uc_publish_post($post_id){

    remove_action('publish_post', 'uc_publish_post');
    wp_update_post(array('ID' => $post_id, 'menu_order' => $post_id));
    add_action('publish_post', 'uc_publish_post');
    rocket_cache_clear_term($post_id);
}
add_action('publish_post', 'uc_publish_post');



/**
 * Set default category of a post
 *
 * @wp-hook pre_option_default_category
 * @return  string Category slug
 */
function uc_get_default_cat_by_url()
{
    if ( ! isset( $_GET['post_cat'] ) )
        return FALSE;

    return array_map( 'sanitize_title', explode( ',', $_GET['post_cat'] ) );
}


/**
 * Add category by URL parameter to auto-drafts.
 *
 * @wp-hook wp_insert_post
 * @param   int $post_ID
 * @param   object $post
 * @return  WP_Error|array An error object or term ID array.
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