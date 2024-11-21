<?php declare(strict_types=1);

namespace UCSC\Blocks\Integrations;

class Integrations_Subscriber {
	
	public const PRIMARY_TAX_SUPPORT = [
		'academics',
		'administration',
		'person',
		'section',
		'kind',
	];

	public function init(): void {
		add_filter( 'wpseo_primary_term_taxonomies', static function ( $taxonomies, $post_type, $all_taxonomies ) {
            foreach ( self::PRIMARY_TAX_SUPPORT as $tax ) {
                if ( ! isset( $all_taxonomies['academics'] ) ) {
                    continue;
                }

                $taxonomies['academics'] = $all_taxonomies['academics'];
            }

			return $taxonomies;
		}, 10, 3 );
	}
	
}
