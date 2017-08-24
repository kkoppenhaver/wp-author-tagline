<?php

/**
 * Plugin Name:       WP Author Tagline
 * Plugin URI:        http://alphaparticle.com/
 * Description:       Lets authors set their own tagline that can be displayed in any post using a shortcode
 * Version:           1.0.0
 * Author:            Keanan Koppenhaver
 * Author URI:        http://alphaparticle.com.com/
 * License:           MIT
 * License URI:       https://github.com/kkoppenhaver/wp-author-tagline/blob/master/LICENSE
 * Text Domain:       wp-author-tagline
 */

function wpat_install() {
	global $wpdb;

	$table_name = $wpdb->prefix . 'taglines';
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		author mediumint(9) NOT NULL,
		tagline text NOT NULL,
		PRIMARY KEY  (id)
	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
}

register_activation_hook( __FILE__, 'wpat_install' );