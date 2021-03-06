<?php
/**
 * WordPress Bitcoin Chart Plugin Public functions.
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
 * WBC_Public
 */
class WBC_Public {

	/**
	 * Construct.
	 *
	 * @access public
	 * @since  0.1.0
	 */
	public function __construct() {
		require_once WBC__PLUGIN_DIR . 'includes/class-wbc-common.php';
		$this->define_public_hooks();
	}

	/**
	 * Define public hooks.
	 *
	 * @access private
	 * @since  0.1.0
	 */
	private function define_public_hooks() {
		// Add Action.
		add_action( 'wp_enqueue_scripts', array( $this, 'register_jquery' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'register_jquery' ) );
		add_action( 'wp_ajax_wp_bitcoin_chart', array( $this, 'wp_bitcoin_chart_get_json_data' ) );
		add_action( 'wp_ajax_nopriv_wp_bitcoin_chart', array( $this, 'wp_bitcoin_chart_get_json_data' ) );

		// Exp: [wp_bitcoin_chart_view].
		// ショートコードで画面にグラフを表示する.
		add_shortcode( 'wp_bitcoin_chart_view', array( $this, 'wp_bitcoin_chart_view' ) );
		// ショートコードで相場変動を表示する.
		add_shortcode( 'wp_bitcoin_chart_market_price', array( $this, 'wp_bitcoin_chart_market_price' ) );
		// ショートコードで画面に最終取引価格を表示する.
		add_shortcode( 'wp_bitcoin_chart_transaction_price', array( $this, 'wp_bitcoin_chart_transaction_price' ) );
	}

	/**
	 * Register jquery.
	 *
	 * @access public
	 * @since  0.1.0
	 * @return void
	 */
	public function register_jquery() {

		$wp_bitcoin_chart_css = get_option( WBC__OPTION_NAME_CHART_CSS );

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
			'loader_url' => plugins_url( 'public/img/loader_blue_32.gif', __FILE__ ),
		);

		wp_localize_script( 'wpbitcoinchartjs', 'wp_bitcoin_chart_ajax', $params );
	}

	/**
	 * WP Bitcoin Chart ajax get data action.
	 * Only Ajax.
	 *
	 * @access public
	 * @since  0.1.0
	 * @return void wp_send_json
	 */
	public function wp_bitcoin_chart_get_json_data() {
		// ブログの外部からのリクエストを間違って処理しないように AJAX リクエストを検証します.
		check_ajax_referer( 'wp-bitcoin-chart-nonce', '_security' );

		$atts = wp_parse_args(
			$_POST,
			array(
				'name'          => WBC__DEFAULT_CHART_NAME,
				'periods'       => DAY_IN_SECONDS,
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
				'market'        => 'bitflyer',
				'exchange'      => 'btcjpy',
			)
		);

		$wbc_data = new WBC_Data( $atts );
		$result   = array(
			'chart' => $wbc_data->make_chart(),
			'atts'  => $atts,
		);

		wp_send_json( $result );
	}

	/**
	 * WP Bitcoin Chart Market Transaction shortcode
	 *
	 * @access public
	 * @since  1.1.0
	 * @param  array $atts User defined attributes in shortcode tag.
	 * @return string
	 */
	public function wp_bitcoin_chart_transaction_price( $atts ) {
		$atts = shortcode_atts(
			array(
				'name'     => WBC__DEFAULT_CHART_NAME,
				'market'   => 'bitflyer',
				'exchange' => 'btcjpy',
			),
			$atts,
			'wp-bitcoin-chart-transaction-price'
		);

		$wbc_data = new WBC_Data( $atts );
		return $wbc_data->output_transaction_price();
	}

	/**
	 * WP Bitcoin Chart Market Price shortcode
	 *
	 * @access public
	 * @since  1.1.0
	 * @param  array $atts User defined attributes in shortcode tag.
	 * @return string
	 */
	public function wp_bitcoin_chart_market_price( $atts ) {
		$atts = shortcode_atts(
			array(
				'name'     => WBC__DEFAULT_CHART_NAME,
				'market'   => 'bitflyer',
				'exchange' => 'btcjpy',
			),
			$atts,
			'wp-bitcoin-chart-market-price'
		);

		$wbc_data = new WBC_Data( $atts );
		return $wbc_data->output_market_price();
	}

	/**
	 * WP Bitcoin Chart view shortcode
	 *
	 * @access public
	 * @since  0.1.0
	 * @param  array $atts User defined attributes in shortcode tag.
	 * @return string
	 */
	public function wp_bitcoin_chart_view( $atts ) {
		// ショートコードの変数 foo と bar を使用することを宣言し、さらに初期値を設定する.
		$atts = shortcode_atts(
			array(
				'name'          => WBC__DEFAULT_CHART_NAME,
				'periods'       => DAY_IN_SECONDS,
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
				'market'        => 'bitflyer',
				'exchange'      => 'btcjpy',
			),
			$atts,
			'wp-bitcoin-chart-view'
		);

		$wbc_data = new WBC_Data( $atts );
		return $wbc_data->output_chart();
	}
}
