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
			'name'     => WP_BITCOIN_CHART__DEFAULT_CHART_NAME,
			'periods'  => WP_BITCOIN_CHART__DEFAULT_CHART_PERIODS,
			'op_color' => WP_BITCOIN_CHART__DEFAULT_OP_COLOR,
			'hp_color' => WP_BITCOIN_CHART__DEFAULT_HP_COLOR,
			'lp_color' => WP_BITCOIN_CHART__DEFAULT_LP_COLOR,
			'cp_color' => WP_BITCOIN_CHART__DEFAULT_CP_COLOR,
			'vo_color' => WP_BITCOIN_CHART__DEFAULT_VO_COLOR,
			'op'       => 0,
			'hp'       => 0,
			'lp'       => 0,
			'cp'       => 0,
			'vo'       => 0,
		),
		$atts,
		'wp-bitcoin-chart-view'
	);

	// 表示内容をreturnする.
	return WP_Bitcoin_Chart::output_chart( $atts );
}

// Exp: [wp-bitcoin-chart-view]
// ショートコードで画面にグラフを表示する.
add_shortcode( 'wp-bitcoin-chart-view', 'wp_bitcoin_chart_view_shortcode' );

/**
 * Register jquery.
 *
 * @return void
 */
function register_jquery() {
	wp_register_script( 'momentjs', 'https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.19.3/moment.min.js', array(), '0.1.0' );
	wp_enqueue_script( 'momentjs' );

	wp_register_script( 'chartjs', 'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js', array(), '0.1.0' );
  wp_enqueue_script( 'chartjs' );
}

add_action( 'wp_enqueue_scripts', 'register_jquery' );
