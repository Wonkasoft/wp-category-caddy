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
  
  // front-end
  /**
   * Outputs the content for the current Category Caddy widget instance.
   *
   * @since 1.0.0
   * @access public
   *
   * @param array $instance Settings for the current Category Caddy widget instance.
   */
  public function widget( $args, $instance ) {
    
  } 

  /**
   * Handles updating settings for the current Category Caddy widget instance.
   *
   * @since 1.0.0
   * @access public
   *
   * @param array $new_instance New settings for this instance as input by the user via
   *                            WP_Widget_Archives::form().
   * @param array $old_instance Old settings for this instance.
   * @return array Updated settings to save.
   */
  public function update( $new_instance, $old_instance ) {

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
    $instance = wp_parse_args( (array) $instance, array( 'title' => '', 'count' => 0, 'dropdown' => '') );
    $title = sanitize_text_field( $instance['title'] );
    $cate = get_categories( array(
      'orderby' => 'name',
      'order' => 'ASC'
      ) );
    ?>
    <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>
    <p><select name="caddy_selected[]" multiple="multiple" style="width: 100%;">
    <?php
    foreach ($cate as $key => $value) {
        ?>
        <option style="padding: 2px 8px" value="<?php echo $value->name; ?>"><?php echo $value->name; ?></option>
        <?php
    }
    ?>
    </select>
    </p>
    <?php
    print_r($_POST['caddy_selected']);
  }
}