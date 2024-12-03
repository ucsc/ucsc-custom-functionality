<?php declare(strict_types=1);

use UCSC\Blocks\Components\Post_Header_Block_Controller;

/**
 * @var array $block current block attributes
 */
$c            = new Post_Header_Block_Controller( $block );
$primary_term = $c->get_primary_category();
?>
<section <?php echo wp_kses_data( get_block_wrapper_attributes() ); ?>>
	<a href="<?php echo get_bloginfo( 'url' ); ?>">
		<?php echo esc_html__( 'Home', 'ucsc' ); ?>
	</a>
	<?php if ( ! empty( $primary_term ) ) : ?>
		<span>
			<?php echo $primary_term ?>
		</span>
	<?php endif; ?>
	<h1>
		<?php echo get_the_title( get_the_ID() ); ?>
	</h1>
	<div>
		<?php echo get_the_excerpt( get_the_ID() ); ?>
	</div>
	<?php echo get_the_date( 'F j, Y', get_the_ID() ); ?>
	<?php echo do_blocks( '<!-- wp:post-author-name /-->' ); ?>
	<?php if ( has_post_thumbnail( get_the_ID() ) ) : ?>
		<?php echo get_the_post_thumbnail( get_the_ID() ); ?>
	<?php endif; ?>
</section>
