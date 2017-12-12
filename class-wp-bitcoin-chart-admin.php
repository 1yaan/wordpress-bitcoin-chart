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
	 * Initial setting Flag to check that init is executed only once.
	 *
	 * @access private
	 * @var boolean
	 */
	private static $initiated = false;

	/**
	 * First executed
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
		add_action( 'admin_menu', array( 'WP_Bitcoin_Chart_Admin', 'add_plugin_admin_menu' ) );
		add_filter( 'plugin_action_links_' . WP_BITCOIN_CHART__PLUGIN_BASENAME, array( 'WP_Bitcoin_Chart_Admin', 'add_plugin_settings_link' ), 'add_plugin_settings_link', 10, 4 );
	}

	/**
	 * Add plugin admin menu pages.
	 *
	 * @return void
	 */
	public static function add_plugin_admin_menu() {
		add_menu_page( 'WP Bitcoin Chart', 'WP Bitcoin Chart', 'manage_options', WP_BITCOIN_CHART__PLUGIN_NAME, array( 'WP_Bitcoin_Chart_Admin', 'display_plugin_admin_page' ), WP_BITCOIN_CHART__PLUGIN_DIR_URL . 'img/bitcoin.png', 15.234 );
	}

	/**
	 * Add action links.
	 *
	 * @param  array $links Plugin index links.
	 * @return array
	 */
	function add_plugin_settings_link( $links ) {
		$links[] = '<a href="' . admin_url( 'options-general.php?page=' . WP_BITCOIN_CHART__PLUGIN_NAME ) . '">' . __( 'Settings' ) . '</a>';
		return $links;
	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since 0.1
	 */
	function display_plugin_admin_page() {
		include_once( WP_BITCOIN_CHART__PLUGIN_DIR . 'views/settings.php' );
	}
}
