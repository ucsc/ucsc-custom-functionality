<?php declare(strict_types=1);

namespace UCSC\Blocks\Traits;

trait With_Get_Field_Key {
	
	public function get_field_key( string $name, string $group_name ): string {
		return sprintf( '%s_%s', $group_name, $name );
	}

}
