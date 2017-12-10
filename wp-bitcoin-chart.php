<?php
/**
 * Plugin Name: WP Bitcoin Chart
 * Plugin URI: https://github.com/1yaan/wp-bitcoin-chart
 * Description: This is WordPress plugin. This plugin views BTC/JPY chart by Chart.js
 * Version: 0.1.0
 * Author: 1yaan
 * Author URI: https://github.com/1yaan
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl.html
 * Text Domain: wp_bitcoin_chart
 *
 * @package wp-bitcoin-chart
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

// This plugin version.
define( 'WP_BITCOIN_CHART__VERSION', '0.1' );
// The absolute path of the directory that contains the file, with trailing slash ("/").
define( 'WP_BITCOIN_CHART__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'WP_BITCOIN_CHART__PLUGIN_DATA_DIR', plugin_dir_path( __FILE__ ) . 'data/' );
define( 'WP_BITCOIN_CHART__DEFAULT_CHART_PERIODS', 86400 );
define( 'WP_BITCOIN_CHART__DEFAULT_CHART_START', 1483319400 ); // January 2, 2017 AM 10: 10 is my son's 11th birthday.
define( 'WP_BITCOIN_CHART__DEFAULT_CHART_NAME', 'WPBITCHART' );

define( 'WP_BITCOIN_CHART__DEFAULT_OP_COLOR', 'Red' );
define( 'WP_BITCOIN_CHART__DEFAULT_HP_COLOR', 'Green' );
define( 'WP_BITCOIN_CHART__DEFAULT_LP_COLOR', 'Blue' );
define( 'WP_BITCOIN_CHART__DEFAULT_CP_COLOR', 'Yellow' );
define( 'WP_BITCOIN_CHART__DEFAULT_VO_COLOR', 'Magenta' );

register_activation_hook( __FILE__, array( 'WpBitcoinChart', 'wp_bitcoin_chart_activation' ) );
register_deactivation_hook( __FILE__, array( 'WpBitcoinChart', 'wp_bitcoin_chart_deactivation' ) );
register_uninstall_hook( __FILE__, array( 'WpBitcoinChart', 'wp_bitcoin_chart_uninstall' ) );

require_once( WP_BITCOIN_CHART__PLUGIN_DIR . 'class-wp-bitcoin-chart.php' );
add_action( 'init', array( 'WP_Bitcoin_Chart', 'init' ) );

if ( is_admin() ) {
	require_once( WP_BITCOIN_CHART__PLUGIN_DIR . 'class-wp-bitcoin-chart-admin.php' );
	add_action( 'init', array( 'WP_Bitcoin_Chart_Admin', 'init' ) );
}

// add wrapper class around deprecated WP Bitcoin Chart functions that are referenced elsewhere.
require_once( WP_BITCOIN_CHART__PLUGIN_DIR . 'wrapper.php' );
