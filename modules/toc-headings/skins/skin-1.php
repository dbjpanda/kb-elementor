<?php

namespace KbElementor\Modules\TocHeadings\Skins;


class Skin_1 extends  Skin_Base {

	protected function _register_controls_actions() {
        parent::_register_controls_actions();
	}

	public function get_id() {
		return 'skin1';
	}

	public function get_title() {
		return __( 'Skin 1', 'toc-headings' );
	}

    public function extra_styles_add() {
    }


	public function render(){
        parent::render();
	}

}