<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.bytestechnolab.com
 * @since             1.0.0
 * @package           Bytes_Role_And_Customer_Based_Pricing_For_Woocommerce
 *
 * @wordpress-plugin
 * Plugin Name:       Role and Customer Based Pricing for WooCommerce
 * Plugin URI:        https://www.bytestechnolab.com
 * Description:       Use this plugin to create pricing rules based on user roles or individual pricing for various customers.
 * Version:           1.0.0
 * Author:            Bytes Technolab
 * Author URI:        https://www.bytestechnolab.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       bytes-role-and-customer-based-pricing-for-woocommerce
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'BYTES_ROLE_AND_CUSTOMER_BASED_PRICING_FOR_WOOCOMMERCE_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-bytes-role-and-customer-based-pricing-for-woocommerce-activator.php
 */
if( !function_exists( 'bytes_role_and_customer_based_pricing_for_woocommerce_activate' ) ){
	function bytes_role_and_customer_based_pricing_for_woocommerce_activate() {
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-bytes-role-and-customer-based-pricing-for-woocommerce-activator.php';
		Bytes_Role_And_Customer_Based_Pricing_For_Woocommerce_Activator::activate();
	}
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-bytes-role-and-customer-based-pricing-for-woocommerce-deactivator.php
 */
if( !function_exists( 'bytes_role_and_customer_based_pricing_for_woocommerce_deactivate' ) ){
	function bytes_role_and_customer_based_pricing_for_woocommerce_deactivate() {
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-bytes-role-and-customer-based-pricing-for-woocommerce-deactivator.php';
		Bytes_Role_And_Customer_Based_Pricing_For_Woocommerce_Deactivator::deactivate();
	}
}

register_activation_hook( __FILE__, 'bytes_role_and_customer_based_pricing_for_woocommerce_activate' );
register_deactivation_hook( __FILE__, 'bytes_role_and_customer_based_pricing_for_woocommerce_deactivate' );

/**
 * Sanitize Array
*/
if( ! function_exists( 'bytes_brcbp_sanitize_array' ) ) {
	function bytes_brcbp_sanitize_array( &$array ) {

		if( ! is_array( $array ) ) return $array;

		foreach ( $array as &$value ) {	
			if( !is_array( $value ) ){
				$value = sanitize_text_field( $value );
			} else {
				bytes_brcbp_sanitize_array( $value );
			}
		}
		return $array;
	}
}

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-bytes-role-and-customer-based-pricing-for-woocommerce.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
if( !function_exists( 'bytes_role_and_customer_based_pricing_for_woocommerce_run' ) ){
	function bytes_role_and_customer_based_pricing_for_woocommerce_run() {
		$plugin = new Bytes_Role_And_Customer_Based_Pricing_For_Woocommerce();
		$plugin->run();

	}
}
bytes_role_and_customer_based_pricing_for_woocommerce_run();