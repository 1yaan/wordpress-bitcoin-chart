<?php
/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
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
 * WBC_Activator
 */
class WBC_Activator {

	/**
	 * Fired when the plugin is activated.
	 *
	 * @since    0.1.0
	 */
	public static function activate() {
		require_once WBC__PLUGIN_DIR . 'public/class-wbc-public.php';
		$wbc = new WBC_Public();
		$wbc->wp_bitcoin_chart_restart();
	} // end activate
}
