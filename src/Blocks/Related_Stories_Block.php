<?php
/**
 * Related stories block field group.
 *
 * @package ucsc
 */

declare(strict_types=1);

namespace UCSC\Blocks\Blocks;

/**
 * Field group for the Related Stories block.
 *
 * A query-loop block with no additional fields, capped at three stories.
 */
class Related_Stories_Block extends Query_Loop {

	/**
	 * Field group name and key.
	 *
	 * @var string
	 */
	public const NAME = 'ucsc_related_stories_block';

	/**
	 * Maximum number of manual cards.
	 *
	 * @var int
	 */
	protected int $max_manual_cards = 3;

	/**
	 * Label for a single card in manual mode.
	 *
	 * @var string
	 */
	protected string $default_manual_card_label = 'Story';

	/**
	 * Attach the group to the Related Stories block.
	 *
	 * @return array
	 */
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

	/**
	 * Field group title.
	 *
	 * @return string
	 */
	protected function get_title(): string {
		return esc_html__( 'Related Stories', 'ucsc' );
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
	 * The query selection fields.
	 *
	 * @return array
	 */
	protected function get_fields(): array {
		return [
			$this->get_query_loop_group( self::NAME ),
		];
	}
}
