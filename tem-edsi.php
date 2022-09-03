<?php
/**
 * CTS - EDSI
 *
 * @package      marceloalbam
 * @author       MA
 * @copyright    2018 Texas A&M AgriLife Communications
 * @license      GPL-2.0+
 *
 * @wordpress-plugin
 * Plugin Name:  CTS - EDSI
 * Plugin URI:   https://github.com/marceloalbam/tem-edsi
 * Description:  Core functionality for CTS on TTIBase (EDSI).
 * Version:      1.0.0
 * Author:       MA
 * Author URI:   https://github.com/marceloalbam
 * Author Email: marce.alba.m@gmail.com
 * Text Domain:  tem-edsi
 * License:      GPL-2.0+
 * License URI:  http://www.gnu.org/licenses/gpl-2.0.txt
 */

/* Autoload */
require 'vendor/autoload.php';

/* Define some useful constants */
define( 'TEDSI_DIRNAME', 'tem-edsi' );
define( 'TEDSI_DIR_PATH', plugin_dir_path( __FILE__ ) );
define( 'TEDSI_DIR_FILE', __FILE__ );
define( 'TEDSI_DIR_URL', plugin_dir_url( __FILE__ ) );
define( 'TEDSI_TEXTDOMAIN', 'tem-edsi' );
define( 'TEDSI_TEMPLATE_PATH', TEDSI_DIR_PATH . 'templates' );

/* Code for plugins */
register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );
register_activation_hook( __FILE__, 'tti_edsi_activation' );

/**
 * Helper option flag to indicate rewrite rules need flushing
 *
 * @since 0.1.0
 * @return void
 */
function tti_edsi_activation() {
	$theme = wp_get_theme();
	if ( 'TTI Base' !== $theme->name ) {
		$error = sprintf(
			/* translators: %s: URL for plugins dashboard page */
			__(
				'Plugin NOT activated: The <strong>CTS - EDSI Plugin</strong> needs the <strong>TTIBase Theme</strong> to be installed and activated first. <a href="%s">Back to plugins page</a>',
				'af4-college'
			),
			get_admin_url( null, '/plugins.php' )
		);
		wp_die( wp_kses_post( $error ) );
	}

	if ( ! get_option( 'tti_edsi_flush_rewrite_rules_flag' ) ) {
		add_option( 'tti_edsi_flush_rewrite_rules_flag', true );
	}
}

/**
 * The core plugin class that is used to initialize the plugin
 */
require TEDSI_DIR_PATH . 'src/class-ttiedsi.php';

/* Autoload all classes */
spl_autoload_register( 'Ttiedsi::autoload' );
Ttiedsi::get_instance();
