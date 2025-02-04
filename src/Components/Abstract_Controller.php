<?php

namespace UCSC\Blocks\Components;

class Abstract_Controller {

    public function get_attributes( $classes = [] ): string {
        
        return wp_kses_data( get_block_wrapper_attributes( [
            'class' => implode(' ',  $classes ),
        ] ) );
    }
    
}