<?php declare(strict_types=1);

use UCSC\Blocks\Components\Featured_Block_Controller;

/**
 * @var array $block current block attributes
 */
$c = new Featured_Block_Controller( $block );

$items = $c->get_items();

//if ( empty( $items ) ) {
//	return;
//}
?>
<section <?php echo wp_kses_data( get_block_wrapper_attributes() ); ?>>
    TEST
</section>
