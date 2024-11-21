<?php

namespace UCSC\Blocks\Integrations;

class ACF_Toolbars {
    
    public const SIMPLE = 'ucsc_simple_toolbar';
    
    public function register_simple_toolbar( array $toolbars ): array {
        $toolbars[ self::SIMPLE ] = [];
        $toolbars[ self::SIMPLE ][1] = [ 'bold', 'italic', 'link' ];
        
        return  $toolbars;
    }

}