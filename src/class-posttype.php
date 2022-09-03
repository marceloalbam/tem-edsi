<?php
/**
 * The file that initializes custom post types
 *
 * A class definition that registers custom post types with their attributes
 *
 * @link       https://github.com/malba231180/tti-edsi/blob/master/src/class-posttype.php
 * @since      0.1.0
 * @package    tti-edsi
 * @subpackage tti-edsi/src
 */

namespace Ttiedsi;

/**
 * The post type registration class
 *
 * @since 0.1.0
 * @return void
 */
class PostType {

	/**
	 * Post type slug
	 *
	 * @var search_file
	 */
	private $post_type;

	/**
	 * Single template file name
	 *
	 * @var single_file
	 */
	private $single_file = false;

	/**
	 * Archive template file name
	 *
	 * @var archive_file
	 */
	private $archive_file = false;

	/**
	 * Search template file name
	 *
	 * @var search_file
	 */
	private $search_file = false;

	/**
	 * Builds and registers the custom taxonomy.
	 *
	 * @param  array  $name       The post type name.
	 * @param  string $path       The post template path.
	 * @param  string $slug       The post type slug.
	 * @param  string $tag        The namespace of the plugin for translation purposes.
	 * @param  array  $taxonomies The taxonomies this post type supports. Accepts arguments found in
	 *                            WordPress core register_post_type function.
	 * @param  string $icon       The icon used in the admin navigation sidebar.
	 * @param  array  $supports   The attributes this post type supports. Accepts arguments found in
	 *                            WordPress core register_post_type function.
	 * @param  array  $templates  The template file names.
	 * @return void
	 */
	public function __construct(
		$name = array(
			'singular' => '',
			'plural'   => '',
		),
		$path,
		$slug,
		$tag,
		$taxonomies = array(
			'category',
			'post_tag',
		),
		$icon = 'dashicons-portfolio',
		$supports = array( 'title' ),
		$templates = array()
	) {

		$this->post_type = $slug;

		if ( array_key_exists( 'single', $templates ) ) {
			$this->single_file = $templates['single'];
		}

		if ( array_key_exists( 'archive', $templates ) ) {
			$this->archive_file = $templates['archive'];
		}

		if ( array_key_exists( 'search', $templates ) ) {
			$this->search_file = $templates['search'];
		}

		$singular = $name['singular'];
		$plural   = $name['plural'];

		// Backend labels.
		$labels = array(
			'name'               => $plural,
			'singular_name'      => $singular,
			'add_new'            => __( 'Add New', 'tti-edsi' ),
			'add_new_item'       => __( 'Add New', 'tti-edsi' ) . " $singular",
			'edit_item'          => __( 'Edit', 'tti-edsi' ) . " $singular",
			'new_item'           => __( 'New', 'tti-edsi' ) . " $singular",
			'view_item'          => __( 'View', 'tti-edsi' ) . " $singular",
			'search_items'       => __( 'Search', 'tti-edsi' ) . " $plural",
			/* translators: placeholder is the plural taxonomy name */
			'not_found'          => sprintf( esc_html__( 'No %d Found', 'tti-edsi' ), $plural ),
			/* translators: placeholder is the plural taxonomy name */
			'not_found_in_trash' => sprintf( esc_html__( 'No %d found in trash', 'tti-edsi' ), $plural ),
			'parent_item_colon'  => '',
			'menu_name'          => $plural,
		);

		// Post type arguments.
		$args = array(
			'can_export'         => true,
			'has_archive'        => true,
			'labels'             => $labels,
			'menu_icon'          => $icon,
			'menu_position'      => 20,
			'public'             => true,
			'publicly_queryable' => true,
			'show_in_rest'       => true,
			'show_in_menu'       => true,
			'show_in_admin_bar'  => true,
			'show_in_nav_menus'  => true,
			'show_ui'            => true,
			'supports'           => $supports,
			'taxonomies'         => $taxonomies,
			'rewrite'            => array(
				'with_front' => false,
				'slug'       => $slug,
			),
		);

		// Register the post type.
		register_post_type( $slug, $args );

		// Register the post type templates.
		require_once TEDSI_DIR_PATH . 'src/class-posttemplates.php';
		$post_template = new PostTemplates( $path, $slug, $this->single_file, $this->archive_file, $this->search_file );

	}

}
