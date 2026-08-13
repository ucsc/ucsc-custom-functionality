<?php
/**
 * Block editor assets.
 *
 * @package ucsc
 */

declare(strict_types=1);

namespace UCSC\Blocks\Assets;

/**
 * Names the built files that make up a block's editor assets.
 */
class Editor_Assets_Enqueuer extends Assets_Enqueuer {

	/**
	 * Script and stylesheet handle. Passed for both by the subscriber.
	 *
	 * @var string
	 */
	public const EDITOR = 'index';

	/**
	 * Built file basename.
	 *
	 * @var string
	 */
	public const EDITOR_FILE_NAME = 'index';

	/**
	 * Generated asset manifest filename.
	 *
	 * @var string
	 */
	public const ASSETS_FILE = self::EDITOR_FILE_NAME . '.asset.php';
}
