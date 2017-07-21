<?php
/**
* The admin-specific functionality of the plugin.
*
* @link       https://wonkasoft.com
* @since      1.0.0
*
* @package    Wp_Jam_Session
* @subpackage Wp_Jam_Session/admin
*/

/**
* The admin-specific functionality of the plugin.
*
* Defines the plugin name, version, and two examples hooks for how to
* enqueue the admin-specific stylesheet and JavaScript.
*
* @package    Wp_Jam_Session
* @subpackage Wp_Jam_Session/admin
* @author     Wonkasoft <info@wonkasoft.com>
*/
$base =  'wp-category-caddy/wp-category-caddy.php';
add_filter( 'plugin_action_links_'. $base, 'wp_category_caddy_add_settings_link_filter' , 10, 1);

function wp_category_caddy_add_settings_link_filter( $links ) { 
 $donate_link = '<a href="https://paypal.me/Wonkasoft" target="blank">Donate</a>';
 array_unshift( $links, $settings_link, $support_link, $donate_link ); 
 return $links; 
}