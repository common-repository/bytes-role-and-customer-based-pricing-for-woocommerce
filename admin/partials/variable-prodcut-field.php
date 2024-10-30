<?php 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$dataLoop = "";
$dataLoop = $loop;
$ID = sanitize_key($variation->ID);

$product = wc_get_product( $ID  ); 
$rolesRules =  get_post_meta( sanitize_key( $ID ), '_bytes_role_based_pricing_rules', true);
$rolesRules = sanitize_meta( '_bytes_role_based_pricing_rules', $rolesRules, 'post' );

$existRole = [];
if( $rolesRules != "" ){
	foreach ( bytes_brcbp_sanitize_array( $rolesRules ) as $key => $value) {
	 	$existRole[] = sanitize_key( $key );
	 } 
} 
$productID = $product->get_id();
$productType = $product->get_type();
?>

<div class="form-filed bytes-pricing-rule-block bytes-pricing-rule-block---<?php echo esc_attr( $productType ); ?>" data-product-type="<?php echo esc_attr( $productType ); ?>" data-product-id="<?php echo esc_attr( $productID ); ?>" data-loop="<?php echo esc_attr( $dataLoop ); ?>" id="bytes-pricing-rule-block-role--<?php echo esc_attr( $productID ); ?>">
	<label class="bytes-pricing-rule-block-name"><?php echo esc_html__('Role based pricing', 'bytes-role-and-customer-based-pricing-for-woocommerce'); ?></label>
	<div class="bytes-pricing-rule-block-content">
		<div class="rcbp-pricing-rules" bis_skin_checked="1">
			<div class="rcbp-no-rules" style="" bis_skin_checked="1">
				<span><?php echo esc_html__('Choose the role to create the new pricing rules that will apply to all users with that role.', 'bytes-role-and-customer-based-pricing-for-woocommerce'); ?></span>
			</div>
			<?php echo do_shortcode('[bytes_roles_rules id="'. esc_attr( $ID ) .'" type="role" loop="'. esc_attr($loop) .'"]'); ?>
		</div>
		<div class="bytes-pricing-rule">
			<div class="bytes-add-new-rule">
				<select class="rcbp-add-new-rule-role bytes-add-new-rule-form__identifier-selector" data-action="bytes_get_product_rule_row_html" data-product-id="<?php echo esc_attr( $productID ); ?>" data-loop="<?php echo esc_attr( $loop ); ?>">
					<?php foreach ( bytes_brcbp_sanitize_array( wp_roles()->roles ) as $key => $WPRole ) : ?>
						<option value="<?php echo esc_attr( $key ); ?>" <?php echo in_array( $key, $existRole ) ? "disabled" : "";  ?> ><?php 
								printf( '%s', esc_html( $WPRole['name'] ) );
							?></option>
					<?php endforeach; ?>
				</select>
		<a class="button bytes-add-new-rule-form-add-button" href="javascript:void(0)" data-type="role">
			<?php echo esc_html__( 'Setup Price', 'bytes-role-and-customer-based-pricing-for-woocommerce' ); ?>
		</a>
				<div class="clear"></div>
			</div>
		</div>		
	</div>
</div>

<div class="form-filed bytes-pricing-rule-block bytes-pricing-rule-block---<?php echo esc_attr( $productType ); ?>" data-product-type="<?php echo esc_attr( $productType ); ?>" data-product-id="<?php echo esc_attr( $productID ); ?>" data-loop="<?php echo esc_attr( $dataLoop ); ?>" id="bytes-pricing-rule-block-customer--<?php echo esc_attr( $productID ); ?>">
	<label class="bytes-pricing-rule-block-name"><?php echo esc_html__('Customer based pricing', 'bytes-role-and-customer-based-pricing-for-woocommerce'); ?></label>
	<div class="bytes-pricing-rule-block-content">
		<div class="rcbp-pricing-rules" bis_skin_checked="1">
			<div class="rcbp-no-rules" style="" bis_skin_checked="1">
				<span><?php echo esc_html__('If you need to make pricing rules only for specific customers (not whole user role), choose the customer name here.', 'bytes-role-and-customer-based-pricing-for-woocommerce'); ?></span>
			</div>
			<?php echo do_shortcode('[bytes_roles_rules id="'. esc_attr($ID) .'" type="customer" loop="'. esc_attr($loop) .'"]'); ?>
		</div>
		<div class="bytes-pricing-rule">
			<div class="bytes-add-new-rule rcbp-add-new-rule-form">
				<select class=" rcbp-add-new-rule-role bytes-add-new-rule-form__identifier-selector rcbp-add-new-rule-form__identifier-selector--customer wc-product-search" data-action="bytes_search_customer_for_rule" data-product-id="<?php echo esc_attr( $productID ); ?>" data-loop="<?php echo esc_attr($loop); ?>"
			data-placeholder="<?php esc_attr_e( 'Select for a customer&hellip;', 'role-and-customer-based-pricing-for-woocommerce' ); ?>" style="width: 200px;">
				</select>
				<a class="button bytes-add-new-rule-form-add-button" href="javascript:void(0)" data-type="customer"><?php echo esc_html__('Setup Price', 'bytes-role-and-customer-based-pricing-for-woocommerce'); ?></a>
				<div class="clear"></div>
			</div>
		</div>		
	</div>
</div>