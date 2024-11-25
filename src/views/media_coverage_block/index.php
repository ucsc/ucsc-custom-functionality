<?php declare(strict_types=1);

use UCSC\Blocks\Components\Media_Coverage_Controller;

/**
 * @var array $block current block attributes
 */
$c     = new Media_Coverage_Controller( $block );
$items = $c->get_items();
$title = $c->get_title();
?>
<section <?php echo $c->get_attributes(); ?>>
	<?php if ( ! empty( $title ) ) : ?>
	<h2 class="ucsc-media-coverage-block__title has-ucsc-primary-blue-color has-five-font-size">
		<?php echo $title; ?>
	</h2>
	<?php endif; ?>

	<?php if ( ! empty( $items ) ) : ?>
	<div class="ucsc-media-coverage-block__grid">
		<?php foreach ( $items as $item ) :  ?>
		<a class="ucsc-media-coverage-block__post" href="<?php echo $item['source_url'];?>"<?php echo ! $c->is_internal_url( $item['source_url'] ) ? ' target="_blank" rel="nofollow"' : '';?>>
			<?php if ( ! empty( $item['image'] ) && $item['image']['id'] > 0 ) : ?>
			<img
				src="<?php echo esc_url( $item['image']['url'] );?>"
				srcset="<?php echo $c->build_srcset( $item['image'] );?>"
				class="ucsc-featured-block__card-image"
			/>
			<?php endif; ?>

			<h3 class="has-two-font-size">
				<small class="has-base-font-size has-dark-gray-color"><?php echo $item['source_title']; ?></small>
				<?php echo $item['title']; ?>
			</h3>
		</a>
		<?php endforeach; ?>
	</div>
	<?php elseif ( is_admin() ) : ?>
	<h3 class="ucsc-media-coverage-block__header-title">
		<?php echo esc_html__( 'Please configure the Media Coverage Block in order to populate content', 'ucsc' ); ?>
	</h3>
	<?php endif; ?>

	<?php if ( $c->get_cta() ) : ?>
		<div class="ucsc-media-coverage-block__cta is-style-ucsc-blue">
			<?php echo $c->get_cta(); ?>
		</div>
	<?php endif; ?>
</section>
