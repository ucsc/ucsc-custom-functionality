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
