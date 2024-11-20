<?php declare(strict_types=1);

namespace UCSC\Blocks\Object_Meta;

class Object_Meta_Definer {
    
    public function register(): void {
        add_action( 'init', static function () {
            ( new Photo_Of_The_Week_Meta() )->init();
        }, 10, 0 );
    }

}