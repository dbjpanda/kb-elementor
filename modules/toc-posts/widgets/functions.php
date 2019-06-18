<?php

/**
 *  Enqueue assets to elementor preview panel
 *  @toDo Remove this action when the issue https://github.com/elementor/elementor/issues/7907 gets resolved
 */
add_action( 'elementor/preview/enqueue_styles', function() {
    wp_enqueue_style('vendor-simpleTreeMenu');
    wp_enqueue_style( 'ke-toc-posts' );
} );


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

            $outputs['<a data-id = "'.$term->term_id.'" class = "ke-toc-posts-term stm-content" href ='.get_term_link($term->term_id).'>'.$term->name.'</a>'] = get_array_of_posts_grouped_by_taxterms( $terms, $term->term_id);
            $posts= get_posts_per_taxterm('category',$term->term_id);

            foreach ($posts as $post){
                $outputs['<a data-id = "'.$term->term_id.'" class = "ke-toc-posts-term stm-content" href ='.get_term_link($term->term_id).'>'.$term->name.'</a>'][] = '<a class = "ke-toc-posts-post stm-content" href='.get_permalink($post->ID).' data-id="'.$post->ID.'">'.$post->post_title.'</a>';
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
            $html .= '<li><span class="stm-icon"></span>'.$key.'<ul class="ke-toc-posts">';
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


/**
 *  Helper short code widgets as Elementor Post widget doesn't work in Archive page.
 *  Display post title: [elementor-kb-archive-post-title]
 *  Display post content: [elementor-kb-archive-post-content]
 *  @toDo Find a better way
 */
function get_archive_post_title(){

    global $first_post_id;
    $post_title = get_post_field('post_title', $first_post_id);
    return '<span id="kb-title">'.$post_title.'</span>';
}
function get_archive_post_content(){

    global $first_post_id;
    $post_content = get_post_field('post_content', $first_post_id);
    return '<article id="kb-content">'.$post_content.'</article>';
}
add_shortcode( 'elementor-kb-archive-post-title', 'get_archive_post_title' );
add_shortcode( 'elementor-kb-archive-post-content', 'get_archive_post_content' );
