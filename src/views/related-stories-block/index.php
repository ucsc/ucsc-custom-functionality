<?php declare(strict_types=1);

use UCSC\Blocks\Components\Related_Stories_Block_Controller;

/**
 * @var array $block current block attributes
 */
$c = new Related_Stories_Block_Controller( $block );

$items = $c->get_items();

if ( empty( $items ) && is_admin() ) {
	?>
	<section <?php echo wp_kses_data( get_block_wrapper_attributes() ); ?>>
		<h3 class="ucsc-related-stories-block__header-title">
			<?php echo esc_html__( 'Select stories or a taxonomy term in the Related Stories block settings', 'ucsc' ); ?>
		</h3>
	</section>
	<?php

	return;
}

if ( empty( $items ) ) {
	return;
}

?>
<aside <?php echo $c->get_attributes(); ?>>
	<h2 class="ucsc-related-stories-block__title has-ucsc-primary-blue-color has-five-font-size">
		<?php echo esc_html__( 'Related Stories', 'ucsc' ); ?>
	</h2>
	<div class="ucsc-related-stories-block__inner">
		<?php foreach ( $items as $key => $item ) :  ?>
		<a href="<?php echo esc_url( get_the_permalink( $item['id'] ) ); ?>" class="ucsc-related-stories-block__card-link">
			<article class="ucsc-related-stories-block__card">
				<?php if ( ! empty( $item['image'] ) && $item['image']['id'] > 0 ) : ?>
				<div class="ucsc-related-stories-block__card-image">
					<?php $image_alt = get_post_meta( $item['image']['id'], '_wp_attachment_image_alt', true ); ?>
					<img 
						src="<?php echo esc_url( $item['image']['url'] );?>" 
						srcset="<?php echo $c->build_srcset( $item['image'] );?>" 
						class="ucsc-related-stories-block__featured-image"
						alt="<?php echo ! empty( $image_alt ) ? esc_attr( get_post_meta( $item['image']['id'], '_wp_attachment_image_alt' )[0] ) : $item['title']; ?>"
					/>
				</div>
				<?php endif; ?>

				<hgroup>
					<?php if ( ! empty( $item['category'] ) ) : ?>
					<p class="ucsc-related-stories-block__card-category">
						<?php echo $item['category']->name ?>
					</p>
					<?php endif; ?>

					<h3 class="ucsc-related-stories-block__card-title has-two-font-size">
						<?php echo esc_html( $item['title'] ); ?>
					</h3>
				</hgroup>

				<time class="ucsc-related-stories-block__card-date" datetime="<?php echo get_the_date( 'Y-m-d', $item['id'] ); ?>">
					<?php echo get_the_date( 'F j, Y', $item['id'] ); ?>
				</time>
			</article>
		</a>
		<?php endforeach; ?>
	</div>
</aside>
