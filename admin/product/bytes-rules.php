<?php 
namespace Bytes\RoleAndCustomerBasedPricing\Admin\Product;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Set Role & Customer based prircing rules
 */

 class Bytes_Pricing_Rules{

 	const PRODUCT_ROLE_SPECIFIC_PRICING_RULES_KEY = '_bytes_role_based_pricing_rules';
	const PRODUCT_CUSTOMER_SPECIFIC_PRICING_RULES_KEY = '_bytes__customer_based_pricing_rules';


	public static function get_pricing_rule( $productId, $parentId = null, $user = null, $validatePricing = true ){
		$productId = sanitize_text_field($productId);
		$product = wc_get_product( $productId );

		if ( ! $product || ! $product->is_type( array(
				'variation',
				'simple',
				'subscription',
				'subscription-variation',
				'course',
			) ) ) {
			return false;
		}

		$parentId = $parentId ? $parentId : sanitize_text_field($product->get_parent_id());
		$user     = $user instanceof WP_User ? $user : wp_get_current_user();

		if( $user->ID == 0 ) return false;

		if ( ! $user ) {
			$user = new WP_User( 0 );
		}

		$customerSpecificRules = self::get_pricing_rule_by_type( $productId, 'customer' );
		if ( empty( $customerSpecificRules ) && $product->get_type() === 'variation' ) {
			$customerSpecificRules = self::get_pricing_rule_by_type( $parentId, 'customer' );
		}
		if( !empty( $customerSpecificRules ) ){
			
			foreach ( bytes_brcbp_sanitize_array( $customerSpecificRules ) as $userId => $rule ) {

				if ( $userId  === $user->user_email ) {
					
					return $rule;
				}
			}
		}
		$roleSpecificRules = self::get_pricing_rule_by_type( $productId, 'role' );
		if ( empty( $roleSpecificRules ) && $product->get_type() === 'variation' ) {
			$roleSpecificRules = self::get_pricing_rule_by_type($parentId, 'role');
		}
		if( !empty( $roleSpecificRules ) ){
			foreach ( bytes_brcbp_sanitize_array( $roleSpecificRules ) as $userId => $rule ) {

				if ( $userId  === $user->roles[0] ) {
					return $rule;
				}
			}
		}
		return false;
	}

	public static function get_pricing_rule_by_type($productId, $type){
		$key = 'role' === $type ? self::PRODUCT_ROLE_SPECIFIC_PRICING_RULES_KEY : self::PRODUCT_CUSTOMER_SPECIFIC_PRICING_RULES_KEY;
		$rules = get_post_meta( $productId, $key, true );
		$rules = sanitize_meta( $key, $rules, 'post' );
		$pricingRules = $rules;
	
		return $pricingRules;
	}

 } 
 new Bytes_Pricing_Rules;