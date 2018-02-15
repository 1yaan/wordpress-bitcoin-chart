<?php
/**
 * Common class.
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

	/**
	 * Make data file.
	 *
	 * @access public
	 * @since  1.0.0
	 * @param  string $filename   ファイル名.
	 * @param  string $write_data ファイルの内容.
	 * @return boolean
	 */
	public static function make_data_file( $filename, $write_data ) {
		// 該当のファイルを作成/更新します.
		$result = file_put_contents( $filename, $write_data );
		chmod( $filename, 0755 );
		return $result;
	}

	/**
	 * WP Bitcoin Chart initialize options.
	 *
	 * @access public
	 * @since  0.1.0
	 * @return void
	 */
	public static function wbc_init_options() {
		// 登録データを全部削除してしまおう.
		self::wbc_delete_options();
	}

	/**
	 * WP Bitcoin Chart delete options.
	 *
	 * @access public
	 * @since  0.1.0
	 * @return void
	 */
	public static function wbc_delete_options() {
		delete_option( WBC__OPTION_NAME_CHART_CSS );
	}

	/**
	 * Get cache transaction filename.
	 *
	 * @access public
	 * @since  1.1.0
	 * @param  string $market     市場.
	 * @param  string $exchange   為替.
	 * @param  string $this_month 対象の月 date('Ym')形式 ex) 201801.
	 * @param  string $now_time   今の日時 date('YmdH')形式 ex) 2018010100.
	 * @return string
	 */
	public static function get_cache_transaction_filename( $market = 'bitflyer', $exchange = 'btcjpy', $this_month = null, $now_time = null ) {
		if ( empty( $this_month ) ) {
			$this_month = date( 'Ym' );
		}
		if ( empty( $now_time ) ) {
			$now_time = date( 'YmdH' );
		}

		// ex) your_dir/wp-bitcoin-chart/data/201801/transaction/cw_bitflyer_btcjpy_2018010100.json .
		return WBC__PLUGIN_DATA_DIR . $this_month . DIRECTORY_SEPARATOR . 'transaction' . DIRECTORY_SEPARATOR . 'cw_' . $market . '_' . $exchange . '_' . $now_time . '.json';
	}

	/**
	 * Get cache market filename.
	 *
	 * @access public
	 * @since  1.1.0
	 * @param  string $market     市場.
	 * @param  string $exchange   為替.
	 * @param  string $this_month 対象の月 date('Ym')形式 ex) 201801.
	 * @param  string $now_time   今の日時 date('YmdH')形式 ex) 2018010100.
	 * @return string
	 */
	public static function get_cache_market_filename( $market = 'bitflyer', $exchange = 'btcjpy', $this_month = null, $now_time = null ) {
		if ( empty( $this_month ) ) {
			$this_month = date( 'Ym' );
		}
		if ( empty( $now_time ) ) {
			$now_time = date( 'YmdH' );
		}

		// ex) your_dir/wp-bitcoin-chart/data/201801/market/cw_bitflyer_btcjpy_2018010100.json .
		return WBC__PLUGIN_DATA_DIR . $this_month . DIRECTORY_SEPARATOR . 'market' . DIRECTORY_SEPARATOR . 'cw_' . $market . '_' . $exchange . '_' . $now_time . '.json';
	}

	/**
	 * Get cache chart filename.
	 *
	 * @access public
	 * @since  0.1.0
	 * @param  integer $periods       取得するデータの時間間隔. 300, 1800, 3600, 86400のみを認めます. 初期値は86400.
	 * @param  integer $from_unixtime データ開始時間.
	 * @param  integer $to_unixtime   データ終了時間.
	 * @param  string  $market        市場.
	 * @param  string  $exchange      為替.
	 * @param  string  $this_month    対象の月 date('Ym')形式 ex) 201801.
	 * @return string
	 */
	public static function get_cache_chart_filename( $periods, $from_unixtime, $to_unixtime, $market = 'bitflyer', $exchange = 'btcjpy', $this_month = null ) {
		if ( empty( $this_month ) ) {
			$this_month = date( 'Ym' );
		}

		// ex) your_dir/wp-bitcoin-chart/data/201801/chart/cw_bitflyer_btcjpy_300_2018010100.json .
		return WBC__PLUGIN_DATA_DIR . $this_month . DIRECTORY_SEPARATOR . 'chart' . DIRECTORY_SEPARATOR . 'cw_' . $market . '_' . $exchange . '_' . strval( $periods ) . '_from' . strval( $from_unixtime ) . '_to' . strval( $to_unixtime ) . '.json';
	}

	/**
	 * Remote get.
	 *
	 * @access public
	 * @since  2.1.0
	 * @param  string $url Remote get url.
	 * @return string
	 */
	public static function wbc_remote_get( $url = '' ) {
		if ( empty( $url ) ) {
			return '';
		}

		// 通信設定.
		$args = array(
			'blocking'    => true,
			'sslverify'   => false,
			'httpversion' => '1.0',
			'headers'     => array(
				'Content-Type' => 'application/json',
			),
		);

		// 通信する.
		$response = wp_remote_get( $url, $args );

		if ( is_wp_error( $response ) ) {
			$error_message = $response->get_error_message();
			echo 'Something went wrong: $error_message';
		}

		return mb_convert_encoding( $response['body'], 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN' );
	}

}
