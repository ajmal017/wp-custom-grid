<?php

/**
 * Fired during plugin activation
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Plugin_Name
 * @subpackage Plugin_Name/includes
 * @author     Your Name <email@example.com>
 */
class WP_Custom_Grid_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		
			//connect to the database
	global $wpdb;
	
	$charset_collate = $wpdb->get_charset_collate();

	//SQL
	$sql = "CREATE TABLE `{$wpdb->base_prefix}wcg_table` (
	  g_id bigint(50) AUTO_INCREMENT,
	  g_name varchar(255),
	  g_layout varchar(255),
	  g_layout_posts varchar(255),
	  PRIMARY KEY (g_id)
	  
	) $charset_collate;";

	
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		
	//create table
	dbDelta($sql);

		
	}
}
