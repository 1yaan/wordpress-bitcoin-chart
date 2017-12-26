<?php
/**
 * Common class.
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
 * WBC_Common
 */
class WBC_Common {

	/**
	 * Is Date Format.
	 *
	 * @access public
	 * @since  0.1.0
	 * @param  date $date Input datetime.
	 * @return boolean
	 */
	public static function is_date_format( $date ) {
		return date( 'Y-m-d', strtotime( $date ) ) === $date;
	}

	/**
	 *  Is Datetime Format.
	 *
	 * @access public
	 * @since  0.1.0
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
	 * @access public
	 * @since  0.1.0
	 * @param  array $target_data 値を取り出したい多次元配列.
	 * @param  mixed $column_key  値を返したいカラム.
	 * @param  mixed $index_key   返す配列のインデックスとして使うカラム.
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
	 * WP Bitcoin Chart restart.
	 *
	 * @access public
	 * @since  0.1.0
	 * @return void
	 */
	public static function wp_bitcoin_chart_restart() {
		// Insert options.
		self::wbc_init_options();

		// Remove /data dir.
		if ( self::remove_dir( WBC__PLUGIN_DATA_DIR ) ) {
			mkdir( WBC__PLUGIN_DATA_DIR, 0755 );
		}
	}

	/**
	 * WP Bitcoin Chart initialize options.
	 *
	 * @access public
	 * @since  0.1.0
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
	 * @access public
	 * @since  0.1.0
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
	 * @access public
	 * @since  0.1.0
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
	 * @access public
	 * @since  0.1.0
	 * @param  integer $periods 取得するデータの時間間隔. 300, 1800, 3600, 86400のみを認めます. 初期値は86400.
	 * @return string
	 */
	public static function get_cache_json_filename( $periods ) {
		return WBC__PLUGIN_DATA_DIR . 'cw_' . strval( $periods ) . '.json';
	}

	/**
	 * Get cache htm filename.
	 *
	 * @access public
	 * @since  0.1.0
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
	 * Remove dir.
	 *
	 * @access public
	 * @since  0.1.0
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
