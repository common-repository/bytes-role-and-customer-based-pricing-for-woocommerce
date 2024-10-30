<?php
 /**
 * Set quninty and Step
 **/

use Bytes\RoleAndCustomerBasedPricing\Admin\Product\Bytes_Pricing_Rules;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Bytes_Quantity_Rule{
	public function __construct() {
		add_filter( 'woocommerce_quantity_input_args', array($this,'bytes_quninty_input_args_update'),999999, 2);
		add_filter( 'woocommerce_available_variation', array($this, 'bytes_available_variation'), 99, 3 );
		add_filter( 'woocommerce_add_to_cart_validation',array($this, 'bytes_add_to_cart_validation'),99,4);
		add_filter( 'woocommerce_update_cart_validation', array($this, 'bytes_update_cart_validation'),99,4);
	}

	public function bytes_quninty_input_args_update($args,$product){
		 if ( $product && $product instanceof WC_Product_Simple ) {
				$product_id = $product->is_type('variable') ? $product->get_parent_id() : $product->get_id();
				$pricingRule = Bytes_Pricing_Rules::get_pricing_rule( $product_id, null, null, false );
				$pricingRule = bytes_brcbp_sanitize_array($pricingRule);
				if ( $pricingRule ) {

					$min     = $pricingRule['minimum'];
					$max     = $pricingRule['maximum'];
					$groupOf = $pricingRule['group_of'];


					if ( '' !== $min ) {
						$args['min_value'] = $min;
					}

					if ( $max ) {
						$args['max_value'] = $max;
					}

					if ( $groupOf ) {
						$args['step'] = $groupOf;
					}
				}
			 }
			return $args;
	}

	public function bytes_available_variation($array, $that, $variation){
		$product_id = $variation->get_id(); 
		$pricingRule = Bytes_Pricing_Rules::get_pricing_rule( $product_id, null, null, false );
		$pricingRule = bytes_brcbp_sanitize_array($pricingRule);
				if ( $pricingRule ) {

					$min     = $pricingRule['minimum'];
					$max     = $pricingRule['maximum'];
					$groupOf = $pricingRule['group_of'];


					if ( '' !== $min ) {
						$array['min_qty'] = $min;	
					}

					if ( $max ) {
						$array['max_qty'] = $max;
					}

					if ( $groupOf ) {
						$array['step'] = $groupOf;
					}
				}
				return $array;
	}

	public function bytes_add_to_cart_validation($passed, $productId, $qty, $variationId = null){

		// Do not show additional notices if there are already notices
		if ( ! $passed ) {
			return false;
		}

			$productId   = $variationId ? $variationId : $productId;
			$pricingRule = Bytes_Pricing_Rules::get_pricing_rule( $productId, null, null, false );
			$pricingRule = bytes_brcbp_sanitize_array($pricingRule);

			if ( $pricingRule ) {

					$min     = $pricingRule['minimum'];
					$max     = $pricingRule['maximum'];
					$groupOf = $pricingRule['group_of'];
					$cartQuantity = $this->getProductCartQuantity( $productId );
					$cartQuantity = $cartQuantity + $qty;
					
					if ( $min ) {
				
						if ( $cartQuantity  < $min ) {
							// translators: %s: minimum quantity
							wc_add_notice( sprintf( __( 'Minimum order quantity for the product is %s',
							'bytes-role-and-customer-based-pricing-for-woocommerce' ), $min ), 'error' );
	
							return false;
						}
					}
	
					if ( $max ) {
	
						if ( $cartQuantity > $max ) {
	
							// translators: %s: maximum quantity
							wc_add_notice( sprintf( 'Maximum order quantity for the product is %s . Please check your cart.', $max ), 'error' );
	
							return false;
						}
					}

					if ( $groupOf ) {

						if ( 0 !== $qty % $groupOf ) {
							// translators: %s: quantity step
							wc_add_notice( sprintf( 'Order quantity must be multiple for %s', $groupOf ), 'error' );

							return false;
						}
					}
			}


			return $passed;

	}
	public function bytes_update_cart_validation($passed, $cart_item_key, $values, $quantity){

			$productId   = $values['variation_id'] ? $values['variation_id'] : $values['product_id'];
			$pricingRule = Bytes_Pricing_Rules::get_pricing_rule( $productId );
			$pricingRule = bytes_brcbp_sanitize_array($pricingRule);

			if ( $pricingRule ) {

					$min     = $pricingRule['minimum'];
					$max     = $pricingRule['maximum'];
					$groupOf = $pricingRule['group_of'];

				if ( $min ) {
					
					if ( $quantity < $min ) {
						/* translators: %s: min qty */
						wc_add_notice( sprintf( __( 'Minimum order quantity for the product is %s',
							'bytes-role-and-customer-based-pricing-for-woocommerce' ), $min ), 'error' );


						return false;
					}
				}

				if ( $max ) {

					if ( $quantity > $max ) {
						/* translators: %s: max qty */
						wc_add_notice( sprintf( __( 'Maximum order quantity for the product is %s',
							'bytes-role-and-customer-based-pricing-for-woocommerce' ), $max ), 'error' );

						return false;
					}
				}

				if ( $groupOf ) {

					if ( 0 !== $quantity % $groupOf ) {
						/* translators: %s: group */
						wc_add_notice( sprintf( __( 'Order quantity must be multiple for %s',
							'bytes-role-and-customer-based-pricing-for-woocommerce' ), $groupOf ), 'error' );

						return false;
					}
				}
			}

			return $passed;
	}

	public function getProductCartQuantity( $product_id ) {
		$qty = 0;

		if ( is_array( wc()->cart->cart_contents ) ) {
			foreach ( wc()->cart->cart_contents as $cart_content ) {
				if ( $cart_content['product_id'] == $product_id ) {
					$qty += $cart_content['quantity'];
				}
			}
		}

		return $qty;
	}

} new Bytes_Quantity_Rule;
