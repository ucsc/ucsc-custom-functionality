<?php declare(strict_types=1);

namespace UCSC\Blocks\Template\Patterns;

abstract class Pattern {
	
	public const SLUG      = '';
	public const NAMESPACE = 'ucsc-custom-functionality';
	
	abstract public function get_args(): array;

	public function register(): void {
		register_block_pattern( '/home-link', $this->get_args() );
	}
	
	protected function build_pattern_name(): string {
		return sprintf( '%s/%s', self::NAMESPACE, static::SLUG );
	}

}
