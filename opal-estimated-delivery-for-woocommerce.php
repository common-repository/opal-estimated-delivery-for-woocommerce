<?php
/**
 * Opal Estimated Delivery for Woocommerce
 *
 * @package       opal-estimated-delivery-for-woocommerce
 * @author        WPOPAL
 * @version       1.0.4
 *
 * @wordpress-plugin
 * Plugin Name:   Opal Estimated Delivery for Woocommerce
 * Plugin URI:    https://wpopal.com/opal-estimated-delivery-for-woocommerce
 * Description:   Our plugin ensures that your customers receive accurate delivery estimates every time.
 * Version:       1.0.4
 * Author:        WPOPAL
 * Author URI:    https://wpopal.com
 * License:       GPLv2 or later
 * License URI:   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * Text Domain:   opal-estimated-delivery-for-woocommerce
 * Domain Path:   /languages
 * Requires Plugins: woocommerce
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

// Plugin name
define( 'OEDW_NAME', 'Opal Estimated Delivery for Woocommerce' );
define( 'OEDW_TEXTDOMAIN', 'opal-estimated-delivery-for-woocommerce' );

// Plugin version
define( 'OEDW_VERSION', '1.0.4' );

// Plugin Root File
define( 'OEDW_PLUGIN_FILE', __FILE__ );

// Plugin base
define( 'OEDW_PLUGIN_BASE', plugin_basename( OEDW_PLUGIN_FILE ) );

// Plugin Folder Path
define( 'OEDW_PLUGIN_DIR',	plugin_dir_path( OEDW_PLUGIN_FILE ) );

// Plugin Folder URL
define( 'OEDW_PLUGIN_URL',	plugin_dir_url( OEDW_PLUGIN_FILE ) );

define(	'OEDW_UPLOAD_DIR', 'oedw_uploads' );
define(	'OEDW_CRON_HOOK', 'oedw_daily_event' );
define(	'OEDW_SETTINGS_KEY', 'oedw_settings_key' );

/**
 * Load the main class for the core functionality
 */
require_once OEDW_PLUGIN_DIR . 'includes/class-opal-estimated-delivery-for-woocommerce.php';

/**
 * The main function to load the only instance
 * of our master class.
 *
 * @author  WPOPAL
 * @since   1.0.4
 * @return  object|OEDW_Start_Instance
 */
function oedw() {
	return OEDW_Start_Instance::instance();
}
oedw();
