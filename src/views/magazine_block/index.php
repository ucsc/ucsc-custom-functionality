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
	<div>
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
		<?php if ( ! empty( $magazines ) ) : ?>
			<?php foreach ( $magazines as $index => $magazine ) : ?>
				<?php if ( ! is_array( $magazine ) ) :?>
					<?php continue; ?>
				<?php endif; ?>
				<div data-key="<?php echo $c->make_tab_key( $magazine[ Magazine_Block::ITEM_TITLE ], $index )?>">
					<h4><?php echo $magazine[ Magazine_Block::ITEM_TITLE ];?></h4>
					<p><?php echo $magazine[ Magazine_Block::ITEM_BYLINE ];?></p>
				</div>
			<?php endforeach; ?>
		<?php endif; ?>
	</div>
	<?php if ( ! empty( $magazines ) ) : ?>
		<div>
			<?php foreach ( $magazines as $index => $magazine ) : ?>
				<?php if ( ! is_array( $magazine ) ) :?>
					<?php continue; ?>
				<?php endif; ?>
				<div id="<?php echo $c->make_tab_key( $magazine[ Magazine_Block::ITEM_TITLE ], $index )?>">
					<?php echo $c->get_image( $magazine ); ?>
					<?php echo $c->get_description( $magazine ); ?>
					<?php echo $c->get_cta( $magazine ); ?>
				</div>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</section>
