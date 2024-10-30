<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://www.bytestechnolab.com
 * @since      1.0.0
 *
 * @package    Bytes_Role_And_Customer_Based_Pricing_For_Woocommerce
 * @subpackage Bytes_Role_And_Customer_Based_Pricing_For_Woocommerce/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Bytes_Role_And_Customer_Based_Pricing_For_Woocommerce
 * @subpackage Bytes_Role_And_Customer_Based_Pricing_For_Woocommerce/includes
 * @author     Bytes WP Team <info@bytestechnolab.com>
 */
class Bytes_Role_And_Customer_Based_Pricing_For_Woocommerce_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'bytes-role-and-customer-based-pricing-for-woocommerce',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
