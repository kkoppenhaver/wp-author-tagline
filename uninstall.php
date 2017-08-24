<?php 

// if uninstall.php is not called by WordPress, die
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}
 
// Drop our custom DB table when the plugin is deleted
global $wpdb;

$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}taglines");