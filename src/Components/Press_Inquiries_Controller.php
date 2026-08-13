<?php
/**
 * Press inquiries block controller.
 *
 * @package ucsc
 */

declare(strict_types=1);

namespace UCSC\Blocks\Components;

use UCSC\Blocks\Blocks\Press_Inquiries_Block;

/**
 * Prepares the Press Inquiries block's contacts and assets.
 *
 * Entirely authored content; no querying.
 */
class Press_Inquiries_Controller {

	/**
	 * The block instance passed to the render callback.
	 *
	 * @var array
	 */
	protected array $block;

	/**
	 * Store the block instance.
	 *
	 * @param mixed $block The block instance supplied by the render callback.
	 */
	public function __construct( $block ) {
		$this->block = (array) $block;
	}

	/**
	 * The block's wrapper attributes.
	 *
	 * @return string
	 */
	public function get_attributes(): string {
		return wp_kses_data(
			get_block_wrapper_attributes(
				[
					'class' => implode(
						' ',
						[
							'ucsc-press-inquiries-block',
							'alignfull',
							'is-layout-constrained',
							'has-global-padding',
						]
					),
				]
			)
		);
	}

	/**
	 * The press contacts, or an empty array.
	 *
	 * @return array
	 */
	public function get_press_contacts(): array {
		$contacts = get_field( Press_Inquiries_Block::PRESS_INQUIRIES );

		if ( empty( $contacts ) ) {
			return [];
		}

		return $contacts;
	}

	/**
	 * A unique id for the block's collapsible panel.
	 *
	 * Derived from the block instance id so several blocks on one page do not
	 * collide.
	 *
	 * @return string
	 */
	public function get_panel_id(): string {
		return esc_attr( 'press-inquiries-block-' . $this->block['id'] );
	}

	/**
	 * The brief.
	 *
	 * @return string
	 */
	public function get_media_text(): string {
		return (string) get_field( Press_Inquiries_Block::MEDIA_TEXT );
	}

	/**
	 * URL of the downloadable paper, or an empty string.
	 *
	 * @return string
	 */
	public function get_media_file(): string {
		return (string) get_field( Press_Inquiries_Block::MEDIA_FILE );
	}

	/**
	 * URL of the downloadable image, or an empty string.
	 *
	 * @return string
	 */
	public function get_media_image(): string {
		return (string) get_field( Press_Inquiries_Block::MEDIA_IMAGE );
	}
}
