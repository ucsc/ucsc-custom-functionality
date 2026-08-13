<?php
/**
 * Call-to-action field contract.
 *
 * @package ucsc
 */

declare(strict_types=1);

namespace UCSC\Blocks\Blocks\Contracts;

/**
 * Implemented by field groups offering a call-to-action link.
 */
interface CTA_Field {

	/**
	 * Default field name for the call to action.
	 *
	 * @var string
	 */
	public const CTA = 'cta';

	/**
	 * The call-to-action link field definition.
	 *
	 * @param string $group_name The owning group name, used to compose the field key.
	 * @param string $label      Editor-facing label.
	 * @param string $name       Field name; defaults to self::CTA when empty.
	 *
	 * @return array
	 */
	public function get_cta_field( string $group_name, string $label, string $name = '' ): array;
}
