<?php declare(strict_types=1);

namespace UCSC\Blocks\Blocks;

class Featured_Block extends Query_Loop {

	public const NAME = 'ucsc_featured_block';
	
	protected function get_locations(): array {
		return [
			[
				[
					'param'    => 'block',
					'operator' => '==',
					'value'    => 'ucsc-custom-functionality/featured-block',
				],
			],
		];
	}

	protected function get_title(): string {
		return esc_html__( 'Featured Block', 'ucsc' );
	}

	protected function get_key(): string {
		return self::NAME;
	}

	protected function get_fields(): array {
		return [
            $this->get_query_loop_group( self::NAME )
        ];
	}
	
}
