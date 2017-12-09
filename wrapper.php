<?php
/**
 * Wrapper Setting Shortcode
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
 * @param array $atts User defined attributes in shortcode tag.
 * @return string
 */
function wp_bitcoin_chart_view_shortcode( array $atts ) {
	// ショートコードの変数 foo と bar を使用することを宣言し、さらに初期値を設定する.
	$atts = shortcode_atts(
		array(
			'foo' => 'no foo',
			'bar' => 'default bar',
		),
		$atts,
		'wp-bitcoin-chart-view'
	);

	// 表示内容をreturnする.
	return 'wp-bitcoin-chart-view: ' . $atts['foo'] . ' ' . $atts['bar'];
}

// Exp: [wp-bitcoin-chart-view]
// ショートコードで画面にグラフを表示する.
add_shortcode( 'wp-bitcoin-chart-view', 'wp_bitcoin_chart_view_shortcode' );
