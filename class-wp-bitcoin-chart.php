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
class WP_Bitcoin_Chart {

  private static $initiated = false;

  /**
	 * init
	 */
	public static function init() {
		if ( !self::$initiated ) {
			self::init_hooks();
		}
	}

	/**
	 * Initializes WordPress hooks
	 */
	private static function init_hooks() {
		self::$initiated = true;
	}

  /**
   * wp_bitcoin_chart_activation
   *
   * @return void
   */
	public static function wp_bitcoin_chart_activation() {}

  /**
   * wp_bitcoin_chart_deactivation
   *
   * @return void
   */
	public static function wp_bitcoin_chart_deactivation() {}

  /**
   * wp_bitcoin_chart_uninstall
   *
   * @return void
   */
	public static function wp_bitcoin_chart_uninstall() {
    // Delete the set options
    // delete_option('');
  }
}
