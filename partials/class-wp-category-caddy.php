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
    
    $title = apply_filters( 'widget_title', empty( $instance['title'] ) ? __( 'Categories' ) : $instance['title'], $instance, $this->id_base );

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
    foreach ( $instance['caddy_selected'] as $key => $value ) {
      $caddy_ids[] = get_cat_ID( $value );
    }

    $caddy_args = array(
      'include' => $caddy_ids,
      'title_li' => '',
      'show_count' => $instance['count'],
      );
  
    wp_list_categories( $caddy_args );
    ?>
    </ul>
    <?php
    echo $args['after_widget'];
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
    $instance['count'] = $new_instance['count'] ? 1 : 0;
    $instance['caddy_selected'] = array_map( 'sanitize_text_field', wp_unslash( $_POST['caddy_selected'] ) );

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
    $instance = wp_parse_args( (array) $instance, array( 'title' => '', 'count' => 0 ) );
    $title = sanitize_text_field( $instance['title'] );
    $selected_options = isset($instance['caddy_selected']) ? $instance['caddy_selected'] : '';

    $cate = get_categories( array(
      'orderby' => 'name',
      'order' => 'ASC'
      ) );
    ?>
    <p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></p>
    
    <p><select name="caddy_selected[]" multiple="multiple" style="width: 100%;">
    <?php
    foreach ($cate as $key => $value) {
      if ( in_array( $value->name, $selected_options ) ) {
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