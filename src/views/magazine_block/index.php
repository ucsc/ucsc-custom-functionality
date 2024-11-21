<?php declare(strict_types=1);

use UCSC\Blocks\Blocks\Magazine_Block;
use UCSC\Blocks\Components\Magazine_Controller;

/**
 * @var array $block current block attributes
 */
$c         = new Magazine_Controller( $block );
$title     = $c->get_title();
$overline  = $c->get_overline();
$magazines = $c->get_magazines();
?>
<section <?php echo wp_kses_data( get_block_wrapper_attributes() ); ?>>
	<div>
		<?php if ( ! empty( $title ) ) : ?>
			<h2><?php echo $title; ?></h2>
		<?php endif; ?>
		<?php if ( ! empty( $overline ) ) : ?>
			<p><?php echo $overline; ?></p>
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
