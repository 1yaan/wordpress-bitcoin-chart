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
 * Description: This plugin displays the data of cryptowatch.jp using Chartjs.
 * Version:     2.0.0
 * Author:      1yaan
 * Author URI:  https://github.com/1yaan
 * License:     GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl.html
 * Domain Path: /languages/
 * Text Domain: wp-bitcoin-chart
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
define( 'WBC__VERSION', '2.0.0' );
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

/*
 * The code that runs during plugin activation.
 */
require_once WBC__PLUGIN_DIR . 'includes/class-wbc-activator.php';
register_activation_hook( __FILE__, array( 'WBC_Activator', 'activate' ) );

/*
 * The code that runs during plugin activation.
 */
require_once WBC__PLUGIN_DIR . 'includes/class-wbc-deactivator.php';
register_deactivation_hook( __FILE__, array( 'WBC_Deactivator', 'deactivate' ) );

require_once WBC__PLUGIN_DIR . 'includes/class-wp-bitcoin-chart.php';
$wbc = new WP_Bitcoin_Chart();
$wbc->run();

if ( is_admin() ) {
	require_once WBC__PLUGIN_DIR . 'admin/class-wbc-admin.php';
	$wbc_admin = new WBC_Admin();
}
