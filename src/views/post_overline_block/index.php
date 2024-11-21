<?php declare(strict_types=1);

use UCSC\Blocks\Object_Meta\Posts_Meta;

if ( is_admin() ) {
	echo esc_html__( 'This block will display post overline meta.', 'ucsc' );
	
	return;
}

$overline = get_field( Posts_Meta::POST_OVERLINE, get_the_ID() );

if ( empty( $overline ) ) {
	return;
}
?>
<section <?php echo wp_kses_data( get_block_wrapper_attributes() ); ?>>
	<?php echo $overline; ?>
</section>
