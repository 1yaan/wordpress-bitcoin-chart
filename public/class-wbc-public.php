<?php
/**
 * WordPress Bitcoin Chart Plugin Public functions.
 *
 * @since      0.1.0
 * @version    0.1
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
	 * @since  0.1.0
	 * @access public
	 */
	public function __construct() {
		$this->define_public_hooks();
	}

	/**
	 * Define public hooks.
	 *
	 * @since  0.1.0
	 * @access private
	 */
	private function define_public_hooks() {
		// Add Action.
		add_action( 'wp_enqueue_scripts', array( $this, 'register_jquery' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'register_jquery' ) );
		add_action( 'wp_ajax_wp_bitcoin_chart', array( $this, 'wp_bitcoin_chart_get_json_data' ) );
		add_action( 'wp_ajax_nopriv_wp_bitcoin_chart', array( $this, 'wp_bitcoin_chart_get_json_data' ) );

		// Exp: [wp_bitcoin_chart_view].
		// ショートコードで画面にグラフを表示する.
		add_shortcode( 'wp_bitcoin_chart_view', array( $this, 'wp_bitcoin_chart_view_shortcode' ) );
	}

	/**
	 * Register jquery.
	 *
	 * @return void
	 * @access public
	 */
	public function register_jquery() {

		$wp_bitcoin_chart_css = get_option( 'wp_bitcoin_chart_css' );

		if ( empty( $wp_bitcoin_chart_css ) ) {
			// 独自のCSSを使用しない場合は、bulma.ioのCSSを使う.
			wp_register_style( 'wbc_style', plugins_url( 'public/css/wbc_style.css', __FILE__ ) );
			wp_enqueue_style( 'wbc_style' );
			wp_register_style( 'fontawesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css' );
			wp_enqueue_style( 'fontawesome' );
		}

		wp_register_script( 'momentjs', 'https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.19.3/moment.min.js', array(), '0.1.0' );
		wp_enqueue_script( 'momentjs' );

		wp_register_script( 'chartjs', 'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js', array(), '0.1.0' );
		wp_enqueue_script( 'chartjs' );

		wp_register_script( 'wpbitcoinchartjs', plugins_url( 'public/js/wp-bitcoin-chart.js', __FILE__ ), array( 'jquery' ), '0.1.0' );
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
	 * @return void wp_send_json
	 * @access public
	 */
	public function wp_bitcoin_chart_get_json_data() {
		// ブログの外部からのリクエストを間違って処理しないように AJAX リクエストを検証します.
		check_ajax_referer( 'wp-bitcoin-chart-nonce', '_security' );

		$atts = $_POST;

		if ( empty( $atts['periods'] ) or ! in_array( $atts['periods'], array( 300, 1800, 3600, 86400 ) ) ) {
			unset( $atts['periods'] );
		}
		if ( empty( $atts['from'] ) or ! $this->is_date_format( $atts['from'] ) ) {
			unset( $atts['from'] );
		}
		if ( empty( $atts['to'] ) or ! $this->is_date_format( $atts['to'] ) ) {
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
	 * WP Bitcoin Chart restart.
	 *
	 * @return void
	 */
	public function wp_bitcoin_chart_restart() {
		// Remove /data dir.
		if ( $this->remove_dir( WBC__PLUGIN_DATA_DIR ) ) {
			mkdir( WBC__PLUGIN_DATA_DIR, 0755 );
		}

		// Insert options.
		$this->wbc_init_options();
	}

	/**
	 * WP Bitcoin Chart initialize options.
	 *
	 * @return void
	 */
	public function wbc_init_options() {
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
	public function wbc_init_option( $periods = WBC__DEFAULT_CHART_PERIODS, $start_unixtime = WBC__DEFAULT_CHART_START ) {
		update_option( 'wp_bitcoin_chart__periods_' . strval( $periods ), WBC__DEFAULT_CHART_START );
	}

	/**
	 * WP Bitcoin Chart delete options.
	 *
	 * @return void
	 */
	public function wbc_delete_options() {
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
	public function get_cache_json_filename( $periods ) {
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
	public function get_cache_htm_filename( $name, $periods, $from_date = null, $to_date = null ) {
		return WBC__PLUGIN_DATA_DIR . 'output_' . $name . '_' . strval( $periods ) . 'from' . $from_date . 'to' . $to_date . '.htm';
	}

	/**
	 * Remove dir.
	 *
	 * @param  string $dir_path target dir path.
	 * @return boolean
	 */
	public function remove_dir( $dir_path ) {
		$filepath = $dir_path . '*';
		foreach ( glob( $filepath ) as $file ) {
			unlink( $file );
		}
		return rmdir( $dir_path );
	}
}
