<?php
/**
 * WP Bitcoin Chart
 *
 * @package   wp-bitcoin-chart
 * @author    1yaan, {@link https://github.com/1yaan https://github.com/1yaan}
 * @version   1.1.0
 * @copyright 1yaan, {@link https://github.com/1yaan https://github.com/1yaan}
 * @license   GPLv2 or later, {@link https://www.gnu.org/licenses/gpl.html https://www.gnu.org/licenses/gpl.html}
 */

/**
 * WP_Bitcoin_Chart
 */
class WP_Bitcoin_Chart {
	/**
	 * Construct.
	 *
	 * @access public
	 * @since  0.1.0
	 */
	public function __construct() {
		$this->load_dependencies();
	}

	/**
	 * Loads the required dependencies for this plugin.
	 *
	 * @access private
	 * @since  0.1.0
	 */
	private function load_dependencies() {
		// Includes dir.
		require_once WBC__PLUGIN_DIR . 'includes/class-wbc-common.php';
		require_once WBC__PLUGIN_DIR . 'includes/class-wbc-data.php';

		// Public dir.
		require_once WBC__PLUGIN_DIR . 'public/class-wbc-public.php';
	}

	/**
	 * Run.
	 *
	 * @access public
	 * @since  1.0.0
	 */
	public function run() {
		$wbc_public = new WBC_Public();
	}
}
