<?php
/**
 * Data class.
 *
 * Function group receive_*** cwからのデータ取得.
 * Function group get_***     データを整えて返す キャッシュ関連処理.
 * Function group output_***  データをHTML/表示加工して返す.
 *
 * @since      0.1.0
 * @version    2.1.0
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

	/**
	 * Field name.
	 *
	 * @access private
	 * @since  1.0.0
	 * @var    integer
	 */
	private $field_name = 0;

	/**
	 * Periods.
	 *
	 * @access private
	 * @since  1.0.0
	 * @var    integer
	 */
	private $periods = 0;

	/**
	 * Market.
	 *
	 * @access private
	 * @since  2.0.0
	 * @var    integer
	 */
	private $market = '';

	/**
	 * Exchange.
	 *
	 * @access private
	 * @since  2.0.0
	 * @var    integer
	 */
	private $exchange = '';

	/**
	 * Assort.
	 *
	 * @access private
	 * @since  1.0.0
	 * @var    integer
	 */
	private $assort = 0;

	/**
	 * From Unixtime.
	 *
	 * @access private
	 * @since  1.0.0
	 * @var    integer
	 */
	private $from_unixtime = 0;

	/**
	 * To Unixtime.
	 *
	 * @access private
	 * @since  1.0.0
	 * @var    integer
	 */
	private $to_unixtime = 0;

	/**
	 * Cache flag.
	 *
	 * @access private
	 * @since  1.0.0
	 * @var    boolean
	 */
	private $is_cache = true;

	/**
	 * Atts.
	 *
	 * @access private
	 * @since  1.0.0
	 * @var    array
	 */
	private $atts;

	/**
	 * Chart data.
	 *
	 * @access private
	 * @since  1.0.0
	 * @var    array
	 */
	private $chartdata;

	/**
	 * Construct.
	 *
	 * @access public
	 * @since  0.1.0
	 * @param  integer $from_unixtime 開始時間のUNIXTIME.
	 * @param  integer $to_unixtime   終了時間のUNIXTIME.
	 * @param  integer $peridos       データ間隔.
	 * @param  integer $assort        表示するグラフの種類.
	 * @param  boolean $is_cache      キャッシュを使用するか.
	 */
	public function __construct( $from_unixtime = null, $to_unixtime = null, $peridos = null, $assort = null, $market = null, $exchange = null, $is_cache = true ) {
		// 表示内容をreturnする.
		if ( defined( 'WP_DEBUG' ) ) {
			$this->is_cache = false;
		} else {
			$this->is_cache = $is_cache;
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
		if ( ! empty( $market ) ) {
			$this->market = $market;
		}
		if ( ! empty( $exchange ) ) {
			$this->exchange = $exchange;
		}
		if ( ! empty( $peridos ) ) {
			$this->peridos = $peridos;
		} else {
			$this->peridos = DAY_IN_SECONDS;
		}
	}

	/**
	 * Set atts.
	 *
	 * Setting atts. from, to, periods等のプロパティも設定します.
	 *
	 * @param  array $atts Input.
	 * @return void
	 */
	public function set_atts( $atts ) {
		$this->atts = $atts;

		if ( ! empty( $atts['name'] ) ) {
			$this->field_name = $atts['name'];
		}
		if ( ! empty( $atts['from'] ) and WBC_Common::is_date_format( $atts['from'] ) ) {
			$this->from_unixtime = strtotime( $atts['from'] );
		}
		if ( ! empty( $atts['to'] ) and WBC_Common::is_date_format( $atts['to'] ) ) {
			$this->to_unixtime = strtotime( $atts['to'] );
		}
		if ( ! empty( $atts['periods'] ) and in_array( $atts['periods'], array( 300, 1800, 3600, 86400 ) ) ) {
			$this->periods = $atts['periods'];
		}
		if ( ! empty( $atts['market'] ) ) {
			$this->market = $atts['market'];
		}
		if ( ! empty( $atts['exchange'] ) ) {
			$this->exchange = $atts['exchange'];
		}
	}

	/**
	 * Get from date
	 *
	 * @return date
	 */
	public function get_from() {
		return date( 'Y-m-d', $this->from_unixtime );
	}

	/**
	 * Get to date.
	 *
	 * @return date
	 */
	public function get_to() {
		return date( 'Y-m-d', $this->to_unixtime );
	}

	/**
	 * Make data file.
	 *
	 * @access public
	 * @since  1.0.0
	 * @param  string $filename   ファイル名.
	 * @param  string $write_data ファイルの内容.
	 * @return boolean
	 */
	public function make_data_file( $filename, $write_data ) {
		// 該当のファイルを作成/更新します.
		$result = file_put_contents( $filename, $write_data );
		chmod( $filename, 0755 );
		return $result;
	}

	/**
	 * Get now price.
	 *
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function get_now_price() {
		// 一番最新のデータの価格を取得します.
		$price    = 0;
		$filename = WBC_Common::get_cache_transaction_filename( $this->market, $this->exchange );
		if ( $this->is_cache and file_exists( $filename ) ) {
			$json = file_get_contents( $filename );
		} else {
			$url  = 'https://api.cryptowat.ch/markets/' . $this->market . '/' . $this->exchange . '/price';
			$json = WBC_Common::wbc_remote_get( $url );
			if ( $this->is_cache ) {
				$this->make_data_file( $filename, $json );
			}
		}
		$cw = json_decode( $json, true )
		if ( ! empty( $cw['result']['price'] ) ) {
			$price = $cw['result']['price'];
		}
		return $price;
	}

	/**
	 * Get market fluctuations.
	 *
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function get_market_fluctuations() {
		// 24時間の市場の相場変動を取得します.
		$price    = 0;
		$filename = WBC_Common::get_cache_market_filename( $this->market, $this->exchange );
		if ( $this->is_cache and file_exists( $filename ) ) {
			$json = file_get_contents( $filename );
		} else {
			$url  = 'https://api.cryptowat.ch/markets/' . $this->market . '/' . $this->exchange . '/summary';
			$json = WBC_Common::wbc_remote_get( $url );
			if ( $this->is_cache ) {
				$this->make_data_file( $filename, $json );
			}
		}
		$cw = json_decode( $json, true )
		if ( ! empty( $cw['result']['price'] ) ) {
			$price = $cw['result']['price'];
		}
		return $price;
	}

	/**
	 * Get only label data.
	 *
	 * @access public
	 * @since  0.1.0
	 * @return array
	 */
	public function get_label() {

		$filename = WBC_Common::get_cache_chart_filename( $this->periods, $this->from_unixtime, $this->to_unixtime, $this->market, $this->exchange );
		$all_data = array();

		if ( $this->is_cache and file_exists( $filename ) ) {
			$json = file_get_contents( $filename );
		} elseif ( $next ) {
			// 保存されているデータの更新.
			$url  = 'https://api.cryptowat.ch/markets/' . $this->market . '/' . $this->exchange . '/ohlc?periods=' . strval( $this->periods );
			$json = WBC_Common::wbc_remote_get( $url );
			if ( $this->is_cache ) {
				$this->make_data_file( $filename, $json );
			}
		}

		$all_data = json_decode( $json, true );
		$periods_keys = WBC_Common::array_column( $all_data['result'][ strval( $this->periods ) ], 0 );

		// 時刻を読めるように変換.
		foreach ( $periods_keys as $key => $data_unixtime ) {
			if ( DAY_IN_SECONDS == $this->periods ) {
				$periods_keys[ $key ] = date( 'n月j日', $data_unixtime );
			} else {
				$periods_keys[ $key ] = date( 'n月j日 H:i', $data_unixtime );
			}
		}

		return $periods_keys;
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

		$filename = WBC_Common::get_cache_chart_filename( $this->periods, $this->from_unixtime, $this->to_unixtime, $this->market, $this->exchange );
		$all_data = array();

		if ( $this->is_cache and file_exists( $filename ) ) {
			$json = file_get_contents( $filename );
		} elseif ( $next ) {
			// 保存されているデータの更新.
			$url  = 'https://api.cryptowat.ch/markets/' . $this->market . '/' . $this->exchange . '/ohlc?periods=' . strval( $this->periods );
			$json = WBC_Common::wbc_remote_get( $url );
			if ( $this->is_cache ) {
				$this->make_data_file( $filename, $json );
			}
		}

		$all_data = json_decode( $json, true );
		$result = WBC_Common::array_column( $all_data['result'][ strval( $this->periods ) ], $this->assort );

		return $result;
	}

	/**
	 * Make chart datasets and labels.
	 *
	 * @access public
	 * @since  0.1.0
	 * @param  array $atts User defined attributes in shortcode tag.
	 * @return string
	 */
	public function make_chart() {
		$labels   = $this->get_label();
		$datasets = array();

		// どのデータを表示するのかを識別して設定する.
		if ( ! empty( $this->atts['op'] ) ) {
			$datasets[] = array(
				'label'       => 'Open Price',
				'borderColor' => $this->atts['op_color'],
				'data'        => $this->get_graph_data( 1 ),
				'drawBorder'  => false,
			);
		}
		if ( ! empty( $this->atts['hp'] ) ) {
			$datasets[] = array(
				'label'       => 'High Price',
				'borderColor' => $this->atts['hp_color'],
				'data'        => $this->get_graph_data( 2 ),
				'drawBorder'  => false,
			);
		}
		if ( ! empty( $this->atts['lp'] ) ) {
			$datasets[] = array(
				'label'       => 'Low Price',
				'borderColor' => $this->atts['lp_color'],
				'data'        => $this->get_graph_data( 3 ),
				'drawBorder'  => false,
			);
		}
		if ( ! empty( $this->atts['cp'] ) ) {
			$datasets[] = array(
				'label'       => 'Close Price',
				'borderColor' => $this->atts['cp_color'],
				'data'        => $this->get_graph_data( 4 ),
				'drawBorder'  => false,
			);
		}
		if ( ! empty( $this->atts['vo'] ) ) {
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
	 * Get output string Transaction price.
	 *
	 * @access public
	 * @since  1.1.0
	 * @param  array $atts User defined attributes in shortcode tag.
	 * @return string
	 */
	public function output_transaction_price( $atts ) {
		$price = $this->get_now_price();
		return number_format( $price );
	}

	/**
	 * Get output string Market price.
	 *
	 * @access public
	 * @since  1.1.0
	 * @param  array $atts User defined attributes in shortcode tag.
	 * @return string
	 */
	public function output_market_price( $atts ) {
		$price = $this->get_market_fluctuations();
		return number_format( $price );
	}

	/**
	 * Get output string for chart.
	 *
	 * @access public
	 * @since  0.1.0
	 * @param  array $atts User defined attributes in shortcode tag.
	 * @return string
	 */
	public function output_chart( $atts ) {
		if ( ! empty( $atts ) ) {
			$this->set_atts( $atts );
		}

		$output_text = '';

		// 短いperiods用の日付.
		$from_date       = $this->get_from();
		$to_date         = $this->get_to();
		$from_date_short = date( 'Y-m-d', strtotime( $this->get_to() . ' -1 day' ) );
		$to_date         = $this->get_to();
		$to_date_short   = $this->get_to();

		$chart = json_encode( $this->make_chart() );

		// ツール部分のHTML.
		$tools_text = <<<EOT
<form action="#" class="wp-bitcoin-chart-form" field-name="{$this->field_name}">
	<input type="hidden" name="name" value="{$this->field_name}">
	<input type="hidden" name="op_color" value="{$this->atts['op_color']}">
	<input type="hidden" name="hp_color" value="{$this->atts['hp_color']}">
	<input type="hidden" name="lp_color" value="{$this->atts['lp_color']}">
	<input type="hidden" name="cp_color" value="{$this->atts['cp_color']}">
	<input type="hidden" name="vo_color" value="{$this->atts['vo_color']}">
	<input type="hidden" name="op" value="{$this->atts['op']}">
	<input type="hidden" name="hp" value="{$this->atts['hp']}">
	<input type="hidden" name="lp" value="{$this->atts['lp']}">
	<input type="hidden" name="cp" value="{$this->atts['cp']}">
	<input type="hidden" name="vo" value="{$this->atts['vo']}">
	<input type="hidden" name="tool_position" value="{$this->atts['tool_position']}">
	<div class="field has-addons">
		<p class="control">
			<span class="select">
				<select name="periods" id="{$this->field_name}-periods" class="param-field wbc-change-periods">
					<option value="300">5 min</option>
					<option value="1800">30 min</option>
					<option value="3600">1 hour</option>
					<option value="86400" selected="selected">1 day</option>
				</select>
			</span>
		</p>
		<p class="control">
			<input type="text" name="from" id='{$this->field_name}-from-input' class='input param-field' value='{$from_date}'>
			<input type="hidden" name="from_default" id='{$this->field_name}-from-default' value='${from_date}'>
			<input type="hidden" name="from_default_short" id='{$this->field_name}-from-default-short' value='${from_date_short}'>
		</p>
		<p class="control">
			<input type="text" name="to" id='{$this->field_name}-from-input' class='input param-field' value='${to_date}'>
			<input type="hidden" name="to_default" id='{$this->field_name}-to-default' value='${to_date}'>
			<input type="hidden" name="to_default_short" id='{$this->field_name}-to-default-short' value='${to_date_short}'>
		</p>
		<p class="control">
			<a class="button wp-bitcoin-chart-refresh-button" form-name="{$this->field_name}_button">
				<i class="fa fa-refresh"></i> 表示
			</a>
		</p>
	</div>
</form>
EOT;

		// スクリプト部分のHTML.
		$scripts_text = <<<EOT
<script>
	var ctx = document.getElementById('{$this->field_name}').getContext('2d');
	var chart = new Chart(ctx, ${chart});
</script>
EOT;

		// 画面に表示する内容を加工して整理する.
		$output_text           = "<div class='wp-bitcoin-chart-field'>";
		$atts['tool_position'] = strtolower( $atts['tool_position'] );
		if ( 'top' == $atts['tool_position'] or 'both' == $atts['tool_position'] ) {
			$output_text .= $tools_text;
		}
		$output_text .= "<canvas id='{$this->field_name}'></canvas>";
		if ( 'bottom' == $atts['tool_position'] or 'both' == $atts['tool_position'] ) {
			$output_text .= $tools_text;
		}
		$output_text .= '</div>';
		$output_text .= $scripts_text;

		return $output_text;
	}
}
