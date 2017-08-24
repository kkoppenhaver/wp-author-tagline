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

register_activation_hook( __FILE__, 'wpat_install' );
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

add_action( 'show_user_profile', 'wpat_add_user_field' );
add_action( 'edit_user_profile', 'wpat_add_user_field' );

function wpat_add_user_field() {
?>
  <h3>Tagline</h3>
  <table class="form-table">
    <tr>
      <th><label for="tagline">User Tagline</label></th>
      <td>
        <input type="text" name="wpat_tagline" id="wpat_tagline" class="regular-text" value="Sample Tagline" />
    </td>
    </tr>
  </table>
<?php
}