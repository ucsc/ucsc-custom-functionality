<?php
/**
 * Plugin block templates.
 *
 * @package ucsc
 */

declare(strict_types=1);

namespace UCSC\Blocks\Template;

/**
 * Registers the plugin's block templates with core.
 *
 * The templates are registered under the plugin namespace with
 * register_block_template() (WordPress 6.7+), so they belong to no theme
 * and follow any theme switch. Each slug is a template-hierarchy slug, so
 * core selects the template on its own. A theme file with the same slug
 * overrides it, and Site Editor edits are saved by core as usual.
 */
class Block_Templates {

	/**
	 * Namespace the templates are registered under.
	 *
	 * @var string
	 */
	public const NAMESPACE = 'ucsc-custom-functionality';

	/**
	 * Hook template registration into init.
	 *
	 * @return void
	 */
	public function init(): void {
		add_action( 'init', [ $this, 'register' ], 10, 0 );
	}

	/**
	 * Register each template. Does nothing before WordPress 6.7.
	 *
	 * @return void
	 */
	public function register(): void {
		if ( ! function_exists( 'register_block_template' ) ) {
			return;
		}

		foreach ( $this->get_templates() as $slug => $template ) {
			register_block_template(
				self::NAMESPACE . '//' . $slug,
				[
					'title'       => $template['title'],
					'description' => $template['description'],
					// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- reading a template shipped inside this plugin, not a remote or user-supplied file.
					'content'     => (string) file_get_contents( UCSC_DIR . '/src/views/templates/' . $slug . '.html' ),
				]
			);
		}
	}

	/**
	 * The templates, keyed by slug. Each slug matches a file in
	 * src/views/templates/.
	 *
	 * @return array<string, array{title: string, description: string}>
	 */
	public function get_templates(): array {
		return [
			'single-post'               => [
				'title'       => __( 'UCSC Single Posts', 'ucsc' ),
				'description' => __( 'Displays a single post with a heading, press coverage, categories, social sharing, and related stories.', 'ucsc' ),
			],
			'archive-photo_of_the_week' => [
				'title'       => __( 'Photo of the Week', 'ucsc' ),
				'description' => __( 'Photo of the Week archive page', 'ucsc' ),
			],
		];
	}
}
