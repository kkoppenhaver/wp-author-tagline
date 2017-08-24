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
	global $user_id;
?>
  <h3>Tagline</h3>
  <table class="form-table">
    <tr>
      <th><label for="tagline">User Tagline</label></th>
      <td>
        <input type="text" name="wpat_tagline" id="wpat_tagline" class="regular-text" value="<?php echo esc_attr( wpat_get_tagline( $user_id ) ); ?>" />
    </td>
    </tr>
  </table>
<?php
}

add_action( 'personal_options_update', 'wpat_save_tagline' );
add_action( 'edit_user_profile_update', 'wpat_save_tagline' );
function wpat_save_tagline( $user_id ) {
  $saved = false;
  if ( current_user_can( 'edit_user', $user_id ) ) {
  	global $wpdb;
  	$table_name = $wpdb->prefix . 'taglines';

  	$tagline = sanitize_text_field( $_POST['wpat_tagline'] );

  	$row = $wpdb->get_results("SELECT * FROM $table_name WHERE author = '". $user_id . "'");

  	if( count( $row ) > 0 ) {
  		$wpdb->update( 
			$table_name, 
			array( 
				'author' => $user_id,
				'tagline' => $tagline
			), 
			array( 'author' => intval($user_id) ), 
			array( 
				'%d',
				'%s',
			), 
			array( '%d' ) 
		);
  	}
  	else {
  		$wpdb->insert( 
			$table_name, 
			array( 
		    	'author' => intval( $user_id ),
				'tagline' => $tagline, 
			), 
			array( 
		        '%d',
				'%s', 
			) 
		);
  	}
  }
  return true;
}

function wpat_get_tagline( $user_id ) {
	global $wpdb;

	$table_name = $wpdb->prefix . 'taglines';

	$result = $wpdb->get_results("SELECT * FROM $table_name WHERE author = $user_id");

	if( count( $result ) > 0 ) {
		return $result[0]->tagline;
	}
	else {
		return '';
	}
}

add_action( 'admin_menu', 'wpat_add_menu' );

function wpat_add_menu(){
	add_management_page( 'Taglines', 'Taglines', 'manage_options', 'wpat_tagline', 'wpat_taglines_admin_view' );
}

function wpat_taglines_admin_view(){
	global $wpdb;

	$table_name = $wpdb->prefix . 'taglines';

	$results = $wpdb->get_results("SELECT * FROM $table_name");
?>
	<div class="wrap">
		<h1 class="wp-heading-inline">Taglines</h1>

		<table cellpadding="10">
			<tr>
				<th>Author</th>
				<th>Tagline</th>
			</tr>
			<?php foreach( $results as $result_row ) : ?>
				<tr>
					<td><?php echo get_userdata( $result_row->author )->display_name; ?></td>
					<td><?php echo $result_row->tagline; ?></td>
				</tr>
			<?php endforeach; ?>
		</table>
	</div>
<?php }

add_shortcode( 'tagline', 'wpat_do_shortcode' );
function wpat_do_shortcode( $atts, $content = null ) {
	global $post;

	$user_id = $post->post_author;

	return wpat_get_tagline( $user_id );
}