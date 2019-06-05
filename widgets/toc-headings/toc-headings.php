<?php


namespace KbElementor\Widgets;

require_once( __DIR__ . '/functions.php' );

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Typography;
use Elementor\Scheme_Color;
use Elementor\Group_Control_Text_Shadow;


// don't call the file directly
if ( !defined( 'ABSPATH' ) ) exit;


class TocHeadings extends Widget_Base {

    public function get_name() {
        return 'ke-toc-headings';
    }

    public function get_title() {
        return __( 'TOC Headings', 'kb-elementor' );
    }

    public function get_icon() {
        return 'fa fa-th-list';
    }

    public function get_keywords() {
        return [ 'table', 'content', 'index' ];
    }

    public function get_categories() {
        return [ 'kb-elementor' ];
    }

    public function get_script_depends() {
            return [
                'kb-elementor-toc-headings'
            ];
    }

    public function get_style_depends() {
        return [
            'kb-elementor-toc-headings'
        ];
    }

    protected function _register_controls() {

        $this->start_controls_section(
            'ke-toc-headings-content',
            [
                'label' => __( 'Configuration', 'ke-toc-headings' ),
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'ke-toc-headings-text',
            [
                'label' => __( 'Text', 'ke-toc-headings' ),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );


        $this->add_control(
            'ke-toc-headings-h1-color',
            [
                'label' => __( 'H1 Color', 'ke-toc-headings' ),
                'type' => Controls_Manager::COLOR,
                'scheme' => [
                    'type' => Scheme_Color::get_type(),
                    'value' => Scheme_Color::COLOR_1,
                ],
                'selectors' => [
                    // Stronger selector to avoid section style from overwriting
                    '{{WRAPPER}} .ke-toc-headings-h1' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'typography',
                'label' => __( 'Typography', 'ke-toc-headings' ),
                'scheme' => Scheme_Typography::TYPOGRAPHY_1,
                'selector' => '{{WRAPPER}} .ke-toc-headings-text',
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'text_shadow',
                'selector' => '{{WRAPPER}} .ke-toc-headings-text',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {

        echo 'test';

    }

    protected function _content_template(){

    }

}