<?php
/**
 * The file that defines the Subsites plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://github.com/malba231180/tti-edsi/blob/master/src/class-subsites.php
 * @since      1.6.0
 * @package    tti-edsi
 * @subpackage tti-edsi/src
 */

namespace Ttiedsi;

/**
 * The subsites plugin class
 *
 * @since 1.6.0
 * @return void
 */
class Subsites {

	/**
	 * Current page id.
	 *
	 * @var page_id
	 */
	private $page_id = false;

	/**
	 * Subsite field.
	 *
	 * @var subsite_field
	 */
	private $subsite = false;

	/**
	 * Initialize the class
	 *
	 * @since 1.6.0
	 * @return void
	 */
	public function __construct() {

		add_action( 'acf/init', array( $this, 'ttie_theme_options_page' ), 11 );

		add_action( 'init', array( $this, 'register_subsite_menus' ) );

		add_action( 'after_setup_theme', array( $this, 'add_image_sizes' ) );

		add_action( 'wp', array( $this, 'load_subsite_content' ) );

	}

	/**
	 * Load subsite content conditionally.
	 *
	 * @since 1.6.11
	 *
	 * @return void
	 */
	public function load_subsite_content() {

		$obj = get_queried_object();

		if ( is_object( $obj ) && property_exists( $obj, 'ID' ) ) {

			$this->page_id = $obj->ID;
			$this->subsite = $this->get_page_subsite_data( $obj->ID );

			if ( false !== $this->subsite ) {

				add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ) );
				add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
				add_action( 'genesis_after_header', array( $this, 'subsite_header' ), 11 );
				add_action( 'genesis_after_header', array( $this, 'do_subsite_menu' ), 11 );
				add_filter( 'wp_nav_menu', array( $this, 'modify_subsite_menu' ), 10, 2 );

			}
		}

	}

	/**
	 * Add image sizes for subsites.
	 *
	 * @since 1.6.6
	 *
	 * @return void
	 */
	public function add_image_sizes() {

		// Subsite header image, 10:1 aspect ratio.
		add_image_size( 'subsite_header_desktop_extra_large', 1920, 192, true );
		add_image_size( 'subsite_header_desktop_large', 1536, 153, true );
		add_image_size( 'subsite_header_desktop_medium', 1440, 144, true );
		add_image_size( 'subsite_header_desktop_medium_small', 1366, 136, true );
		// Subsite header image, 10:3 aspect ratio, mobile only.
		add_image_size( 'subsite_header_mobile_large', 1280, 384, true );
		add_image_size( 'subsite_header_mobile_small', 640, 192, true );

	}

	/**
	 * Return the slug form of a navigation menu display name.
	 *
	 * @since 1.6.0
	 * @param string $name The display name of the navigation menu.
	 *
	 * @return string
	 */
	private function menu_slug( $name ) {

		return str_replace( '-', '_', sanitize_title( $name ) ) . '_menu';

	}

	/**
	 * Register global public scripts for plugin.
	 *
	 * @since 1.6.11
	 *
	 * @return void
	 */
	public function register_scripts() {

		wp_register_script(
			'tti-edsi-subsite-menu',
			TEDSI_DIR_URL . 'js/subsite-menu.min.js',
			array( 'agriflex-public', 'foundation' ),
			filemtime( TEDSI_DIR_PATH . 'js/subsite-menu.min.js' ),
			true
		);

	}


	/**
	 * Enqueue global public scripts for plugin.
	 *
	 * @since 1.6.11
	 *
	 * @return void
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( 'tti-edsi-subsite-menu' );

	}

	/**
	 * Returns slug of current page's subsite.
	 *
	 * @param int $page_id The page ID to check.
	 * @since 1.6.5
	 * @return array | false
	 */
	private function get_page_subsite_data( $page_id ) {

		$fields = get_field( 'subsites', 'option' );

		if ( ! empty( $fields ) ) {

			foreach ( $fields as $field ) {

				$menu_slug  = $this->menu_slug( $field['name'] );
				$locations  = get_nav_menu_locations();
				$menu_obj   = get_term( $locations[ $menu_slug ], 'nav_menu' );
				$menu_items = wp_get_nav_menu_items( $menu_obj->term_id );
				$value      = array(
					'menu_obj'  => $menu_obj,
					'menu_name' => $menu_obj->name,
					'menu_slug' => $menu_slug,
					'menu_id'   => $menu_obj->term_id,
					'field'     => $field,
				);

				if ( is_object( $field['main_page'] ) && $page_id === $field['main_page']->ID ) {

					// Is subsite main page.
					return $value;

				} else {

					// Is subsite menu item.
					foreach ( $menu_items as $menu_item ) {

						$menu_item_page_id = (int) get_post_meta( $menu_item->ID, '_menu_item_object_id', true );

						if ( $page_id === $menu_item_page_id ) {

							return $value;

						}
					}
				}
			}
		}

		return false;

	}

	/**
	 * Add Options Fields
	 *
	 * @since 1.6.0
	 *
	 * @return void
	 */
	public function ttie_theme_options_page() {

		acf_add_local_field(
			array(
				'parent'            => 'group_5e14d2d88b326',
				'key'               => 'field_5f0f561adf8d4',
				'label'             => 'Subsites',
				'name'              => 'subsites',
				'type'              => 'repeater',
				'instructions'      => '',
				'required'          => 0,
				'conditional_logic' => 0,
				'wrapper'           => array(
					'width' => '',
					'class' => '',
					'id'    => '',
				),
				'collapsed'         => '',
				'min'               => 0,
				'max'               => 0,
				'layout'            => 'block',
				'button_label'      => 'Add Subsite',
				'sub_fields'        => array(
					array(
						'key'               => 'field_5f0f567ead2cf',
						'label'             => 'Name',
						'name'              => 'name',
						'type'              => 'text',
						'instructions'      => '',
						'required'          => 0,
						'conditional_logic' => 0,
						'wrapper'           => array(
							'width' => '',
							'class' => '',
							'id'    => '',
						),
						'default_value'     => '',
						'placeholder'       => '',
						'prepend'           => '',
						'append'            => '',
						'maxlength'         => 144,
					),
					array(
						'key'               => 'field_5f4d59bb11a91',
						'label'             => 'Main Page',
						'name'              => 'main_page',
						'type'              => 'post_object',
						'instructions'      => '',
						'required'          => 0,
						'conditional_logic' => 0,
						'wrapper'           => array(
							'width' => '',
							'class' => '',
							'id'    => '',
						),
						'post_type'         => array(
							0 => 'page',
						),
						'taxonomy'          => '',
						'allow_null'        => 1,
						'multiple'          => 0,
						'return_format'     => 'object',
						'ui'                => 1,
					),
					array(
						'key'               => 'field_5f3436cfaaf8c',
						'label'             => 'Header',
						'name'              => 'header',
						'type'              => 'group',
						'instructions'      => '',
						'required'          => 0,
						'conditional_logic' => 0,
						'wrapper'           => array(
							'width' => '',
							'class' => '',
							'id'    => '',
						),
						'layout'            => 'block',
						'sub_fields'        => array(
							array(
								'key'               => 'field_5f3436e2aaf8d',
								'label'             => 'Image',
								'name'              => 'image',
								'type'              => 'image',
								'instructions'      => 'Must be at least 1920px wide and 384px tall',
								'required'          => 0,
								'conditional_logic' => 0,
								'wrapper'           => array(
									'width' => '',
									'class' => '',
									'id'    => '',
								),
								'return_format'     => 'array',
								'preview_size'      => 'medium',
								'library'           => 'all',
								'min_width'         => '1920',
								'min_height'        => '384',
								'min_size'          => '',
								'max_width'         => '',
								'max_height'        => '',
								'max_size'          => '',
								'mime_types'        => 'jpg, jpeg',
							),
							array(
								'key'               => 'field_5f343c6a81d73',
								'label'             => 'Title',
								'name'              => 'title',
								'type'              => 'text',
								'instructions'      => '',
								'required'          => 0,
								'conditional_logic' => 0,
								'wrapper'           => array(
									'width' => '',
									'class' => '',
									'id'    => '',
								),
								'default_value'     => '',
								'placeholder'       => '',
								'prepend'           => '',
								'append'            => '',
								'maxlength'         => '',
							),
							array(
								'key'               => 'field_5f343b7881d72',
								'label'             => 'Description',
								'name'              => 'description',
								'type'              => 'text',
								'instructions'      => '',
								'required'          => 0,
								'conditional_logic' => 0,
								'wrapper'           => array(
									'width' => '',
									'class' => '',
									'id'    => '',
								),
								'default_value'     => '',
								'placeholder'       => '',
								'prepend'           => '',
								'append'            => '',
								'maxlength'         => '',
							),
						),
					),
				),
			)
		);
	}

	/**
	 * Register subsite menus based on custom fields.
	 *
	 * @since 1.6.0
	 * @return void
	 */
	public function register_subsite_menus() {

		$field = get_field( 'subsites', 'option' );

		if ( $field ) {

			$locations = array();

			foreach ( $field as $subsite ) {

				$name               = $subsite['name'] . ' Navigation Menu';
				$slug               = $this->menu_slug( $subsite['name'] );
				$locations[ $slug ] = __( $name, 'tti-edsi' ); // phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText

			}

			register_nav_menus( $locations );

		}

	}

	/**
	 * Display subsite menu.
	 *
	 * @since 1.6.0
	 *
	 * @return void
	 */
	public function do_subsite_menu() {

		$subsite          = $this->subsite;
		$field            = $subsite['field'];
		$menu_linked_name = $subsite['menu_name'];

		if (
			array_key_exists( 'main_page', $field ) &&
			is_object( $field['main_page'] )
		) {

			$args = array(
				'open'    => '<a %s>',
				'close'   => '</a>',
				'content' => $subsite['menu_name'],
				'context' => 'subsite-menu-title',
				'atts'    => array(
					'href' => get_permalink( $field['main_page']->ID ),
				),
				'echo'    => false,
			);

			$menu_linked_name = genesis_markup( $args );

		}

		$menu_content = '<div id="%s" class="subsite-menu dark-blue" data-sticky-container><div class="sticky-menu" data-sticky data-margin-top="0" data-top-anchor="subsite-header:bottom" data-sticky-on="small"><div class="grid-container"><div class="grid-x grid-padding-x"><div class="subsite-title menu-title cell small-auto medium-shrink h4"><div class="grid-x"><div class="cell auto">%s</div><div class="cell shrink show-for-small-only"><div class="title-bars title-bar-right"><div class="title-bar title-bar-sub-navigation" data-responsive-toggle="nav-menu-secondary" style="display: inline-block;"><button class="menu-icon" type="button" data-toggle="nav-menu-secondary">&bull;&bull;&bull;<span class="screen-reader-text">Menu - %s</span></button></div></div></div></div></div><div class="cell small-12 medium-auto"><div id="nav-menu-secondary">';

		echo wp_kses_post(
			sprintf(
				$menu_content,
				$subsite['menu_slug'],
				$menu_linked_name,
				$subsite['menu_name']
			)
		);

		genesis_nav_menu(
			[
				'theme_location'  => $subsite['menu_slug'],
				'container_class' => 'grid-container',
				'menu_class'      => 'menu grid-x grid-padding-x',
			]
		);

		echo wp_kses_post( '</div></div></div></div></div></div>' );

	}

	/**
	 * Filters the HTML content for subsite navigation menus.
	 *
	 * @since 1.6.5
	 *
	 * @param string   $nav_menu The HTML content for the navigation menu.
	 * @param stdClass $args     An object containing wp_nav_menu() arguments.
	 */
	public function modify_subsite_menu( $nav_menu, $args ) {

		if ( $this->subsite['menu_slug'] === $args->theme_location ) {

			$nav_menu = str_replace( '<ul', '<ul data-responsive-menu="accordion medium-dropdown"', $nav_menu );

		}

		return $nav_menu;

	}

	/**
	 * Output the subsite header
	 *
	 * @since 1.6.6
	 * @return void
	 */
	public function subsite_header() {

		$subsite = $this->subsite;

		// Image.
		$field        = $subsite['field'];
		$image_id     = $field['header']['image']['ID'];
		$image_meta   = wp_get_attachment_metadata( $image_id );
		$image_sizes  = array_keys( $image_meta['sizes'] );
		$desktop_size = '';
		if ( in_array( 'subsite_header_desktop_extra_large', $image_sizes, true ) ) {
			$desktop_size = 'subsite_header_desktop_extra_large';
		} elseif ( in_array( 'subsite_header_desktop_large', $image_sizes, true ) ) {
			$desktop_size = 'subsite_header_desktop_large';
		} elseif ( in_array( 'subsite_header_desktop_medium', $image_sizes, true ) ) {
			$desktop_size = 'subsite_header_desktop_medium';
		} elseif ( in_array( 'subsite_header_desktop_medium_small', $image_sizes, true ) ) {
			$desktop_size = 'subsite_header_desktop_medium_small';
		}
		$mobile_size = 'subsite_header_mobile_large';
		$mobile_img  = wp_get_attachment_image( $image_id, $mobile_size, false, array( 'class' => "hide-for-medium attachment-$mobile_size size-$mobile_size" ) );
		$desktop_img = wp_get_attachment_image( $image_id, $desktop_size, false, array( 'class' => "hide-for-small-only attachment-$desktop_size size-$desktop_size" ) );

		// Text.
		$site_title       = $field['header']['title'];
		$site_description = $field['header']['description'];
		$banner_text      = '';

		if ( ! empty( $site_title ) ) {
			$banner_text .= '<div class="title">' . $site_title . '</div>';
		}

		if ( ! empty( $site_description ) ) {
			$banner_text .= '<div class="subtitle">' . $site_description . '</div>';
		}

		if ( ! empty( $banner_text ) ) {
			$banner_text = "<div class=\"wrap\"><div class=\"grid-container\"><div class=\"banner-text\">{$banner_text}</div></div></div>";
		}

		// Output.
		$subsite_header = sprintf(
			'<div id="subsite-header" class="banner subsite-header">%s%s%s</div>',
			$mobile_img,
			$desktop_img,
			$banner_text
		);

		echo wp_kses_post( $subsite_header );

	}


}
