<?php
/**
 * Query-loop field group base class.
 *
 * @package ucsc
 */

declare(strict_types=1);

namespace UCSC\Blocks\Blocks;

use UCSC\Blocks\Blocks\Contracts\Taxonomies;
use UCSC\Blocks\Blocks\Traits\With_Taxonomies;

/**
 * Base for field groups that let an editor choose which posts to display.
 *
 * Offers three mutually exclusive modes, switched by the QUERY_TYPE button
 * group and shown or hidden with ACF conditional logic:
 *
 *   latest     the most recent posts, no further configuration
 *   automatic  every post in a chosen taxonomy term
 *   manual     a hand-picked, ordered list of posts
 *
 * Subclasses override the protected properties to change the card label,
 * limits and allowed post types. Query_Loop_Controller reads the saved values
 * back out.
 */
abstract class Query_Loop extends ACF_Group implements Taxonomies {

	use With_Taxonomies;

	/**
	 * Field name of the group wrapping the whole query configuration.
	 *
	 * @var string
	 */
	public const QUERY_LOOP = 'query_loop';

	/**
	 * Field name of the mode selector.
	 *
	 * @var string
	 */
	public const QUERY_TYPE = 'query_type';

	/**
	 * Mode value: most recent posts.
	 *
	 * @var string
	 */
	public const LATEST = 'latest';

	/**
	 * Mode value: pull from a taxonomy term.
	 *
	 * @var string
	 */
	public const AUTOMATIC = 'automatic';

	/**
	 * Mode value: hand-picked posts.
	 *
	 * @var string
	 */
	public const MANUAL = 'manual';

	/**
	 * Field name of the group holding the automatic-mode fields.
	 *
	 * @var string
	 */
	public const AUTOMATIC_GROUP = 'automatic_group';

	/**
	 * Field name of the category selector.
	 *
	 * @var string
	 */
	public const CATEGORIES = 'categories';

	/**
	 * Field name of the manual-mode repeater.
	 *
	 * @var string
	 */
	public const MANUAL_CARDS = 'manual_cards';

	/**
	 * Field name of a single row within the manual repeater.
	 *
	 * @var string
	 */
	public const MANUAL_CARD = 'manual_card';

	/**
	 * Label for a single card in manual mode. Override per block.
	 *
	 * @var string
	 */
	protected string $default_manual_card_label = 'Card';

	/**
	 * Maximum number of manual cards. Override per block.
	 *
	 * @var int
	 */
	protected int $max_manual_cards = 4;

	/**
	 * Minimum number of manual cards. Override per block.
	 *
	 * @var int
	 */
	protected int $min_manual_cards = 0;

	/**
	 * Editor instructions shown on the manual repeater. Override per block.
	 *
	 * @var string
	 */
	protected string $instructions = '';

	/**
	 * The owning group name, used to compose every field key.
	 *
	 * Set by get_query_loop_group() rather than declared, because the field
	 * definitions are built before the subclass name is otherwise available.
	 *
	 * @var string
	 */
	protected string $block_name = '';

	/**
	 * Post types selectable in manual mode. Override per block.
	 *
	 * @var string[]
	 */
	protected array $allowed_post_types = [
		'post',
	];

	/**
	 * Build the whole query-selection field group.
	 *
	 * @param string $name               The owning group name.
	 * @param array  $allowed_post_types Post types offered in manual mode; falls back to the default.
	 *
	 * @return array
	 */
	public function get_query_loop_group( string $name, array $allowed_post_types = [] ): array {
		$this->block_name = $name;

		if ( ! empty( $allowed_post_types ) ) {
			$this->allowed_post_types = $allowed_post_types;
		}

		return [
			'key'        => $this->get_field_key( self::QUERY_LOOP, $name ),
			'type'       => 'group',
			'label'      => esc_html__( 'Posts Selection', 'ucsc' ),
			'name'       => self::QUERY_LOOP,
			'sub_fields' => $this->get_sub_fields(),
		];
	}

	/**
	 * The three mode-specific field sets.
	 *
	 * @return array
	 */
	protected function get_sub_fields(): array {
		return [
			$this->get_query_type_filed(),
			$this->get_automatic_query(),
			$this->get_manual_query(),
		];
	}

	/**
	 * The mode selector. Defaults to latest.
	 *
	 * Note: the method name is misspelled ("filed"); left as-is to avoid an
	 * unrelated rename in a documentation change.
	 *
	 * @return array
	 */
	protected function get_query_type_filed(): array {
		return [
			'key'           => $this->get_field_key( self::QUERY_TYPE, $this->block_name ),
			'type'          => 'button_group',
			'layout'        => 'vertical',
			'name'          => self::QUERY_TYPE,
			'label'         => esc_html__( 'Curated Content', 'ucsc' ),
			'choices'       => [
				self::LATEST    => esc_html__( 'Most Recent Posts', 'ucsc' ),
				self::AUTOMATIC => esc_html__( 'Pull from category', 'ucsc' ),
				self::MANUAL    => esc_html__( 'Select manually', 'ucsc' ),
			],
			'default_value' => self::LATEST,
		];
	}

	/**
	 * Fields shown in automatic mode: taxonomy and term pickers.
	 *
	 * Note: the label is esc_html__( '', 'ucsc' ), which returns the PO file's
	 * metadata header rather than an empty string. Tracked in #106.
	 *
	 * @return array
	 */
	protected function get_automatic_query(): array {
		return [
			'key'               => $this->get_field_key( self::AUTOMATIC_GROUP, $this->block_name ),
			'type'              => 'group',
			'label'             => esc_html__( '', 'ucsc' ),
			'name'              => self::QUERY_LOOP,
			'sub_fields'        => [
				$this->get_taxonomies_list( $this->block_name ),
				$this->get_taxonomies_items( $this->block_name ),
			],
			'conditional_logic' => [
				[
					[
						'field'    => $this->get_field_key( self::QUERY_TYPE, $this->block_name ),
						'operator' => '==',
						'value'    => self::AUTOMATIC,
					],
				],
			],
			'wrapper'           => [
				'class' => 'acf-no-style',
			],
		];
	}

	/**
	 * A category selector.
	 *
	 * Not currently wired into get_sub_fields(); kept for blocks that query
	 * categories directly rather than through the taxonomy picker.
	 *
	 * @return array
	 */
	protected function get_categories_field(): array {
		return [
			'key'           => $this->get_field_key( self::CATEGORIES, self::AUTOMATIC_GROUP ),
			'label'         => esc_html__( 'Categories', 'ucsc' ),
			'name'          => self::CATEGORIES,
			'type'          => 'taxonomy',
			'taxonomy'      => 'category',
			'add_term'      => 0,
			'field_type'    => 'select',
			'return_format' => 'id',
			'instructions'  => esc_html__( 'Select the category to query.', 'ucsc' ),
		];
	}

	/**
	 * The manual-mode repeater of hand-picked posts.
	 *
	 * @return array
	 */
	protected function get_manual_query(): array {
		return [
			'key'               => $this->get_field_key( self::MANUAL_CARDS, $this->block_name ),
			'type'              => 'repeater',
			'name'              => self::MANUAL_CARDS,
			'sub_fields'        => [
				$this->get_manual_card(),
			],
			'button_label'      => sprintf( 'Add %s', $this->default_manual_card_label ),
			'min'               => $this->min_manual_cards,
			'max'               => $this->max_manual_cards,
			'instructions'      => $this->instructions,
			'layout'            => 'block',
			'conditional_logic' => [
				[
					[
						'field'    => $this->get_field_key( self::QUERY_TYPE, $this->block_name ),
						'operator' => '==',
						'value'    => self::MANUAL,
					],
				],
			],
		];
	}

	/**
	 * A single post picker within the manual repeater.
	 *
	 * @return array
	 */
	protected function get_manual_card(): array {
		return [
			'key'           => $this->get_field_key( self::MANUAL_CARD, $this->block_name ),
			'label'         => $this->default_manual_card_label,
			'post_type'     => $this->allowed_post_types,
			'type'          => 'post_object',
			'name'          => self::MANUAL_CARD,
			'return_format' => 'id',
			'ui'            => 1,
			'required'      => 0,
		];
	}
}
