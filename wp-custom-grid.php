<?php

/**
 * Plugin Name:       WP Custom Grids
 * Plugin URI:        http://gauthamsarang.in/wpcg
 * Description:       Flexible Grids for displaying posts by dragging and dropping
 * Version:           1.0.0
 * Author:            Gautham Sarang
 * Author URI:        http://gauthamsarang.in/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-custom-grid
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'WP_Custom_Grid_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wpcg-activator.php
 */
function activate_wp_custom_grid() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wpcg-activator.php';
	WP_Custom_Grid_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wpcg-deactivator.php
 */
function deactivate_wp_custom_grid() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wpcg-deactivator.php';
	WP_Custom_Grid_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wp_custom_grid' );
register_deactivation_hook( __FILE__, 'deactivate_wp_custom_grid' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wpcg.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wp_custom_grid() {

	$plugin = new WP_Custom_Grids();
	$plugin->run();

}
run_wp_custom_grid();
