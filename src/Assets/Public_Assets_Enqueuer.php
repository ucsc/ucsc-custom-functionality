<?php
/**
 * Front-end block assets.
 *
 * @package ucsc
 */

declare(strict_types=1);

namespace UCSC\Blocks\Assets;

/**
 * Names the built files that make up a block's front-end assets.
 */
class Public_Assets_Enqueuer extends Assets_Enqueuer {

	/**
	 * Script handle and .js basename.
	 *
	 * @var string
	 */
	public const PUBLIC = 'index';

	/**
	 * Stylesheet handle and .css basename.
	 *
	 * @var string
	 */
	public const PUBLIC_CSS = 'style-index';

	/**
	 * Generated asset manifest filename.
	 *
	 * @var string
	 */
	public const ASSETS_FILE = self::PUBLIC . '.asset.php';
}
