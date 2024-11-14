<?php declare(strict_types=1);

namespace UCSC\Blocks\Blocks;

use UCSC\Blocks\Blocks\Traits\With_Query_Loop;

class Featured_Block extends ACF_Group {
    
    use With_Query_Loop;

    public const NAME = 'ucsc_featured_block';
    
    protected function get_locations(): array {
        return [
            [
                [
                    'param'    => 'block',
                    'operator' => '==',
                    'value'    => 'ucsc-custom-functionality/featured-block',
                ],
            ],
        ];
    }

    protected function get_title(): string {
        return esc_html__( 'Featured Block', 'tribe' );
    }

    protected function get_key(): string {
        return self::NAME;
    }

    protected function get_fields(): array {
        return [];
    }
}