<?php
/**
 * wrapper
 *
 * @package   wp-bitcoin-chart
 * @author    1yaan, {@link https://github.com/1yaan https://github.com/1yaan}
 * @version   0.1
 * @copyright 1yaan, {@link https://github.com/1yaan https://github.com/1yaan}
 * @license   GPLv2 or later, {@link https://www.gnu.org/licenses/gpl.html https://www.gnu.org/licenses/gpl.html}
 */

/**
 * WP Bitcoin Chart view shortcode
 *
 * @return string
 */
function wp_bitcoin_chart_view_shortcode($atts) {
	return "";
}

// Exp: [wp-bitcoin-chart-view]
// ショートコードで画面にグラフを表示する。
add_shortcode('wp-bitcoin-chart-view', 'wp_bitcoin_chart_view_shortcode');
