<?php declare(strict_types=1);

namespace UCSC\Blocks\Components;

use UCSC\Blocks\Blocks\Post_Header_Block;
use UCSC\Blocks\Components\Traits\With_Primary_Term;

class Post_Header_Block_Controller {
	
	use With_Primary_Term;
	
	protected array $block;

	public function __construct( $block ) {
		$this->block = (array) $block;
	}

	public function get_attributes(): string {
		return wp_kses_data( get_block_wrapper_attributes([
			'class' => implode(' ', [
				'ucsc-post-header-block',
				'alignfull',
				'has-ucsc-primary-blue-background-color',
				'has-white-color',
				'has-global-padding',
				'is-layout-constrained',
			]),
		]) );
	}
	
	public function get_layout() {
		return get_field( Post_Header_Block::LAYOUT ) ?? Post_Header_Block::LAYOUT_BIG;
	}
	
	public function get_primary_category(): ?string {
		$category = $this->get_primary_term( get_the_ID() );
		
		if ( empty( $category ) ) {
			return null;
		}
		
		return (string) $category->name;
	}

	public function get_image(): array {
		$thumbnail_id = get_post_thumbnail_id( get_the_ID() );
		if ( $thumbnail_id === false ) {
			return [];
		}

		$thumbnail = get_post( $thumbnail_id );

		return [
			'image'       => get_the_post_thumbnail( get_the_ID() ),
			'description' => $thumbnail->post_excerpt,
			'attribution' => $thumbnail->post_content,
		];
	}
	
}
