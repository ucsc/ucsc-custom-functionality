<?php
/**
 * ACF field group base class.
 *
 * @package ucsc
 */

declare(strict_types=1);

namespace UCSC\Blocks\Blocks;

use UCSC\Blocks\Traits\With_Get_Field_Key;

/**
 * Base for every ACF field group the plugin registers.
 *
 * Each block is three coordinated pieces: this field group defines what the
 * editor sees, a controller in src/Components/ reads the saved values, and a
 * view in src/views/ renders them. Subclasses supply the four abstract methods
 * and are instantiated by Core after the block itself is registered.
 *
 * Field *names* are declared as constants on the subclass and referenced by
 * both the group and its controller, so the two never drift apart.
 */
abstract class ACF_Group {

	use With_Get_Field_Key;

	/**
	 * Whether this group should register.
	 *
	 * Lets a subclass opt out without being removed from Core's registry.
	 *
	 * @var bool
	 */
	public bool $enabled = true;

	/**
	 * Where the group appears, as ACF location rules.
	 *
	 * For blocks this is a single 'block' rule naming the registered block.
	 *
	 * @return array
	 */
	abstract protected function get_locations(): array;

	/**
	 * The group's title, shown in the editor sidebar.
	 *
	 * @return string
	 */
	abstract protected function get_title(): string;

	/**
	 * The group's ACF key.
	 *
	 * Field keys are composed from this via get_field_key(), so it must stay
	 * stable — changing it orphans every value already saved against it.
	 *
	 * @return string
	 */
	abstract protected function get_key(): string;

	/**
	 * The group's field definitions.
	 *
	 * @return array
	 */
	abstract protected function get_fields(): array;

	/**
	 * Register the field group with ACF.
	 *
	 * @return void
	 */
	public function init(): void {
		if ( ! $this->enabled ) {
			return;
		}

		acf_add_local_field_group( $this->get_group_args() );
	}

	/**
	 * Assemble the arguments for acf_add_local_field_group().
	 *
	 * @return array
	 */
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
