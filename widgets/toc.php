<?php


namespace KbElementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Typography;
use Elementor\Scheme_Color;
use Elementor\Group_Control_Text_Shadow;



// don't call the file directly
if ( !defined( 'ABSPATH' ) ) exit;


class Toc extends Widget_Base {


    public function get_name() {
        return 'kb-elementor';
    }

    public function get_title() {
        return __( 'TOC', 'kb-elementor-toc' );
    }

    public function get_icon() {
        return 'fa fa-folder-open';
    }

    public function get_categories() {
        return [ 'general' ];
    }


    protected function _register_controls() {

        $this->start_controls_section(
            'elementor_kb_content',
            [
                'label' => __( 'Configuration', 'kb-elementor-toc' ),
            ]
        );

//        $this->add_control(
//            'title',
//            [
//                'label' => __( 'Title', 'kb-elementor-toc' ),
//                'type' => Controls_Manager::TEXT,
//            ]
//        );
//        $this->add_responsive_control(
//            'elementor_kb_columns',
//            [
//                'label' => __( 'Columns', 'kb-elementor-toc' ),
//                'type' => Controls_Manager::SELECT,
//                'default' => '4',
//                'tablet_default' => '2',
//                'mobile_default' => '1',
//                'options' => [
//                    '1' => '1',
//                    '2' => '2',
//                    '3' => '3',
//                    '4' => '4',
//                    '5' => '5',
//                    '6' => '6',
//                ],
//                'selectors' => [
//                    '{{WRAPPER}}.elementor-kb-term-href' => 'flex-basis: calc( 1 / {{VALUE}} * 100% );',
//                ],
//            ]
//        );
//



        $this->add_control(
            'elementor_kb_ajax_enable',
            [
                'label' => __( 'Ajax', 'kb-elementor-toc' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __( 'Enable', 'toc' ),
                'label_off' => __( 'Disable', 'toc' ),
                'return_value' => 'yes',
                'default' => 'no',
            ]
        );

        $this->end_controls_section();




        $this->start_controls_section(
            'section_title_style',
            [
                'label' => __( 'Text', 'kb-elementor-toc' ),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );


        $this->add_control(
            'elementor_kb_term_href_color',
            [
                'label' => __( 'Term Color', 'kb-elementor-toc' ),
                'type' => Controls_Manager::COLOR,
                'scheme' => [
                    'type' => Scheme_Color::get_type(),
                    'value' => Scheme_Color::COLOR_1,
                ],
                'selectors' => [
                    // Stronger selector to avoid section style from overwriting
                    '{{WRAPPER}} .elementor-kb-term-href' => 'color: {{VALUE}};',
                ],
            ]
        );


        $this->add_control(
            'elementor_kb_post__href_color',
            [
                'label' => __( 'Post Color', 'kb-elementor-toc' ),
                'type' => Controls_Manager::COLOR,
                'scheme' => [
                    'type' => Scheme_Color::get_type(),
                    'value' => Scheme_Color::COLOR_1,
                ],
                'selectors' => [
                    // Stronger selector to avoid section style from overwriting
                    '{{WRAPPER}} .elementor-kb-post-href' => 'color: {{VALUE}};',
                ],
            ]
        );



        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'typography',
                'label' => __( 'Typography', 'kb-elementor-toc' ),
                'scheme' => Scheme_Typography::TYPOGRAPHY_1,
                'selector' => '{{WRAPPER}} .elementor-kb-text',
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'text_shadow',
                'selector' => '{{WRAPPER}} .elementor-kb-text',
            ]
        );

        $this->end_controls_section();
    }


















    /**
     * Render Table of content
     */
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
                    $posts_and_terms[] = '<a class = "elementor-kb-post-href stm-content" href='.get_permalink($post->ID).' data-id="'.$post->ID.'">'.$post->post_title.'</a>';
                }
            }

        }
        else{

            // It is a singular post so retrieve it's parent term and start building TOC starting from that term.
            global $post;
            $parent_term_id_of_current_post = get_the_terms($post, 'category')[0]->term_id;

            // Return any post if exiting under the current term
            $post_of_current_term_only = get_posts_per_taxterm('category', $parent_term_id_of_current_post);

            // Merge everything
            // @toDo Find a way to merge by category and post order
            foreach ($post_of_current_term_only  as $post){
                $posts_and_terms[] = '<a class = "elementor-kb-post-href stm-content" href='.get_permalink($post->ID).' data-id="'.$post->ID.'">'.$post->post_title.'</a>';
            }
        }


        echo '<ul style="display:none;" id="elementor-kb" class="elementor-kb-text elementor-kb" data-ajax-enable="'.$this->get_settings_for_display( 'elementor_kb_ajax_enable' ).'">';
        if (!empty($posts_and_terms)){
            echo convert_array_to_html_list($posts_and_terms);
            wp_reset_postdata();
        }
        echo '</ul>';

    }







    /**
     *
     */
    protected function _content_template(){

    }







}