<?php declare(strict_types=1);

namespace UCSC\Blocks\Assets;

class Public_Assets_Enqueuer extends Assets_Enqueuer {

	public const PUBLIC      = 'index';
	public const PUBLIC_CSS  = 'style-index';
	public const ASSETS_FILE = self::PUBLIC . '.asset.php';

}
