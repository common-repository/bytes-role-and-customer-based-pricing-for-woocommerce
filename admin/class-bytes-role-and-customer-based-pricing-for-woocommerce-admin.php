<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.bytestechnolab.com
 * @since      1.0.0
 *
 * @package    Bytes_Role_And_Customer_Based_Pricing_For_Woocommerce
 * @subpackage Bytes_Role_And_Customer_Based_Pricing_For_Woocommerce/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Bytes_Role_And_Customer_Based_Pricing_For_Woocommerce
 * @subpackage Bytes_Role_And_Customer_Based_Pricing_For_Woocommerce/admin
 * @author     Bytes WP Team <info@bytestechnolab.com>
 */
class Bytes_Role_And_Customer_Based_Pricing_For_Woocommerce_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$pluginStatus = add_action( 'admin_init', array( $this, 'check_woocommerce_plugin_active' ));
		if($pluginStatus){
			add_action( 'admin_notices', array( $this, 'woocommerce_not_active_notice' ));
		}

	}

	/**
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/bytes-role-and-customer-based-pricing-for-woocommerce-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/bytes-role-and-customer-based-pricing-for-woocommerce-admin.js', array( 'jquery' ), $this->version, false );
		wp_localize_script( $this->plugin_name, 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) , 'brcbp_ajax_nonce' => wp_create_nonce( 'brcbp_ajax_nonce' ) ) );
	}

	public function check_woocommerce_plugin_active(){
		$plugin_path = 'woocommerce/woocommerce.php';
		if (is_plugin_inactive($plugin_path)) {
			// Deactivate the plugin
			deactivate_plugins('bytes-role-and-customer-based-pricing-for-woocommerce/bytes-role-and-customer-based-pricing-for-woocommerce.php');
			unset( $_GET['activate'] );
			return true;
		}else{
			remove_all_actions( 'admin_notices' );
			return false;
		}
	}

	/**
	 * Display an admin notice & Deactivate plugin if WooCommerce is not active.
	 */
	public function woocommerce_not_active_notice() {

	  	echo "<div class='notice notice-error is-dismissible'> 
			    <p>WooCommerce is required for Bytes Role and Customer Based Pricing for WooCommerce plugin to work. Please install and activate WooCommerce.</p>
			</div>";
	}

	/**
	 * Add Product Tab
	 */
	public function bytes_product_tab_include() {
        include plugin_dir_path( dirname( __FILE__ ))  . 'admin/product/bytes-product-tab-for-role-customer-based-price.php';
        include plugin_dir_path( dirname( __FILE__ ))  . 'admin/product/bytes-variable-product-for-role-customer-based-price.php';
        
    }    

}
