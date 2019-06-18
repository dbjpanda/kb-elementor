<?php


namespace KbElementor\Modules\TocHeadings\Widgets;

use KbElementor\Modules\TocHeadings\Skins;
use KbElementor\Base\Base_Widget;
use Elementor\Controls_Manager;

// don't call the file directly
if ( !defined( 'ABSPATH' ) ) exit;


class Toc_Headings extends Base_Widget {

    public function get_name() {
        return 'toc-headings';
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

    public function get_script_depends() {
            return [
                'jquery-ui-draggable',
                'vendor-tocbot',
                'ke-toc-headings'
            ];
    }

    public function get_style_depends() {
        return [
            'ke-toc-headings'
        ];
    }

    protected $_has_template_content = false;

    protected function _register_skins() {

        $this->add_skin( new Skins\Skin_1( $this ) );
        $this->add_skin( new Skins\Skin_2( $this ) );
    }

    protected function _register_controls() {

        /**
         *  Content Tab : General
         */
        $this->start_controls_section(
            'content_general_section',
            [
                'label' => __( 'General', 'toc-headings' ),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'heading_selectors',
            [
                'label'    => __( 'Heading Tags', 'toc-headings' ),
                'type'     => Controls_Manager::SELECT2,
                'multiple' => true,
                'default'  => ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'],
                'options' => [
                    'h1' => esc_html__( 'H1', 'toc-headings' ),
                    'h2' => esc_html__( 'H2', 'toc-headings' ),
                    'h3' => esc_html__( 'H3', 'toc-headings' ),
                    'h4' => esc_html__( 'H4', 'toc-headings' ),
                    'h5' => esc_html__( 'H5', 'toc-headings' ),
                    'h6' => esc_html__( 'H6', 'toc-headings' ),
                ],
            ]
        );

        $this->add_responsive_control(
            'heading_offset',
            [
                'label'     => __( 'Heading Offset', 'toc-headings' ),
                'type'      => Controls_Manager::SLIDER,
                'default'   => [
                    'size' => 70,
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 500,
                    ],
                ]
            ]
        );

        $this->end_controls_section();

        /**
         *  Content Tab : Title
         */
        $this->start_controls_section(
            'content_title_section',
            [
                'label' => __( 'Title', 'toc-headings' ),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'toc_title',
            [
                'label'       => __( 'Title', 'toc-headings' ),
                'type'        => Controls_Manager::TEXT,
                'default'     => 'Table of Content',
                'label_block' => false,
            ]
        );

        $this->add_control(
            'title_alignment',
            [
                'label'   => __( 'Alignment', 'toc-headings' ),
                'type'    => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __( 'Left', 'toc-headings' ),
                        'icon'  => 'fa fa-align-left',
                    ],
                    'center' => [
                        'title' => __( 'Center', 'toc-headings' ),
                        'icon'  => 'fa fa-align-center',
                    ],
                    'right' => [
                        'title' => __( 'Right', 'toc-headings' ),
                        'icon'  => 'fa fa-align-right',
                    ],
                ],
                'default'     => 'center',
                'condition' => [
                    'toc_title!' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .ke-toc-title' => 'text-align: {{VALUE}};',
                ]
            ]
        );

        $this->end_controls_section();

        /**
         *  Content Tab : Button
         */
        $this->start_controls_section(
            'toggle_button',
            [
                'label' => __( 'Button', 'toc-headings' ),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_control(
            'is_toggle_button',
            [
                'label' => __( 'Toggle Button', 'toc-headings' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'label_on' => 'Enable',
                'label_off' => 'Disable',
            ]
        );

        $this->add_control(
            'active_toggle_button_class',
            [
                'label'       => __( 'Active Button', 'toc-headings' ),
                'type'        => Controls_Manager::ICON,
                'default'     => 'ke-hide-button',
                'label_block' => true,
                'condition' => [
                    'is_toggle_button' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'inactive_toggle_button_class',
            [
                'label'       => __( 'Inactive Button', 'toc-headings' ),
                'type'        => Controls_Manager::ICON,
                'default'     => 'ke-show-button',
                'label_block' => true,
                'condition' => [
                    'is_toggle_button' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'button_icon_indent',
            [
                'label' => __( 'Icon Spacing', 'toc-headings' ),
                'type'  => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'size' => 5,
                ],
                'condition' => [
                    'is_toggle_button!' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .ke-toc-title i' => 'margin-left: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        /**
         *  Content Tab : Scroll
         */
        $this->start_controls_section(
            'smooth_scroll',
            [
                'label' => __( 'Scroll', 'toc-headings' ),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_control(
            'smooth_scroll_enabled',
            [
                'label' => __( 'Smooth Scroll', 'toc-headings' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __( 'Enable'),
                'label_off' => __( 'Disable'),
                'default' => 'yes',
            ]
        );

        $this->add_responsive_control(
            'scroll_duration',
            [
                'label'     => __( 'Scroll Duration', 'toc-headings' ),
                'type'      => Controls_Manager::SLIDER,
                'default'   => [
                    'size' => 420,
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 5000,
                        'step' => 50,
                    ],
                ],
                'condition' => [
                    'smooth_scroll_enabled' => 'yes',
                ],
            ]
        );

        $this->end_controls_section();

        /**
         *  Content Tab : Additional
         */
        $this->start_controls_section(
            'content_additional_section',
            [
                'label' => __( 'Additional', 'toc-headings' ),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'stick_to_content',
            [
                'label' => __( 'Stick to content', 'toc-headings' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __( 'Enable'),
                'label_off' => __( 'Disable'),
                'default' => 'no',
                'description' => 'Experimental Feature. Use section navigator if you do not see edit button.'
            ]
        );

        $this->add_control(
            'stick_to_content_alignment',
            [
                'label'   => __( 'Alignment', 'toc-headings' ),
                'type'    => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __( 'Left', 'toc-headings' ),
                        'icon'  => 'fa fa-align-left',
                    ],
                    'right' => [
                        'title' => __( 'Right', 'toc-headings' ),
                        'icon'  => 'fa fa-align-right',
                    ],
                ],
                'default'     => 'left',
                'condition' => [
                    'stick_to_content' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'content_selector',
            [
                'label'       => __( 'Content Selector', 'toc-headings' ),
                'description'       => __( 'Which content area should be searched for headings tag. Provide a unique selector to override default one. Refresh to see changes.', 'toc-headings' ),
                'type'        => Controls_Manager::TEXT,
                'label_block' => true,
                'default'     => '.ke-toc-headings-content',
                'placeholder' => '.ke-toc-headings-content',
                'selectors' => [
                    '(desktop)' => '/*Kb Elementor workaround start*/ } /*Heading Offset*/ {{VALUE}} h1, {{VALUE}} h2, {{VALUE}} h3, {{VALUE}} h4, {{VALUE}} h5, {{VALUE}} h6 { padding-top: {{heading_offset.SIZE}}{{heading_offset.UNIT}}; margin-top: -{{heading_offset.SIZE}}{{heading_offset.UNIT}} } /*Focused Heading Outline*/ {{VALUE}} h1:focus, {{VALUE}} h2:focus, {{VALUE}} h3:focus, {{VALUE}} h4:focus, {{VALUE}} h5:focus, {{VALUE}} h6:focus{ outline:none; text-decoration: underline } { /*Kb Elementor workaround end*/',
                    '(tablet)' => '/*Kb Elementor workaround start*/ } /*Heading Offset*/ {{VALUE}} h1, {{VALUE}} h2, {{VALUE}} h3, {{VALUE}} h4, {{VALUE}} h5, {{VALUE}} h6 { padding-top: {{heading_offset_tablet.SIZE}}{{heading_offset_tablet.UNIT}}; margin-top: -{{heading_offset_tablet.SIZE}}{{heading_offset_tablet.UNIT}} } /*Focused Heading Outline*/ {{VALUE}} h1:focus, {{VALUE}} h2:focus, {{VALUE}} h3:focus, {{VALUE}} h4:focus, {{VALUE}} h5:focus, {{VALUE}} h6:focus{ outline:none; text-decoration: underline } { /*Kb Elementor workaround end*/',
                    '(mobile)' => '/*Kb Elementor workaround start*/ } /*Heading Offset*/ {{VALUE}} h1, {{VALUE}} h2, {{VALUE}} h3, {{VALUE}} h4, {{VALUE}} h5, {{VALUE}} h6 { padding-top: {{heading_offset_mobile.SIZE}}{{heading_offset_mobile.UNIT}}; margin-top: -{{heading_offset_mobile.SIZE}}{{heading_offset_mobile.UNIT}} } /*Focused Heading Outline*/ {{VALUE}} h1:focus, {{VALUE}} h2:focus, {{VALUE}} h3:focus, {{VALUE}} h4:focus, {{VALUE}} h5:focus, {{VALUE}} h6:focus{ outline:none; text-decoration: underline } { /*Kb Elementor workaround end*/',
                ],
            ]
        );

        $this->end_controls_section();

    }


}