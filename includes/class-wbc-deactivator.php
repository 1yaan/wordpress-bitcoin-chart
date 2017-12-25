<?php
/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
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
 * WBC_Deactivator
 */
class WBC_Deactivator {

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since    0.1.0
	 */
	public static function deactivate() {
		require_once WBC__PLUGIN_DIR . 'public/class-wbc-public.php';
		$wbc = new WBC_Public();
		$wbc->wbc_delete_options();
	} // end deactivate
}
