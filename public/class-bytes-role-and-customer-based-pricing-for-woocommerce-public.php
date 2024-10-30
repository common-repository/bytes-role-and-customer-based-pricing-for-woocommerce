<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.bytestechnolab.com
 * @since      1.0.0
 *
 * @package    Bytes_Role_And_Customer_Based_Pricing_For_Woocommerce
 * @subpackage Bytes_Role_And_Customer_Based_Pricing_For_Woocommerce/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Bytes_Role_And_Customer_Based_Pricing_For_Woocommerce
 * @subpackage Bytes_Role_And_Customer_Based_Pricing_For_Woocommerce/public
 * @author     Bytes WP Team <info@bytestechnolab.com>
 */
class Bytes_Role_And_Customer_Based_Pricing_For_Woocommerce_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Bytes_Role_And_Customer_Based_Pricing_For_Woocommerce_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Bytes_Role_And_Customer_Based_Pricing_For_Woocommerce_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/bytes-role-and-customer-based-pricing-for-woocommerce-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Bytes_Role_And_Customer_Based_Pricing_For_Woocommerce_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Bytes_Role_And_Customer_Based_Pricing_For_Woocommerce_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/bytes-role-and-customer-based-pricing-for-woocommerce-public.js', array( 'jquery' ), $this->version, false );

	}


	public function frontend_load_dependencies(){
		require_once plugin_dir_path(__FILE__) . 'view/bytes-pricing-rule.php';
		require_once plugin_dir_path(__FILE__) . 'view/bytes-quantity-rule.php';
		require_once plugin_dir_path(dirname(__FILE__))  . 'admin/product/bytes-rules.php';
		require_once plugin_dir_path(dirname( __FILE__ ))  . 'admin/wpbyt-user-roles/class-wpbyt-user-roles.php';
	}
	
}
