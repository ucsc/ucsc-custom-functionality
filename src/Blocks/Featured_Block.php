<?php declare(strict_types=1);

namespace UCSC\Blocks\Blocks;

use UCSC\Blocks\Blocks\Contracts\CTA_Field;
use UCSC\Blocks\Blocks\Traits\With_CTA_Field;

class Featured_Block extends Query_Loop implements CTA_Field {
    
    use With_CTA_Field;

	public const NAME = 'ucsc_featured_block';

    public const CTA_FIELD = 'featured_cta';
	
	protected string $default_manual_card_label = 'Article';
	
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
		return esc_html__( 'Featured Stories', 'ucsc' );
	}

	protected function get_key(): string {
		return self::NAME;
	}

	protected function get_fields(): array {
		return [
			$this->get_query_loop_group( self::NAME ),
            $this->get_cta_field( self::NAME, 'All News Link', self::CTA_FIELD ),
		];
	}
	
}
