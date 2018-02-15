<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @since      0.1.0
 * @version    2.1.0
 * @package    wp-bitcoin-chart
 * @author     1yaan, {@link https://github.com/1yaan https://github.com/1yaan}
 * @copyright  1yaan, {@link https://github.com/1yaan https://github.com/1yaan}
 * @license    GPLv2 or later, {@link https://www.gnu.org/licenses/gpl.html https://www.gnu.org/licenses/gpl.html}
 */

// If uninstall, not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( WBC__OPTION_NAME_CHART_CSS );

// Older version's options.
delete_option( 'wp_bitcoin_chart_check_periods_300' );
delete_option( 'wp_bitcoin_chart_check_periods_1800' );
delete_option( 'wp_bitcoin_chart_check_periods_3600' );
delete_option( 'wp_bitcoin_chart_check_periods_86400' );
delete_option( 'wp_bitcoin_chart__summary' );
delete_option( 'wp_bitcoin_chart__price' );
