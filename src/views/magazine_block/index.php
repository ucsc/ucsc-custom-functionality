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
	<header class="ucsc-magazine-block__header">
		<?php if ( ! empty( $title_1 ) || ! empty( $title_2 ) ) : ?>
		<h2>
			<?php if ( ! empty( $title_1 ) ) : ?>
			<span class="ucsc-magazine-block__title-line-1">
				<?php echo $title_1; ?>
			</span>
			<?php endif; ?>

			<?php if ( ! empty( $title_2 ) ) : ?>
			<span class="ucsc-magazine-block__title-line-2">
				<?php echo $title_2; ?>
			</span>
			<?php endif; ?>
		</h2>
		<?php endif; ?>

		<?php if ( ! empty( $subtitle ) ) : ?>
		<p><?php echo $subtitle; ?></p>
		<?php endif; ?>

		<div role="tablist">
			<?php foreach ( $magazines as $index => $magazine ) : ?>
			<button
				role="tab"
				data-key="<?php echo $c->make_tab_key( $magazine, $index )?>"
				aria-labelledby="<?php echo $c->make_tab_key( $magazine, $index, 'label' )?>"
				aria-selected="false"
				aria-controls="<?php echo $c->make_tab_key( $magazine, $index, 'panel' )?>"
			>
				<span id="<?php echo $c->make_tab_key( $magazine, $index, 'label' )?>">
					<?php echo $magazine[ Magazine_Block::ITEM_TITLE ];?>
				</span>
				<span>
					<?php echo $magazine[ Magazine_Block::ITEM_BYLINE ];?>
				</span>
			</button>
			<?php endforeach; ?>
		</tablist>
	</header>

	<div class="ucsc-magazine-block__content">
		<?php foreach ( $magazines as $index => $magazine ) : ?>
		<div
			id="<?php echo $c->make_tab_key( $magazine, $index, 'panel' )?>"
			role="tabpanel"
			aria-labelledby="<?php echo $c->make_tab_key( $magazine, $index, 'label' )?>"
		>
			<?php echo $c->get_image( $magazine ); ?>
			<?php echo $c->get_description( $magazine ); ?>
			<?php echo $c->get_cta( $magazine ); ?>
		</div>
		<?php endforeach; ?>
	</div>
</section>
