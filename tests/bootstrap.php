<?php
/**
 * PHPUnit bootstrap.
 *
 * The suite runs without a WordPress install. Brain Monkey stubs the hook
 * API and lets each test declare the WordPress functions it needs; the few
 * constants and classes the code under test references at load time are
 * defined here.
 *
 * @package ucsc
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

if ( ! defined( 'MINUTE_IN_SECONDS' ) ) {
	define( 'MINUTE_IN_SECONDS', 60 );
}

if ( ! defined( 'DAY_IN_SECONDS' ) ) {
	define( 'DAY_IN_SECONDS', 86400 );
}

// Deliberately not the checkout's parent directory, so code that builds the
// plugin's path from WP_PLUGIN_DIR fails as it would on a renamed install.
if ( ! defined( 'WP_PLUGIN_DIR' ) ) {
	define( 'WP_PLUGIN_DIR', '/srv/www/wp-content/plugins' );
}

if ( ! class_exists( 'WP_Screen' ) ) {
	/**
	 * Minimal stand-in for the admin screen object.
	 */
	class WP_Screen {
		/**
		 * Screen base.
		 *
		 * @var string
		 */
		public string $base = '';
	}
}

if ( ! class_exists( 'WP_Block_Template' ) ) {
	/**
	 * Minimal stand-in for a block template.
	 */
	class WP_Block_Template {}
}
