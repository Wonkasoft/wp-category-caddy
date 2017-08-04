<?php
/*
* Class for WP Category Caddy
* 
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
    $caddy_ops = array(
      'classname' => 'caddy_categories',
      'description' => __('The Archives you select by category to be carried by your caddy.'),
      'customize_selective_refresh' => true,
      );
    parent::__construct( false, __( 'Category Caddy' ), $caddy_ops );
  }
  
  /**
   * Outputs the content for the current Category Caddy widget instance.
   *
   * @since 1.0.0
   * @access public
   *
   * @param array $instance Settings for the current Category Caddy widget instance.
   * front-end
   */
  public function widget( $args, $instance ) {
    
    $title = apply_filters( 'widget_title', empty( $instance['title'] ) ? __( 'Category Caddy' ) : $instance['title'], $instance, $this->id_base );
    if ( ! empty( $instance['caddy_page_id'] ) && is_page( $instance['caddy_page_id'] ) ) {
      echo $args['before_widget'];
      if ( $title ) {
        echo $args['before_title'] . $title . $args['after_title'];
      }

      ?>
      <ul>
      <?php
      /**
       * Filters the arguments for the Category Caddy widget.
       *
       * @since 1.0.0
       *
       * @loop that builds out the list of categories IDs from the Caddy Widget.
       */

      $custom_caddy = array();
      if ($instance['caddy_selected'] !== 'Uncategorized') {
        $caddy_id = get_cat_ID( $instance['caddy_selected'] );
        $custom_caddy = array(
            'show_post_count' => $instance['count'],
            'caddy_select' => $instance['caddy_selected'],
            'caddy_select_id' => $caddy_id
          );
      }
      wp_get_custom_archives( $custom_caddy );
      ?>
      </ul>
      <?php
      echo $args['after_widget'];
    }
  } 

  /**
   * Handles updating settings for the current Category Caddy widget instance.
   *
   * @since 1.0.0
   * @access public
   *
   * @param array $new_instance New settings for this instance as input by the user via
   *                            WP_Widget::form().
   * @param array $old_instance Old settings for this instance.
   * @return array Updated settings to save.
   */
  public function update( $new_instance, $old_instance ) {
    $instance = $old_instance;
    $instance['title'] = sanitize_text_field( $new_instance['title'] );
    $instance['caddy_page_id'] = sanitize_text_field( $new_instance['caddy_page_id'] );
    $instance['count'] = $new_instance['count'] ? 1 : 0;
    $instance['caddy_selected'] = sanitize_text_field( $new_instance['caddy_selected'] );

    return $instance;
  }

  // back-end
  /**
   * Outputs the settings form for the Category Caddy widget.
   *
   * @since 1.0.0
   * @access public
   *
   * @param array $instance Current settings.
   */
  public function form( $instance ) {
    $instance = wp_parse_args( (array) $instance, array(
    'title' => '', 
    'count' => 0, 
    'caddy_selected' => '', 
    'caddy_page_id' => ''
    ) );
    $title = sanitize_text_field( $instance['title'] );
    $selected_options = isset( $instance['caddy_selected'] ) ? $instance['caddy_selected']: '';
    $page_selected = isset( $instance['caddy_page_id'] ) ? $instance['caddy_page_id']: '';

    $caddy_pages = get_pages( array(
      'sort_order'   => 'ASC'
     ) );

    $cate = get_categories( array(
      'orderby' => 'name',
      'order' => 'ASC'
      ) );
    ?>
    <style type="text/css">.select-dropdown{width:100%;}</style>
    <p>
    <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></p>
    <p>
    <label for="<?php echo $this->get_field_id( 'caddy_page_id' ); ?>"><?php _e( 'Page to show on:' ); ?></label>
    <select id="<?php echo $this->get_field_id( 'caddy_page_id' ); ?>" name="<?php echo $this->get_field_name( 'caddy_page_id' ); ?>" class="select-dropdown">
    <?php
    foreach ($caddy_pages as $key => $value) {
      if ( $value->ID == $page_selected ) {
        ?>
        <option style="padding: 2px 8px" value="<?php echo $value->ID; ?>" selected><?php echo $value->post_title; ?></option>
        <?php
      } else {
        ?>
        <option style="padding: 2px 8px" value="<?php echo $value->ID; ?>"><?php echo $value->post_title; ?></option>
        <?php
      }
    }
    ?>
    </select>
    </p>
    <p>
    <label for="<?php echo $this->get_field_id( 'caddy_selected' ); ?>"><?php _e( 'Categories:' ); ?></label>
    <select id="<?php echo $this->get_field_id( 'caddy_selected' ); ?>" name="<?php echo $this->get_field_name( 'caddy_selected' ); ?>" class="select-dropdown">
    <?php
    foreach ($cate as $key => $value) {
      if ( $value->name == $selected_options ) {
        ?>
        <option style="padding: 2px 8px" value="<?php echo $value->name; ?>" selected><?php echo $value->name; ?></option>
        <?php
      } else {
        ?>
        <option style="padding: 2px 8px" value="<?php echo $value->name; ?>"><?php echo $value->name; ?></option>
        <?php
      }
    }
    ?>
    </select>
    </p>
    <p><input class="checkbox" type="checkbox"<?php checked( $instance['count'] ); ?> id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>" /> <label for="<?php echo $this->get_field_id('count'); ?>"><?php _e('Show post counts'); ?></label>
    </p>
    <?php
  }
}