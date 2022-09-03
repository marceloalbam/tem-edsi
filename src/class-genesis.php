<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://github.com/marceloalbam/tem-edsi/blob/master/src/class-genesis.php
 * @since      1.0.2
 * @package    tem-edsi
 * @subpackage tem-edsi/src
 */

namespace Ttiedsi;

/**
 * The core plugin class
 *
 * @since 1.0.2
 * @return void
 */
class Genesis {

	/**
	 * Initialize the class
	 *
	 * @since 1.0.2
	 * @return void
	 */
	public function __construct() {

			add_action( 'get_header', array( $this, 'relocate_post_header' ) );

	}

	/**
	 * Move post header conditionally
	 *
	 * @since 1.0.3
	 * @return void
	 */
	public function relocate_post_header() {

		$template_slug = get_page_template_slug();

		if ( 'edsi.php' !== $template_slug && is_singular( array( 'post', 'page' ) ) ) {

			remove_action( 'genesis_entry_header', 'genesis_entry_header_markup_open' );
			remove_action( 'genesis_entry_header', 'genesis_entry_header_markup_close' );
			remove_action( 'genesis_entry_header', 'genesis_do_post_title' );
			remove_action( 'genesis_post_title', 'genesis_do_post_title' );
			add_action( 'genesis_before_content_sidebar_wrap', 'genesis_entry_header_markup_open', 5 );
			add_action( 'genesis_before_content_sidebar_wrap', 'genesis_entry_header_markup_close', 15 );
			add_action( 'genesis_before_content_sidebar_wrap', 'genesis_do_post_title' );

		}

	}
}
