<?php declare(strict_types=1);
 
namespace UCSC\Blocks\Template;

use UCSC\Blocks\Template\Patterns\Home_Link;
use UCSC\Blocks\Template\Patterns\Post_Footer;
use UCSC\Blocks\Template\Patterns\Post_Header;

class Template_Subscriber {

	public function init(): void {
		$block_renderer = Blocks_Render::instance();
		
		add_filter( 'render_block', static function ( $block_content = '', $block = [] ) use ( $block_renderer ) {
			$block_content = $block_renderer->adjust_author_block( $block_content, $block );
			$block_content = $block_renderer->adjust_featured_image_block( $block_content, $block );
			
			return $block_content;
		}, 100, 2 );
		
		add_action( 'init', static function (): void {
			( new Home_Link() )->register();
			( new Post_Footer() )->register();
			( new Post_Header() )->register();
		}, 10, 0 );



		add_action( 'init', static function (): void {
			$post_type_object           = get_post_type_object( 'post' );
			$post_type_object->template = [
				[
					'core/pattern',
					[
						'slug' => 'ucsc-custom-functionality/post-header',
					],
				],
				[
					'core/paragraph',
					[
						'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc tincidunt nisi erat, sed dapibus sapien consequat ac. Morbi tincidunt, leo at sagittis commodo, diam nisl sollicitudin dolor, non mattis lectus felis at odio. Vivamus luctus vel ligula nec lacinia. Nullam sollicitudin diam a leo fermentum elementum. Proin varius molestie lacinia. Nulla velit neque, cursus id purus non, consequat hendrerit magna. Nulla sed dolor ut felis condimentum convallis. Nullam maximus tellus sed justo facilisis, id condimentum justo aliquam. Ut feugiat ligula eu nunc tristique, vitae tempus elit pretium. Maecenas id arcu sit amet neque lacinia finibus. Praesent in nisi in neque efficitur faucibus.',
					],
				],
				[
					'core/pattern',
					[
						'slug' => 'ucsc-custom-functionality/post-footer',
					],
				],
			];
		}, 10, 0 );
	}

}
