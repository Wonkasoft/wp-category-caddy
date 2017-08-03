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

/**
 * Display archive links based on type and format.
 *
 * @since 1.2.0
 * @since 4.4.0 $post_type arg was added.
 *
 * @see get_archives_link()
 *
 * @global wpdb      $wpdb
 * @global WP_Locale $wp_locale
 *
 * @param string|array $args {
 *     Default archive links arguments. Optional.
 *
 *     @type string     $type            Type of archive to retrieve. Accepts 'daily', 'weekly', 'monthly',
 *                                       'yearly', 'postbypost', or 'alpha'. Both 'postbypost' and 'alpha'
 *                                       display the same archive link list as well as post titles instead
 *                                       of displaying dates. The difference between the two is that 'alpha'
 *                                       will order by post title and 'postbypost' will order by post date.
 *                                       Default 'monthly'.
 *     @type string|int $limit           Number of links to limit the query to. Default empty (no limit).
 *     @type string     $format          Format each link should take using the $before and $after args.
 *                                       Accepts 'link' (`<link>` tag), 'option' (`<option>` tag), 'html'
 *                                       (`<li>` tag), or a custom format, which generates a link anchor
 *                                       with $before preceding and $after succeeding. Default 'html'.
 *     @type string     $before          Markup to prepend to the beginning of each link. Default empty.
 *     @type string     $after           Markup to append to the end of each link. Default empty.
 *     @type bool       $show_post_count Whether to display the post count alongside the link. Default false.
 *     @type bool|int   $echo            Whether to echo or return the links list. Default 1|true to echo.
 *     @type string     $order           Whether to use ascending or descending order. Accepts 'ASC', or 'DESC'.
 *                                       Default 'DESC'.
 *     @type string     $post_type       Post type. Default 'post'.
 * }
 * @return string|void String when retrieving.
 */
function wp_get_custom_archives( $args = '' ) {
	global $wpdb, $wp_locale;

	$defaults = array(
		'type' => 'monthly', 'limit' => '',
		'format' => 'html', 'before' => '',
		'after' => '', 'show_post_count' => false,
		'echo' => 1, 'order' => 'DESC',
		'post_type' => 'post',
		'caddy_select' => '',
		'caddy_select_id' => ''
	);

	$r = wp_parse_args( $args, $defaults );

	$post_type_object = get_post_type_object( $r['post_type'] );
	if ( ! is_post_type_viewable( $post_type_object ) ) {
		return;
	}

	$post_caddy_object = get_posts( array( 'category' => $r['caddy_select_id'] ) );
	foreach ($post_caddy_object as $key => $value) {
		$post_caddy_objects[] = $value->ID;
	}

	$r['post_type'] = $post_type_object->name;

	if ( '' == $r['type'] ) {
		$r['type'] = 'monthly';
	}

	if ( ! empty( $r['limit'] ) ) {
		$r['limit'] = absint( $r['limit'] );
		$r['limit'] = ' LIMIT ' . $r['limit'];
	}

	$order = strtoupper( $r['order'] );
	if ( $order !== 'ASC' ) {
		$order = 'DESC';
	}

	// this is what will separate dates on weekly archive links
	$archive_week_separator = '&#8211;';

	$sql_where = $wpdb->prepare( "WHERE post_type = %s AND post_status = 'publish'", $r['post_type'] );

	/**
	 * Filters the SQL WHERE clause for retrieving archives.
	 *
	 * @since 2.2.0
	 *
	 * @param string $sql_where Portion of SQL query containing the WHERE clause.
	 * @param array  $r         An array of default arguments.
	 */
	$where = apply_filters( 'getarchives_where', $sql_where, $r );

	/**
	 * Filters the SQL JOIN clause for retrieving archives.
	 *
	 * @since 2.2.0
	 *
	 * @param string $sql_join Portion of SQL query containing JOIN clause.
	 * @param array  $r        An array of default arguments.
	 */
	$join = apply_filters( 'getarchives_join', '', $r );

	$output = '';

	$last_changed = wp_cache_get_last_changed( 'posts' );

	$limit = $r['limit'];

	if ( 'monthly' == $r['type'] ) {
		$query = "SELECT YEAR(post_date) AS `year`, MONTH(post_date) AS `month`, count(ID) as posts, ID as id FROM $wpdb->posts $join $where GROUP BY YEAR(post_date), MONTH(post_date) ORDER BY post_date $order $limit";
		$key = md5( $query );
		$key = "wp_get_archives:$key:$last_changed";
		if ( ! $results = wp_cache_get( $key, 'posts' ) ) {
			$results = $wpdb->get_results( $query );
			wp_cache_set( $key, $results, 'posts' );
		}
		var_dump($r['caddy_select']);
		if ( $results ) {
			$after = $r['after'];
			foreach ( (array) $results as $result ) {
				if ( in_array($result->id, $post_caddy_objects) ) {
					$url = get_month_link( $result->year, $result->month);
					$url = add_query_arg( 'category', $r['caddy_select'], $url );
					if ( 'post' !== $r['post_type'] ) {
						$url = add_query_arg( 'post_type', $r['post_type'], $url );
					}
					/* translators: 1: month name, 2: 4-digit year */
					$text = sprintf( __( '%1$s %2$d' ), $wp_locale->get_month( $result->month ), $result->year );
					if ( $r['show_post_count'] ) {
						$r['after'] = '&nbsp;(' . $result->posts . ')' . $after;
					}
					$output .= get_archives_link( $url, $text, $r['format'], $r['before'], $r['after'] );
				}
			}
		}
	} elseif ( 'yearly' == $r['type'] ) {
		$query = "SELECT YEAR(post_date) AS `year`, count(ID) as posts FROM $wpdb->posts $join $where GROUP BY YEAR(post_date) ORDER BY post_date $order $limit";
		$key = md5( $query );
		$key = "wp_get_archives:$key:$last_changed";
		if ( ! $results = wp_cache_get( $key, 'posts' ) ) {
			$results = $wpdb->get_results( $query );
			wp_cache_set( $key, $results, 'posts' );
		}
		if ( $results ) {
			$after = $r['after'];
			foreach ( (array) $results as $result) {
				$url = get_year_link( $result->year );
				if ( 'post' !== $r['post_type'] ) {
					$url = add_query_arg( 'post_type', $r['post_type'], $url );
				}
				$text = sprintf( '%d', $result->year );
				if ( $r['show_post_count'] ) {
					$r['after'] = '&nbsp;(' . $result->posts . ')' . $after;
				}
				$output .= get_archives_link( $url, $text, $r['format'], $r['before'], $r['after'] );
			}
		}
	} elseif ( 'daily' == $r['type'] ) {
		$query = "SELECT YEAR(post_date) AS `year`, MONTH(post_date) AS `month`, DAYOFMONTH(post_date) AS `dayofmonth`, count(ID) as posts FROM $wpdb->posts $join $where GROUP BY YEAR(post_date), MONTH(post_date), DAYOFMONTH(post_date) ORDER BY post_date $order $limit";
		$key = md5( $query );
		$key = "wp_get_archives:$key:$last_changed";
		if ( ! $results = wp_cache_get( $key, 'posts' ) ) {
			$results = $wpdb->get_results( $query );
			wp_cache_set( $key, $results, 'posts' );
		}
		if ( $results ) {
			$after = $r['after'];
			foreach ( (array) $results as $result ) {
				$url  = get_day_link( $result->year, $result->month, $result->dayofmonth );
				if ( 'post' !== $r['post_type'] ) {
					$url = add_query_arg( 'post_type', $r['post_type'], $url );
				}
				$date = sprintf( '%1$d-%2$02d-%3$02d 00:00:00', $result->year, $result->month, $result->dayofmonth );
				$text = mysql2date( get_option( 'date_format' ), $date );
				if ( $r['show_post_count'] ) {
					$r['after'] = '&nbsp;(' . $result->posts . ')' . $after;
				}
				$output .= get_archives_link( $url, $text, $r['format'], $r['before'], $r['after'] );
			}
		}
	} elseif ( 'weekly' == $r['type'] ) {
		$week = _wp_mysql_week( '`post_date`' );
		$query = "SELECT DISTINCT $week AS `week`, YEAR( `post_date` ) AS `yr`, DATE_FORMAT( `post_date`, '%Y-%m-%d' ) AS `yyyymmdd`, count( `ID` ) AS `posts` FROM `$wpdb->posts` $join $where GROUP BY $week, YEAR( `post_date` ) ORDER BY `post_date` $order $limit";
		$key = md5( $query );
		$key = "wp_get_archives:$key:$last_changed";
		if ( ! $results = wp_cache_get( $key, 'posts' ) ) {
			$results = $wpdb->get_results( $query );
			wp_cache_set( $key, $results, 'posts' );
		}
		$arc_w_last = '';
		if ( $results ) {
			$after = $r['after'];
			foreach ( (array) $results as $result ) {
				if ( $result->week != $arc_w_last ) {
					$arc_year       = $result->yr;
					$arc_w_last     = $result->week;
					$arc_week       = get_weekstartend( $result->yyyymmdd, get_option( 'start_of_week' ) );
					$arc_week_start = date_i18n( get_option( 'date_format' ), $arc_week['start'] );
					$arc_week_end   = date_i18n( get_option( 'date_format' ), $arc_week['end'] );
					$url            = add_query_arg( array( 'm' => $arc_year, 'w' => $result->week, ), home_url( '/' ) );
					if ( 'post' !== $r['post_type'] ) {
						$url = add_query_arg( 'post_type', $r['post_type'], $url );
					}
					$text           = $arc_week_start . $archive_week_separator . $arc_week_end;
					if ( $r['show_post_count'] ) {
						$r['after'] = '&nbsp;(' . $result->posts . ')' . $after;
					}
					$output .= get_archives_link( $url, $text, $r['format'], $r['before'], $r['after'] );
				}
			}
		}
	} elseif ( ( 'postbypost' == $r['type'] ) || ('alpha' == $r['type'] ) ) {
		$orderby = ( 'alpha' == $r['type'] ) ? 'post_title ASC ' : 'post_date DESC, ID DESC ';
		$query = "SELECT * FROM $wpdb->posts $join $where ORDER BY $orderby $limit";
		$key = md5( $query );
		$key = "wp_get_archives:$key:$last_changed";
		if ( ! $results = wp_cache_get( $key, 'posts' ) ) {
			$results = $wpdb->get_results( $query );
			wp_cache_set( $key, $results, 'posts' );
		}
		if ( $results ) {
			foreach ( (array) $results as $result ) {
				if ( $result->post_date != '0000-00-00 00:00:00' ) {
					$url = get_permalink( $result );
					if ( $result->post_title ) {
						/** This filter is documented in wp-includes/post-template.php */
						$text = strip_tags( apply_filters( 'the_title', $result->post_title, $result->ID ) );
					} else {
						$text = $result->ID;
					}
					$output .= get_archives_link( $url, $text, $r['format'], $r['before'], $r['after'] );
				}
			}
		}
	}
	if ( $r['echo'] ) {
		echo $output;
	} else {
		return $output;
	}
}