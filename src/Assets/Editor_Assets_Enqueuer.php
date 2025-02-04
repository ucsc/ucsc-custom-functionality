<?php declare(strict_types=1);

namespace UCSC\Blocks\Assets;

class Editor_Assets_Enqueuer extends Assets_Enqueuer {

	public const EDITOR           = 'index';
	public const EDITOR_FILE_NAME = 'index';
	public const ASSETS_FILE      = self::EDITOR_FILE_NAME . '.asset.php';

}
