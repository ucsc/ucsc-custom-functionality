<?php
/**
 * News block field group.
 *
 * @package ucsc
 */

declare(strict_types=1);

namespace UCSC\Blocks\Blocks;

/**
 * Field group for the News block.
 *
 * The only block registered on every site rather than news sites alone, and
 * the only one whose posts come from a remote REST API instead of the local
 * database. It therefore defines its own taxonomy fields rather than using the
 * Taxonomies contract, because its choices are remote taxonomies.
 */
class News_Block extends ACF_Group {

	/**
	 * Field group name and key.
	 *
	 * @var string
	 */
	public const NAME = 'news_query_block';

	/**
	 * Heading field name.
	 *
	 * @var string
	 */
	public const TITLE = 'news_title';
	/**
	 * Description field name.
	 *
	 * @var string
	 */
	public const DESCRIPTION = 'news_desc';
	/**
	 * Header alignment field name.
	 *
	 * @var string
	 */
	public const LAYOUT = 'layout';
	/**
	 * Header alignment value: left.
	 *
	 * @var string
	 */
	public const LAYOUT_LEFT = 'layout_left';
	/**
	 * Header alignment value: centred. The default.
	 *
	 * @var string
	 */
	public const LAYOUT_CENTRE = 'layout_centre';

	/**
	 * "More news" link field name.
	 *
	 * @var string
	 */
	public const MORE_NEWS_LINK = 'more_news_link';

	/**
	 * Remote taxonomy selector field name.
	 *
	 * @var string
	 */
	public const TAXONOMIES = 'taxonomies';
	/**
	 * Remote term selector field name.
	 *
	 * @var string
	 */
	public const TAX_ITEMS = 'taxonomy_items';

	/**
	 * Toggle field name: hide the excerpt.
	 *
	 * @var string
	 */
	public const HIDE_EXCERPT = 'hide_excerpt';
	/**
	 * Toggle field name: hide the featured image.
	 *
	 * @var string
	 */
	public const HIDE_IMAGE = 'hide_image';
	/**
	 * Toggle field name: hide the published date.
	 *
	 * @var string
	 */
	public const HIDE_DATE = 'hide_date';
	/**
	 * Toggle field name: hide the author.
	 *
	 * @var string
	 */
	public const HIDE_AUTHOR = 'hide_author';
	/**
	 * Toggle field name: hide tags.
	 *
	 * @var string
	 */
	public const HIDE_TAGS = 'hide_tags';
	/**
	 * Toggle field name: hide the category.
	 *
	 * @var string
	 */
	public const HIDE_CATEGORY = 'hide_category';

	/**
	 * Remote taxonomies an editor may query.
	 *
	 * Acts as the allow-list for the taxonomy dropdown, and is the list the
	 * unsanitised AJAX handler should be validating against; see #103.
	 *
	 * @var string[]
	 */
	public const ALLOWED_TAX = [
		'academics',
		'administration',
		'category',
		'colleges',
		'person',
		'post_tag',
	];

	/**
	 * Attach the group to the News block.
	 *
	 * @return array
	 */
	protected function get_locations(): array {
		return [
			[
				[
					'param'    => 'block',
					'operator' => '==',
					'value'    => 'ucsc-custom-functionality/news-block',
				],
			],
		];
	}

	/**
	 * Field group title.
	 *
	 * Note: reads "Modal Block", which looks like a copy-paste leftover
	 * rather than a description of this block.
	 *
	 * @return string
	 */
	protected function get_title(): string {
		return esc_html__( 'Modal Block', 'ucsc' );
	}

	/**
	 * Field group key.
	 *
	 * @return string
	 */
	protected function get_key(): string {
		return self::NAME;
	}

	/**
	 * The block's fields: content, query and display toggles.
	 *
	 * @return array
	 */
	protected function get_fields(): array {
		$toggle_fields = [
			self::HIDE_IMAGE    => esc_html__( 'Hide Featured Image', 'ucsc' ),
			self::HIDE_CATEGORY => esc_html__( 'Hide Category', 'ucsc' ),
			self::HIDE_EXCERPT  => esc_html__( 'Hide Excerpt', 'ucsc' ),
			self::HIDE_DATE     => esc_html__( 'Hide Published Date', 'ucsc' ),
			self::HIDE_AUTHOR   => esc_html__( 'Hide Author', 'ucsc' ),
			self::HIDE_TAGS     => esc_html__( 'Hide Tags', 'ucsc' ),
		];

		$fields = [];
		foreach ( $toggle_fields as $name => $label ) {
			$fields[] = $this->get_toggle_field( $name, $label );
		}

		return array_merge(
			[
				$this->get_title_field(),
				$this->get_desc_field(),
				$this->get_layout_field(),
				$this->get_more_news_link_field(),
				$this->get_taxonomies_list(),
				$this->get_taxonomies_items(),
				$this->get_posts_per_page_field(),
			],
			$fields
		);
	}

	/**
	 * Remote taxonomy selector. Choices are filled in by News_Blocks_Hooks.
	 *
	 * @return array
	 */
	private function get_taxonomies_list(): array {
		return [
			'key'           => $this->get_field_key( self::TAXONOMIES, self::NAME ),
			'label'         => esc_html__( 'Type of taxonomy', 'ucsc' ),
			'name'          => self::TAXONOMIES,
			'type'          => 'select',
			'choices'       => [],
			'ui'            => 1,
			'return_format' => 'value',
			'instructions'  => esc_html__( 'Select a taxonomy to query.', 'ucsc' ),
		];
	}

	/**
	 * Remote term selector. Multi-select, AJAX-backed.
	 *
	 * @return array
	 */
	private function get_taxonomies_items(): array {
		return [
			'key'           => $this->get_field_key( self::TAX_ITEMS, self::NAME ),
			'label'         => esc_html__( 'Taxonomy terms', 'ucsc' ),
			'name'          => self::TAX_ITEMS,
			'type'          => 'select',
			'multiple'      => 1,
			'ui'            => 1,
			'ajax'          => 1,
			'choices'       => [],
			'return_format' => 'value',
			'instructions'  => esc_html__( 'Select the taxonomy term(s) to query.', 'ucsc' ),
		];
	}

	/**
	 * Build one display toggle.
	 *
	 * @param string $name  Field name.
	 * @param string $label Editor-facing label.
	 *
	 * @return array
	 */
	private function get_toggle_field( string $name, string $label ): array {
		return [
			'key'           => $this->get_field_key( $name, self::NAME ),
			'label'         => $label,
			'name'          => $name,
			'type'          => 'true_false',
			'ui'            => 1,
			'default_value' => 0,
		];
	}

	/**
	 * Heading shown above the posts.
	 *
	 * @return array
	 */
	private function get_title_field(): array {
		return [
			'key'   => $this->get_field_key( self::TITLE, self::NAME ),
			'label' => esc_html__( 'Title', 'ucsc' ),
			'name'  => self::TITLE,
			'type'  => 'text',
		];
	}

	/**
	 * Header alignment, centred by default.
	 *
	 * @return array
	 */
	private function get_layout_field(): array {
		return [
			'key'           => $this->get_field_key( self::LAYOUT, self::NAME ),
			'label'         => esc_html__( 'Header Alignment', 'ucsc' ),
			'name'          => self::LAYOUT,
			'type'          => 'radio',
			'ui'            => 1,
			'default_value' => self::LAYOUT_CENTRE,
			'choices'       => [
				self::LAYOUT_CENTRE => esc_html__( 'Center', 'ucsc' ),
				self::LAYOUT_LEFT   => esc_html__( 'Left', 'ucsc' ),
			],
		];
	}

	/**
	 * Description shown beneath the heading.
	 *
	 * @return array
	 */
	private function get_desc_field(): array {
		return [
			'key'   => $this->get_field_key( self::DESCRIPTION, self::NAME ),
			'label' => esc_html__( 'Description', 'ucsc' ),
			'name'  => self::DESCRIPTION,
			'type'  => 'textarea',
		];
	}

	/**
	 * Optional link rendered after the posts.
	 *
	 * @return array
	 */
	private function get_more_news_link_field(): array {
		return [
			'key'   => $this->get_field_key( self::MORE_NEWS_LINK, self::NAME ),
			'label' => esc_html__( 'More News Link', 'ucsc' ),
			'name'  => self::MORE_NEWS_LINK,
			'type'  => 'link',
		];
	}

	/**
	 * How many posts to render.
	 *
	 * Blocks inserted before this field existed have no saved value; see #106
	 * for the resulting empty-block behaviour.
	 *
	 * @return array
	 */
	private function get_posts_per_page_field(): array {
		return [
			'key'           => $this->get_field_key( 'posts_per_page', self::NAME ),
			'label'         => esc_html__( 'Number of Posts to Show', 'ucsc' ),
			'name'          => 'posts_per_page',
			'type'          => 'select',
			'choices'       => [
				3 => '3 Posts',
				6 => '6 Posts',
				9 => '9 Posts',
			],
			'default_value' => 3,
			'ui'            => 1,
			'return_format' => 'value',
			'instructions'  => esc_html__( 'Select the number of posts to display in the block.', 'ucsc' ),
		];
	}
}
