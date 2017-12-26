<?php
/**
 * Data class.
 *
 * @since      0.1.0
 * @version    1.0.0
 * @package    wp-bitcoin-chart
 * @subpackage wp-bitcoin-chart/includes
 * @author     1yaan, {@link https://github.com/1yaan https://github.com/1yaan}
 * @copyright  1yaan, {@link https://github.com/1yaan https://github.com/1yaan}
 * @license    GPLv2 or later, {@link https://www.gnu.org/licenses/gpl.html https://www.gnu.org/licenses/gpl.html}
 */

/**
 * WBC_Data
 */
class WBC_Data {

	private $periods = 0;
	private $assort = 0;
	private $from_unixtime = 0;
	private $to_unixtime = 0;
	private $is_cache = true;
	private $atts;
	private $chartdata;

	/**
	 * Construct.
	 *
	 * @access public
	 * @since  0.1.0
	 * @param  integer $from_unixtime 開始時間のUNIXTIME
	 * @param  integer $to_unixtime   終了時間のUNIXTIME
	 * @param  integer $peridos       データ間隔
	 * @param  integer $assort        表示するグラフの種類
	 */
	public function __construct( $from_unixtime = null, $to_unixtime = null, $peridos = null, $assort = null ) {
		// 表示内容をreturnする.
		if ( defined( 'WP_DEBUG' ) ) {
			$this->is_cache = false;
		}
		if ( ! empty( $from_unixtime ) ) {
			$this->from_unixtime = $from_unixtime;
		}
		if ( ! empty( $to_unixtime ) ) {
			$this->to_unixtime = $to_unixtime;
		}
		if ( ! empty( $assort ) ) {
			$this->assort = $assort;
		}
		if ( ! empty( $peridos ) ) {
			$this->peridos = $peridos;
		} else {
			$this->peridos = WBC__DEFAULT_CHART_PERIODS;
		}
	}

	/**
	 * Set atts.
	 *
	 * attsを設定します. from, to, periods等のプロパティも設定します.
	 *
	 * @param  array $atts Input.
	 * @return void
	 */
	public function setAtts( $atts ) {
		$this->atts = $atts;

		if ( ! empty( $atts['from'] ) and WBC_Common::is_date_format( $atts['from'] ) ) {
			$this->from_unixtime = strtotime( $atts['from'] );
		}
		if ( ! empty( $atts['to'] ) and WBC_Common::is_date_format( $atts['to'] ) ) {
			$this->to_unixtime = strtotime( $atts['to'] );
		}
		if ( ! empty( $atts['periods'] ) and in_array( $atts['periods'], array( 300, 1800, 3600, 86400 ) ) ) {
			$this->periods = $atts['periods'];
		}
	}

	public function make_data_file() {
		// 該当のファイルを作成/更新します.
	}

	public function get_market_fluctuations() {
		// 24時間の市場の相場変動を取得します.
	}

	public function get_now_price() {
		// 一番最新のデータの価格を取得します.
	}

	/**
	 * Get only label data.
	 *
	 * @access public
	 * @since  0.1.0
	 * @param  boolean $next 次の処理をするかどうか.
	 * @return array
	 */
	public function get_label( $next = true ) {

		$filename = WBC_Common::get_cache_json_filename( $this->periods );
		$all_data = array();

		if ( file_exists( $filename ) ) {
			$all_data = file_get_contents( $filename );
			$all_data = array_keys( json_decode( $all_data, true ) );
			// 時刻を読めるように変換.
			foreach ( $all_data as $key => $data_unixtime ) {
				if ( ! empty( $this->from_unixtime ) and $data_unixtime < $this->from_unixtime ) {
					// from よりも前のデータは削除する.
					unset( $all_data[ $key ] );
					continue;
				} elseif ( ! empty( $this->to_unixtime ) and $data_unixtime > $this->to_unixtime ) {
					// to よりも後のデータは削除する.
					unset( $all_data[ $key ] );
					continue;
				}
				if ( WBC__CHART_PERIODS_ONE_DAY == $this->periods ) {
					$all_data[ $key ] = date( 'n月j日', $data_unixtime );
				} else {
					$all_data[ $key ] = date( 'n月j日 H:i', $data_unixtime );
				}
			}
		} elseif ( $next ) {
			// 保存されているデータの更新.
			$this->receive_cryptowatch_data( $this->periods, true );
			$this->get_label( false );
		}

		return $all_data;
	}

	/**
	 * Get only single graph data.
	 *
	 * @access public
	 * @since  0.1.0
	 * @param  integer $assort 取得するデータの種類です. 1: Open Price, 2: High Price, 3: Low Price, 4: Close Price, 5: Volume. 先頭のデータは日付です.
	 * @param  boolean $next   次の処理をするかどうか.
	 * @return array
	 */
	public function get_graph_data( $assort = null, $next = true ) {
		if ( ! empty( $assort ) ) {
			$this->assort = $assort;
		}
		if ( empty( $this->assort ) ) {
			return array();
		}

		$filename = WBC_Common::get_cache_json_filename( $this->periods );
		$result   = array();

		if ( file_exists( $filename ) ) {
			$all_data = file_get_contents( $filename );
			$all_data = json_decode( $all_data, true );

			// from to の対応をする.
			if ( ! empty( $this->from_unixtime ) or ! empty( $this->to_unixtime ) ) {
				foreach ( $all_data as $data_unixtime => $data ) {
					if ( ! empty( $this->from_unixtime ) and $data_unixtime < $this->from_unixtime ) {
						// from よりも前のデータは削除する.
						unset( $all_data[ $data_unixtime ] );
					} elseif ( ! empty( $this->to_unixtime ) and $data_unixtime > $this->to_unixtime ) {
						// to よりも後のデータは削除する.
						unset( $all_data[ $data_unixtime ] );
					}
				}
			}
			// 該当のデータの取り出し.
			$result = WBC_Common::array_column( $all_data, $this->assort );
		} elseif ( $next ) {
			// 保存されているデータの更新.
			$this->receive_cryptowatch_data();
			$this->get_graph_data( $this->assort, false );
		}

		return $result;
	}

	/**
	 * Receive cryptowatch data.
	 * Cryptowatch.jpからデータを取得します.この処理は再帰的な処理を含みます.
	 * データが取得できなくなるまで取得します.
	 *
	 * @access public
	 * @since  0.1.0
	 * @return integer response status. 1: No period. 2: Interval is too short. 3: Cannot create json file. 99: Finished.
	 */
	public function receive_cryptowatch_data() {
		// No periods. Exist.
		if ( ! in_array( $this->periods, array( 300, 1800, 3600, 86400 ) ) ) {
			return 1;
		}

		if ( ! $this->is_cache ) {
			WBC_Common::wbc_init_option( $this->periods );
		}

		$last_access = get_option( 'wp_bitcoin_chart__periods_' . strval( $this->periods ) );

		$now_time = time();

		// Interval is too short.
		if ( ( $now_time - $last_access ) < $this->periods ) {
			return 2;
		}

		// https://cryptowatch.jp/bitflyer/btcjpy からデータを取得します.
		$json = file_get_contents( 'https://api.cryptowat.ch/markets/bitflyer/btcjpy/ohlc?periods=' . strval( $this->periods ) . '&after=' . strval( $last_access ) );
		$json = mb_convert_encoding( $json, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN' );
		$cw   = json_decode( $json, true );

		if ( ! empty( $cw['result'][ strval( $this->periods ) ] ) ) {

			$periods_keys = WBC_Common::array_column( $cw['result'][ strval( $this->periods ) ], 0 );
			$periods_data = array_combine( $periods_keys, $cw['result'][ strval( $this->periods ) ] );
			$filename     = WBC_Common::get_cache_json_filename( $this->periods );

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
			update_option( 'wp_bitcoin_chart__periods_' . strval( $this->periods ), $last_access );

			// Finished.
			return 99;
		}

		// Failed.
		return 4;
	}

	/**
	 * Get chart datasets and labels.
	 *
	 * @access public
	 * @since  0.1.0
	 * @param  array $atts User defined attributes in shortcode tag.
	 * @return string
	 */
	public function get_chart( $atts = null ) {
		if ( ! empty( $atts ) ) {
			$this->setAtts( $atts );
		}

		$labels   = $this->get_label();
		$datasets = array();

		// どのデータを表示するのかを識別して設定する.
		if ( ! empty( $atts['op'] ) ) {
			$datasets[] = array(
				'label'       => 'Open Price',
				'borderColor' => $this->atts['op_color'],
				'data'        => $this->get_graph_data( 1 ),
				'drawBorder'  => false,
			);
		}
		if ( ! empty( $atts['hp'] ) ) {
			$datasets[] = array(
				'label'       => 'High Price',
				'borderColor' => $this->atts['hp_color'],
				'data'        => $this->get_graph_data( 2 ),
				'drawBorder'  => false,
			);
		}
		if ( ! empty( $atts['lp'] ) ) {
			$datasets[] = array(
				'label'       => 'Low Price',
				'borderColor' => $this->atts['lp_color'],
				'data'        => $this->get_graph_data( 3 ),
				'drawBorder'  => false,
			);
		}
		if ( ! empty( $atts['cp'] ) ) {
			$datasets[] = array(
				'label'       => 'Close Price',
				'borderColor' => $this->atts['cp_color'],
				'data'        => $this->get_graph_data( 4 ),
				'drawBorder'  => false,
			);
		}
		if ( ! empty( $atts['vo'] ) ) {
			$datasets[] = array(
				'label'       => 'Volume',
				'borderColor' => $this->atts['vo_color'],
				'data'        => $this->get_graph_data( 5 ),
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
	 * Get output string for chart.
	 *
	 * @access public
	 * @since  0.1.0
	 * @param  array   $atts  User defined attributes in shortcode tag.
	 * @return string
	 */
	public function output_chart( $atts ) {
		$name        = $atts['name'];
		$periods     = $atts['periods'];
		$from_date   = $atts['from'];
		$to_date     = $atts['to'];
		$filename    = WBC_Common::get_cache_htm_filename( $name, $periods, $from_date, $to_date );
		$output_text = '';

		// 短いperiods用の日付.
		$from_date_short = date( 'Y-m-d', strtotime( $atts['to'] . ' -1 day' ) );
		$to_date_short   = $atts['to'];

		// キャッシュが有効の場合は、キャッシュを利用する.
		if ( $this->is_cache ) {
			$now_time    = time();
			$last_access = get_option( 'wp_bitcoin_chart__periods_' . strval( $periods ) );
			if ( file_exists( $filename ) and ( $now_time - $last_access ) > $periods ) {
				$output_text = file_get_contents( $filename );
				return $output_text;
			}
		}

		$chart = json_encode( $this->get_chart( $atts ) );

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
}
