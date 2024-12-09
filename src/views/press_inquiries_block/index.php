<?php declare(strict_types=1);

use UCSC\Blocks\Blocks\Press_Inquiries_Block;
use UCSC\Blocks\Components\Press_Inquiries_Controller;

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
<section <?php echo $c->get_attributes(); ?>>
	<div class="ucsc-press-inquiries-block__container">
		<div>
			<button class="ucsc-press-inquiries-block__toggle" type="button" aria-expanded="false" aria-controls="<?php echo $c->get_panel_id(); ?>">
				<?php echo esc_html__( 'Press Inquiries', 'ucsc' ); ?>
			</button>
		</div>
		<div class="ucsc-press-inquiries-block__inner-wrapper" id="<?php echo $c->get_panel_id(); ?>" inert>
			<div class="ucsc-press-inquiries-block__inner">
				<div class="ucsc-press-inquiries-block__content">

					<?php if ( ! empty( $contacts ) ) : ?>
					<div>
						<h2><?php echo esc_html__( 'Press Contact', 'ucsc' ); ?></h2>
						<div class="ucsc-press-inquiries-block__contacts" >
							<?php foreach ( $contacts as $contact ) : ?>
								<?php if ( empty( $contact ) ) : ?>
									<?php continue; ?>
								<?php endif; ?>
							<div class="ucsc-press-inquiries-block__contact" >
								<h3><?php echo $contact[ Press_Inquiries_Block::PRESS_NAME ]; ?></h3>
								<p class="ucsc-press-inquiries-block__link-wrapper">
									<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
										<path d="M14 14.5H2C1.172 14.5 0.5 13.828 0.5 13V3C0.5 2.172 1.172 1.5 2 1.5H14C14.828 1.5 15.5 2.172 15.5 3V13C15.5 13.828 14.828 14.5 14 14.5Z" />
										<path d="M2.5 4.5L8 9L13.5 4.5" />
									</svg>
									<a href="mailto:<?php echo $contact[ Press_Inquiries_Block::PRESS_EMAIL ]; ?>">
										<?php echo $contact[ Press_Inquiries_Block::PRESS_EMAIL ]; ?>
									</a>
								</p>
								<p class="ucsc-press-inquiries-block__link-wrapper">
									<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
										<path d="M10.5137 9.76347L9.29171 11.2915C7.39815 10.1789 5.82025 8.60103 4.70771 6.70747L6.23571 5.48547C6.41483 5.34213 6.54142 5.14349 6.5957 4.9206C6.64997 4.6977 6.62888 4.46311 6.53571 4.25347L5.14271 1.11647C5.04282 0.891671 4.86633 0.709661 4.64472 0.602884C4.42311 0.496108 4.17078 0.471507 3.93271 0.533467L1.28071 1.22047C1.03335 1.28532 0.81832 1.43855 0.676258 1.65118C0.534197 1.86381 0.474949 2.12112 0.509714 2.37447C0.975006 5.68821 2.50649 8.76038 4.87265 11.1265C7.2388 13.4927 10.311 15.0242 13.6247 15.4895C13.878 15.5244 14.1352 15.4652 14.3478 15.3231C14.5603 15.181 14.7133 14.9659 14.7777 14.7185L15.4657 12.0675C15.5277 11.8294 15.5031 11.5771 15.3963 11.3555C15.2895 11.1339 15.1075 10.9574 14.8827 10.8575L11.7457 9.46447C11.5361 9.37154 11.3017 9.35047 11.0789 9.40454C10.8561 9.45862 10.6574 9.5848 10.5137 9.76347Z" />
									</svg>
									<a href="tel:<?php echo $contact[ Press_Inquiries_Block::PRESS_PHONE ]; ?>">
										<?php echo $contact[ Press_Inquiries_Block::PRESS_PHONE ]; ?>
									</a>
								</p>
							</div>
							<?php endforeach; ?>

						</div>
					</div>
					<?php endif; ?>

					<?php if ( ! empty( $media_text ) || ! empty( $media_file ) || ! empty( $media_image ) ) : ?>
					<div>
						<h2><?php echo esc_html__( 'Media Access', 'ucsc' ); ?></h2>

						<?php if ( ! empty( $media_file ) ) : ?>
						<p class="ucsc-press-inquiries-block__link-wrapper">
							<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round">
								<path d="M9.5 0.5V5.5H14.5" />
								<path d="M9.5 0.5H1.5V15.5H14.5V5.5L9.5 0.5Z" />
							</svg>
							<a href="<?php echo $media_file ?>" target="_blank">
								<?php echo esc_html__( 'Access Paper', 'ucsc' ); ?>
							</a>
						</p>
						<?php endif; ?>

						<?php if ( ! empty( $media_image ) ) : ?>
							<p class="ucsc-press-inquiries-block__link-wrapper">
								<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
									<path d="M0.5 12.5L3.5 9.5L5.5 11.5L10.5 6.5L15.5 11.5" />
									<path d="M14 15.5H2C1.60218 15.5 1.22064 15.342 0.93934 15.0607C0.658035 14.7794 0.5 14.3978 0.5 14V2C0.5 1.60218 0.658035 1.22064 0.93934 0.93934C1.22064 0.658035 1.60218 0.5 2 0.5H14C14.3978 0.5 14.7794 0.658035 15.0607 0.93934C15.342 1.22064 15.5 1.60218 15.5 2V14C15.5 14.3978 15.342 14.7794 15.0607 15.0607C14.7794 15.342 14.3978 15.5 14 15.5Z" />
									<path d="M5 6.5C5.82843 6.5 6.5 5.82843 6.5 5C6.5 4.17157 5.82843 3.5 5 3.5C4.17157 3.5 3.5 4.17157 3.5 5C3.5 5.82843 4.17157 6.5 5 6.5Z" />
								</svg>
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
				</div>
			</div>
		</div>
	</div>
</section>
