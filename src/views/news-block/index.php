<?php
/**
 * News block view.
 *
 * Rendered by Core::render_template(). Although the block is registered
 * from build/views/, that path is rewritten back to src/views/ before the
 * template is included — so edits here take effect without a rebuild, and
 * the copy webpack emits into build/ is never executed.
 *
 * @package ucsc
 */

declare(strict_types=1);

/**
 * The block instance supplied by the render callback.
 *
 * @var array $block Current block attributes.
 */
$c = new \UCSC\Blocks\Components\News_Block_Controller( $block );

$items = $c->get_items();

if ( is_admin() && empty( $items ) ) {
	?>
	<section <?php echo wp_kses_data( get_block_wrapper_attributes() ); ?>>
		<h3><?php echo esc_html__( 'Select a taxonomy and term(s) in the News Block settings', 'ucsc' ); ?></h3>
	</section>
	<?php
	return;
}

if ( empty( $items ) ) {
	return;
}
?>
<section <?php echo wp_kses_data( get_block_wrapper_attributes() ); ?>>
	<?php if ( ! empty( $c->get_title() ) || ! empty( $c->get_description() ) ) : ?>
		<div class="ucsc-news-block__header<?php echo esc_attr( $c->get_alignment() ); ?>">
			<?php if ( ! empty( $c->get_title() ) ) : ?>
				<h2 class="ucsc-news-block__header-title"><?php echo esc_html( $c->get_title() ); ?></h2>
			<?php endif; ?>

			<?php if ( ! empty( $c->get_description() ) ) : ?>
				<div class="ucsc-news-block__header-description"><?php echo wp_kses_post( $c->get_description() ); ?></div>
			<?php endif; ?>
		</div>
	<?php endif; ?>

	<?php if ( count( $items ) < 1 ) { ?>
		<p><?php esc_html_e( 'No articles found', 'ucsc' ); ?></p>
	<?php } else { ?>
		<div class="ucsc-news-block__cards-wrapper">
		<?php foreach ( $items as $item ) : ?>
			<a href="<?php echo esc_url( $item['permalink'] ); ?>" class="ucsc-news-block__card">
				<?php if ( ! empty( $item['image'] ) ) : ?>
					<img 
	src="<?php echo esc_url( $item['image']['raw_url'] ); ?>" 
	srcset="<?php echo esc_attr( $c->build_srcset( $item['image']['sizes'] ) ); ?>" 
	class="ucsc-news-block__card-image"
	alt="<?php echo esc_attr( $item['image']['alt'] ?? '' ); ?>"
/>
				<?php endif; ?>

				<?php if ( ! empty( $item['categories'] ) ) : ?>
					<span class="ucsc-news-block__card-categories">
						<?php echo esc_html( implode( ', ', $item['categories'] ) ); ?>
					</span>
				<?php endif; ?>
				
				<h3 class="ucsc-news-block__card-title">
					<span class="ucsc-news-block__card-title--inner">
						<?php echo esc_html( $item['title'] ); ?>
					</span>
				</h3>
	
				<?php if ( ! empty( $item['excerpt'] ) ) : ?>
					<div class="ucsc-news-block__card-excerpt">
						<?php echo wp_kses_post( $item['excerpt'] ); ?>
					</div>
				<?php endif; ?>

				<?php if ( ! empty( $item['publish_date'] ) || ! empty( $item['authors'] ) || ! empty( $item['tags'] ) ) : ?>
					<div class="ucsc-news-block__card-meta">
						<?php if ( ! empty( $item['publish_date'] ) ) : ?>
							<time class="ucsc-news-block__card-date" datetime="<?php echo esc_attr( $item['raw_date'] ); ?>">
								<?php echo esc_html( $item['publish_date'] ); ?>
							</time>
						<?php endif; ?>

						<?php if ( ! empty( $item['publish_date'] ) && ! empty( $item['authors'] ) ) : ?>
						<span class="ucsc-news-block__meta-separator"></span>
						<?php endif; ?>
			
						<?php if ( ! empty( $item['authors'] ) ) : ?>
							<span class="ucsc-news-block__card-authors">
								<?php echo count( $item['authors'] ) > 1 ? esc_html__( 'By', 'ucsc' ) . ' ' . esc_html( implode( ' and ', $item['authors'] ) ) : esc_html( reset( $item['authors'] ) ); ?>
							</span>
						<?php endif; ?>
	
						<?php if ( ! empty( $item['tags'] ) ) : ?>
							<span class="ucsc-news-block__card-tags">
							<?php foreach ( $item['tags'] as $tag ) { ?>
								<span class="ucsc-news-block__card-tag">
									<?php echo esc_html( $tag ); ?>
								</span>
							<?php } ?>
						</span>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</a>
		<?php endforeach; ?>
		</div>

		<?php if ( ! empty( $c->get_more_news_link() ) ) : ?>
			<div class="ucsc-news-block__more-news is-style-ucsc-blue">
				<a href="<?php echo esc_url( $c->get_more_news_link()['url'] ); ?>" target="<?php echo esc_attr( $c->get_more_news_link()['target'] ); ?>" class="ucsc-news-block__more-news-link wp-element-button">
					<?php echo esc_html( $c->get_more_news_link()['title'] ); ?>
				</a>
			</div>
		<?php endif; ?>
	<?php } ?>
</section>
