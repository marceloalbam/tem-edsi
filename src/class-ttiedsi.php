<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://github.com/marceloalbam/tem-edsi/blob/master/src/class-ttiedsi.php
 * @since      1.0.0
 * @package    tem-edsi
 * @subpackage tem-edsi/src
 */

/**
 * The core plugin class
 *
 * @since 0.1.0
 * @return void
 */
class Ttiedsi {

	/**
	 * File name
	 *
	 * @var file
	 */
	private static $file = __FILE__;

	/**
	 * Instance
	 *
	 * @var instance
	 */
	private static $instance;

	/**
	 * Initialize the class
	 *
	 * @since 0.1.0
	 * @return void
	 */
	private function __construct() {

		$this->register_templates();

		add_action( 'init', array( $this, 'init' ) );

		/* Subsites. */
		require_once TEDSI_DIR_PATH . 'src/class-subsites.php';
		$ado_subsites = new \Ttiedsi\Subsites();

	}

	/**
	 * Initialize the various classes
	 *
	 * @since 0.1.0
	 * @return void
	 */
	public function init() {

		/* Genesis customizations. */
		require_once TEDSI_DIR_PATH . 'src/class-genesis.php';
		$ado_genesis = new \Ttiedsi\Genesis();

		/* Set up asset files */
		require_once TEDSI_DIR_PATH . 'src/class-assets.php';
		$ado_assets = new \Ttiedsi\Assets();

		/* Flush rewrite rules on plugin installation */
		if ( get_option( 'ttie_flush_rewrite_rules_flag' ) ) {
			flush_rewrite_rules();
			delete_option( 'ttie_flush_rewrite_rules_flag' );
		}

	}

	/**
	 * Initialize Advanced Custom Fields files
	 *
	 * @since 0.1.0
	 * @return void
	 */
	public function custom_fields() {
		if ( class_exists( 'acf' ) ) {
			require_once TEDSI_DIR_PATH . 'fields/edsi-fields.php';
		}
	}

	/**
	 * Add ACF toolbars
	 *
	 * @since 0.1.0
	 * @param array $toolbars The list of toolbars.
	 * @return array
	 */
	public function toolbars( $toolbars ) {

		/* Add new toolbars */
		$toolbars['Simple Text']    = array();
		$toolbars['Simple Text'][1] = array( 'formatselect', 'bold', 'italic', 'underline', 'link', 'unlink', 'alignleft', 'aligncenter', 'alignjustify', 'bullist', 'numlist' );

		$toolbars['Simple Title']    = array();
		$toolbars['Simple Title'][1] = array( 'bold', 'italic', 'underline' );

		return $toolbars;

	}

	/**
	 * Initialize page templates
	 *
	 * @since 0.1.0
	 * @return void
	 */
	private function register_templates() {

		require_once TEDSI_DIR_PATH . '/src/class-pagetemplate.php';
		$edsi = new \Ttiedsi\PageTemplate( TEDSI_TEMPLATE_PATH, 'edsi.php', 'EDSI' );
		$edsi->register();

	}

	/**
	 * Autoloads any classes called within the theme
	 *
	 * @since 0.1.0
	 * @param string $classname The name of the class.
	 * @return void.
	 */
	public static function autoload( $classname ) {

		$filename = dirname( __FILE__ ) .
			DIRECTORY_SEPARATOR .
			str_replace( '_', DIRECTORY_SEPARATOR, $classname ) .
			'.php';

		if ( file_exists( $filename ) ) {
			require $filename;
		}

	}

	/**
	 * Return instance of class
	 *
	 * @since 0.1.0
	 * @return object.
	 */
	public static function get_instance() {

		return null === self::$instance ? new self() : self::$instance;

	}

	/**
	 * Remove heading tags from agency post excerpt.
	 *
	 * @since 0.1.0
	 * @param string  $post_excerpt The post excerpt.
	 * @param WP_Post $post Post object.
	 * @return string.
	 */
	public static function agency_post_excerpt( $post_excerpt, $post ) {
		if ( 'agency' === $post->post_type ) {
			$post_excerpt   = preg_replace( '/<h[2-6]{1}[^>]*>[^<]*<\/h[2-6]{1}\s*>/', '', $post->post_content );
			$post_excerpt   = wp_strip_all_tags( $post_excerpt, true );
			$excerpt_length = apply_filters( 'excerpt_length', 55 );
			$excerpt_more   = apply_filters( 'excerpt_more', ' [&hellip;]' );
			$post_excerpt   = wp_trim_words( $post_excerpt, $excerpt_length, $excerpt_more );
		}

		return $post_excerpt;
	}

}
