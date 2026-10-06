<?php
/**
 * Guarded reads from the ACF AJAX request body.
 *
 * @package ucsc
 */

declare(strict_types=1);

namespace UCSC\Blocks\Traits;

/**
 * Reads the values the editor posts to ACF's select-search AJAX handler.
 *
 * ACF verifies the AJAX nonce before the acf/fields/select/query filter
 * fires, so the readers here do not repeat that check; they only guard for
 * absent or non-string values, unslash, and sanitise. Callers are still
 * responsible for validating the result against whatever allow-list applies.
 */
trait With_Posted_Input {

	/**
	 * A posted value sanitised as a key: lowercase alphanumerics, '-' and '_'.
	 *
	 * @param string $name The $_POST key to read.
	 *
	 * @return string The sanitised value, or '' when absent or not a string.
	 */
	protected function get_posted_key( string $name ): string {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified by ACF's AJAX handler before the acf/fields/select/query filter fires.
		if ( ! isset( $_POST[ $name ] ) || ! is_string( $_POST[ $name ] ) ) {
			return '';
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- see above.
		return sanitize_key( wp_unslash( $_POST[ $name ] ) );
	}

	/**
	 * The search string posted by the editor.
	 *
	 * @return string The sanitised search string, or '' when absent.
	 */
	protected function get_posted_search(): string {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified by ACF's AJAX handler before the acf/fields/select/query filter fires.
		if ( ! isset( $_POST['s'] ) || ! is_string( $_POST['s'] ) ) {
			return '';
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- see above.
		return sanitize_text_field( wp_unslash( $_POST['s'] ) );
	}
}
