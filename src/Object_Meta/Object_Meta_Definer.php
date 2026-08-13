<?php
/**
 * Object meta registration.
 *
 * @package ucsc
 */

declare(strict_types=1);

namespace UCSC\Blocks\Object_Meta;

/**
 * Registers the plugin's custom object meta on init.
 */
class Object_Meta_Definer {

	/**
	 * Register every meta definition.
	 *
	 * Deferred to init because register_post_meta() requires the post type to
	 * exist first.
	 *
	 * @return void
	 */
	public function register(): void {
		add_action(
			'init',
			static function () {
				( new Photo_Of_The_Week_Meta() )->init();
			},
			10,
			0
		);
	}
}
