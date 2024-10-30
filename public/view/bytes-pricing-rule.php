<?php
/**
 * Set Role & Customer based pricing
 */

use Bytes\RoleAndCustomerBasedPricing\Admin\Product\Bytes_Pricing_Rules;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Bytes_Pricing_Rule
{
	
	public function __construct(){
		add_filter( 'woocommerce_product_get_regular_price', array(
			$this,
			'adjustRegularPrice'
		), 99, 2 );

		add_filter( 'woocommerce_product_get_sale_price', array(
			$this,
			'adjustSalePrice'
		), 99, 2 );

		add_filter( 'woocommerce_product_get_price', array(
			$this,
			'adjustPrice'
		), 99, 2 );

		// Variations
		add_filter( 'woocommerce_product_variation_get_regular_price', array(
			$this,
			'adjustRegularPrice'
		), 99, 2 );

		add_filter( 'woocommerce_product_variation_get_sale_price', array(
			$this,
			'adjustSalePrice'
		), 99, 2 );

		add_filter( 'woocommerce_product_variation_get_price', array(
			$this,
			'adjustPrice'
		), 99, 2 );

		// Variable (price range)
		add_filter( 'woocommerce_variation_prices_price', array( $this, 'adjustPrice' ), 99, 3 );

		// Variation
		add_filter( 'woocommerce_variation_prices_regular_price', array(
			$this,
			'adjustRegularPrice'
		), 99, 3 );

	}

	public function adjustRegularPrice( $price, WC_Product $product ) {

		$pricingRule = Bytes_Pricing_Rules::get_pricing_rule( $product->get_id() );
		
		if ( $pricingRule ) {

			if ( isset($pricingRule['pricing_type']) && sanitize_text_field($pricingRule['pricing_type']) === 'flat' && isset($pricingRule['regular_price']) ) {
				$price = sanitize_text_field($pricingRule['regular_price']);
			} else if ( isset($pricingRule['pricing_type']) && sanitize_text_field($pricingRule['pricing_type']) === 'percentage' && isset($pricingRule['discount']) && !$product->get_sale_price()) {
				// Do no modify regular price if "sale_price" chosen
				$price = ( $price * ( ( 100 - $pricingRule['discount'] ) / 100 ) );
			}
		}
		
		return $price;
	}

	public function adjustSalePrice($price, WC_Product $product){

		$pricingRule = Bytes_Pricing_Rules::get_pricing_rule( $product->get_id() );
	if(!empty($price)){
		if ( $pricingRule ) {

			if ( isset($pricingRule['pricing_type']) && sanitize_text_field($pricingRule['pricing_type']) === 'flat' && isset($pricingRule['sale_price']) ) {
				$price = sanitize_text_field($pricingRule['sale_price']);
			} else if ( isset($pricingRule['pricing_type']) && $pricingRule['pricing_type'] === 'percentage' && isset($pricingRule['discount']) ) {
				
				$price = ( $price * ( ( 100 - $pricingRule['discount'] ) / 100 ) );
			}
		}
	}
		return $price;

	}

	public function adjustPrice($price, WC_Product $product){
	
		$pricingRule = Bytes_Pricing_Rules::get_pricing_rule( $product->get_id() );
		if ( $pricingRule ) {
			
			if ( isset($pricingRule['pricing_type']) && sanitize_text_field($pricingRule['pricing_type']) === 'flat' && isset($pricingRule['sale_price'])) {
				$price = $pricingRule['sale_price'];
			} else if( isset($pricingRule['pricing_type']) && sanitize_text_field($pricingRule['pricing_type']) === 'percentage' && isset($pricingRule['discount']) ){
				$price = ( $price * ( ( 100 - $pricingRule['discount'] ) / 100 ) );
			}else if ( isset($pricingRule['pricing_type']) && sanitize_text_field($pricingRule['pricing_type']) === 'flat' && isset($pricingRule['regular_price']) ) { 
				$price = $pricingRule['regular_price'];
			}else if( isset($pricingRule['pricing_type']) && sanitize_text_field($pricingRule['pricing_type']) === 'percentage' && isset($pricingRule['discount']) ){
				
				$price = ( $price * ( ( 100 - $pricingRule['discount'] ) / 100 ) );
			}
		}

		return $price;
	}

} new Bytes_Pricing_Rule;
