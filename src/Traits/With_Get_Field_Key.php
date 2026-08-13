<?php
/**
 * ACF field key composition.
 *
 * @package ucsc
 */

declare(strict_types=1);

namespace UCSC\Blocks\Traits;

/**
 * Composes ACF field keys from a field name and its group name.
 */
trait With_Get_Field_Key {

	/**
	 * Build the ACF field key for a field within a group.
	 *
	 * The result is "{group}_{name}". ACF filters that target a specific field
	 * by key — the "acf/fields/.../query/key=" hooks in src/Hooks/ — depend on
	 * this exact composition, so changing the format breaks them silently.
	 *
	 * @param string $name       The field name, normally a class constant.
	 * @param string $group_name The owning field group's name.
	 *
	 * @return string The composed field key.
	 */
	public function get_field_key( string $name, string $group_name ): string {
		return sprintf( '%s_%s', $group_name, $name );
	}
}
