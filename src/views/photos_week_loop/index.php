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
			<ul>
			<?php while ( $posts->have_posts() ) :
				$posts->the_post();  ?>
				<li class="wp-block-post post-<?php echo get_the_ID(); ?> photo_of_the_week type-photo_of_the_week status-publish hentry">
					<div>
						<?php echo $c->get_image() ?>
					</div>
					<div>
						<h2><?php echo get_the_title() ?></h2>
						<?php $author = get_field( Photo_Of_The_Week_Meta::PHOTOGRAPHER, get_the_ID() ); ?>
						<?php if ( ! empty( $author ) ) : ?>
							<?php echo $author; ?>
						<?php endif; ?>
						<a href="<?php echo get_the_permalink( get_the_ID() ); ?>" target="_blank">
							<?php echo esc_html__( 'Download Image', 'ucsc' ); ?>
						</a>
					</div>
				</li>
			<?php endwhile; ?>
			</ul>
			<!-- Pagination -->
			<nav class="wp-block-query-pagination is-content-justification-center is-layout-flex wp-container-core-query-pagination-is-layout-1 wp-block-query-pagination-is-layout-flex">
				<?php
				echo $c->get_pagination( $posts, $paged );
				?>
			</nav>
		<?php else : ?>
			<p>Not found</p>
		<?php endif; ?>
	</div>
</div>
<?php wp_reset_postdata(); ?>
