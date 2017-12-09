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
	 * initiated 初期設定initが1回だけ実行されることをチェックします。
	 *
	 * @var boolean
	 */
	private static $initiated = false;

	/**
	 * initialize
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
	public static function wp_bitcoin_chart_activation() {}

	/**
	 * WP Bitcoin Chart deactivation
	 *
	 * @return void
	 */
	public static function wp_bitcoin_chart_deactivation() {}

	/**
	 * WP Bitcoin Chart Plugin uninstall
	 *
	 * @return void
	 */
	public static function wp_bitcoin_chart_uninstall() {
	}
}
