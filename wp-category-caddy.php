<?php
/*
* Plugin Name: WP Category Caddy
* Plugin URI:  https://wonkasoft.com/wp-category-caddy
* Description: Basic WordPress Plugin for a custom widget for listing selected Archives by categories.
* Version:     1.0.0
* Author:      Wonkasoft
* Author URI:  https://wonkasoft.com
* License:     GPL2
* License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
  die;
}

function wp_category_caddy_init_setup() {

// Add theme support for selective refresh for widgets.
  add_theme_support( 'customize-selective-refresh-widgets' );
  
}
add_action('init', 'wp_category_caddy_init_setup');

// Require Class Once
require plugin_dir_path( __FILE__ ) . 'partials/class-wp-category-caddy.php';

add_action( 'widgets_init', function() {
  register_widget( 'wp_category_caddy' );
} );

add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'wp_category_caddy_add_settings_link_filter', 10, 1 );
function wp_category_caddy_add_settings_link_filter( $links ) { 
  $donate_link = '<a href="https://paypal.me/Wonkasoft" target="blank">Donate</a>';
  array_unshift( $links, $donate_link ); 
  return $links; 
}