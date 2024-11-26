<?php declare(strict_types=1);

use UCSC\Blocks\Blocks\Magazine_Block;
use UCSC\Blocks\Components\Magazine_Block_Controller;

/**
 * @var array $block current block attributes
 */
$c         = new Magazine_Block_Controller( $block );
$title_1   = $c->get_title_line_1();
$title_2   = $c->get_title_line_2();
$subtitle  = $c->get_subtitle();
$magazines = $c->get_magazines();
?>
<section <?php echo $c->get_attributes(); ?>>
	<div class="ucsc-magazine-block__inner">
		<header class="ucsc-magazine-block__header">
			<?php if ( ! empty( $title_1 ) || ! empty( $title_2 ) ) : ?>
			<h2 class="ucsc-magazine-block__title">
				<?php if ( ! empty( $title_1 ) ) : ?>
				<span class="ucsc-magazine-block__title-line-1 has-four-font-size">
					<?php echo $title_1; ?>
				</span>
				<?php endif; ?>

				<?php if ( ! empty( $title_2 ) ) : ?>
				<span class="ucsc-magazine-block__title-line-2 has-six-font-size">
					<?php echo $title_2; ?>
				</span>
				<?php endif; ?>
			</h2>
			<?php endif; ?>

			<?php if ( ! empty( $subtitle ) ) : ?>
			<p class="ucsc-magazine-block__subtitle has-one-font-size"><?php echo $subtitle; ?></p>
			<?php endif; ?>

			<div class="ucsc-magazine-block__tabs" role="tablist">
				<?php foreach ( $magazines as $index => $magazine ) : ?>
				<button
					class="ucsc-magazine-block__tab"
					role="tab"
					tabindex="-1"
					data-key="<?php echo $c->make_tab_key( $magazine, $index )?>"
					aria-labelledby="<?php echo $c->make_tab_key( $magazine, $index, 'label' )?>"
					aria-selected="false"
					aria-controls="<?php echo $c->make_tab_key( $magazine, $index, 'panel' )?>"
				>
					<span id="<?php echo $c->make_tab_key( $magazine, $index, 'label' )?>" class="ucsc-magazine-block__post-title">
						<?php echo $magazine[ Magazine_Block::ITEM_TITLE ];?>
					</span>
					<?php if ( $magazine[ Magazine_Block::ITEM_BYLINE ] ) : ?>
					<span class="ucsc-magazine-block__post-author has-base-font-size">
						<?php echo esc_html( 'By', 'ucsc' ); ?>
						<?php echo $magazine[ Magazine_Block::ITEM_BYLINE ]; ?>
					</span>
					<?php endif; ?>
				</button>
				<?php endforeach; ?>
			</div>
		</header>

		<div class="ucsc-magazine-block__panels">
			<?php foreach ( $magazines as $index => $magazine ) : ?>
			<div
				id="<?php echo $c->make_tab_key( $magazine, $index, 'panel' )?>"
				role="tabpanel"
				aria-labelledby="<?php echo $c->make_tab_key( $magazine, $index, 'label' )?>"
				aria-hidden="true"
				inert
				class="ucsc-magazine-block__panel"
			>
				<?php echo $c->get_image( $magazine ); ?>

				<?php  $cta = $c->get_cta( $magazine ); ?>
				<?php  $description = $c->get_description( $magazine ); ?>
				<?php if ( $description || $cta ) : ?>
				<div class="ucsc-magazine-block__post-excerpt">
					<?php if ( $description ) : ?>
					<p>
						<?php echo $description; ?>
					</p>
					<?php endif;?>

					<?php if ( $cta ) : ?>
					<div class="ucsc-magazine-block__post-cta is-style-ucsc-blue">
						<a href="<?php echo $cta['href'] ?>" target="<?php echo $cta['target'] ?>" class="wp-element-button">
							<?php echo $cta['title'] ?>
							<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 10 10" fill="none">
								<path d="M0.767045 9.84203L0 9.08138L7.86222 1.21277H1.57244L1.58523 0.158081H9.67756V8.2632H8.61009L8.62287 1.97981L0.767045 9.84203Z" fill="currentColor"/>
							</svg>
						</a>
					</div>
					<?php endif;?>
				</div>
				<?php endif; ?>

			</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
