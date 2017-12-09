<?php
/**
 * WP Bitcoin Chart Admin
 *
 * @package   wp-bitcoin-chart
 * @author    1yaan, {@link https://github.com/1yaan https://github.com/1yaan}
 * @version   0.1
 * @copyright 1yaan, {@link https://github.com/1yaan https://github.com/1yaan}
 * @license   GPLv2 or later, {@link https://www.gnu.org/licenses/gpl.html https://www.gnu.org/licenses/gpl.html}
 */

/**
 * WP_Bitcoin_Chart_Admin
 */
class WP_Bitcoin_Chart_Admin {

	/**
	 * initiated
	 *
	 * @var boolean
	 */
	private static $initiated = false;

	/**
	 * init
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
}
