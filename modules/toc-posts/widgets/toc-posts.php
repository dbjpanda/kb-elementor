<?php


namespace KbElementor\Modules\TocPosts\Widgets;

require_once( __DIR__ . '/functions.php' );

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Typography;
use Elementor\Scheme_Color;
use Elementor\Group_Control_Text_Shadow;
use KbElementor\Base\Base_Widget;


// don't call the file directly
if ( !defined( 'ABSPATH' ) ) exit;


class Toc_Posts extends Base_Widget {

    public function get_name() {
        return 'ke-toc-posts';
    }

    public function get_title() {
        return __( 'TOC Posts', 'kb-elementor' );
    }

    public function get_icon() {
        return 'fa fa-book';
    }

    public function get_keywords() {
        return [ 'table', 'content', 'index', 'tree', 'terms'];
    }


    public function get_script_depends() {
        if ( is_user_logged_in()) {
            return [
                'vendor-simpleTreeMenu',
                'ke-toc-posts',
                'jquery-ui-sortable',
                'ke-order-posts-tags'
            ];
        }
        else{
            return [
                'vendor-simpleTreeMenu',
                'ke-toc-posts'
            ];
        }
    }

    public function get_style_depends() {
        return [
            'vendor-simpleTreeMenu',
            'kb-elementor-toc-posts'
        ];
    }

    protected function _register_controls() {

        $this->start_controls_section(
            'ke-toc-posts-content',
            [
                'label' => __( 'Configuration', 'ke-toc-posts' ),
            ]
        );

        $this->add_control(
            'ke-toc-posts-ajax-enable',
            [
                'label' => __( 'Ajax', 'ke-toc-posts' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __( 'Enable', 'toc' ),
                'label_off' => __( 'Disable', 'toc' ),
                'return_value' => 'yes',
                'default' => 'no',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'ke-toc-posts-text',
            [
                'label' => __( 'Text', 'ke-toc-posts' ),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'ke-toc-posts-term-color',
            [
                'label' => __( 'Term Color', 'ke-toc-posts' ),
                'type' => Controls_Manager::COLOR,
                'scheme' => [
                    'type' => Scheme_Color::get_type(),
                    'value' => Scheme_Color::COLOR_1,
                ],
                'selectors' => [
                    // Stronger selector to avoid section style from overwriting
                    '{{WRAPPER}} .ke-toc-posts-term' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'ke-toc-posts-post-color',
            [
                'label' => __( 'Post Color', 'ke-toc-posts' ),
                'type' => Controls_Manager::COLOR,
                'scheme' => [
                    'type' => Scheme_Color::get_type(),
                    'value' => Scheme_Color::COLOR_1,
                ],
                'selectors' => [
                    // Stronger selector to avoid section style from overwriting
                    '{{WRAPPER}} .ke-toc-posts-post' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'typography',
                'label' => __( 'Typography', 'ke-toc-posts' ),
                'scheme' => Scheme_Typography::TYPOGRAPHY_1,
                'selector' => '{{WRAPPER}} .ke-toc-posts-text',
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'text_shadow',
                'selector' => '{{WRAPPER}} .ke-toc-posts-text',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {

        if (!is_singular()){
            $quired_object = get_queried_object();

            if($quired_object){
                $current_term_id = $quired_object->term_id;

                $terms = get_terms('category', array(
                    'hide_empty' => true,
                    'child_of' => $current_term_id,
                    'orderby'  => 'term_order',
                    'order'    => 'ASC'
                ));

                // This will return terms under the current term and then posts under those terms.
                $posts_and_terms = get_array_of_posts_grouped_by_taxterms($terms, $parent_id = $current_term_id );


                // Return any post if exiting under the current term
                $post_of_current_term_only = get_posts_per_taxterm('category', $current_term_id);


                // Merge everything
                // @toDo Find a way to merge by category and post order
                foreach ($post_of_current_term_only as $post){
                    $posts_and_terms[] = '<a class = "ke-toc-posts-post stm-content" href='.get_permalink($post->ID).' data-id="'.$post->ID.'">'.$post->post_title.'</a>';
                }
            }

        }
        else{ // @toDo remove this to a new widget `Related Posts`

            // It is a singular post so retrieve it's parent term and start building TOC starting from that term.
            global $post;
            $parent_term_id_of_current_post = get_the_terms($post, 'category')[0]->term_id;

            // Return any post if exiting under the current term
            $post_of_current_term_only = get_posts_per_taxterm('category', $parent_term_id_of_current_post);

            // Merge everything
            // @toDo Find a way to merge by category and post order
            foreach ($post_of_current_term_only  as $post){
                $posts_and_terms[] = '<a class = "ke-toc-posts-post stm-content" href='.get_permalink($post->ID).' data-id="'.$post->ID.'">'.$post->post_title.'</a>';
            }
        }

        echo '<ul style="display:none;" id="ke-toc-posts" class="ke-toc-posts ke-toc-posts-text" data-ajax-enable="'.$this->get_settings_for_display( 'ke-toc-posts-ajax-enable' ).'">';
        if (!empty($posts_and_terms)){
            echo convert_array_to_html_list($posts_and_terms);
            wp_reset_postdata();
        }
        echo '</ul>';


    }

    protected function _content_template(){

    }

}