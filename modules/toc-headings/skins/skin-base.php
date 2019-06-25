<?php

namespace KbElementor\Modules\TocHeadings\Skins;

use Elementor\Controls_Manager;
use Elementor\Skin_Base as Elementor_Skin_Base;
use Elementor\Widget_Base;
use Elementor\Scheme_Color;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Typography;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

abstract class Skin_Base extends Elementor_Skin_Base {

    protected function _register_controls_actions() {
        add_action( 'elementor/element/toc-headings/content_general_section/before_section_end', [ $this, 'register_content_controls' ] );
        add_action( 'elementor/element/toc-headings/content_general_section/after_section_end', [ $this, 'register_style_controls' ] );
    }

    public function register_content_controls( Widget_Base $widget ) {
        $this->parent = $widget;
    }

	public function register_style_controls( Widget_Base $widget ) {
        $this->parent = $widget;

        /**
         *  Style Tab : List
         */
        $this->start_controls_section(
            'style_list_section_1',
            [
                'label' => __( 'List', 'toc-headings' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'list_notation_style',
            [
                'label' => __( 'Notation style', 'toc-headings' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'ke-toc-list-float',
                'options' => [
                    'ke-toc-list-none' => __( 'None', 'toc-headings' ),
                    'ke-toc-list-decimal' => __( 'Decimal', 'toc-headings' ),
                    'ke-toc-list-roman' => __( 'Roman', 'toc-headings' ),
                    'ke-toc-list-float' => __( 'Float', 'toc-headings' ),
                ],
            ]
        );

        $this->add_control(
            'list_notation_color',
            [
                'label' => __( 'Notation color', 'toc-headings' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#7a7a7a',
                'scheme' => [
                    'type' => Scheme_Color::get_type(),
                    'value' => Scheme_Color::COLOR_1,
                ],
                'selectors' => [
                    '{{WRAPPER}} .toc-list-item' => 'color: {{VALUE}}',
                ],
            ]
        );
	
	$this->add_control(
            'active_notation_color',
            [
                'label' => __( 'Active notation color', 'toc-headings' ),
                'type' => Controls_Manager::COLOR,
                'separator' => 'after',
		'default' => '#67d34a',
                'scheme' => [
                    'type' => Scheme_Color::get_type(),
                    'value' => Scheme_Color::COLOR_1,
                ],
                'selectors' => [
                    // Stronger selector to avoid section style from overwriting
                    '{{WRAPPER}} .is-active-li' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'list_text_typography',
                'label' => __( 'Typography', 'toc-headings' ),
                'scheme' => Scheme_Typography::TYPOGRAPHY_1,
                'selector' => '{{WRAPPER}} .toc-link, {{WRAPPER}} .toc-list-item'
            ]
        );

        $this->add_control(
            'list_text_color',
            [
                'label' => __( 'Text Color', 'toc-headings' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#333333',
                'scheme' => [
                    'type' => Scheme_Color::get_type(),
                    'value' => Scheme_Color::COLOR_1,
                ],
                'selectors' => [
                    // Stronger selector to avoid section style from overwriting
                    '{{WRAPPER}} .toc-link' => 'color: {{VALUE}};',
                ],
            ]
        );
	
	$this->add_control(
            'active_text_color',
            [
                'label' => __( 'Active text color', 'toc-headings' ),
                'type' => Controls_Manager::COLOR,
                'separator' => 'after',
                'default' => '#67d34a',
                'scheme' => [
                    'type' => Scheme_Color::get_type(),
                    'value' => Scheme_Color::COLOR_1,
                ],
                'selectors' => [
                    // Stronger selector to avoid section style from overwriting
                    '{{WRAPPER}} .is-active-link' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'list_bg_color',
            [
                'label' => __( 'Background Color', 'toc-headings' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#ffffff',
                'scheme' => [
                    'type' => Scheme_Color::get_type(),
                    'value' => Scheme_Color::COLOR_1,
                ],
                'selectors' => [
                    // Stronger selector to avoid section style from overwriting
                    '{{WRAPPER}} .ke-toc-headings-nav' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'list_text_shadow',
                'label' => __( 'Shadow', 'toc-headings' ),
                'selector' => '{{WRAPPER}} .toc-link',
            ]
        );

        $this->add_responsive_control(
            'list_indent',
            [
                'label'     => __( 'Indent', 'toc-headings' ),
                'type'      => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em', '%' ],
                'default'   => [
                    'size' => 20,
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 200,
                    ],
                ],
                'selectors' => [
                    '(desktop) .ke-toc-headings-nav ul:not(:first-child)' => 'padding-left: {{SIZE}}{{UNIT}};',
                    '(tablet) .ke-toc-headings-nav ul:not(:first-child)' => 'padding-left: {{SIZE}}{{UNIT}};',
                    '(mobile) .ke-toc-headings-nav ul:not(:first-child)' => 'padding-left: {{SIZE}}{{UNIT}};',
                ]
            ]
        );

        $this->add_responsive_control(
            'list_ul_padding',
            [
                'label' => __( 'Padding', 'toc-headings' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'default'   => [
                    'size' => [
                            'left' => 12,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .ke-toc-headings-nav > ul' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        /**
         *  Style Tab : Title
         */
        $this->start_controls_section(
            'style_title_section',
            [
                'label' => __( 'Title', 'toc-headings' ),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'toc_title!' => '',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'label' => __( 'Typography', 'toc-headings' ),
                'scheme' => Scheme_Typography::TYPOGRAPHY_1,
                'selector' => '{{WRAPPER}} .ke-toc-title'
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => __( 'Color', 'toc-headings' ),
                'default' => '#262626',
                'type' => Controls_Manager::COLOR,
                'scheme' => [
                    'type' => Scheme_Color::get_type(),
                    'value' => Scheme_Color::COLOR_1,
                ],
                'selectors' => [
                    // Stronger selector to avoid section style from overwriting
                    '{{WRAPPER}} .ke-toc-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'title_bg_color',
            [
                'label' => __( 'Background Color', 'toc-headings' ),
                'default' => '#ffffff',
                'type' => Controls_Manager::COLOR,
                'scheme' => [
                    'type' => Scheme_Color::get_type(),
                    'value' => Scheme_Color::COLOR_1,
                ],
                'selectors' => [
                    // Stronger selector to avoid section style from overwriting
                    '{{WRAPPER}} .ke-toc-title' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'title_text_shadow',
                'label' => __( 'Shadow', 'toc-headings' ),
                'selector' => '{{WRAPPER}} .ke-toc-title',
            ]
        );

        $this->add_responsive_control(
            'title_margin',
            [
                'label' => __( 'Margin', 'toc-headings' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'default'   => [
                    'size' => [
                        'bottom' => 2,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .ke-toc-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        /**
         *  Style Tab : Button
         */
        $this->start_controls_section(
            'style_button_section',
            [
                'label' => __( 'Button', 'toc-headings' ),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'is_toggle_button' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'button_color',
            [
                'label' => __( 'Color', 'toc-headings' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#ff0000',
                'scheme' => [
                    'type' => Scheme_Color::get_type(),
                    'value' => Scheme_Color::COLOR_1,
                ],
                'selectors' => [
                    // Stronger selector to avoid section style from overwriting
                    '{{WRAPPER}} .ke-toc-title > i' => 'color: {{VALUE}};',
                ]
            ]
        );

        $this->add_responsive_control(
            'button_size',
            [
                'label'     => __( 'Size', 'toc-headings' ),
                'type'      => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em', '%' ],
                'default'   => [
                    'size' => 10,
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 200,
                    ],
                ],
                'selectors' => [
                    '(desktop) .ke-toc-title > i' => 'font-size: {{SIZE}}{{UNIT}};',
                    '(tablet) .ke-toc-title > i' => 'font-size: {{SIZE}}{{UNIT}};',
                    '(mobile) .ke-toc-title > i' => 'font-size: {{SIZE}}{{UNIT}};',
                ]
            ]
        );

        $this->end_controls_section();

    }

    private function get_skin_settings($control_id){
        return $this->parent->get_settings_for_display()[ $this->get_id().'_' .$control_id];
    }


    public function render() {

        $settings = $this->parent->get_settings_for_display();

        $this->parent->add_render_attribute(
            'wrapper',
            [
                'class' => [ 'ke-toc-headings-nav', $this->get_skin_settings('list_notation_style')],
                'data-content-selector' => $settings['content_selector'],
                'data-heading-selectors' => $settings['heading_selectors'],
                'data-heading-offset' => $settings['heading_offset']['size'],
                'data-stick-to-content' => $settings['stick_to_content'],
                'data-stick-to-content-alignment' => $settings['stick_to_content_alignment'],
                'data-smooth-scroll-enabled' => $settings['smooth_scroll_enabled'],
                'data-scroll-duration' => $settings['scroll_duration']['size'],
                'data-title' => $settings['toc_title'],
                'data-title-icon-active' => $settings['active_toggle_button_class'],
                'data-title-icon-inactive' => $settings['inactive_toggle_button_class']
            ]
        );

        ?>
            <div <?php echo $this->parent->get_render_attribute_string( 'wrapper' ); ?> ></div>
        <?php

    }


}
