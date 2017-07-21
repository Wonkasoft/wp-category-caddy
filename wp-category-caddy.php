<?php
/*
* Plugin Name: WP Category Caddy
* Plugin URI:  https://wonkasoft.com/wp-category-list-widget
* Description: Basic WordPress Plugin for a custom widget for listing selected categories
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

function wp_category_list_init_setup() {

// Add theme support for selective refresh for widgets.
  add_theme_support( 'customize-selective-refresh-widgets' );
  
}
add_action('init', 'wp_category_list_init_setup');

// Require Class Once
require plugin_dir_path( __FILE__ ) . 'partials/class-wp-category-caddy.php';