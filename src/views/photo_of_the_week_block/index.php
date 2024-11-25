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
<section <?php echo $c->get_attributes(); ?>>
	<figure class="ucsc-photo-of-the-week-block__inner">
		<?php echo $photo['image']; ?>
		<figcaption>
			<div class="ucsc-photo-of-the-week-block__caption">
				<h2 class="ucsc-photo-of-the-week-block__title has-six-font-size">
					<?php echo $c->get_title(); ?>
				</h2>
				<hr />
				<p>
					<span class="ucsc-photo-of-the-week-block__post-title">
						<?php echo $photo['title']; ?>
					</span>
					<?php if ( strlen( $photo['author'] ) > 1 ) : ?>
					<span class="ucsc-photo-of-the-week-block__post-author">
						<svg width="16" height="16" fill="none" stroke="currentColor" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg">
							<path d="M2.5.5h4M9.5 12.5a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/>
							<path d="M14.5 15.5h-13a1 1 0 0 1-1-1v-10a1 1 0 0 1 1-1h13a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1Z"/>
						</svg>
						<?php echo esc_html__( 'By ', 'ucsc' ) . $photo['author']; ?>
					</span>
					<?php endif; ?>
				</p>
				<hr />
				<a class="ucsc-photo-of-the-week-block__download-link has-ucsc-pacific-blue-color" href="<?php echo $photo['download'];?>" target="_blank">
					<svg width="16" height="16" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg">
						<path d="M8 .5v11M3.5 7 8 11.5 12.5 7M.5 15.5h15" />
					</svg>
					<?php echo esc_html__( 'Download image', 'ucsc' ); ?>
				</a>
			</div>
			<div class="ucsc-photo-of-the-week-block__cta ucsc-photo-of-the-week-block__cta--desktop is-style-ucsc-outline-white">
				<?php echo $c->get_cta( [ 'wp-element-button' ] ); ?>
			</div>
		</figcaption>
	</figure>
	<div class="ucsc-photo-of-the-week-block__cta ucsc-photo-of-the-week-block__cta--mobile is-style-ucsc-outline-white">
		<?php echo $c->get_cta( [ 'wp-element-button' ] ); ?>
	</div>
</section>
