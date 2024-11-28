<?php declare(strict_types=1);

namespace UCSC\Blocks\Components;

use UCSC\Blocks\Components\Traits\With_Primary_Term;

class Post_Header_Block_Controller {
    
    use With_Primary_Term;
    
    protected array $block;

    public function __construct( $block ) {
        $this->block = (array) $block;
    }
    
    public function get_primary_category(): ?string {
        $category = $this->get_primary_term( get_the_ID() );
        
        if ( empty( $category ) ) {
            return null;
        }
        
        return (string) $category->name;
    }
    
}