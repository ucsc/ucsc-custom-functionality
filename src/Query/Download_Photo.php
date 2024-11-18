<?php declare(strict_types=1);

namespace UCSC\Blocks\Query;

use UCSC\Blocks\Object_Meta\Photo_Of_The_Week_Meta;

class Download_Photo {
	
	public function download_photo_of_the_week_single(): void {
		$photo = get_field( Photo_Of_The_Week_Meta::IMAGE );
		
		if ( empty( $photo ) ) {
			wp_safe_redirect( '/' );
			exit;
		}

		$content = file_get_contents( $photo['url'] );
		// Set the appropriate headers for download
		header( 'Content-Description: File Transfer' );
		header( 'Content-Type: application/octet-stream' );
		header( 'Content-Disposition: attachment; filename="' . $photo['filename'] . '"' );
		header( 'Content-Length: ' . strlen( $content ) );
		header( 'Cache-Control: must-revalidate' );
		header( 'Pragma: public' );
		header( 'Expires: 0' );

		// Output the image content
		echo $content;
        exit;
	}

}
