<?php declare(strict_types=1);

use UCSC\Blocks\Components\Featured_News_Block_Controller;

/**
 * @var array $block current block attributes
 */
$c = new Featured_News_Block_Controller( $block );

$items = $c->get_items();

if ( empty( $items ) && is_admin() ) {
	?>
	<section <?php echo wp_kses_data( get_block_wrapper_attributes() ); ?>>
		<h3 class="ucsc-featured-news-block__header-title">
			<?php echo esc_html__( 'Please configure the Featured Block in order to populate content', 'ucsc' ); ?>
		</h3>
	</section>
	<?php

	return;
}

?>
<section <?php echo wp_kses_data( get_block_wrapper_attributes() ); ?>>
    <div class="ucsc-featured-news-block__inner">
        <?php foreach ( $items as $key => $item ) :  ?>
            <a href="<?php echo esc_url( get_the_permalink( $item['id'] ) ); ?>" class="ucsc-featured-news-block__card<?php echo $key === 0 ? esc_attr( ' ucsc-featured-news-block__card--sticky' ) : ''; ?>">
                <?php if ( ! empty( $item['image'] ) && $item['image']['id'] > 0 ) : ?>
                    <div class="ucsc-featured-news-block__card-image">
                        <?php $image_alt = get_post_meta( $item['image']['id'], '_wp_attachment_image_alt', true ); ?>
                        <img 
                            src="<?php echo esc_url( $item['image']['url'] );?>" 
                            srcset="<?php echo $c->build_srcset( $item['image'] );?>" 
                            class="ucsc-featured-news-block__featured-image"
                            alt="<?php echo ! empty( $image_alt ) ? esc_attr( get_post_meta( $item['image']['id'], '_wp_attachment_image_alt' )[0] ) : $item['title']; ?>"
                        />
                    </div>
                <?php endif; ?>
                <?php if ( ! empty( $item['category'] ) ) : ?>
                    <span class="ucsc-featured-news-block__category">
                        <?php echo $item['category']->name ?>
                    </span>
                <?php endif; ?>
    
                <h2 class="ucsc-featured-news-block__card-title">
                    <span class="ucsc-featured-news-block__card-title--inner">
                        <?php echo $item['title']; ?>
                    </span>
                </h2>
    
                <?php if ( $key === 0 && ! empty( $item['excerpt'] ) ) : ?>
                    <p class="ucsc-featured-news-block__card-excerpt"><?php echo $item['excerpt']; ?></p>
                <?php endif; ?>
            </a>
        <?php endforeach; ?>
        <?php echo $c->get_cta(); ?>
    </div>
</section>
