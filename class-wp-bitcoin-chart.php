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
	 * First executed
	 *
	 * @return void
	 */
	public static function init() {
		self::init_hooks();
	}

	/**
	 * Initializes WordPress hooks
	 *
	 * @return void
	 */
	private static function init_hooks() {
		// Add Action.
		add_action( 'wp_enqueue_scripts', array( 'WP_Bitcoin_Chart', 'register_jquery' ) );
		add_action( 'admin_enqueue_scripts', array( 'WP_Bitcoin_Chart', 'register_jquery' ) );
		add_action( 'wp_ajax_wp_bitcoin_chart', array( 'WP_Bitcoin_Chart', 'wp_bitcoin_chart_get_json_data' ) );
		add_action( 'wp_ajax_nopriv_wp_bitcoin_chart', array( 'WP_Bitcoin_Chart', 'wp_bitcoin_chart_get_json_data' ) );

		// Exp: [wp_bitcoin_chart_view]
		// ショートコードで画面にグラフを表示する.
		add_shortcode( 'wp_bitcoin_chart_view', array( 'WP_Bitcoin_Chart', 'wp_bitcoin_chart_view_shortcode' ) );
	}

	/**
	 * WP Bitcoin Chart activation
	 *
	 * @return void
	 */
	public static function wp_bitcoin_chart_activation() {
		// Restart.
		self::wp_bitcoin_chart_restart();
	}

	/**
	 * WP Bitcoin Chart deactivation
	 *
	 * @return void
	 */
	public static function wp_bitcoin_chart_deactivation() {
		// WP_DEBUGが設定されていたら、フラグを削除する.
		self::wbc_delete_options();
	}

	/**
	 * WP Bitcoin Chart Plugin uninstall
	 *
	 * @return void
	 */
	public static function wp_bitcoin_chart_uninstall() {
		// Delete options.
		// True, if option is successfully deleted. False on failure, or option does not exist.
		self::wbc_delete_options();
	}

	/**
	 * WP Bitcoin Chart restart.
	 *
	 * @return void
	 */
	public static function wp_bitcoin_chart_restart() {
		// Remove /data dir.
		if ( self::remove_dir( WBC__PLUGIN_DATA_DIR ) ) {
			mkdir( WBC__PLUGIN_DATA_DIR, 0755 );
		}

		// Insert options.
		self::wbc_init_options();
	}

	/**
	 * WP Bitcoin Chart initialize options.
	 *
	 * @return void
	 */
	public static function wbc_init_options() {
		update_option( 'wp_bitcoin_chart_check_periods_300', WBC__DEFAULT_CHART_START );
		update_option( 'wp_bitcoin_chart_check_periods_1800', WBC__DEFAULT_CHART_START );
		update_option( 'wp_bitcoin_chart_check_periods_3600', WBC__DEFAULT_CHART_START );
		update_option( 'wp_bitcoin_chart_check_periods_86400', WBC__DEFAULT_CHART_START );
	}

	/**
	 * WP Bitcoin Chart initialize options.
	 *
	 * @param  integer $periods        取得するデータの時間間隔. 300, 1800, 3600, 86400のみを認めます. 初期値は86400.
	 * @param  integer $start_unixtime 開始時刻のUNIX時間.
	 * @return void
	 */
	public static function wbc_init_option( $periods = WBC__DEFAULT_CHART_PERIODS, $start_unixtime = WBC__DEFAULT_CHART_START ) {
		update_option( 'wp_bitcoin_chart__periods_' . strval( $periods ), WBC__DEFAULT_CHART_START );
	}

	/**
	 * WP Bitcoin Chart delete options.
	 *
	 * @return void
	 */
	public static function wbc_delete_options() {
		delete_option( 'wp_bitcoin_chart_check_periods_300' );
		delete_option( 'wp_bitcoin_chart_check_periods_1800' );
		delete_option( 'wp_bitcoin_chart_check_periods_3600' );
		delete_option( 'wp_bitcoin_chart_check_periods_86400' );
	}

	/**
	 * Get cache json filename.
	 *
	 * @param  integer $periods 取得するデータの時間間隔. 300, 1800, 3600, 86400のみを認めます. 初期値は86400.
	 * @return string
	 */
	public static function get_cache_json_filename( $periods ) {
		return WBC__PLUGIN_DATA_DIR . 'cw_' . strval( $periods ) . '.json';
	}

	/**
	 * Get cache htm filename.
	 *
	 * @param  string  $name      id name.
	 * @param  integer $periods   取得するデータの時間間隔. 300, 1800, 3600, 86400のみを認めます. 初期値は86400.
	 * @param  date    $from_date from data.
	 * @param  date    $to_date   to data.
	 * @return string
	 */
	public static function get_cache_htm_filename( $name, $periods, $from_date = null, $to_date = null ) {
		return WBC__PLUGIN_DATA_DIR . 'output_' . $name . '_' . strval( $periods ) . 'from' . $from_date . 'to' . $to_date . '.htm';
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
		$filename    = self::get_cache_htm_filename( $name, $periods, $from_date, $to_date );
		$output_text = '';

		// 短いperiods用の日付.
		$from_date_short = date( 'Y-m-d', strtotime( $atts['to'] . ' -1 day' ) );
		$to_date_short   = $atts['to'];

		// キャッシュが有効の場合は、キャッシュを利用する.
		if ( $cache ) {
			$now_time    = time();
			$last_access = get_option( 'wp_bitcoin_chart__periods_' . strval( $periods ) );
			if ( file_exists( $filename ) and ( $now_time - $last_access ) > $periods ) {
				$output_text = file_get_contents( $filename );
				return $output_text;
			}
		}

		$chart = json_encode( self::get_chart( $atts ) );

		// ツール部分のHTML.
		$tools_text = <<<EOT
<form action="#" class="wp-bitcoin-chart-form" field-name="{$atts['name']}">
	<input type="hidden" name="name" value="{$atts['name']}">
	<input type="hidden" name="op_color" value="{$atts['op_color']}">
	<input type="hidden" name="hp_color" value="{$atts['hp_color']}">
	<input type="hidden" name="lp_color" value="{$atts['lp_color']}">
	<input type="hidden" name="cp_color" value="{$atts['cp_color']}">
	<input type="hidden" name="vo_color" value="{$atts['vo_color']}">
	<input type="hidden" name="op" value="{$atts['op']}">
	<input type="hidden" name="hp" value="{$atts['hp']}">
	<input type="hidden" name="lp" value="{$atts['lp']}">
	<input type="hidden" name="cp" value="{$atts['cp']}">
	<input type="hidden" name="vo" value="{$atts['vo']}">
	<input type="hidden" name="tool_position" value="{$atts['tool_position']}">
	<div class="field has-addons">
		<p class="control">
			<span class="select">
				<select name="periods" id="${name}-periods" class="param-field wbc-change-periods">
					<option value="300">10 min</option>
					<option value="1800">30 min</option>
					<option value="3600">1 hour</option>
					<option value="86400" selected="selected">1 day</option>
				</select>
			</span>
		</p>
		<p class="control">
			<input type="text" name="from" id='${name}-from-input' class='input param-field' value='${from_date}'>
			<input type="hidden" name="from_default" id='${name}-from-default' value='${from_date}'>
			<input type="hidden" name="from_default_short" id='${name}-from-default-short' value='${from_date_short}'>
		</p>
		<p class="control">
			<input type="text" name="to" id='${name}-from-input' class='input param-field' value='${to_date}'>
			<input type="hidden" name="to_default" id='${name}-to-default' value='${to_date}'>
			<input type="hidden" name="to_default_short" id='${name}-to-default-short' value='${to_date_short}'>
		</p>
		<p class="control">
			<a class="button wp-bitcoin-chart-refresh-button" form-name="{$atts['name']}_button">
				<i class="fa fa-refresh"></i> 表示
			</a>
		</p>
	</div>
</form>
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
			$output_text .= $tools_text;
		}
		$output_text .= "<canvas id='${name}'></canvas>";
		if ( 'bottom' == $atts['tool_position'] or 'both' == $atts['tool_position'] ) {
			$output_text .= $tools_text;
		}
		$output_text .= '</div>';
		$output_text .= $scripts_text;

		file_put_contents( $filename, $output_text );
		chmod( $filename, 0755 );

		return $output_text;
	}

	/**
	 * Get chart datasets and labels.
	 *
	 * @param  array $atts User defined attributes in shortcode tag.
	 * @return string
	 */
	public static function get_chart( $atts ) {
		$from_unixtime = null;
		if ( ! empty( $atts['from'] ) ) {
			$from_unixtime = strtotime( $atts['from'] );
		}
		$to_unixtime = null;
		if ( ! empty( $atts['to'] ) ) {
			$to_unixtime = strtotime( $atts['to'] );
		}

		$periods  = $atts['periods'];
		$labels   = self::get_data_label( $periods, $from_unixtime, $to_unixtime );
		$datasets = array();

		// どのデータを表示するのかを識別して設定する.
		if ( ! empty( $atts['op'] ) ) {
			$datasets[] = array(
				'label'       => 'Open Price',
				'borderColor' => $atts['op_color'],
				'data'        => self::get_graph_data( $periods, 1, $from_unixtime, $to_unixtime ),
				'drawBorder'  => false,
			);
		}
		if ( ! empty( $atts['hp'] ) ) {
			$datasets[] = array(
				'label'       => 'High Price',
				'borderColor' => $atts['hp_color'],
				'data'        => self::get_graph_data( $periods, 2, $from_unixtime, $to_unixtime ),
				'drawBorder'  => false,
			);
		}
		if ( ! empty( $atts['lp'] ) ) {
			$datasets[] = array(
				'label'       => 'Low Price',
				'borderColor' => $atts['lp_color'],
				'data'        => self::get_graph_data( $periods, 3, $from_unixtime, $to_unixtime ),
				'drawBorder'  => false,
			);
		}
		if ( ! empty( $atts['cp'] ) ) {
			$datasets[] = array(
				'label'       => 'Close Price',
				'borderColor' => $atts['cp_color'],
				'data'        => self::get_graph_data( $periods, 4, $from_unixtime, $to_unixtime ),
				'drawBorder'  => false,
			);
		}
		if ( ! empty( $atts['vo'] ) ) {
			$datasets[] = array(
				'label'       => 'Volume',
				'borderColor' => $atts['vo_color'],
				'data'        => self::get_graph_data( $periods, 5, $from_unixtime, $to_unixtime ),
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
	 * @param  integer   $periods        取得するデータの時間間隔. 300, 1800, 3600, 86400のみを認めます. 初期値は86400.
	 * @param  timestamp $from_unixtime  開始時間.
	 * @param  timestamp $to_unixtime    終了時間.
	 * @param  boolean   $next           次の処理をするかどうか.
	 * @return array
	 */
	public static function get_data_label( $periods = WBC__DEFAULT_CHART_PERIODS, $from_unixtime = null, $to_unixtime = null, $next = true ) {

		$filename = self::get_cache_json_filename( $periods );
		$all_data = array();

		if ( file_exists( $filename ) ) {
			$all_data = file_get_contents( $filename );
			$all_data = array_keys( json_decode( $all_data, true ) );
			// 時刻を読めるように変換.
			foreach ( $all_data as $key => $data_unixtime ) {
				if ( ! empty( $from_unixtime ) and $data_unixtime < $from_unixtime ) {
					// from よりも前のデータは削除する.
					unset( $all_data[ $key ] );
					continue;
				} elseif ( ! empty( $to_unixtime ) and $data_unixtime > $to_unixtime ) {
					// to よりも後のデータは削除する.
					unset( $all_data[ $key ] );
					continue;
				}
				if ( WBC__CHART_PERIODS_ONE_DAY == $periods ) {
					$all_data[ $key ] = date( 'n月j日', $data_unixtime );
				} else {
					$all_data[ $key ] = date( 'n月j日 H:i', $data_unixtime );
				}
			}
		} elseif ( $next ) {
			// 保存されているデータの更新.
			self::get_cryptowatch_data( $periods, true );
			self::get_data_label( $periods, $from_unixtime, $to_unixtime, false );
		}

		return $all_data;
	}

	/**
	 * Get only single graph data.
	 *
	 * @param  integer   $periods        取得するデータの時間間隔. 300, 1800, 3600, 86400のみを認めます. 初期値は86400.
	 * @param  integer   $assort         取得するデータの種類です. 1: Open Price, 2: High Price, 3: Low Price, 4: Close Price, 5: Volume. 先頭のデータは日付です.
	 * @param  timestamp $from_unixtime  開始時間.
	 * @param  timestamp $to_unixtime    終了時間.
	 * @param  boolean   $next           次の処理をするかどうか.
	 * @return array
	 */
	public static function get_graph_data( $periods = WBC__DEFAULT_CHART_PERIODS, $assort = null, $from_unixtime = null, $to_unixtime = null, $next = true ) {

		$filename = self::get_cache_json_filename( $periods );
		$result   = array();

		if ( null !== $assort and file_exists( $filename ) ) {
			$all_data = file_get_contents( $filename );
			$all_data = json_decode( $all_data, true );
			// from to の対応をする.
			if ( ! empty( $from_unixtime ) or ! empty( $to_unixtime ) ) {
				foreach ( $all_data as $data_unixtime => $data ) {
					if ( ! empty( $from_unixtime ) and $data_unixtime < $from_unixtime ) {
						// from よりも前のデータは削除する.
						unset( $all_data[ $data_unixtime ] );
					} elseif ( ! empty( $to_unixtime ) and $data_unixtime > $to_unixtime ) {
						// to よりも後のデータは削除する.
						unset( $all_data[ $data_unixtime ] );
					}
				}
			}

			$result = self::array_column( $all_data, $assort );
		} elseif ( $next ) {
			// 保存されているデータの更新.
			self::get_cryptowatch_data( $periods, true );
			self::get_data_label( $periods, $assort, $from_unixtime, $to_unixtime, false );
		}

		return $result;
	}

	/**
	 * Get cryptowatch data
	 * Cryptowatch.jpからデータを取得します.この処理は再帰的な処理を含みます.
	 * データが取得できなくなるまで取得します.
	 *
	 * @param  integer $periods 取得するデータの時間間隔. 300, 1800, 3600, 86400のみを認めます. 初期値は86400.
	 * @param  boolean $clear   キャッシュデータをクリアするか.
	 * @return integer response status. 1: No period. 2: Interval is too short. 3: Cannot create json file. 99: Finished.
	 */
	public static function get_cryptowatch_data( $periods = WBC__DEFAULT_CHART_PERIODS, $clear = false ) {
		// No periods. Exist.
		if ( ! in_array( $periods, array( 300, 1800, 3600, 86400 ) ) ) {
			return 1;
		}

		// 最後にアクセスした時間を取得します.
		if ( $clear ) {
			self::wbc_init_option( $periods, strtotime( '+1 month' ) );
			return self::get_cryptowatch_data( $periods, false );
		} else {
			$last_access = get_option( 'wp_bitcoin_chart__periods_' . strval( $periods ) );
		}

		$now_time = time();

		// Interval is too short.
		if ( ( $now_time - $last_access ) < $periods ) {
			return 2;
		}

		// https://cryptowatch.jp/bitflyer/btcjpy からデータを取得します.
		$json = file_get_contents( 'https://api.cryptowat.ch/markets/bitflyer/btcjpy/ohlc?periods=' . strval( $periods ) . '&after=' . strval( $last_access ) );
		$json = mb_convert_encoding( $json, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN' );
		$cw   = json_decode( $json, true );

		if ( ! empty( $cw['result'][ strval( $periods ) ] ) ) {

			$periods_keys = self::array_column( $cw['result'][ strval( $periods ) ], 0 );
			$periods_data = array_combine( $periods_keys, $cw['result'][ strval( $periods ) ] );
			$filename     = self::get_cache_json_filename( $periods );

			if ( file_exists( $filename ) ) {
				// 2つの配列のデータをマージする.
				$all_data     = file_get_contents( $filename );
				$all_data     = json_decode( $all_data, true );
				$periods_data = array_merge( $all_data, $periods_data );
			}

			$result = file_put_contents( $filename, json_encode( $periods_data ) );
			chmod( $filename, 0755 );
			if ( false === $result ) {
				return 3;
			}

			// アクセス直前の時間を_periodsに設定します.
			$last_access = $now_time;

			// 現在時刻を最後にアクセスした時間とします.
			update_option( 'wp_bitcoin_chart__periods_' . strval( $periods ), $last_access );

			// Finished.
			return 99;
		}

		// Failed.
		return 4;
	}

	/**
	 * Register jquery.
	 *
	 * @return void
	 */
	public static function register_jquery() {

		$wp_bitcoin_chart_css = get_option( 'wp_bitcoin_chart_css' );

		if ( empty( $wp_bitcoin_chart_css ) ) {
			// 独自のCSSを使用しない場合は、bulma.ioのCSSを使う.
			wp_register_style( 'wbc_style', plugins_url( 'css/wbc_style.css', __FILE__ ) );
			wp_enqueue_style( 'wbc_style' );
			wp_register_style( 'fontawesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css' );
			wp_enqueue_style( 'fontawesome' );
		}

		wp_register_script( 'momentjs', 'https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.19.3/moment.min.js', array(), '0.1.0' );
		wp_enqueue_script( 'momentjs' );

		wp_register_script( 'chartjs', 'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js', array(), '0.1.0' );
		wp_enqueue_script( 'chartjs' );

		wp_register_script( 'wpbitcoinchartjs', plugins_url( 'js/wp-bitcoin-chart.js', __FILE__ ), array( 'jquery' ), '0.1.0' );
		wp_enqueue_script( 'wpbitcoinchartjs' );

		$params = array(
			'ajax_url'   => admin_url( 'admin-ajax.php' ),
			'_security'  => wp_create_nonce( 'wp-bitcoin-chart-nonce' ),
			'loader_url' => plugins_url( 'img/loader_blue_32.gif', __FILE__ ),
		);

		wp_localize_script( 'wpbitcoinchartjs', 'wp_bitcoin_chart_ajax', $params );
	}

	/**
	 * WP Bitcoin Chart view shortcode
	 *
	 * @param array $atts User defined attributes in shortcode tag.
	 * @return string
	 */
	public static function wp_bitcoin_chart_view_shortcode( $atts ) {
		// ショートコードの変数 foo と bar を使用することを宣言し、さらに初期値を設定する.
		$atts = shortcode_atts(
			array(
				'name'          => WBC__DEFAULT_CHART_NAME,
				'periods'       => WBC__DEFAULT_CHART_PERIODS,
				'op_color'      => WBC__DEFAULT_OP_COLOR,
				'hp_color'      => WBC__DEFAULT_HP_COLOR,
				'lp_color'      => WBC__DEFAULT_LP_COLOR,
				'cp_color'      => WBC__DEFAULT_CP_COLOR,
				'vo_color'      => WBC__DEFAULT_VO_COLOR,
				'op'            => 0,
				'hp'            => 0,
				'lp'            => 0,
				'cp'            => 0,
				'vo'            => 0,
				'from'          => date( 'Y-m-d', strtotime( '-1 month' ) ),
				'to'            => date( 'Y-m-d' ),
				'tool_position' => 'top', // none, top, bottom or both.
			),
			$atts,
			'wp-bitcoin-chart-view'
		);

		// 表示内容をreturnする.
		$cache = true;
		if ( defined( 'WP_DEBUG' ) ) {
			$cache = false;
		}

		return self::output_chart( $atts, $cache );
	}

	/**
	 * WP Bitcoin Chart ajax get data action.
	 * Only Ajax.
	 *
	 * @return void wp_send_json
	 */
	public static function wp_bitcoin_chart_get_json_data() {
		// ブログの外部からのリクエストを間違って処理しないように AJAX リクエストを検証します.
		check_ajax_referer( 'wp-bitcoin-chart-nonce', '_security' );

		$atts = $_POST;

		if ( empty( $atts['periods'] ) or ! in_array( $atts['periods'], array( 300, 1800, 3600, 86400 ) ) ) {
			unset( $atts['periods'] );
		}
		if ( empty( $atts['from'] ) or ! self::is_date_format( $atts['from'] ) ) {
			unset( $atts['from'] );
		}
		if ( empty( $atts['to'] ) or ! self::is_date_format( $atts['to'] ) ) {
			unset( $atts['to'] );
		}

		$atts = wp_parse_args(
			$atts,
			array(
				'name'          => WBC__DEFAULT_CHART_NAME,
				'periods'       => WBC__DEFAULT_CHART_PERIODS,
				'op_color'      => WBC__DEFAULT_OP_COLOR,
				'hp_color'      => WBC__DEFAULT_HP_COLOR,
				'lp_color'      => WBC__DEFAULT_LP_COLOR,
				'cp_color'      => WBC__DEFAULT_CP_COLOR,
				'vo_color'      => WBC__DEFAULT_VO_COLOR,
				'op'            => 0,
				'hp'            => 0,
				'lp'            => 0,
				'cp'            => 0,
				'vo'            => 0,
				'from'          => date( 'Y-m-d', strtotime( '-1 month' ) ),
				'to'            => date( 'Y-m-d' ),
				'tool_position' => 'top', // none, top, bottom or both.
			)
		);

		$chart = self::get_chart( $atts );

		$result = array(
			'chart' => $chart,
			'atts'  => $atts,
		);

		wp_send_json( $result );
	}

	/**
	 * Is Date Format.
	 *
	 * @param  date $date Input datetime.
	 * @return boolean
	 */
	public static function is_date_format( $date ) {
		return date( 'Y-m-d', strtotime( $date ) ) === $date;
	}

	/**
	 *  Is Datetime Format.
	 *
	 * @param  datetime $datetime Input datetime.
	 * @return boolean
	 */
	public static function is_datetime_format( $datetime ) {
		return date( 'Y-m-d H:i:s', strtotime( $datetime ) ) === $datetime;
	}

	/**
	 * 指定したキーの値を取得する。2次元配列のみ対応.
	 * array_columnがPHP5.5以下で使えないのでコピペ.
	 *
	 * @param array $target_data 値を取り出したい多次元配列.
	 * @param mixed $column_key  値を返したいカラム.
	 * @param mixed $index_key   返す配列のインデックスとして使うカラム.
	 * @return array             入力配列の単一のカラムを表す値の配列を返し.
	 */
	public static function array_column( $target_data, $column_key, $index_key = null ) {

		if ( false === is_array( $target_data ) || 0 === count( $target_data ) ) {
			return false;
		}

		$result = array();
		foreach ( $target_data as $array ) {
			if ( false === array_key_exists( $column_key, $array ) ) {
				continue;
			}
			if ( false === is_null( $index_key ) and true === array_key_exists( $index_key, $array ) ) {
				$result[ $array[ $index_key ] ] = $array[ $column_key ];
				continue;
			}
			$result[] = $array[ $column_key ];
		}

		if ( 0 === count( $result ) ) {
			return false;
		}

		return $result;
	}

	/**
	 * Remove dir.
	 *
	 * @param  string $dir_path target dir path.
	 * @return boolean
	 */
	public static function remove_dir( $dir_path ) {
		$filepath = $dir_path . '*';
		foreach ( glob( $filepath ) as $file ) {
			unlink( $file );
		}
		return rmdir( $dir_path );
	}
}
