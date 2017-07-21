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
class wp_category_caddy extends WP_Widget {
  public $plugin_name = 'wp_category_caddy';

  // Setup widget name description etc...
  function __construct() {
    parent::__construct(false, $name = __( 'WP Category Caddy' ) );
  }


  // back-end
  public function form( $instance ) {
    
  }
  public function update( $new_instance, $old_instance ) {
    
  }
  // front-end
  public function widget( $args, $instance ) {
    
  } 
}

add_action( 'widgets_init', function() {
  register_widget( 'wp_category_caddy' );
} );

add_filter( 'plugin_action_links_' . $plugin_name, 'wp_category_caddy_add_settings_link_filter', 10, 1 );
function wp_category_caddy_add_settings_link_filter( $links ) { 
  $donate_link = '<a href="https://paypal.me/Wonkasoft" target="blank">Donate</a>';
  array_unshift($links, $settings_link, $support_link, $donate_link); 
  return $links; 
}