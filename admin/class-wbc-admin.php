<?php
/**
 * WP Bitcoin Chart Admin.
 *
 * @since      0.1.0
 * @version    1.1.0
 * @package    wp-bitcoin-chart
 * @subpackage wp-bitcoin-chart/includes
 * @author     1yaan, {@link https://github.com/1yaan https://github.com/1yaan}
 * @copyright  1yaan, {@link https://github.com/1yaan https://github.com/1yaan}
 * @license    GPLv2 or later, {@link https://www.gnu.org/licenses/gpl.html https://www.gnu.org/licenses/gpl.html}
 */

/**
 * WBC_Admin
 */
class WBC_Admin {

	/**
	 * Construct.
	 *
	 * @since  0.1.0
	 * @access public
	 */
	public function __construct() {
		$this->define_admin_hooks();
	}

	/**
	 * Initializes WordPress hooks
	 *
	 * @access private
	 * @since  0.1.0
	 * @return void
	 */
	private function define_admin_hooks() {
		add_filter( 'plugin_action_links_' . WBC__PLUGIN_BASENAME, array( $this, 'add_plugin_settings_link' ) );
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );
	}

	/**
	 * Add action links.
	 *
	 * @access public
	 * @since  0.1.0
	 * @param  array $links Plugin index links.
	 * @return array
	 */
	public function add_plugin_settings_link( $links ) {
		$links[] = '<a href="' . admin_url( 'admin.php?page=' . WBC__PLUGIN_NAME ) . '">' . __( 'Settings' ) . '</a>';
		return $links;
	}

	/**
	 * Add plugin admin menu pages.
	 *
	 * @access public
	 * @since  0.1
	 * @return void
	 */
	public function add_plugin_admin_menu() {
		add_menu_page( 'WP Bitcoin Chart', 'WP Bitcoin Chart', 'manage_options', WBC__PLUGIN_NAME, array( $this, 'display_plugin_admin_page' ), WBC__PLUGIN_DIR_URL . 'public/img/bitcoin.png' );
	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @access public
	 * @since  0.1
	 * @return void
	 */
	public function display_plugin_admin_page() {
		include_once( WBC__PLUGIN_DIR . 'admin/includes/settings.php' );
	}

	/**
	 * Run.
	 *
	 * @access public
	 * @since  1.0.0
	 */
	public function run() {
	}
}
