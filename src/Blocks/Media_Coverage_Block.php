<?php declare(strict_types=1);

namespace UCSC\Blocks\Blocks;

use UCSC\Blocks\Blocks\Contracts\CTA_Field;
use UCSC\Blocks\Blocks\Traits\With_CTA_Field;

class Media_Coverage_Block extends Query_Loop implements CTA_Field {
	
	use With_CTA_Field;

	public const NAME = 'ucsc_media_coverage_block';

	public const CTA_FIELD   = 'media_coverage_cta';
	public const TITLE_FIELD = 'media_coverage_title';
	
	protected string $default_manual_card_label = 'Media Coverage';
	protected array $allowed_post_types         = [ 'media_coverage', ];
	protected int $max_manual_cards             = 6;
	
	protected function get_locations(): array {
		return [
			[
				[
					'param'    => 'block',
					'operator' => '==',
					'value'    => 'ucsc-custom-functionality/media-coverage-block',
				],
			],
		];
	}

	protected function get_title(): string {
		return esc_html__( 'Media Coverage', 'ucsc' );
	}

	protected function get_key(): string {
		return self::NAME;
	}

	protected function get_fields(): array {
		return [
			$this->get_block_title_field(),
			$this->get_query_loop_group( self::NAME ),
			$this->get_cta_field( self::NAME, 'All Coverage Link', self::CTA_FIELD ),
		];
	}
	
	protected function get_block_title_field(): array {
		return [
			'type'  => 'text',
			'name'  => self::TITLE_FIELD,
			'key'   => $this->get_field_key( self::TITLE_FIELD, self::NAME ),
			'label' => esc_html__( 'Title', 'ucsc' ),
		];
	}
	
}
