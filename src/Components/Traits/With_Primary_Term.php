<?php
/**
 * Primary term resolution.
 *
 * @package ucsc
 */

declare(strict_types=1);

namespace UCSC\Blocks\Components\Traits;

use WP_Term;

/**
 * Picks the single term to display for a post.
 *
 * Posts usually carry several categories, but the card layouts show only one.
 */
trait With_Primary_Term {

	/**
	 * Resolve the term to display for a post.
	 *
	 * Prefers the primary term an editor set through Yoast. Without Yoast, or
	 * without a primary term set, falls back to the post's first term.
	 *
	 * On a category archive the current category is avoided where possible, so
	 * cards in a category listing are not all labelled with that same category.
	 *
	 * @param int|null $post_id  Post to inspect; defaults to the current post.
	 * @param string   $taxonomy Taxonomy to read.
	 *
	 * @return WP_Term|null The term to display, or null when the post has none.
	 */
	public function get_primary_term( ?int $post_id = null, string $taxonomy = 'category' ): ?WP_Term {
		$terms = get_the_terms( get_post( $post_id ), $taxonomy );

		// No terms set.
		if ( is_wp_error( $terms ) || empty( $terms ) ) {
			return null;
		}

		// Yoast enabled.
		if ( $this->has_yoast() ) {
			$primary_term_id = yoast_get_primary_term_id( $taxonomy, $post_id );
		}

		// No Yoast fallback.
		if ( empty( $primary_term_id ) ) {
			$primary_term_id = reset( $terms )->term_id;
		}

		/* If we're viewing a category archive, try to use different terms than the current archive. */
		if ( is_category() ) {
			$current_cat_id = get_queried_object_id();
			$count          = count( $terms );

			if ( $count > 1 && $current_cat_id === $primary_term_id ) {
				foreach ( $terms as $term ) {
					if ( $term->term_id !== $current_cat_id ) {
						$primary_term_id = $term->term_id;
						break;
					}
				}
			}
		}

		return get_term( $primary_term_id );
	}

	/**
	 * Whether Yoast SEO is available to supply a primary term.
	 *
	 * @return bool
	 */
	protected function has_yoast(): bool {
		return function_exists( 'yoast_get_primary_term_id' );
	}
}
