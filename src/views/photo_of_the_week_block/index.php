<?php declare(strict_types=1);

use UCSC\Blocks\Components\Photo_Of_The_Week_Block_Controller;

/**
 * @var array $block current block attributes
 */
$c     = new Photo_Of_The_Week_Block_Controller( $block );
$photo = $c->get_photo();
if ( empty( $photo ) && is_admin() ) {
	?>
	<section <?php echo wp_kses_data( get_block_wrapper_attributes() ); ?>>
		<h3 class="ucsc-photo-of-the-week-block__header-title">
			<?php echo esc_html__( 'Please select photo in order to populate content', 'ucsc' ); ?>
		</h3>
	</section>
	<?php

	return;
}

if ( empty( $photo ) ) {
	return;
}

?>
<section <?php echo wp_kses_data( get_block_wrapper_attributes() ); ?>>
	<div>
		<?php echo $c->get_title(); ?>
		<hr />
		<?php echo $photo['title']; ?>
		<?php echo strlen( $photo['author'] ) > 1 ? esc_html__( 'By ', 'ucsc' ) . $photo['author'] : ''; ?>
		<hr />
		<a href="<?php echo $photo['download'];?>" target="_blank">
			<?php echo esc_html__( 'Download Image', 'ucsc' ); ?>
		</a>
		<?php echo $c->get_cta(); ?>
	</div>
	<div>
		<?php echo $photo['image'];?>
	</div>
</section>
