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
	<?php if ( $c->is_horizontal_layout() ) : ?>
	<div class="ucsc-post-header-block__columns-container alignfull is-layout-constrained has-global-padding has-ucsc-primary-blue-background-color has-white-color">
	<?php endif; ?>

	<nav class="ucsc-post-header-block__breadcrumb">
		<a href="<?php echo get_bloginfo( 'url' ); ?>">
			<svg xmlns="http://www.w3.org/2000/svg" width="12" height="13" viewBox="0 0 12 13" fill="none" stroke="currentColor" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round">
				<path d="M6 0.875L1.125 6.125V12.125H4.875V9.125H7.125V12.125H10.875V6.125L6 0.875Z" />
			</svg>
			<?php echo esc_html__( 'Home', 'ucsc' ); ?>
		</a>
	</nav>

	<?php if ( $c->is_horizontal_layout() ) : ?>
	<div class="ucsc-post-header-block__columns">
	<?php endif; ?>

	<header class="ucsc-post-header-block__header alignfull is-layout-constrained has-global-padding  has-ucsc-primary-blue-background-color has-white-color">
		<hgroup>
			<?php if ( ! empty( $primary_term ) ) : ?>
				<p class="ucsc-post-header-block__eyebrow has-one-font-size has-ucsc-primary-yellow-color">
					<?php echo $primary_term ?>
				</span>
			<?php endif; ?>
			<h1 class="ucsc-post-header-block__title has-seven-font-size">
				<?php echo get_the_title( get_the_ID() ); ?>
			</h1>
		</hgroup>

		<p class="ucsc-post-header-block__excerpt has-one-font-size">
			<?php echo get_the_excerpt( get_the_ID() ); ?>
		</p>

		<div class="ucsc-post-header-block__meta">
			<time datetime="<?php echo get_the_date( 'Y-m-d', get_the_ID() ); ?>">
				<?php echo get_the_date( 'F j, Y', get_the_ID() ); ?>
			</time>
			<span role="separator"></span>
			<p class="ucsc-post-header-block__authors">
				<?php echo do_blocks( '<!-- wp:post-author-name /-->' ); ?>
			</p>
		</div>
	</header>

	<?php if ( $image ) : ?>
	<figure class="ucsc-post-header-block__figure">
		<div class="ucsc-post-header-block__image-container">
			<div class="alignfull is-layout-constrained has-global-padding">
				<div>
					<?php echo $image['image']; ?>
				</div>
			</div>
		</div>

		<?php if ( $image['description'] || $image['attribution'] ) : ?>
		<figcaption class="alignfull is-layout-constrained has-global-padding">
			<?php if ( $image['description'] ) : ?>
				<p><?php echo esc_html( $image['description'] ); ?></p>
			<?php endif; ?>
			<?php if ( $image['attribution'] ) : ?>
				<p class="ucsc-post-header-block__attribution">
					<svg xmlns="http://www.w3.org/2000/svg" width="16" height="17" viewBox="0 0 16 17" fill="none" stroke="currentColor" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round">
						<path d="M2.5 1H6.5" />
						<path d="M9.5 13C11.1569 13 12.5 11.6569 12.5 10C12.5 8.34315 11.1569 7 9.5 7C7.84315 7 6.5 8.34315 6.5 10C6.5 11.6569 7.84315 13 9.5 13Z" />
						<path d="M14.5 16H1.5C0.948 16 0.5 15.552 0.5 15V5C0.5 4.448 0.948 4 1.5 4H14.5C15.052 4 15.5 4.448 15.5 5V15C15.5 15.552 15.052 16 14.5 16Z" />
					</svg>
					<?php echo esc_html( $image['attribution'] ); ?>
				</p>
			<?php endif; ?>
		</figcaption>
		<?php endif; ?>
	</figure>
	<?php endif; ?>

	<?php if ( $c->is_horizontal_layout() ) : ?>
	</div>
	</div>
	<?php endif; ?>
</section>
