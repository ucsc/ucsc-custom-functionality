<?php declare(strict_types=1);

namespace UCSC\Blocks\Blocks\Contracts;

interface CTA_Field {
	
	public const CTA = 'cta';
	
	public function get_cta_field( string $group_name, string $label, string $name = '' ): array;

}
