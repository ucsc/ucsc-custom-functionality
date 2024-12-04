<?php declare(strict_types=1);

use UCSC\Blocks\Components\Post_Header_Block_Controller;

/**
 * @var array $block current block attributes
 */
$c            = new Post_Header_Block_Controller( $block );
$primary_term = $c->get_primary_category();
$image        = $c->get_image();
?>
<section <?php echo $c->get_attributes(); ?>>
	<header class="ucsc-post-header-block__header">
		<nav class="ucsc-post-header-block__breadcrumb">
			<a href="<?php echo get_bloginfo( 'url' ); ?>">
				<svg xmlns="http://www.w3.org/2000/svg" width="12" height="13" viewBox="0 0 12 13" fill="none" stroke="currentColor" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round">
					<path d="M6 0.875L1.125 6.125V12.125H4.875V9.125H7.125V12.125H10.875V6.125L6 0.875Z" />
				</svg>
				<?php echo esc_html__( 'Home', 'ucsc' ); ?>
			</a>
		</nav>

		<hgroup>
			<?php if ( ! empty( $primary_term ) ) : ?>
				<p class="ucsc-post-header-block__eyebrow">
					<?php echo $primary_term ?>
				</span>
			<?php endif; ?>
			<h1>
				<?php echo get_the_title( get_the_ID() ); ?>
			</h1>
		</hgroup>

		<p class="ucsc-post-header-block__excerpt">
			<?php echo get_the_excerpt( get_the_ID() ); ?>
		</p>

		<div class="ucsc-post-header-block__meta">
			<?php echo get_the_date( 'F j, Y', get_the_ID() ); ?>
			<?php echo do_blocks( '<!-- wp:post-author-name /-->' ); ?>
		</div>
	</header>

	<?php if ( $image ) : ?>
	<figure class="ucsc-post-header-block__image">
		<?php echo $image['image']; ?>

		<?php if ( $image['description'] || $image['attribution'] ) : ?>
		<figcaption>
			<?php if ( $image['description'] ) : ?>
				<p><?php echo esc_html( $image['description'] ); ?></p>
			<?php endif; ?>
			<?php if ( $image['attribution'] ) : ?>
				<p><?php echo esc_html( $image['attribution'] ); ?></p>
			<?php endif; ?>
		</figcaption>
		<?php endif; ?>
	</figure>
	<?php endif; ?>
</section>
