<?php declare(strict_types=1);

namespace UCSC\Blocks\Blocks\Traits;

trait With_CTA_Field {
	
	public function get_cta_field( string $group_name, string $label, string $name = '' ): array {
		return [
			'label' => $label,
			'type'  => 'link',
			'name'  => $name ?: self::CTA,
			'key'   => $this->get_field_key( $name, $group_name ),
		];
	}

}
