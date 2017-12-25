<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * Plugin Name: WP Bitcoin Chart
 * Plugin URI:  https://1yaan.github.io/wp-bitcoin-chart/
 * Description: This is WordPress plugin. This plugin views BTC/JPY chart by Chart.js
 * Version:     0.1.0
 * Author:      1yaan
 * Author URI:  https://github.com/1yaan
 * License:     GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl.html
 * Domain Path: /languages/
 * Text Domain: wp_bitcoin_chart
 *
 * @link        https://github.com/1yaan/wp-bitcoin-chart
 * @since       0.1.0
 * @package     wp-bitcoin-chart
 */

/*
Copyright 2017 1yaan

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// Make sure we don't expose any info if called directly.
if ( ! function_exists( 'add_action' ) ) {
	echo "Hi there! I'm just a plugin, not much I can do when called directly.";
	exit;
}

ini_set( 'allow_url_fopen', true );

// This plugin version.
define( 'WBC__VERSION', '0.1' );
// The absolute path of the directory that contains the file, with trailing slash ("/").
define( 'WBC__PLUGIN_NAME', 'wp-bitcoin-chart' );
define( 'WBC__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'WBC__PLUGIN_DIR_URL', plugin_dir_url( __FILE__ ) );
define( 'WBC__PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'WBC__PLUGIN_DATA_DIR', plugin_dir_path( __FILE__ ) . 'data/' );
define( 'WBC__DEFAULT_CHART_PERIODS', 86400 );
define( 'WBC__CHART_PERIODS_ONE_DAY', 86400 );
define( 'WBC__DEFAULT_CHART_START', 1483319400 ); // January 2, 2017 AM 10: 10 is my son's 11th birthday.
define( 'WBC__DEFAULT_CHART_NAME', 'WPBITCHART' );
define( 'WBC__DEFAULT_OP_COLOR', 'Red' );
define( 'WBC__DEFAULT_HP_COLOR', 'Green' );

define( 'WBC__DEFAULT_LP_COLOR', 'Blue' );
define( 'WBC__DEFAULT_CP_COLOR', 'Yellow' );
define( 'WBC__DEFAULT_VO_COLOR', 'Magenta' );

define( 'WBC__DEFAULT_PERIODS_300_NAME', 'wp_bitcoin_chart_check_periods_300' );
define( 'WBC__DEFAULT_PERIODS_1800_NAME', 'wp_bitcoin_chart_check_periods_1800' );
define( 'WBC__DEFAULT_PERIODS_3600_NAME', 'wp_bitcoin_chart_check_periods_3600' );
define( 'WBC__DEFAULT_PERIODS_86400_NAME', 'wp_bitcoin_chart_check_periods_86400' );

register_activation_hook( __FILE__, array( 'WP_Bitcoin_Chart', 'wp_bitcoin_chart_activation' ) );
register_deactivation_hook( __FILE__, array( 'WP_Bitcoin_Chart', 'wp_bitcoin_chart_deactivation' ) );
register_uninstall_hook( __FILE__, array( 'WP_Bitcoin_Chart', 'wp_bitcoin_chart_uninstall' ) );

require_once( WBC__PLUGIN_DIR . 'class-wp-bitcoin-chart.php' );
add_action( 'init', array( 'WP_Bitcoin_Chart', 'init' ) );

if ( is_admin() ) {
	require_once WBC__PLUGIN_DIR . 'admin/class-wbc-admin.php';
	$wbc_admin = new WBC_Admin();
}

// Add Action.
add_action( 'wp_enqueue_scripts', array( 'WP_Bitcoin_Chart', 'register_jquery' ) );
add_action( 'admin_enqueue_scripts', array( 'WP_Bitcoin_Chart', 'register_jquery' ) );
add_action( 'wp_ajax_wp_bitcoin_chart', array( 'WP_Bitcoin_Chart', 'wp_bitcoin_chart_get_json_data' ) );
add_action( 'wp_ajax_nopriv_wp_bitcoin_chart', array( 'WP_Bitcoin_Chart', 'wp_bitcoin_chart_get_json_data' ) );

// Exp: [wp_bitcoin_chart_view]
// ショートコードで画面にグラフを表示する.
add_shortcode( 'wp_bitcoin_chart_view', array( 'WP_Bitcoin_Chart', 'wp_bitcoin_chart_view_shortcode' ) );
