<?php declare(strict_types=1);

namespace UCSC\Blocks\Post_Types;

abstract class Post_Types {
	
	public const NAME = '';
	
	abstract public function get_args(): array;

	public function register(): void {
		register_post_type( static::NAME, $this->get_args() );
	}

}
