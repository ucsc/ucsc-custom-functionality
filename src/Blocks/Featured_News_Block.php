<?php
/**
 * Featured news block field group.
 *
 * @package ucsc
 */

declare(strict_types=1);

namespace UCSC\Blocks\Blocks;

use UCSC\Blocks\Blocks\Contracts\CTA_Field;
use UCSC\Blocks\Blocks\Traits\With_CTA_Field;

/**
 * Field group for the Featured Stories block.
 *
 * A query-loop block with an added call-to-action link.
 */
class Featured_News_Block extends Query_Loop implements CTA_Field {

	use With_CTA_Field;

	/**
	 * Field group name and key.
	 *
	 * @var string
	 */
	public const NAME = 'ucsc_featured_news_block';

	/**
	 * Call-to-action field name.
	 *
	 * @var string
	 */
	public const CTA_FIELD = 'featured_cta';

	/**
	 * Label for a single card in manual mode.
	 *
	 * @var string
	 */
	protected string $default_manual_card_label = 'Article';

	/**
	 * Attach the group to the Featured News block.
	 *
	 * @return array
	 */
	protected function get_locations(): array {
		return [
			[
				[
					'param'    => 'block',
					'operator' => '==',
					'value'    => 'ucsc-custom-functionality/featured-news-block',
				],
			],
		];
	}

	/**
	 * Field group title.
	 *
	 * @return string
	 */
	protected function get_title(): string {
		return esc_html__( 'Featured Stories', 'ucsc' );
	}

	/**
	 * Field group key.
	 *
	 * @return string
	 */
	protected function get_key(): string {
		return self::NAME;
	}

	/**
	 * The query selection fields plus an all-news link.
	 *
	 * @return array
	 */
	protected function get_fields(): array {
		return [
			$this->get_query_loop_group( self::NAME ),
			$this->get_cta_field( self::NAME, 'All News Link', self::CTA_FIELD ),
		];
	}
}
