<?php
/**
 * Post header block controller.
 *
 * @package ucsc
 */

declare(strict_types=1);

namespace UCSC\Blocks\Components;

use UCSC\Blocks\Blocks\Post_Header_Block;
use UCSC\Blocks\Components\Traits\With_Primary_Term;

/**
 * Prepares the post header's layout, category and featured image.
 *
 * The image caption and credit are read from the attachment's excerpt and
 * content fields, which is where WordPress stores them.
 */
class Post_Header_Block_Controller {

	use With_Primary_Term;

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
	 * The block's wrapper attributes, varying with the chosen layout.
	 *
	 * @return string
	 */
	public function get_attributes(): string {
		$classes = [
			'ucsc-post-header-block',
			'alignfull',
		];

		if ( $this->is_horizontal_layout() ) {
			$classes[] = 'ucsc-post-header-block--horizontal';
		}

		return wp_kses_data(
			get_block_wrapper_attributes(
				[
					'class' => implode( ' ', $classes ),
				]
			)
		);
	}

	/**
	 * Whether the small-image layout is selected.
	 *
	 * The small-image treatment lays the header out horizontally.
	 *
	 * @return bool
	 */
	public function is_horizontal_layout(): bool {
		return $this->get_layout() === Post_Header_Block::LAYOUT_SMALL;
	}

	/**
	 * The post's primary category name, or null.
	 *
	 * @return string|null
	 */
	public function get_primary_category(): ?string {
		$category = $this->get_primary_term( get_the_ID() );

		if ( empty( $category ) ) {
			return null;
		}

		return (string) $category->name;
	}

	/**
	 * The featured image with its caption and credit, or an empty array.
	 *
	 * @return array
	 */
	public function get_image(): array {
		$thumbnail_id = get_post_thumbnail_id( get_the_ID() );
		if ( empty( $thumbnail_id ) ) {
			return [];
		}

		$thumbnail = get_post( $thumbnail_id );

		return [
			'image'       => get_the_post_thumbnail( get_the_ID() ),
			'description' => $thumbnail->post_excerpt,
			'attribution' => $thumbnail->post_content,
		];
	}

	/**
	 * The chosen layout, defaulting to the small image.
	 *
	 * @return mixed
	 */
	protected function get_layout() {
		return get_field( Post_Header_Block::LAYOUT ) ?? Post_Header_Block::LAYOUT_SMALL;
	}
}
