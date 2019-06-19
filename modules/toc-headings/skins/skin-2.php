<?php

namespace KbElementor\Modules\TocHeadings\Skins;


class Skin_2 extends  Skin_Base{

	protected function _register_controls_actions() {
        parent::_register_controls_actions();
//		add_action( 'elementor/element/ke-toc-headings/style_list_skin_section/before_section_end', [ $this,'extra_styles_add' ] );
	}

	public function get_id() {
		return 'skin2';
	}

	public function get_title() {
		return __( 'Skin 2', 'toc-headings' );
	}

    public function extra_styles_add() {
    }

	public function render(){
        parent::render();
	}

}