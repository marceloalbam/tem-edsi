<?php
/**
 * The file that defines css and js files loaded for the plugin
 *
 * A class definition that includes css and js files used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://github.com/marceloalbam/tem-edsi/blob/master/src/class-assets.php
 * @since      1.0.0
 * @package    tem-edsi
 * @subpackage tem-edsi/src
 */

namespace Ttiedsi;

/**
 * Add assets
 *
 * @package marceloalbam
 * @since 0.1.0
 */
class Assets {

	/**
	 * Initialize the class
	 *
	 * @since 0.1.0
	 * @return void
	 */
	public function __construct() {

		// Register script for single-agency page template.
		add_action( 'wp_enqueue_scripts', array( $this, 'register_agency_script' ) );

		// Register global styles used in the theme.
		add_action( 'wp_enqueue_scripts', array( $this, 'register_styles' ), 2 );

		// Enqueue extension styles.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ), 2 );

	}

	/**
	 * Registers all styles used within the plugin
	 *
	 * @since 0.1.0
	 * @return void
	 */
	public function register_styles() {

		global $wp_query;
		$template_name = get_post_meta( $wp_query->post->ID, '_wp_page_template', true );

	}

	/**
	 * Enqueues extension styles
	 *
	 * @since 0.1.0
	 * @return void
	 */
	public function enqueue_styles() {

		global $wp_query;
		$template_name = get_post_meta( $wp_query->post->ID, '_wp_page_template', true );

	}

	/**
	 * Registers the agency script
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function register_agency_script() {

	}


}
