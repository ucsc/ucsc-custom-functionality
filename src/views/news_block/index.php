<?php declare(strict_types=1);

/**
 * @var array $block current block attributes
 */
$c = new \UCSC\Blocks\Components\News_Block_Controller( $block );

$items = $c->get_items();

if ( empty( $items ) ) {
	return;
}
?>
<section>
	<h2><?php echo $c->get_title(); ?></h2>
	<div><?php echo $c->get_description(); ?></div>
	<?php foreach ( $items as $item ) : ?>
		<a href="<?php echo $item['permalink'];?>">
			<?php if ( ! empty( $item['image'] ) ) : ?>
				<img src="<?php echo $item['image']['raw_url'];?>" srcset="<?php echo $c->build_srcset( $item['image']['sizes']) ;?>" />
			<?php endif; ?>

			<?php if ( ! empty( $item['categories'] ) ) : ?>
				<?php echo implode( ', ', $item['categories'] ); ?>
			<?php endif; ?>
			<?php echo $item['title'];?>

			<?php if ( ! empty( $item['excerpt'] ) ) : ?>
				<?php echo $item['excerpt']; ?>
			<?php endif; ?>

			<?php if ( ! empty( $item['authors'] ) ) : ?>
				<?php echo count( $item['authors'] ) > 1 ? esc_html__( 'By', 'ucsc' ) . ' ' . implode( ' and ', $item['authors'] ) : reset( $item['authors'] ); ?>
			<?php endif; ?>

			<?php if ( ! empty( $item['tags'] ) ) : ?>
				<?php echo implode( ', ', $item['tags'] ); ?>
			<?php endif; ?>
			<hr />
			<?php if ( ! empty( $item['publish_date'] ) ) : ?>
				<?php echo $item['publish_date']; ?>
			<?php endif; ?>
		</a>
	<?php endforeach; ?>
</section>
