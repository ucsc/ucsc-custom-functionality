<?php declare(strict_types=1);

use UCSC\Blocks\Components\Press_Inquiries_Controller;
use UCSC\Blocks\Object_Meta\Posts_Meta;

/**
 * @var array $block current block attributes
 */
$c           = new Press_Inquiries_Controller( $block );
$contacts    = $c->get_press_contacts();
$media_text  = $c->get_media_text();
$media_image = $c->get_media_image();
$media_file  = $c->get_media_file();

$is_empty = empty( $contacts ) && empty( $media_image ) && empty( $media_file ) && empty( $media_text );

if ( is_admin() && $is_empty ) {
    echo esc_html__( 'This block will display post press inquiries section.', 'ucsc' );

    return;
}

if ( $is_empty ) {
	return;
}
?>
<section <?php echo wp_kses_data( get_block_wrapper_attributes() ); ?>>
	<p><?php echo esc_html__( 'Press Inquiries', 'ucsc' ); ?></p>
	<?php if ( ! empty( $contacts ) ) : ?>
	<div>
		<h3><?php echo esc_html__( 'Press Contact', 'ucsc' ); ?></h3>
		<?php foreach ( $contacts as $contact ) : ?>
			<?php if ( empty( $contact ) ) : ?>
				<?php continue; ?>
			<?php endif; ?>
			<h5><?php echo $contact[ Posts_Meta::PRESS_NAME ]; ?></h5>
			<p>
				<a href="mailto:<?php echo $contact[ Posts_Meta::PRESS_EMAIL ]; ?>">
					<?php echo $contact[ Posts_Meta::PRESS_EMAIL ]; ?>
				</a>
			</p>
			<p>
				<a href="tel:<?php echo $contact[ Posts_Meta::PRESS_PHONE ]; ?>">
					<?php echo $contact[ Posts_Meta::PRESS_PHONE ]; ?>
				</a>
			</p>
		<?php endforeach; ?>
	</div>
	<?php endif; ?>
	<?php if ( ! empty( $media_text ) || ! empty( $media_file ) || ! empty( $media_image ) ) : ?>
		<div>
			<h3><?php echo esc_html__( 'Media Access', 'ucsc' ); ?></h3>
			<?php if ( ! empty( $media_file ) ) : ?>
			<p>
				<a href="<?php echo $media_file ?>" target="_blank">
					<?php echo esc_html__( 'Access Paper', 'ucsc' ); ?>
				</a>
			</p>
			<?php endif; ?>
			<?php if ( ! empty( $media_image ) ) : ?>
				<p>
					<a href="<?php echo $media_image ?>" target="_blank">
						<?php echo esc_html__( 'Image Download', 'ucsc' ); ?>
					</a>
				</p>
			<?php endif; ?>
			<?php if ( ! empty( $media_text ) ) : ?>
				<?php echo $media_text ?>
			<?php endif; ?>
		</div>
	<?php endif; ?>
</section>
