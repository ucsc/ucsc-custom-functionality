<?php declare(strict_types=1);

use UCSC\Blocks\Components\Photo_Of_The_Week_Archive_Controller;
use UCSC\Blocks\Object_Meta\Photo_Of_The_Week_Meta;

$c     = new Photo_Of_The_Week_Archive_Controller();
$paged = get_query_var( 'paged' ) ? (int) get_query_var( 'paged' ) : 1;
$posts = $c->get_query( $paged );
?>
<div class="archive-query wp-block-template-part">
	<div class="wp-block-query ucsc__post-query-loop has-global-padding is-layout-constrained wp-block-query-is-layout-constrained">
		<?php if ( $posts->have_posts() ) : ?>
			<?php while ( $posts->have_posts() ) : ?>
				<?php $posts->the_post(); ?>
			<figure class="wp-block-post post-<?php echo get_the_ID(); ?> photo-of-the-week type-photo_of_the_week status-publish hentry">
				<?php echo $c->get_image(); ?>
				<figcaption>
					<div class="photo-of-the-week__caption">
						<h2 class="photo-of-the-week__title has-ucsc-primary-blue-color has-two-font-size">
							<?php echo get_the_title(); ?>
						</h2>
						<?php $author = get_field( Photo_Of_The_Week_Meta::PHOTOGRAPHER, get_the_ID() ); ?>
						<?php if ( ! empty( $author ) ) : ?>
						<p class="photo-of-the-week__author">
							<svg width="16" height="16" fill="none" stroke="currentColor" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg">
								<path d="M2.5.5h4M9.5 12.5a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/>
								<path d="M14.5 15.5h-13a1 1 0 0 1-1-1v-10a1 1 0 0 1 1-1h13a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1Z"/>
							</svg>
							<?php echo $author; ?>
						</p>
						<?php endif; ?>
					</div>
					<a class="photo-of-the-week__download-link" href="<?php echo get_the_permalink( get_the_ID() ); ?>" target="_blank">
						<svg width="16" height="16" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg">
							<path d="M8 .5v11M3.5 7 8 11.5 12.5 7M.5 15.5h15" />
						</svg>
						<?php echo esc_html__( 'Download Image', 'ucsc' ); ?>
					</a>
				</figcaption>
			</figure>
			<?php endwhile; ?>
	
			<!-- Pagination -->
			<nav class="wp-block-query-pagination is-content-justification-center is-layout-flex wp-container-core-query-pagination-is-layout-1 wp-block-query-pagination-is-layout-flex">
				<?php if ( $paged === 1 ) : ?>
					<span class="page-numbers prev">
						<?php echo esc_html__( 'Previous', 'ucsc' ); ?>
					</span>
				<?php endif; ?>
				
				<?php echo $c->get_pagination( $posts, $paged ); ?>

				<?php if ( $paged === $posts->max_num_pages ) : ?>
					<span class="page-numbers next">
						<?php echo esc_html__( 'Next', 'ucsc' ); ?>
					</span>
				<?php endif; ?>
			</nav>
		<?php else : ?>
			<p>Not found</p>
		<?php endif; ?>
	</div>
</div>
<?php wp_reset_postdata(); ?>
