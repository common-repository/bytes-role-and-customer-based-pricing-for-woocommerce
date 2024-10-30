<?php 
/**
 * 
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Bytes_Variable_Product_For_Role_Customer_Based_Price{
	
	public function __construct()
	{
		add_action( 'woocommerce_variation_options_pricing', array( $this, 'bytes_get_product_rule_row_html_for_varation' ), 10, 3 );
	}

	public function bytes_get_product_rule_row_html_for_varation( $loop, $variation_data, WP_Post $variation ){ 

		echo '<div id="bytes-role-specific-pricing" class="panel woocommerce_role_specific_pricing_panel woocommerce_options_panel">';
    		include plugin_dir_path( dirname( __FILE__ ))  . 'partials/variable-prodcut-field.php';
    	echo '</div>';	
	}

	public function bytes_get_product_rule_row_html(){
    	$response = array();

    	if(  ! isset( $_POST[ 'ajax_nonce' ] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ 'ajax_nonce' ] ) ), "brcbp_ajax_nonce") ){
			$response = array( "success" => false, "html" => __( "Nonce fail to validate. Please try again.", "bytes-role-and-customer-based-pricing-for-woocommerce") );
    		echo esc_html( wp_send_json( $response ) );
    		return;
    	}
    	
    	if( isset($_POST['type']) && sanitize_text_field($_POST['type']) == "customer" ){
    		
    		$user = get_user_by( 'id', sanitize_text_field( $_POST['identifier'] ) );
    		$_POST['identifier'] = sanitize_email($user->user_email);
    	}
    	ob_start(); 
    	?>
    	<div class="rcbp-pricing-rule rcbp-pricing-rule--<?php echo esc_attr( sanitize_text_field( $_POST['identifier'] ) ); ?>" data-identifier-slug="<?php echo esc_attr( sanitize_text_field( $_POST['identifier'] ) ); ?>" data-identifier="<?php echo esc_attr( ucfirst( sanitize_text_field( $_POST['identifier'] ) ) ); ?>" data-type="<?php echo esc_attr( sanitize_text_field( $_POST['type'] ) ); ?>">
	    	<div class="rcbp-pricing-rule__header">
				<div class="rcbp-pricing-rule__name">
					<b><?php echo esc_html( ucfirst( sanitize_text_field( $_POST[ 'identifier' ] ) ) ) ?></b>
				</div>
				<div class="rcbp-pricing-rule__actions">
					<span class="rcbp-pricing-rule__action-toggle-view rcbp-pricing-rule__action-toggle-view--close"></span>
					<a href="#" class="rcbp-pricing-rule-action--delete"><?php echo esc_html__('Remove', 'bytes-role-and-customer-based-pricing-for-woocommerce'); ?></a>
				</div>
			</div>

	    	<div class="rcbp-pricing-rule__content" style="display: block;">
		    	<div class="rcbp-pricing-rule-form rcbp-pricing-rule-form--product">
					<div class="rcbp-pricing-rule-form__prices">
					        <div class="rcbp-pricing-rule-form__pricing-type">
								
					            <p class="form-field _rcbp_role_pricing_type[<?php echo esc_attr( sanitize_text_field( $_POST[ 'identifier' ] ) ); ?>]_field">
					                <label><?php echo esc_html__('Pricing type', 'bytes-role-and-customer-based-pricing-for-woocommerce'); ?></label>
					                <input type="radio" value="flat" name="_rcbp_role_pricing_type[<?php echo esc_attr( sanitize_text_field( $_POST[ 'identifier' ] ) ); ?>][<?php echo esc_attr( $loop ); ?>]" class="rcbp-pricing-type-input" checked="checked" id="_rcbp_role_pricing_type[<?php echo esc_attr( sanitize_text_field( $_POST[ 'identifier' ] ) ); ?>]-flat">
					                <label class="rcbp-pricing-rule-form__pricing-type-label" for="_rcbp_role_pricing_type[<?php echo esc_attr( sanitize_text_field( $_POST[ 'identifier' ] ) ); ?>]-flat">
										<?php echo esc_html__('Flat prices', 'bytes-role-and-customer-based-pricing-for-woocommerce'); ?>
									</label>
					                <input type="radio" value="percentage" name="_rcbp_role_pricing_type[<?php echo esc_attr( sanitize_text_field( $_POST[ 'identifier' ] ) ); ?>]" class="rcbp-pricing-type-input" max="99" id="_rcbp_role_pricing_type[<?php echo esc_attr( sanitize_text_field( $_POST[ 'identifier' ] ) ); ?>]-percentage">
					                <label class="rcbp-pricing-rule-form__pricing-type-label" for="_rcbp_role_pricing_type[<?php echo esc_attr( sanitize_text_field( $_POST[ 'identifier' ] ) ); ?>]-percentage">
									<?php echo esc_html__('Percentage discount', 'bytes-role-and-customer-based-pricing-for-woocommerce'); ?></label>
					            </p>
					        </div>
					        <div class="rcbp-pricing-rule-form__flat_prices" style="">
					            <section class="notice notice-warning rcbp-pricing-rule-form__flat-prices-warning">
					                <p><?php echo esc_html__('Note that prices indicated below will be applied to the whole range of products you specified in the products/categories sections. Be careful to not mess up the pricing.', 'bytes-role-and-customer-based-pricing-for-woocommerce'); ?></p>
					            </section>
								
					            <p class="form-field _rcbp_role_regular_price[<?php echo esc_attr( sanitize_text_field( $_POST[ 'identifier' ] ) ); ?>]_field">
					                <label for="_rcbp_role_regular_price[<?php echo esc_attr( sanitize_text_field( $_POST[ 'identifier' ] ) ); ?>]"><?php echo esc_html__('Regular price ($)', 'bytes-role-and-customer-based-pricing-for-woocommerce'); ?></label>
									<input type="text" value="" placeholder="Specify the regular price for <?php echo esc_attr( sanitize_text_field( $_POST[ 'identifier' ] ) ); ?> user role" class="wc_input_price" name="_rcbp_role_regular_price[<?php echo esc_attr( sanitize_text_field( $_POST[ 'identifier' ] ) ); ?>]" id="_rcbp_role_regular_price[<?php echo esc_attr( sanitize_text_field( $_POST[ 'identifier' ] ) ); ?>]">
									<span class="woocommerce-help-tip" tabindex="0" aria-label="If you don't want to change standard product pricing - leave field empty."></span></p>
								
					            <p class="form-field _rcbp_role_sale_price[<?php echo esc_attr( sanitize_text_field( $_POST[ 'identifier' ] ) ); ?>]_field">
					                <label for="_rcbp_role_sale_price[<?php echo esc_attr( sanitize_text_field( $_POST[ 'identifier' ] ) ); ?>]"><?php echo esc_html__('Sale price ($)', 'bytes-role-and-customer-based-pricing-for-woocommerce'); ?></label>
									<input type="text" value="" placeholder="Specify the sale price for <?php echo esc_attr( sanitize_text_field( $_POST[ 'identifier' ] ) ); ?> user role" class="wc_input_price" name="_rcbp_role_sale_price[<?php echo esc_attr( sanitize_text_field( $_POST[ 'identifier' ] ) ); ?>]" id="_rcbp_role_sale_price[<?php echo esc_attr( sanitize_text_field( $_POST[ 'identifier' ] ) ); ?>]">
									<span class="woocommerce-help-tip" tabindex="0" aria-label="If you don't want to change standard product pricing - leave field empty."></span></p>
					        </div>
					        <div class="rcbp-pricing-rule-form__percentage_discount" style="display:none">
								
					            <div class="rcbp-pricing-premium-content">
									<p class="form-field _rcbp_role_discount[<?php echo esc_attr( sanitize_text_field( $_POST[ 'identifier' ] ) ); ?>]_field">
					                    <label for="_rcbp_role_discount[<?php echo esc_attr( sanitize_text_field( $_POST[ 'identifier' ] ) ); ?>]">
											<?php echo esc_html__('Discount (%)', 'bytes-role-and-customer-based-pricing-for-woocommerce'); ?>
										</label>
					              		<input type="number" step="any" value="" name="_rcbp_role_discount[<?php echo esc_attr( sanitize_text_field( $_POST[ 'identifier' ] ) ); ?>]" id="_rcbp_role_discount[<?php echo esc_attr( sanitize_text_field( $_POST[ 'identifier' ] ) ); ?>]">
					                </p>
					            </div>
					        </div>
					 </div>
					 <div class="rcbp-pricing-rule-form__product_quantity rcbp-pricing-premium-content">
				        <p class="form-field _rcbp_role_minimum[<?php echo esc_attr( sanitize_text_field( $_POST[ 'identifier' ] ) ); ?>]_field">
				            <label for="_rcbp_role_minimum[<?php echo esc_attr( sanitize_text_field( $_POST[ 'identifier' ] ) ); ?>]">
							<?php echo esc_html__('Minimum quantity', 'bytes-role-and-customer-based-pricing-for-woocommerce'); ?></label>
							<span class="woocommerce-help-tip" tabindex="0" aria-label="Specify the minimal amount of products that <b><?php echo esc_attr( sanitize_text_field( $_POST[ 'identifier' ] ) ); ?></b> can purchase."></span>
							<input type="number" step="1" name="_rcbp_role_minimum[<?php echo esc_attr( sanitize_text_field( $_POST[ 'identifier' ] ) ); ?>]" id="_rcbp_role_minimum[<?php echo esc_attr( sanitize_text_field( $_POST[ 'identifier' ] ) ); ?>]" value="">
				        </p>

						
				        <p class="form-field _rcbp_role_maximum[<?php echo esc_attr( sanitize_text_field( $_POST[ 'identifier' ] ) ); ?>]_field">
				            <label for="_rcbp_role_maximum[<?php echo esc_attr( sanitize_text_field( $_POST[ 'identifier' ] ) ); ?>]">
							<?php echo esc_html__('Maximum quantity', 'bytes-role-and-customer-based-pricing-for-woocommerce'); ?></label>
							<span class="woocommerce-help-tip" tabindex="0" aria-label="Specify the maximum number of products available for purchase by <b><?php echo esc_attr( sanitize_text_field( $_POST[ 'identifier' ] ) ); ?></b> in one order."></span><input type="number" step="1" name="_rcbp_role_maximum[<?php echo esc_attr( sanitize_text_field( $_POST[ 'identifier' ] ) ); ?>]" id="_rcbp_role_maximum[<?php echo esc_attr( sanitize_text_field( $_POST[ 'identifier' ] ) ); ?>]" value="">
				        </p>

						
				        <p class="form-field _rcbp_role_group_of[<?php echo esc_attr( sanitize_text_field( $_POST[ 'identifier' ] ) ); ?>]_field">
				            <label for="_rcbp_role_group_of[<?php echo esc_attr( sanitize_text_field( $_POST[ 'identifier' ] ) ); ?>]">
								<?php echo esc_html__('Quantity step', 'bytes-role-and-customer-based-pricing-for-woocommerce'); ?>
							</label>
							<span class="woocommerce-help-tip" tabindex="0" aria-label="Specify by how many products quantity will increase or decrease when a customer adds the product to the cart for purchase by <b><?php echo esc_attr( sanitize_text_field( $_POST[ 'identifier' ] ) ); ?></b>. Leave blank if products can be added one by one.">
							</span>
							<input type="number" step="1" name="_rcbp_role_group_of[<?php echo esc_attr( sanitize_text_field( $_POST[ 'identifier' ] ) ); ?>]" id="_rcbp_role_group_of[<?php echo esc_attr( sanitize_text_field( $_POST[ 'identifier' ] ) ); ?>]" value="">
				        </p>
				    </div>
				</div>
			</div>
		</div>

    	<?php
    	$html = ob_get_clean();
    	
    	$response = array( "success" => true, "html" =>  $html );
    	
    	echo esc_html( wp_send_json( $response ) );
    }
} new Bytes_Variable_Product_For_Role_Customer_Based_Price;
