<?php declare(strict_types=1);

namespace UCSC\Blocks\Blocks;

use UCSC\Blocks\Traits\With_Get_Field_Key;

abstract class ACF_Group {
	
	use With_Get_Field_Key;

	public bool $enabled = true;

	abstract protected function get_locations(): array;

	abstract protected function get_title(): string;

	abstract protected function get_key(): string;

	abstract protected function get_fields(): array;

	public function init(): void {
		if ( ! $this->enabled ) {
			return;
		}
		
		acf_add_local_field_group( $this->get_group_args() );
	}

	protected function get_group_args(): array {
		return [
			'key'                   => $this->get_key(),
			'title'                 => $this->get_title(),
			'menu_order'            => 0,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen'        => '',
			'fields'                => $this->get_fields(),
			'location'              => $this->get_locations(),
		];
	}
	
}
