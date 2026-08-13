<?php
/**
 * Media coverage block field group.
 *
 * @package ucsc
 */

declare(strict_types=1);

namespace UCSC\Blocks\Blocks;

use UCSC\Blocks\Blocks\Contracts\CTA_Field;
use UCSC\Blocks\Blocks\Traits\With_CTA_Field;

/**
 * Field group for the Media Coverage block.
 *
 * A query-loop block over the media_coverage post type rather than posts,
 * with a heading and a call-to-action link.
 */
class Media_Coverage_Block extends Query_Loop implements CTA_Field {

	use With_CTA_Field;

	/**
	 * Field group name and key.
	 *
	 * @var string
	 */
	public const NAME = 'ucsc_media_coverage_block';

	/**
	 * Call-to-action field name.
	 *
	 * @var string
	 */
	public const CTA_FIELD = 'media_coverage_cta';
	/**
	 * Heading field name.
	 *
	 * @var string
	 */
	public const TITLE_FIELD = 'media_coverage_title';

	/**
	 * Label for a single card in manual mode.
	 *
	 * @var string
	 */
	protected string $default_manual_card_label = 'Media Coverage';
	/**
	 * Manual mode selects media coverage entries, not posts.
	 *
	 * @var string[]
	 */
	protected array $allowed_post_types = [ 'media_coverage' ];
	/**
	 * Maximum number of manual cards.
	 *
	 * @var int
	 */
	protected int $max_manual_cards = 6;

	/**
	 * Attach the group to the Media Coverage block.
	 *
	 * @return array
	 */
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

	/**
	 * Field group title.
	 *
	 * @return string
	 */
	protected function get_title(): string {
		return esc_html__( 'Media Coverage', 'ucsc' );
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
	 * The heading, query selection fields and coverage link.
	 *
	 * @return array
	 */
	protected function get_fields(): array {
		return [
			$this->get_block_title_field(),
			$this->get_query_loop_group( self::NAME ),
			$this->get_cta_field( self::NAME, 'All Coverage Link', self::CTA_FIELD ),
		];
	}

	/**
	 * Heading shown above the coverage list.
	 *
	 * @return array
	 */
	protected function get_block_title_field(): array {
		return [
			'type'  => 'text',
			'name'  => self::TITLE_FIELD,
			'key'   => $this->get_field_key( self::TITLE_FIELD, self::NAME ),
			'label' => esc_html__( 'Title', 'ucsc' ),
		];
	}
}
