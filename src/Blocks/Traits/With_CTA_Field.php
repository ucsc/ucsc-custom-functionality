<?php
/**
 * Shared call-to-action field definition.
 *
 * @package ucsc
 */

declare(strict_types=1);

namespace UCSC\Blocks\Blocks\Traits;

/**
 * Supplies the call-to-action link field for the CTA_Field contract.
 */
trait With_CTA_Field {

	/**
	 * The call-to-action link field.
	 *
	 * ACF link fields return an array of title, url and target.
	 *
	 * @param string $group_name The owning group name, used to compose the field key.
	 * @param string $label      Editor-facing label.
	 * @param string $name       Field name; falls back to self::CTA when empty.
	 *
	 * @return array
	 */
	public function get_cta_field( string $group_name, string $label, string $name = '' ): array {
		return [
			'label' => $label,
			'type'  => 'link',
			'name'  => $name ?: self::CTA,
			'key'   => $this->get_field_key( $name, $group_name ),
		];
	}
}
