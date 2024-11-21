<?php declare(strict_types=1);

if ( empty( $items ) && is_admin() ) {
	?>
	<section <?php echo wp_kses_data( get_block_wrapper_attributes() ); ?>>
		<h3 class="ucsc-photo-week-block__header-title">
			<?php echo esc_html__( 'Please create a Photo of the Week post in order to populate content', 'ucsc' ); ?>
		</h3>
	</section>
	<?php

	return;
}

?>
<section <?php echo wp_kses_data( get_block_wrapper_attributes() ); ?>>
	
</section>
