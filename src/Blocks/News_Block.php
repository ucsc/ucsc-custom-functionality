<?php declare(strict_types=1);

namespace UCSC\Blocks\Blocks;

class News_Block extends ACF_Group {

	public const NAME = 'news_query_block';

	public const TITLE        = 'news_title';
	public const DESCRIPTION   = 'news_desc';
	public const LAYOUT        = 'layout';
	public const LAYOUT_LEFT   = 'layout_left';
	public const LAYOUT_CENTRE = 'layout_centre';
	
	public const MORE_NEWS_LINK = 'more_news_link';

	public const TAXONOMIES = 'taxonomies';
	public const TAX_ITEMS  = 'taxonomy_items';

	public const HIDE_EXCERPT  = 'hide_excerpt';
	public const HIDE_IMAGE    = 'hide_image';
	public const HIDE_DATE     = 'hide_date';
	public const HIDE_AUTHOR   = 'hide_author';
	public const HIDE_TAGS     = 'hide_tags';
	public const HIDE_CATEGORY = 'hide_category';

	public const ALLOWED_TAX = [
		'academics',
		'administration',
		'category',
		'colleges',
		'person',
		'post_tag',
	];

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

	protected function get_title(): string {
		return esc_html__( 'Modal Block', 'ucsc' );
	}

	protected function get_key(): string {
		return self::NAME;
	}

	protected function get_fields(): array {
		$toggle_fields = [
			self::HIDE_IMAGE 	=> esc_html__( 'Hide Featured Image', 'ucsc' ),
			self::HIDE_CATEGORY => esc_html__( 'Hide Category', 'ucsc' ),
			self::HIDE_EXCERPT  => esc_html__( 'Hide Excerpt', 'ucsc' ),
			self::HIDE_DATE 	=> esc_html__( 'Hide Published Date', 'ucsc' ),
			self::HIDE_AUTHOR 	=> esc_html__( 'Hide Author', 'ucsc' ),
			self::HIDE_TAGS 	=> esc_html__( 'Hide Tags', 'ucsc' ),
		];

		$fields = [];
		foreach ( $toggle_fields as $name => $label ) {
			$fields[] = $this->get_toggle_field( $name, $label );
		}

		return array_merge( [
			$this->get_title_field(),
			$this->get_desc_field(),
			$this->get_layout_field(),
			$this->get_more_news_link_field(),
			$this->get_taxonomies_list(),
			$this->get_taxonomies_items(),
			$this->get_posts_per_page_field(), // Add the dropdown field here
		], $fields );
	}

	private function get_taxonomies_list(): array {
		return [
			'key'           => $this->get_field_key( self::TAXONOMIES, self::NAME ),
			'label'         => esc_html__( 'Type of taxonomy', 'ucsc' ),
			'name'          => self::TAXONOMIES,
			'type'          => 'select',
			'choices'       => [],
			'ui'       		=> 1,
			'return_format' => 'value',
			'instructions'  => esc_html__( 'Select a taxonomy to query.', 'ucsc' ),
		];
	}

	private function get_taxonomies_items(): array {
		return [
			'key'           => $this->get_field_key( self::TAX_ITEMS, self::NAME ),
			'label'         => esc_html__( 'Taxonomy terms', 'ucsc' ),
			'name'          => self::TAX_ITEMS,
			'type'          => 'select',
			'multiple'      => 1,
			'ui'      		=> 1,
			'ajax'			=> 1,
			'choices'       => [],
			'return_format' => 'value',
			'instructions'  => esc_html__( 'Select the taxonomy term(s) to query.', 'ucsc' ),
		];
	}

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

	private function get_title_field(): array {
		return [
			'key'   => $this->get_field_key( self::TITLE, self::NAME ),
			'label' => esc_html__( 'Title', 'ucsc' ),
			'name'  => self::TITLE,
			'type'  => 'text',
		];
	}

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

	private function get_desc_field(): array {
		return [
			'key'   => $this->get_field_key( self::DESCRIPTION, self::NAME ),
			'label' => esc_html__( 'Description', 'ucsc' ),
			'name'  => self::DESCRIPTION,
			'type'  => 'textarea',
		];
	}
	
	private function get_more_news_link_field(): array {
		return [
			'key'   => $this->get_field_key( self::MORE_NEWS_LINK, self::NAME ),
			'label' => esc_html__( 'More News Link', 'ucsc' ),
			'name'  => self::MORE_NEWS_LINK,
			'type'  => 'link',
		];
	}

	private function get_posts_per_page_field(): array {
		return [
			'key'           => $this->get_field_key( 'posts_per_page', self::NAME ),
			'label'         => esc_html__( 'Number of Posts to Show', 'ucsc' ),
			'name'          => 'posts_per_page',
			'type'          => 'select',
			'choices'       => [
				3  => '3 Posts',
				6  => '6 Posts',
				9  => '9 Posts'
			],
			'default_value' => 6, // Default to  posts
			'ui'            => 1,
			'return_format' => 'value',
			'instructions'  => esc_html__( 'Select the number of posts to display in the block.', 'ucsc' ),
		];
	}

}
