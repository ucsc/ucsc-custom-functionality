<?php declare(strict_types=1);

namespace UCSC\Blocks\Blocks;

class Related_Stories_Block extends Query_Loop {

	public const NAME = 'ucsc_related_stories_block';
    
    protected int $max_manual_cards = 3;
	
	protected string $default_manual_card_label = 'Story';
	
	protected function get_locations(): array {
		return [
			[
				[
					'param'    => 'block',
					'operator' => '==',
					'value'    => 'ucsc-custom-functionality/related-stories-block',
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
		];
	}
	
}
