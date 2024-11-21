<?php declare(strict_types=1);
 
namespace UCSC\Blocks\Template;

class Template_Subscriber {

	public function init(): void {
		$block_renderer = Blocks_Render::instance();
		
		add_filter( 'render_block', static function ( $block_content = '', $block = [] ) use ( $block_renderer ) {
			$block_content = $block_renderer->adjust_author_block( $block_content, $block );
			$block_content = $block_renderer->adjust_featured_image_block( $block_content, $block );
			
			return $block_content;
		}, 100, 2 );
	}

}
