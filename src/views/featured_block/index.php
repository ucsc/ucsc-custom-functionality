<?php declare(strict_types=1);

use UCSC\Blocks\Components\Featured_Block_Controller;

/**
 * @var array $block current block attributes
 */
$c = new Featured_Block_Controller( $block );

$items = $c->get_items();

if ( empty( $items ) && is_admin() ) {
	?>
	<section <?php echo wp_kses_data( get_block_wrapper_attributes() ); ?>>
		<h3 class="ucsc-featured-block__header-title">
			<?php echo esc_html__( 'Please configure the Featured Block in order to populate content', 'ucsc' ); ?>
		</h3>
	</section>
	<?php

	return;
}

?>
<section <?php echo wp_kses_data( get_block_wrapper_attributes() ); ?>>
	<?php foreach ( $items as $key => $item ) :  ?>
		<?php if ( ! empty( $item['image'] ) && $item['image']['id'] > 0 ) : ?>
			<?php $image_alt = get_post_meta( $item['image']['id'], '_wp_attachment_image_alt', true ); ?>
			<img 
				src="<?php echo esc_url( $item['image']['url'] );?>" 
				srcset="<?php echo $c->build_srcset( $item['image'] );?>" 
				class="ucsc-featured-block__card-image"
				alt="<?php echo ! empty( $image_alt ) ? esc_attr( get_post_meta( $item['image']['id'], '_wp_attachment_image_alt' )[0] ) : $item['title']; ?>"
			/>
		<?php endif; ?>
		<?php if ( ! empty( $item['category'] ) ) : ?>
			<a href="<?php echo get_category_link( $item['category'] ); ?>">
				<?php echo $item['category']->name ?>
			</a>
		<?php endif; ?>
		<h2>
			<?php echo $item['title']; ?>
		</h2>

		<?php if ( $key === 0 && ! empty( $item['excerpt'] ) ) : ?>
			<?php echo $item['excerpt']; ?>
		<?php endif; ?>
	<?php endforeach; ?>
	<?php echo $c->get_cta(); ?>
</section>
