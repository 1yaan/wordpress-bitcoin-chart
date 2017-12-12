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
		$wp_bitcoin_chart_check_periods_300   = get_option( 'wp_bitcoin_chart_check_periods_300' );
		$wp_bitcoin_chart_check_periods_1800  = get_option( 'wp_bitcoin_chart_check_periods_1800' );
		$wp_bitcoin_chart_check_periods_3600  = get_option( 'wp_bitcoin_chart_check_periods_3600' );
		$wp_bitcoin_chart_check_periods_86400 = get_option( 'wp_bitcoin_chart_check_periods_86400' );

		if ( ! $wp_bitcoin_chart_check_periods_300 ) {
			$wp_bitcoin_chart_check_periods_300 = WP_BITCOIN_CHART__DEFAULT_CHART_START;
		}
		if ( ! $wp_bitcoin_chart_check_periods_1800 ) {
			$wp_bitcoin_chart_check_periods_1800 = WP_BITCOIN_CHART__DEFAULT_CHART_START;
		}
		if ( ! $wp_bitcoin_chart_check_periods_3600 ) {
			$wp_bitcoin_chart_check_periods_3600 = WP_BITCOIN_CHART__DEFAULT_CHART_START;
		}
		if ( ! $wp_bitcoin_chart_check_periods_86400 ) {
			$wp_bitcoin_chart_check_periods_86400 = WP_BITCOIN_CHART__DEFAULT_CHART_START;
		}

		update_option( 'wp_bitcoin_chart_check_periods_300', $wp_bitcoin_chart_check_periods_300 );
		update_option( 'wp_bitcoin_chart_check_periods_1800', $wp_bitcoin_chart_check_periods_1800 );
		update_option( 'wp_bitcoin_chart_check_periods_3600', $wp_bitcoin_chart_check_periods_3600 );
		update_option( 'wp_bitcoin_chart_check_periods_86400', $wp_bitcoin_chart_check_periods_86400 );

		// Check data directory.
		if ( ! file_exists( WP_BITCOIN_CHART__PLUGIN_DATA_DIR ) ) {
			mkdir( WP_BITCOIN_CHART__PLUGIN_DATA_DIR, 0777 );
		}

		// 有効下時点でのデータを保持しておく.
		self::get_cryptowatch_data( 300 );
		self::get_cryptowatch_data( 1800 );
		self::get_cryptowatch_data( 3600 );
		self::get_cryptowatch_data( 86400 );
	}

	/**
	 * WP Bitcoin Chart deactivation
	 *
	 * @return void
	 */
	public static function wp_bitcoin_chart_deactivation() {
		if ( defined( 'WP_DEBUG' ) ) {
			// WP_DEBUGが設定されていたら、フラグを削除する.
			delete_option( 'wp_bitcoin_chart_check_periods_300' );
			delete_option( 'wp_bitcoin_chart_check_periods_1800' );
			delete_option( 'wp_bitcoin_chart_check_periods_3600' );
			delete_option( 'wp_bitcoin_chart_check_periods_86400' );
		}
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
	 * Get output string for chart.
	 *
	 * @param  array   $atts  User defined attributes in shortcode tag.
	 * @param  boolean $cache Use filecache.
	 * @return string
	 */
	public static function output_chart( $atts, $cache = true ) {
		$name        = $atts['name'];
		$periods     = $atts['periods'];
		$from_date   = $atts['from'];
		$to_date     = $atts['to'];
		$filename    = WP_BITCOIN_CHART__PLUGIN_DATA_DIR . 'output_' . $name . '_' . strval( $periods ) . '.htm';
		$output_text = '';

		// キャッシュが有効の場合は、キャッシュを利用する.
		if ( $cache ) {
			$now_time    = time();
			$last_access = get_option( 'wp_bitcoin_chart_check_periods_' . strval( $periods ) );
			if ( ( $now_time - $last_access ) > $periods and file_exists( $filename ) ) {
				$output_text = file_get_contents( $filename );
				return $output_text;
			}
		}

		$chart = json_encode( self::get_chart( $atts ) );

		// ツール部分のHTML.
		$tools_text = <<<EOT
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
		From:&nbsp;<input type='text' id='${name}-from-input' value='${from_date}'>&nbsp;To:&nbsp;<input type='text' id='myChart-to-input' value='${to_date}'>
	</div>
</div>
EOT;

		// スクリプト部分のHTML.
		$scripts_text = <<<EOT
<script>
	var ctx = document.getElementById('${name}').getContext('2d');
	var chart = new Chart(ctx, ${chart});
</script>
EOT;

		// 画面に表示する内容を加工して整理する.
		$output_text           = "<div class='wp-bitcoin-chart-field'>";
		$atts['tool_position'] = strtolower( $atts['tool_position'] );
		if ( 'top' == $atts['tool_position'] or 'both' == $atts['tool_position'] ) {
			$output_text .= $tools;
		}
		$output_text .= "<canvas id='${name}'></canvas>";
		if ( 'bottom' == $atts['tool_position'] or 'both' == $atts['tool_position'] ) {
			$output_text .= $tools_text;
		}
		$output_text .= '</div>';
		$output_text .= $scripts_text;

		file_put_contents( $filename, $output_text );

		return $output_text;
	}

	/**
	 * Get chart datasets and labels.
	 *
	 * @param  array $atts User defined attributes in shortcode tag.
	 * @return string
	 */
	public static function get_chart( $atts ) {
		if ( ! empty( $atts['from'] ) ) {
			$from_timestamp = strtotime( $atts['from'] );
		}
		if ( ! empty( $atts['to'] ) ) {
			$to_timestamp = strtotime( $atts['to'] );
		}

		$periods  = $atts['periods'];
		$labels   = self::get_data_label( $periods, $from_timestamp, $to_timestamp );
		$datasets = array();

		// どのデータを表示するのかを識別して設定する.
		if ( ! empty( $atts['op'] ) ) {
			$datasets[] = array(
				'label'       => 'Open Price',
				'borderColor' => $atts['op_color'],
				'data'        => self::get_graph_data( $periods, 1, $from_timestamp, $to_timestamp ),
				'drawBorder'  => false,
			);
		}
		if ( ! empty( $atts['hp'] ) ) {
			$datasets[] = array(
				'label'       => 'High Price',
				'borderColor' => $atts['hp_color'],
				'data'        => self::get_graph_data( $periods, 2, $from_timestamp, $to_timestamp ),
				'drawBorder'  => false,
			);
		}
		if ( ! empty( $atts['lp'] ) ) {
			$datasets[] = array(
				'label'       => 'Low Price',
				'borderColor' => $atts['lp_color'],
				'data'        => self::get_graph_data( $periods, 3, $from_timestamp, $to_timestamp ),
				'drawBorder'  => false,
			);
		}
		if ( ! empty( $atts['cp'] ) ) {
			$datasets[] = array(
				'label'       => 'Close Price',
				'borderColor' => $atts['cp_color'],
				'data'        => self::get_graph_data( $periods, 4, $from_timestamp, $to_timestamp ),
				'drawBorder'  => false,
			);
		}
		if ( ! empty( $atts['vo'] ) ) {
			$datasets[] = array(
				'label'       => 'Volume',
				'borderColor' => $atts['vo_color'],
				'data'        => self::get_graph_data( $periods, 5, $from_timestamp, $to_timestamp ),
				'drawBorder'  => false,
			);
		}
		$chart = array(
			'type' => 'line',
			'data' => array(
				'labels'   => array_values( $labels ),
				'datasets' => $datasets,
				'options'  => array(
					'title' => array(
						'display' => true,
						'text'    => 'BTC/JPY',
					),
				),
			),
		);
		return $chart;
	}

	/**
	 * Get only label data.
	 *
	 * @param  integer   $periods 取得するデータの時間間隔. 300, 1800, 3600, 86400のみを認めます. 初期値は86400.
	 * @param  timestamp $from_timestamp 開始時間.
	 * @param  timestamp $to_timestamp   終了時間.
	 * @return array
	 */
	public static function get_data_label( $periods = WP_BITCOIN_CHART__DEFAULT_CHART_PERIODS, $from_timestamp = null, $to_timestamp = null ) {

		$filename = WP_BITCOIN_CHART__PLUGIN_DATA_DIR . 'cw_' . strval( $periods ) . '.json';
		$all_data = array();

		if ( file_exists( $filename ) ) {
			$all_data = file_get_contents( $filename );
			$all_data = array_keys( json_decode( $all_data, true ) );
			// 時刻を読めるように変換.
			foreach ( $all_data as $key => $data_timestamp ) {
				if ( ! empty( $from_timestamp ) and $data_timestamp < $from_timestamp ) {
					// from よりも前のデータは削除する.
					unset( $all_data[ $key ] );
					continue;
				}
				if ( ! empty( $to_timestamp ) and $data_timestamp > $to_timestamp ) {
					// to よりも後のデータは削除する.
					unset( $all_data[ $key ] );
					continue;
				}
				if ( WP_BITCOIN_CHART__CHART_PERIODS_ONE_DAY == $periods ) {
					$all_data[ $key ] = date( 'n月j日', $data_timestamp );
				} else {
					$all_data[ $key ] = date( 'n月j日 H:i', $data_timestamp );
				}
			}
		}

		return $all_data;
	}

	/**
	 * Get only single graph data.
	 *
	 * @param  integer   $periods 取得するデータの時間間隔. 300, 1800, 3600, 86400のみを認めます. 初期値は86400.
	 * @param  integer   $assort 取得するデータの種類です. 1: Open Price, 2: High Price, 3: Low Price, 4: Close Price, 5: Volume. 先頭のデータは日付です.
	 * @param  timestamp $from_timestamp 開始時間.
	 * @param  timestamp $to_timestamp   終了時間.
	 * @return array
	 */
	public static function get_graph_data( $periods = WP_BITCOIN_CHART__DEFAULT_CHART_PERIODS, $assort = null, $from_timestamp = null, $to_timestamp = null ) {

		$filename = WP_BITCOIN_CHART__PLUGIN_DATA_DIR . 'cw_' . strval( $periods ) . '.json';
		$result   = array();

		if ( null !== $assort and file_exists( $filename ) ) {
			$all_data = file_get_contents( $filename );
			$all_data = json_decode( $all_data, true );
			// from to の対応をする.
			if ( ! empty( $from_timestamp ) or ! empty( $to_timestamp ) ) {
				foreach ( $all_data as $data_timestamp => $data ) {
					if ( ! empty( $from_timestamp ) and $data_timestamp < $from_timestamp ) {
						// from よりも前のデータは削除する.
						unset( $all_data[ $data_timestamp ] );
					}
					if ( ! empty( $to_timestamp ) and $data_timestamp > $to_timestamp ) {
						// to よりも後のデータは削除する.
						unset( $all_data[ $data_timestamp ] );
					}
				}
			}

			$result = array_column( $all_data, $assort );
		}

		return $result;
	}

	/**
	 * Get cryptowatch data
	 * Cryptowatch.jpからデータを取得します.この処理は再帰的な処理を含みます.
	 * データが取得できなくなるまで取得します.
	 *
	 * @param  integer $periods 取得するデータの時間間隔. 300, 1800, 3600, 86400のみを認めます. 初期値は86400.
	 * @return integer response status. 1: No period. 2: Interval is too short. 3: Cannot create json file. 99: Finished.
	 */
	public static function get_cryptowatch_data( $periods = WP_BITCOIN_CHART__DEFAULT_CHART_PERIODS ) {
		// No periods. Exist.
		if ( empty( $periods ) or ! in_array( $periods, array( 300, 1800, 3600, 86400 ) ) ) {
			return 1;
		}

		// 最後にアクセスした時間を取得します.
		$last_access = get_option( 'wp_bitcoin_chart_check_periods_' . strval( $periods ) );
		$now_time    = time();

		// Interval is too short.
		if ( ! defined( 'WP_DEBUG' ) and ( $now_time - $last_access ) < $periods ) {
			return 2;
		}

		// https://cryptowatch.jp/bitflyer/btcjpy からデータを取得します.
		$json = file_get_contents( 'https://api.cryptowat.ch/markets/bitflyer/btcjpy/ohlc?periods=' . strval( $periods ) . '&after=' . strval( $last_access ) );

		$json = mb_convert_encoding( $json, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN' );
		$cw   = json_decode( $json, true );

		if ( ! empty( $cw['result'][ strval( $periods ) ] ) ) {

			$periods_keys = array_column( $cw['result'][ strval( $periods ) ], 0 );
			$periods_data = array_combine( $periods_keys, $cw['result'][ strval( $periods ) ] );
			$filename     = WP_BITCOIN_CHART__PLUGIN_DATA_DIR . 'cw_' . strval( $periods ) . '.json';

			if ( file_exists( $filename ) ) {
				// 2つの配列のデータをマージする.
				$all_data     = file_get_contents( $filename );
				$all_data     = json_decode( $all_data, true );
				$periods_data = array_merge( $all_data, $periods_data );
			}

			$result = file_put_contents( $filename, json_encode( $periods_data ) );
			if ( false === $result ) {
				return 3;
			}
		}

		// アクセス直前の時間をcheck_periodsに設定します.
		$last_access = $now_time;

		// 現在時刻を最後にアクセスした時間とします.
		update_option( 'wp_bitcoin_chart_check_periods_' . strval( $periods ), $last_access );

		// Finished.
		return 99;
	}
}
