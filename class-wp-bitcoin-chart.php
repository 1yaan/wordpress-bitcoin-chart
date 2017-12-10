<?php
/**
 * WP Bitcoin Chart
 *
 * @package   wp-bitcoin-chart
 * @author    1yaan, {@link https://github.com/1yaan https://github.com/1yaan}
 * @version   0.1
 * @copyright 1yaan, {@link https://github.com/1yaan https://github.com/1yaan}
 * @license   GPLv2 or later, {@link https://www.gnu.org/licenses/gpl.html https://www.gnu.org/licenses/gpl.html}
 */

/**
 * WP_Bitcoin_Chart
 */
class WP_Bitcoin_Chart {

	/**
	 * Initial setting Flag to check that init is executed only once.
	 *
	 * @access private
	 * @var boolean
	 */
	private static $initiated = false;

	/**
	 * First executed
	 *
	 * @return void
	 */
	public static function init() {
		if ( ! self::$initiated ) {
			self::init_hooks();
		}
	}

	/**
	 * Initializes WordPress hooks
	 *
	 * @return void
	 */
	private static function init_hooks() {
		self::$initiated = true;
	}

	/**
	 * WP Bitcoin Chart activation
	 *
	 * @return void
	 */
	public static function wp_bitcoin_chart_activation() {
		// Insert options.
		$wp_bitcoin_chart_check_periods_300 = get_option( 'wp_bitcoin_chart_check_periods_300' );
		$wp_bitcoin_chart_check_periods_1800 = get_option( 'wp_bitcoin_chart_check_periods_1800' );
		$wp_bitcoin_chart_check_periods_3600 = get_option( 'wp_bitcoin_chart_check_periods_3600' );
		$wp_bitcoin_chart_check_periods_86400 = get_option( 'wp_bitcoin_chart_check_periods_86400' );

		if ( !$wp_bitcoin_chart_check_periods_300 ) $wp_bitcoin_chart_check_periods_300 = WP_BITCOIN_CHART__DEFAULT_CHART_START;
		if ( !$wp_bitcoin_chart_check_periods_1800 ) $wp_bitcoin_chart_check_periods_1800 = WP_BITCOIN_CHART__DEFAULT_CHART_START;
		if ( !$wp_bitcoin_chart_check_periods_3600 ) $wp_bitcoin_chart_check_periods_3600 = WP_BITCOIN_CHART__DEFAULT_CHART_START;
		if ( !$wp_bitcoin_chart_check_periods_86400 ) $wp_bitcoin_chart_check_periods_86400 = WP_BITCOIN_CHART__DEFAULT_CHART_START;

		update_option( 'wp_bitcoin_chart_check_periods_300', $wp_bitcoin_chart_check_periods_300 );
		update_option( 'wp_bitcoin_chart_check_periods_1800', $wp_bitcoin_chart_check_periods_1800 );
		update_option( 'wp_bitcoin_chart_check_periods_3600', $wp_bitcoin_chart_check_periods_3600 );
		update_option( 'wp_bitcoin_chart_check_periods_86400', $wp_bitcoin_chart_check_periods_86400 );

		// Check data directory.
		if ( ! file_exists( WP_BITCOIN_CHART__PLUGIN_DATA_DIR ) ) mkdir( WP_BITCOIN_CHART__PLUGIN_DATA_DIR, 0777 );
	}

	/**
	 * WP Bitcoin Chart deactivation
	 *
	 * @return void
	 */
	public static function wp_bitcoin_chart_deactivation() {
		// 無効化時点でのデータを保持しておく.
		get_cryptowatch_data();
	}

	/**
	 * WP Bitcoin Chart Plugin uninstall
	 *
	 * @return void
	 */
	public static function wp_bitcoin_chart_uninstall() {
		// Delete options.
		// True, if option is successfully deleted. False on failure, or option does not exist.
		delete_option( 'wp_bitcoin_chart_check_periods_300' );
		delete_option( 'wp_bitcoin_chart_check_periods_1800' );
		delete_option( 'wp_bitcoin_chart_check_periods_3600' );
		delete_option( 'wp_bitcoin_chart_check_periods_86400' );
	}

	/**
	 * output chart
	 *
	 * @param  array $atts User defined attributes in shortcode tag.
	 * @param  boolean $cache Use filecache.
	 * @return string
	 */
	public static function output_chart( $atts, $cache = true ) {

		$filename = WP_BITCOIN_CHART__PLUGIN_DATA_DIR . 'output_' . strval( $periods ) . '.htm';
		$output_text = "";

		// キャッシュが有効の場合は、キャッシュを利用する.
		if ( $cache ) {
			$now_time = time();
			$periods = $atts['periods'];
			$last_access = get_option( 'wp_bitcoin_chart_check_periods_' . strval( $periods ) );
			if ( ( $now_time - $last_access ) > $periods and file_exists( $filename ) ) {
				$output_text = file_get_contents( $filename );
				return $output_text;
			}
		}

		$name = $atts['name'];
		$chart = json_decode( get_chart( $atts ) );
		$output_text = <<<EOT
<div class='wp-bitcoin-chart-field'>
	<canvas id='${name}'></canvas>
	<div class='columns'>
		<div class='column'>
			<div class='buttons has-addons'>
				Zoom:&nbsp;
				<a href='#' id='${name}-zoom-10min' class='button'>10min</a>
				<a href='#' id='${name}-zoom-30min' class='button'>30min</a>
				<a href='#' id='${name}-zoom-1hour' class='button'>1hour</a>
				<a href='#' id='${name}-zoom-1day' class='button'>1day</a>
			</div>
		</div>
		<div class='column'>
			From:&nbsp;<input type='text' id='${name}-from-input' value='2017-11-30'>&nbsp;To:&nbsp;<input type='text' id='myChart-to-input' value='2017-12-06'>
		</div>
	</div>
</div>
<script>var ctx = document.getElementById('${name}').getContext('2d');var chart = new Chart(ctx, ${chart});</script>
EOT;

		file_put_contents( $filename, $output_text );

		return $output_text;
	}

	/**
	 * get chart
	 *
	 * @param  array $atts User defined attributes in shortcode tag.
	 * @return string
	 */
	public static function get_chart( $atts ) {
		$labels = get_data_label( $atts['periods'] );
		$datasets = array();

		// どのデータを表示するのかを識別して設定する.
		if ( array_key_exists( 'op', $atts ) ) {
			$datasets[] = array(
				"label" => "Open Price",
				"borderColor" => $atts['op_color'],
				"data" => get_graph_data( $atts['periods'], 0 ),
				"drawBorder" => false,
			);
		}
		if ( array_key_exists( 'hp', $atts ) ) {
			$datasets[] = array(
				"label" => "High Price",
				"borderColor" => $atts['hp_color'],
				"data" => get_graph_data( $atts['periods'], 1 ),
				"drawBorder" => false,
			);
		}
		if ( array_key_exists( 'lp', $atts ) ) {
			$datasets[] = array(
				"label" => "Low Price",
				"borderColor" => $atts['lp_color'],
				"data" => get_graph_data( $atts['periods'], 2 ),
				"drawBorder" => false,
			);
		}
		if ( array_key_exists( 'cp', $atts ) ) {
			$datasets[] = array(
				"label" => "Close Price",
				"borderColor" => $atts['cp_color'],
				"data" => get_graph_data( $atts['periods'], 3 ),
				"drawBorder" => false,
			);
		}
		if ( array_key_exists( 'vo', $atts ) ) {
			$datasets[] = array(
				"label" => "Volume",
				"borderColor" => $atts['vo_color'],
				"data" => get_graph_data( $atts['periods'], 4 ),
				"drawBorder" => false,
			);
		}
		$chart = array(
			"type" => "line",
			"data" => array(
				"labels" => $labels,
				"datasets" => $datasets,
				"options" => array(
					"title" => array(
						"display" => true,
						"text" => "BTC/JPY"
					)
				)
			)
	  );
		return $chart;
	}

	/**
	 * get_data_label
	 * @param  int $periods 取得するデータの時間間隔. 300, 1800, 3600, 86400のみを認めます. 初期値は86400.
	 * @return array
	 */
	public static function get_data_label( int $periods = WP_BITCOIN_CHART__DEFAULT_CHART_PERIODS ) {

		$filename = WP_BITCOIN_CHART__PLUGIN_DATA_DIR . 'cw_' . strval( $periods ) . '.json';
		$result = array();

		if ( file_exists( $filename ) ) {
			$all_data = file_get_contents( $filename );
			$result = array_keys($all_data);
		}

		return $result;
	}

	/**
	 * get_graph_data
	 * @param  int $periods 取得するデータの時間間隔. 300, 1800, 3600, 86400のみを認めます. 初期値は86400.
	 * @param  int $assort 取得するデータの種類です。 0: Open Price, 1: High Price, 2: Low Price, 3: Close Price, 4: Volume
	 * @return array
	 */
	public static function get_graph_data( int $periods = WP_BITCOIN_CHART__DEFAULT_CHART_PERIODS, int $assort = NULL ) {

		$filename = WP_BITCOIN_CHART__PLUGIN_DATA_DIR . 'cw_' . strval( $periods ) . '.json';
		$result = array();

		if ( $assort !== NULL and file_exists( $filename ) ) {
			$all_data = file_get_contents( $filename );
			$result = array_column( $all_data, $assort );
		}

		return $result;
	}

	/**
	 * get cryptowatch data
	 * Cryptowatch.jpからデータを取得します.この処理は再帰的な処理を含みます.
	 * データが取得できなくなるまで取得します。
	 *
	 * @param  int $periods 取得するデータの時間間隔. 300, 1800, 3600, 86400のみを認めます. 初期値は86400.
	 * @return int response status. 1: No period. 2: Interval is too short. 3: Cannot create json file. 99: Finished.
	 */
	public static function get_cryptowatch_data( int $periods = WP_BITCOIN_CHART__DEFAULT_CHART_PERIODS ) {
		// No periods. Exist.
		if ( empty( $periods ) or ! in_array( $periods, array( 300, 1800, 3600, 86400 ) ) ) return 1;

		// 最後にアクセスした時間を取得します.
		$last_access = get_option( 'wp_bitcoin_chart_check_periods_' . strval( $periods ) );
		$now_time = time();

		// Interval is too short.
		if ( ( $now_time - $last_access ) < $periods ) return 2;

		// https://cryptowatch.jp/bitflyer/btcjpy からデータを取得します.
		$json = file_get_contents( 'https://api.cryptowat.ch/markets/bitflyer/btcjpy/ohlc?periods=' . strval( $periods ) . '&after=' . strval( $last_access ) );
		$json = mb_convert_encoding( $json, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN' );
		$cw = json_decode( $json, true );

		if ( ! empty( $cw['result'][strval( $periods )] ) ) {

			$this_period_keys = array_column( $cw['result'][strval( $periods )], 0 );
			$this_period_data = array_combine( $this_period_keys, $cw['result'][strval( $periods )] );
			$filename = WP_BITCOIN_CHART__PLUGIN_DATA_DIR . 'cw_' . strval( $periods ) . '.json';

			if ( file_exists( $filename ) ) {
				// 2つの配列のデータをマージする.
				$all_data = file_get_contents( $filename );
				$all_data = json_decode( $all_data, true );
				$arr = array_merge( $all_data, $this_period_data );
				$result = file_put_contents( $filename, json_encode( $arr ) );
				if ( $result === false ) return 3;
			}
		}

		// アクセス直前の時間をcheck_periodsに設定します.
		$last_access = $now_time;

		// 現在時刻を最後にアクセスした時間とします。
		update_option( 'wp_bitcoin_chart_check_periods_' . strval( $periods ), $last_access );

		// Finished.
		return 99;
	}
}
