<?php
/**
 * The file that renders the edsi page content
 *
 * A custom page template for EDSI
 *
 * @link       https://github.com/marceloalbam/tem-edsi/blob/master/templates/edsi.php
 * @since      0.1.0
 * @package    tem-edsi
 * @subpackage tem-edsi/templates
 */

/**
 * Template Name: EDSI
 */
remove_action( 'genesis_entry_header', 'genesis_entry_header_markup_open', 5 );
remove_action( 'genesis_entry_header', 'genesis_entry_header_markup_close', 15 );
remove_action( 'genesis_entry_header', 'genesis_do_post_title' );
remove_action( 'genesis_post_title', 'genesis_do_post_title' );
add_filter( 'genesis_pre_get_option_site_layout', '__genesis_return_full_width_content' );
add_filter( 'genesis_structural_wrap-site-inner', 'ttie_class_site_inner_wrap' );
add_filter( 'safe_style_css', 'ttie_add_safe_style' );
add_action( 'genesis_entry_content', 'ttie_edsi_content' );
remove_action( 'genesis_entry_content', 'genesis_do_post_content' );
remove_action( 'genesis_header', 'tti_do_nav', 12 );
add_action( 'genesis_header', 'tti_do_nav_edsi', 12 );

// Template CSS.
add_action( 'wp_enqueue_scripts', 'agdorg_register_edsi_styles', 1 );
add_action( 'wp_enqueue_scripts', 'agdorg_enqueue_edsi_styles', 2 );

/**
 * Redesign edsi main menu.
 */
function tti_do_nav_edsi() {
	echo '<div class="edsi-primary-menu"><img src="' . wp_kses_post( TEDSI_DIR_URL ) . 'images/edsilogo.webp"></img><nav class="nav-primary" aria-label="Main" itemscope itemtype="https://schema.org/SiteNavigationElemnt" id="genesis-nav-primary">';
	echo wp_nav_menu( array( 'menu' => 'primary' ) );
	echo wp_kses_post( tti_mobile_menu_close() );
	echo '</nav></div>';
}

/**
 * Registers template styles.
 *
 * @since 1.4.4
 * @return void
 */
function agdorg_register_edsi_styles() {

	wp_register_style(
		'tti-edsi-edsi-styles',
		TEDSI_DIR_URL . 'css/edsi.css',
		array( 'tti-base', 'tti-style' ),
		filemtime( TEDSI_DIR_PATH . 'css/edsi.css' ),
		'screen'
	);

}

/**
 * Enqueues template styles.
 *
 * @since 1.4.4
 * @return void
 */
function agdorg_enqueue_edsi_styles() {

	wp_enqueue_style( 'tti-edsi-edsi-styles' );

}

/**
 * Add grid class name
 *
 * @since 1.1.3
 * @param string $output The wrap HTML.
 * @return string
 */
function ttie_class_site_inner_wrap( $output ) {

	$output = str_replace( 'class="grid-container ', 'class="grid-container full ', $output );

	return $output;

}

/**
 * Add safe styles for Item 5 form html in custom field.
 *
 * @since 0.6.7
 * @param array $styles Current list of safe styles.
 * @return array
 */
function ttie_add_safe_style( $styles ) {
	$styles[] = 'position';
	$styles[] = 'left';
	$styles[] = 'display';
	$styles[] = 'background-size';
	return $styles;
}

/**
 * Provide content for the edsi page template.
 *
 * @since 0.1.0
 * @return void
 */
function ttie_edsi_content() {

	// Do page content.
		the_content();
}

genesis();
