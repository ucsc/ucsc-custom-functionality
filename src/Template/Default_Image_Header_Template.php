<?php declare(strict_types=1);

namespace UCSC\Blocks\Template;

class Default_Image_Header_Template {
    
    public const NAME = 'default_image_header_template';
    
    public function register_template() {
        if ( ! is_single() ) {
            return;
        }

        $theme        = wp_get_theme();
        $block_source = 'custom';

        if ( file_exists( $theme->theme_root . '/' . $theme->stylesheet . '/templates/default-image-header.html' ) ) {
            $template_contents = file_get_contents( $theme->theme_root . '/' . $theme->stylesheet . '/templates/default-image-header.html' );
            $block_source      = 'theme';
        } else {
            $template_contents = file_get_contents( UCSC_DIR . '/src/views/archives/default-image-header.html' );
        }
    }
}