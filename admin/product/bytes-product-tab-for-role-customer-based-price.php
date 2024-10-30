<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

Class Bytes_Product_Tab_For_Role_Customer_Based_Price{

	private $product_data_tab = 'bytes-role-specific-pricing';

	public function __construct() {

		add_filter( 'woocommerce_product_data_tabs', array( $this, 'register' ) );
		add_action( 'woocommerce_product_data_panels', array( $this, 'render' ) );
		add_action( 'wp_ajax_bytes_get_product_rule_row_html', array( $this ,'bytes_get_product_rule_row_html',) );
		add_action( 'wp_ajax_bytes_search_customer_for_rule', array( $this ,'bytes_search_customer_for_rule') );
		add_action( 'woocommerce_process_product_meta', array( $this ,'brcbp_save_product_custom_tab' ) );
		add_shortcode( 'bytes_roles_rules', array( $this ,'bytes_rcbp_roles_rules_shortcode' ) );
	}

	public function register( $productTabs ) {

		$productTabs[ $this->product_data_tab ] = array(
			'label'  => __( 'Role & User based pricing', 'role-and-customer-based-pricing-for-woocommerce' ),
			'target' => $this->product_data_tab,
			'class'  => array(
				'show_if_simple',
				'show_if_variable',
				'show_if_course',
				'show_if_subscription',
				'show_if_variable_subscription'
			)
		);
		
		return $productTabs;
	}

	public function render(){
		global $post;
		$product = wc_get_product( sanitize_key($post->ID) );
		if( $product ){ ?>
			<div id="<?php echo esc_attr( $this->product_data_tab ); ?>" class="panel woocommerce_role_specific_pricing_panel woocommerce_options_panel">
				<?php wp_nonce_field( 'brcbp_woo_nonce' , "brcbp_woo_nonce"); ?>
				<?php include plugin_dir_path( dirname( __FILE__ ) )  . 'partials/product-tab-field.php'; ?>				
			</div>
			<?php 
		}
	}

	public function bytes_get_product_rule_row_html(){

		/**
	 	* Available variables
	 	*
		* @var string $_POST['identifier']
		* @var string $_POST['type']
		* @var int|false $loop
		* @var int $_POST['productID']
		*/
    	$response = array();
    	$userID = "";

    	if(  ! isset( $_POST[ 'ajax_nonce' ] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ 'ajax_nonce' ] ) ), "brcbp_ajax_nonce") ){
			$response = array( "success" => false, "html" => __( "Nonce fail to validate. Please try again.", "bytes-role-and-customer-based-pricing-for-woocommerce") );
    		echo esc_html( wp_send_json( $response ) );
    		return;
    	}
    	
    	if( isset( $_POST[ 'type' ] ) && sanitize_text_field( $_POST[ 'type' ] ) == "customer" ){
    		$user = get_user_by( 'id', sanitize_text_field( $_POST[ 'identifier' ] ) );
    		$_POST[ 'identifier' ] = sanitize_email($user->user_email);
    		$userID = $user->ID;
    	}

    	$loop = false;
    	if( isset( $_POST[ 'loop' ] ) ){
    		$loop = "_variation[".sanitize_text_field($_POST['loop'])."]"; 
    	}

    	ob_start(); ?>

    	<div class="rcbp-pricing-rule rcbp-pricing-rule--<?php echo esc_attr( sanitize_text_field( $_POST[ 'identifier' ] ) )." ". esc_attr( sanitize_text_field( $_POST['type'] ) );   ?>" data-identifier-slug="<?php echo esc_attr( sanitize_text_field( $_POST['identifier'] ) ) ; ?>" data-userid="<?php echo esc_attr( $userID ); ?>" data-identifier="<?php echo esc_attr( ucfirst( sanitize_text_field( $_POST[ 'identifier' ] ) ) ); ?>" data-type="<?php echo esc_attr( sanitize_text_field( $_POST[ 'type' ] ) ); ?>">
	    	<div class="rcbp-pricing-rule__header">
				<div class="rcbp-pricing-rule__name">
					<b><?php echo esc_attr( ucfirst( sanitize_text_field( $_POST[ 'identifier' ] ) ) ); ?></b>
				</div>
				<div class="rcbp-pricing-rule__actions">
					<span class="rcbp-pricing-rule__action-toggle-view rcbp-pricing-rule__action-toggle-view--close"></span>
					<a href="#" class="rcbp-pricing-rule-action--delete" data-identifier="<?php echo esc_attr( sanitize_text_field( $_POST['identifier'] ) ); ?>"><?php echo esc_html__( 'Remove', 'bytes-role-and-customer-based-pricing-for-woocommerce' ); ?></a>
				</div>
			</div>

	    	<div class="rcbp-pricing-rule__content" style="display: block;">
		    	<div class="rcbp-pricing-rule-form rcbp-pricing-rule-form--product">
					<div class="rcbp-pricing-rule-form__prices">
				        <div class="rcbp-pricing-rule-form__pricing-type">
							<?php $fieldName = "_rcbp_role_pricing_type{$loop}[".sanitize_text_field( $_POST['identifier'] )."]"; 
							$hidden = "_rcbp_variation_id{$loop}[".sanitize_text_field( $_POST['productID'] )."]"; ?>

							<input type="hidden" name="<?php echo esc_attr( $hidden ); ?>" value="<?php echo esc_attr( sanitize_text_field( $_POST['productID'] ) ); ?>">
				            <p class="form-field _rcbp_role_pricing_type[<?php echo esc_attr( sanitize_text_field( $_POST[ 'identifier' ] ) ); ?>]_field <?php echo esc_attr($fieldName); ?>">
				                <label><?php echo esc_html__('Pricing type', 'bytes-role-and-customer-based-pricing-for-woocommerce'); ?></label>
				                <input type="radio" value="flat" name="<?php echo esc_attr($fieldName); ?>" class="rcbp-pricing-type-input" checked="checked" id="_rcbp_role_pricing_type[<?php echo esc_attr( sanitize_text_field( $_POST[ 'identifier' ] ) ); ?>]-flat">
				                <label class="rcbp-pricing-rule-form__pricing-type-label" for="_rcbp_role_pricing_type[<?php echo esc_attr( sanitize_text_field( $_POST['identifier']) ); ?>]-flat">
								<?php echo esc_html__('Flat prices', 'bytes-role-and-customer-based-pricing-for-woocommerce'); ?></label>
				                <input type="radio" value="percentage" name="<?php echo esc_attr($fieldName); ?>" class="rcbp-pricing-type-input" max="99" id="_rcbp_role_pricing_type[<?php echo esc_attr( sanitize_text_field( $_POST[ 'identifier' ] ) ); ?>]-percentage">
				                <label class="rcbp-pricing-rule-form__pricing-type-label" for="_rcbp_role_pricing_type[<?php echo esc_attr( sanitize_text_field( $_POST[ 'identifier' ] ) ); ?>]-percentage">
								<?php echo esc_html__('Percentage discount', 'bytes-role-and-customer-based-pricing-for-woocommerce'); ?></label>
				            </p>
				        </div>
				        <div class="rcbp-pricing-rule-form__flat_prices" style="">
				            <section class="notice notice-warning rcbp-pricing-rule-form__flat-prices-warning">
				                <p><?php echo esc_html__('Note that prices indicated below will be applied to the whole range of products you specified in the products/categories sections. Be careful to not mess up the pricing.', 'bytes-role-and-customer-based-pricing-for-woocommerce'); ?></p>
				            </section>
							<?php $fieldName = "_rcbp_role_regular_price{$loop}[".sanitize_text_field( $_POST['identifier'] )."]"; ?>
				            <p class="form-field _rcbp_role_regular_price[<?php echo esc_attr( sanitize_text_field( $_POST[ 'identifier' ] ) ); ?>]_field">
				                <label for="<?php echo esc_attr( $fieldName ); ?>"><?php echo esc_html__('Regular price ($)', 'bytes-role-and-customer-based-pricing-for-woocommerce'); ?></label>
								<input type="text" value="" placeholder="Specify the regular price for <?php echo esc_attr( sanitize_text_field( $_POST[ 'identifier' ] ) ); ?> user role" class="wc_input_price" name="<?php echo esc_attr($fieldName); ?>" id="_rcbp_role_regular_price[<?php echo esc_attr( sanitize_text_field( $_POST['identifier']) ); ?>]">
								<span class="woocommerce-help-tip" tabindex="0" aria-label="If you don't want to change standard product pricing - leave field empty."></span>
							</p>
							<?php $fieldName = "_rcbp_role_sale_price{$loop}[".sanitize_text_field( $_POST['identifier'] )."]"; ?>
				            <p class="form-field _rcbp_role_sale_price[<?php echo esc_attr( sanitize_text_field( $_POST['identifier']) ); ?>]_field">
				            	<label for="<?php echo esc_attr($fieldName); ?>"><?php echo esc_html__('Sale price ($)', 'bytes-role-and-customer-based-pricing-for-woocommerce'); ?></label>
								<input type="text" value="" placeholder="Specify the sale price for <?php echo esc_attr( sanitize_text_field( $_POST['identifier']) ); ?> user role" class="wc_input_price" name="<?php echo esc_attr($fieldName); ?>" id="_rcbp_role_sale_price[<?php echo esc_attr( sanitize_text_field( $_POST['identifier']) ); ?>]">
								<span class="woocommerce-help-tip" tabindex="0" aria-label="If you don't want to change standard product pricing - leave field empty."></span>
							</p>
				        </div>
				        <div class="rcbp-pricing-rule-form__percentage_discount" style="display:none">
							<div class="rcbp-pricing-premium-content">
								<?php $fieldName = "_rcbp_role_discount{$loop}[".sanitize_text_field( $_POST['identifier'] )."]"; ?>
				                <p class="form-field _rcbp_role_discount[<?php echo esc_attr( sanitize_text_field( $_POST['identifier']) ); ?>]_field">
				                    <label for="<?php echo esc_attr($fieldName); ?>"><?php echo esc_html__('Discount (%)', 'bytes-role-and-customer-based-pricing-for-woocommerce'); ?></label>
				                    <input type="number" step="any" value="" name="<?php echo esc_attr($fieldName); ?>" id="_rcbp_role_discount[<?php echo esc_attr( sanitize_text_field( $_POST['identifier']) ); ?>]">
				                </p>
				            </div>
				        </div>
					 </div>
					 <div class="rcbp-pricing-rule-form__product_quantity rcbp-pricing-premium-content">
					 	<?php $fieldName = "_rcbp_role_minimum{$loop}[".sanitize_text_field( $_POST['identifier'] )."]"; ?>
				        <p class="form-field _rcbp_role_minimum[<?php echo esc_attr( sanitize_text_field( $_POST[ 'identifier' ] ) ); ?>]_field">
				            <label for="<?php echo esc_attr($fieldName); ?>"><?php echo esc_html__('Minimum quantity', 'bytes-role-and-customer-based-pricing-for-woocommerce'); ?></label>
							<span class="woocommerce-help-tip" tabindex="0" aria-label="Specify the minimal amount of products that <b><?php echo esc_attr( sanitize_text_field( $_POST['identifier']) ); ?></b> can purchase."></span>
							<input type="number" step="1" name="<?php echo esc_attr($fieldName); ?>" id="_rcbp_role_minimum[<?php echo esc_attr( sanitize_text_field( $_POST['identifier']) ); ?>]" value="">
				        </p>

						<?php $fieldName = "_rcbp_role_maximum{$loop}[".sanitize_text_field( $_POST['identifier'] )."]";  ?>
				        <p class="form-field _rcbp_role_maximum[<?php echo esc_attr( sanitize_text_field( $_POST[ 'identifier' ] ) ); ?>]_field">
				            <label for="<?php echo esc_attr($fieldName); ?>"><?php echo esc_html__('Maximum quantity', 'bytes-role-and-customer-based-pricing-for-woocommerce'); ?></label>
							<span class="woocommerce-help-tip" tabindex="0" aria-label="Specify the maximum number of products available for purchase by <b><?php echo esc_attr( sanitize_text_field( $_POST[ 'identifier' ] ) ); ?></b> in one order."></span><input type="number" step="1" name="<?php echo esc_attr($fieldName); ?>" id="_rcbp_role_maximum[<?php echo esc_attr( sanitize_text_field( $_POST['identifier']) ); ?>]" value="">
				        </p>

						<?php $fieldName = "_rcbp_role_group_of{$loop}[".sanitize_text_field( $_POST['identifier'] )."]";  ?>
				        <p class="form-field _rcbp_role_group_of[<?php echo esc_attr( sanitize_text_field( $_POST[ 'identifier' ] ) ); ?>]_field">
				            <label for="<?php echo esc_attr($fieldName); ?>"><?php echo esc_html__('Quantity step', 'bytes-role-and-customer-based-pricing-for-woocommerce'); ?></label>
							<span class="woocommerce-help-tip" tabindex="0" aria-label="Specify by how many products quantity will increase or decrease when a customer adds the product to the cart for purchase by <b><?php echo esc_attr( sanitize_text_field( $_POST[ 'identifier' ] ) ); ?></b>. Leave blank if products can be added one by one."></span><input type="number" step="1" 
							name="<?php echo esc_attr($fieldName); ?>" id="_rcbp_role_group_of[<?php echo esc_attr( sanitize_text_field( $_POST['identifier']) ); ?>]" value="">
				        </p>
				    </div>
				</div>
			</div>
		</div>

    	<?php
    	$html = ob_get_clean();
    	$response = array( "success" => true, "html" => $html );
    	echo esc_html( wp_send_json( $response ) );
    }

   	public function brcbp_save_product_custom_tab( $post_id ) {
		
		if( ! isset( $_POST['brcbp_woo_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST[ 'brcbp_woo_nonce' ] ) ), 'brcbp_woo_nonce' ) ) {
   			return;
   		}

		$product = wc_get_product( sanitize_text_field($post_id) );

	    $rolesRules = []; 
	    if( isset( $_POST[ 'rule_tab_data' ] ) && sanitize_text_field( $_POST[ 'rule_tab_data' ] ) == true ){


		    if( isset( $_POST[ '_rcbp_role_pricing_type' ] ) ) {
		    	foreach ( bytes_brcbp_sanitize_array( $_POST['_rcbp_role_pricing_type'] ) as $key => $value ) {
		    		$rolesRules[] = sanitize_text_field( $key );
		    	}
		    }
		    $data = [];
		    foreach( bytes_brcbp_sanitize_array( $rolesRules ) as $keys => $rolesRules ) {
		    	;
		    	if( isset( $_POST[ '_rcbp_role_pricing_type' ] ) ){
			    	foreach ( bytes_brcbp_sanitize_array( $_POST[ '_rcbp_role_pricing_type' ] ) as $key => $value ) {
			    		if( $key == $rolesRules ){
			    			$data[ $rolesRules ][ "pricing_type" ] = sanitize_text_field( $value );
			    		}
			    	}
			    }

			    if( isset( $_POST[ '_rcbp_role_regular_price'] ) ){
			    	foreach ( bytes_brcbp_sanitize_array( $_POST[ '_rcbp_role_regular_price' ] ) as $key => $value ) {
			    		if( $key == $rolesRules ){
			    			$data[ $rolesRules ][ "regular_price" ] = sanitize_text_field( $value );
			    		}
			    	}
			    }

			    if( isset( $_POST[ '_rcbp_role_sale_price' ] ) ){
			    	foreach ( bytes_brcbp_sanitize_array( $_POST[ '_rcbp_role_sale_price' ] ) as $key => $value ){
			    		if( $key == $rolesRules ){
			    			$data[ $rolesRules ][ "sale_price" ] = sanitize_text_field( $value );
			    		}
			    	}
			    }

			    if( isset( $_POST[ '_rcbp_role_discount' ] ) ){
			    	foreach ( bytes_brcbp_sanitize_array( $_POST[ '_rcbp_role_discount' ] ) as $key => $value ) {
			    		if( $key == $rolesRules ){
			    			$data[$rolesRules]["discount" ] = sanitize_text_field( $value );
			    		}
			    	}
			    }
			     if( isset( $_POST[ '_rcbp_role_minimum' ] ) ){
			    	foreach ( bytes_brcbp_sanitize_array( $_POST[ '_rcbp_role_minimum' ] ) as $key => $value ) {
			    		if( $key == $rolesRules ){
			    			$data[ $rolesRules ][ "minimum" ] = sanitize_text_field( $value );
			    		}
			    	}
			    }
			    if( isset( $_POST[ '_rcbp_role_maximum' ] ) ){
			    	foreach ( bytes_brcbp_sanitize_array( $_POST[ '_rcbp_role_maximum' ] ) as $key => $value ) {
			    		if( $key == $rolesRules ){
			    			$data[$rolesRules][ "maximum" ] = sanitize_text_field( $value );
			    		}
			    	}
			    }
			    if( isset( $_POST[ '_rcbp_role_group_of' ] ) ){
			    	foreach ( bytes_brcbp_sanitize_array( $_POST[ '_rcbp_role_group_of' ] ) as $key => $value ) {
			    		if($key == $rolesRules){
			    			$data[ $rolesRules ][ "group_of" ] = sanitize_text_field( $value );
			    		}
			    	}
			    }
			    $data[ $rolesRules ][ 'product_id' ] = sanitize_text_field( $post_id );
			}


			$role = array(); 

		  	foreach ( bytes_brcbp_sanitize_array( wp_roles()->roles ) as $key => $WPRole ): 
		  		
		  		if( array_key_exists( $key, $data ) ):
					$role[$key] = bytes_brcbp_sanitize_array( $data[$key] );
					unset( $data[$key] );
		  		endif;

		  	endforeach; 
			
			$role = bytes_brcbp_sanitize_array( $role );
			$data = bytes_brcbp_sanitize_array( $data );
			
			update_post_meta( sanitize_text_field($post_id), '_bytes_role_based_pricing_rules', $role );
			update_post_meta( sanitize_text_field($post_id), '_bytes__customer_based_pricing_rules', $data );
		
		  	$product->save();	 
		}	
		if( isset( $_POST[ 'variable_post_id' ] ) ){

			for ( $i=0; $i < count( $_POST[ 'variable_post_id' ] ) ; $i++ ) { 
				$rolesRules = [];
				if( isset( $_POST[ '_rcbp_role_pricing_type_variation' ][$i] ) ) {
					foreach ( bytes_brcbp_sanitize_array( $_POST[ '_rcbp_role_pricing_type_variation' ][$i] ) as $key => $value ){
			    		$rolesRules[] = sanitize_text_field( $key );
			    	}
			    }
			    $data = [];

			   	foreach ( bytes_brcbp_sanitize_array( $rolesRules ) as $keys => $rolesRules ){
		    	
			    	if( isset( $_POST[ '_rcbp_role_pricing_type_variation' ][$i] ) ){
				    	foreach ( bytes_brcbp_sanitize_array( $_POST[ '_rcbp_role_pricing_type_variation' ][$i] ) as $key => $value ){
				    		if( $key == $rolesRules ){
				    			$data[ $rolesRules ][ "pricing_type" ] = sanitize_text_field( $value );
				    		}
				    	}
				    }

				    if( isset( $_POST[ '_rcbp_role_regular_price_variation' ][$i] ) ){
				    	foreach ( bytes_brcbp_sanitize_array( $_POST[ '_rcbp_role_regular_price_variation' ][$i] ) as $key => $value ){
				    		if( $key == $rolesRules ){
				    			$data[ $rolesRules ][ "regular_price" ] = sanitize_text_field( $value );
				    		}
				    	}
				    }

				    if( isset( $_POST[ '_rcbp_role_sale_price_variation' ][$i] ) ){
				    	foreach( bytes_brcbp_sanitize_array( $_POST[ '_rcbp_role_sale_price_variation' ][$i] ) as $key => $value ) {
				    		if( $key == $rolesRules ){
				    			$data[ $rolesRules ][ "sale_price" ] = sanitize_text_field( $value );
				    		}
				    	}
				    }

				    if( isset( $_POST[ '_rcbp_role_discount_variation' ][$i] ) ){
				    	foreach( bytes_brcbp_sanitize_array( $_POST[ '_rcbp_role_discount_variation' ][$i] ) as $key => $value ) {
				    		if( $key == $rolesRules ){
				    			$data[ $rolesRules ][ "discount" ] = sanitize_text_field( $value );
				    		}
				    	}
				    }
				    if( isset( $_POST[ '_rcbp_role_minimum_variation' ][$i] ) ){
				    	foreach( bytes_brcbp_sanitize_array( $_POST[ '_rcbp_role_minimum_variation' ][$i] ) as $key => $value ) {
				    		if( $key == $rolesRules ){
				    			$data[ $rolesRules ][ "minimum" ] = sanitize_text_field( $value );
				    		}
				    	}
				    }
				    if( isset( $_POST[ '_rcbp_role_maximum_variation' ][$i] ) ){
				    	foreach( bytes_brcbp_sanitize_array( $_POST[ '_rcbp_role_maximum_variation' ][$i] ) as $key => $value) {
				    		if( $key == $rolesRules ){
				    			$data[ $rolesRules ][ "maximum" ] = sanitize_text_field( $value );
				    		}
				    	}
				    }
				    if( isset( $_POST[ '_rcbp_role_group_of_variation' ][$i] ) ){
				    	foreach( bytes_brcbp_sanitize_array( $_POST[ '_rcbp_role_group_of_variation' ][$i] ) as $key => $value ) {
				    		if( $key == $rolesRules ){
				    			$data[ $rolesRules ][ "group_of" ] = sanitize_text_field( $value );
				    		}
				    	}
				    }
				    $data[ $rolesRules ][ 'product_id' ] = bytes_brcbp_sanitize_array( $_POST[ 'variable_post_id' ][$i] );
				}
			   
			    $role = [];
			  	foreach ( bytes_brcbp_sanitize_array( wp_roles()->roles ) as $key => $WPRole ): 
			  		if( array_key_exists( $key, $data ) ):
						echo esc_html( $key )."\n <br>";
						$role[ $key ] = bytes_brcbp_sanitize_array( $data[ $key ] );
						unset( $data[ $key ] );
			  		endif;
				endforeach; 

				$role = bytes_brcbp_sanitize_array( $role );
				$data = bytes_brcbp_sanitize_array( $data );

				update_post_meta( sanitize_text_field($_POST[ 'variable_post_id' ][$i]), '_bytes_role_based_pricing_rules', $role );
				update_post_meta( sanitize_text_field($_POST[ 'variable_post_id' ][$i]), '_bytes__customer_based_pricing_rules', $data );
			}
		}
	}

	public function bytes_rcbp_roles_rules_shortcode( $atts ) {
		/**
	 	* Available variables
	 	*
		* @var string $key
		* @var string $atts['type']
		* @var int|false $atts['loop']
		* @var int|false $loop
		* @var int $productID
		*/
		$atts = bytes_brcbp_sanitize_array( $atts );
		ob_start();
		$product = wc_get_product( $atts[ 'id' ] ); 
		$rolesRules = ""; 
		$loop = false;
		$isUser = false;

		if( $atts[ 'type' ] == "role" ){
			$rolesRules =  get_post_meta( sanitize_text_field($atts[ 'id' ]), '_bytes_role_based_pricing_rules', true );
			$rolesRules = sanitize_meta( '_bytes_role_based_pricing_rules', $rolesRules, 'post' );
		}else{
			$rolesRules =  get_post_meta( sanitize_text_field($atts[ 'id' ]), '_bytes__customer_based_pricing_rules', true );
			$rolesRules = sanitize_meta( '_bytes__customer_based_pricing_rules', $rolesRules, 'post' );
			$isUser = true;
		}
		
		if( isset( $atts['loop'] ) ){
			$loop = "_variation[{$atts['loop']}]"; 
		}
		$productID = $atts['id'];
		if( $rolesRules != "" ){

			foreach( bytes_brcbp_sanitize_array( $rolesRules ) as $key => $value ){
				
			 	if( $isUser ){
					$user = get_user_by( 'email', $key );
					$userId = sanitize_key($user->ID);	
				}
			 	?>
		    	<div class="rcbp-pricing-rule rcbp-pricing-rule--<?php echo esc_attr( $key ); ?> <?php echo esc_attr( $atts[ 'type' ] ) ?>" data-identifier-slug="<?php echo  esc_attr( ucfirst( $key ) ); ?>" data-identifier="<?php echo esc_attr( $key ); ?>" data-userid="<?php echo ( $isUser ) ? esc_attr( $userId ) :'';?>" data-type="<?php echo esc_attr( $atts[ 'type' ] ); ?>">
			    	<div class="rcbp-pricing-rule__header">
						<div class="rcbp-pricing-rule__name">
							<b><?php echo esc_attr( ucfirst( $key ) ); ?></b>
						</div>
						<div class="rcbp-pricing-rule__actions">
							<span class="rcbp-pricing-rule__action-toggle-view rcbp-pricing-rule__action-toggle-view--close"></span>
							<a href="#" class="rcbp-pricing-rule-action--delete"><?php echo esc_html__( 'Remove', 'bytes-role-and-customer-based-pricing-for-woocommerce' ); ?></a>
						</div>
					</div>

			    	<div class="rcbp-pricing-rule__content" style="display: none;">
				    	<div class="rcbp-pricing-rule-form rcbp-pricing-rule-form--product">
							<div class="rcbp-pricing-rule-form__prices">
						        <div class="rcbp-pricing-rule-form__pricing-type">
									<?php $fieldName = "_rcbp_role_pricing_type{$loop}[{$key}]"; 
									$hidden = "_rcbp_variation_id{$loop}[{$productID}]"; ?>
									<input type="hidden" name="<?php echo esc_attr( $hidden ); ?>" value="<?php echo esc_attr($productID); ?>">
						            <p class="form-field _rcbp_role_pricing_type[<?php echo esc_attr( $key ); ?>]_field <?php echo esc_attr($fieldName); ?>">
						            	<label><?php echo esc_html__('Pricing type', 'bytes-role-and-customer-based-pricing-for-woocommerce'); ?></label>
						                <input type="radio" value="flat" name="<?php echo esc_attr($fieldName); ?>" class="rcbp-pricing-type-input" 
										<?php if($value['pricing_type'] == 'flat'): echo "checked"; endif; ?> id="_rcbp_role_pricing_type[<?php echo esc_attr($key); ?>]-flat">
						                <label class="rcbp-pricing-rule-form__pricing-type-label" for="_rcbp_role_pricing_type[<?php echo esc_attr($key); ?>]-flat">
											<?php echo esc_html__('Flat prices', 'bytes-role-and-customer-based-pricing-for-woocommerce'); ?></label>
						                <input type="radio" value="percentage" name="<?php echo esc_attr($fieldName); ?>" class="rcbp-pricing-type-input" 
										<?php if($value['pricing_type'] == 'percentage'): echo "checked"; endif; ?> max="99" id="_rcbp_role_pricing_type[<?php echo esc_attr($key); ?>]-percentage">
						                <label class="rcbp-pricing-rule-form__pricing-type-label" for="_rcbp_role_pricing_type[<?php echo esc_attr($key); ?>]-percentage">
											<?php echo esc_html__('Percentage discount', 'bytes-role-and-customer-based-pricing-for-woocommerce'); ?></label>
						            </p>
						        </div>
						        
				        		<div class="rcbp-pricing-rule-form__flat_prices" style="display: <?php echo ( $value['pricing_type'] == 'flat' ) ? "block" : "none"; ?>">
						           	<?php $fieldName = "_rcbp_role_regular_price{$loop}[{$key}]"; ?>
									
						            <p class="form-field _rcbp_role_regular_price[<?php echo esc_attr($key); ?>]_field">
						       			<label for="<?php echo esc_attr($fieldName); ?>]"><?php echo esc_html__('Regular price ($)', 'bytes-role-and-customer-based-pricing-for-woocommerce'); ?></label>
										<input type="text" value="<?php echo esc_attr($value['regular_price']); ?>" placeholder="Specify the regular price for <?php echo esc_attr( $key ); ?> user role" class="wc_input_price" name="<?php echo esc_attr($fieldName); ?>" id="_rcbp_role_regular_price[<?php echo esc_attr( $key ); ?>]">
										<span class="woocommerce-help-tip" tabindex="0" aria-label="If you don't want to change standard product pricing - leave field empty."></span>
									</p>
									<?php $fieldName = "_rcbp_role_sale_price{$loop}[{$key}]"; ?>
						            <p class="form-field _rcbp_role_sale_price[<?php echo esc_attr($key); ?>]_field">
							      		<label for="<?php echo esc_attr($fieldName); ?>"><?php echo esc_html__('Sale price ($)', 'bytes-role-and-customer-based-pricing-for-woocommerce'); ?></label>
										<input type="text" value="<?php echo esc_attr($value['sale_price']); ?>" placeholder="Specify the sale price for <?php echo esc_attr($key); ?> user role" class="wc_input_price" name="<?php echo esc_attr($fieldName); ?>" id="_rcbp_role_sale_price[<?php echo esc_attr($key); ?>]">
											<span class="woocommerce-help-tip" tabindex="0" aria-label="If you don't want to change standard product pricing - leave field empty."></span>
									</p>
						        </div>
						        <div class="rcbp-pricing-rule-form__percentage_discount" style="display: <?php echo ( $value['pricing_type'] == 'percentage' ) ? "block" : "none"; ?>">
									<div class="rcbp-pricing-premium-content">
										<?php $fieldName = "_rcbp_role_discount{$loop}[{$key}]"; ?>
						                <p class="form-field _rcbp_role_discount[<?php echo esc_attr($key); ?>]_field">
											<label for="<?php echo esc_attr($fieldName); ?>"><?php esc_html_e('Discount (%)', 'bytes-role-and-customer-based-pricing-for-woocommerce'); ?></label>
											<input type="number" step="any" value="<?php echo esc_attr( $value[ 'discount' ] ); ?>" name="<?php echo esc_attr($fieldName); ?>" id="_rcbp_role_discount[<?php echo esc_attr( $key ); ?>]">
						                </p>
						            </div>
						        </div>
						 	</div>

						 	<div class="rcbp-pricing-rule-form__product_quantity rcbp-pricing-premium-content">
							 	<?php $fieldName = "_rcbp_role_minimum{$loop}[{$key}]"; ?>
						        <p class="form-field _rcbp_role_minimum[<?php echo esc_attr($key); ?>]_field">
						       		<label for="<?php echo esc_attr($fieldName); ?>"><?php echo esc_html__('Minimum quantity', 'bytes-role-and-customer-based-pricing-for-woocommerce'); ?></label>
								<span class="woocommerce-help-tip" tabindex="0" aria-label="Specify the minimal amount of products that <b><?php echo esc_attr($key); ?></b> can purchase."></span><input type="number" step="1" name="<?php echo esc_attr($fieldName); ?>" id="_rcbp_role_minimum[<?php echo esc_attr($key); ?>]" 
							value="<?php echo esc_attr($value['minimum']); ?>">
						        </p>

								<?php $fieldName = "_rcbp_role_maximum{$loop}[{$key}]"; ?>
						        <p class="form-field _rcbp_role_maximum[<?php echo esc_attr($key); ?>]_field">
						            <label for="<?php echo esc_attr($fieldName); ?>"><?php echo esc_html__('Maximum quantity', 'bytes-role-and-customer-based-pricing-for-woocommerce'); ?></label>
									<span class="woocommerce-help-tip" tabindex="0" aria-label="Specify the maximum number of products available for purchase by <b><?php echo esc_attr($key); ?></b> in one order."></span>
									<input type="number" step="1" name="<?php echo esc_attr($fieldName); ?>" id="_rcbp_role_maximum[<?php echo esc_attr($key); ?>]" 
									value="<?php echo esc_attr($value['maximum']); ?>">
						        </p>

								<?php $fieldName = "_rcbp_role_group_of{$loop}[{$key}]"; ?>
						        <p class="form-field _rcbp_role_group_of[<?php echo esc_attr($key); ?>]_field">
						            <label for="<?php echo esc_attr($fieldName); ?>"><?php echo esc_html__('Quantity step', 'bytes-role-and-customer-based-pricing-for-woocommerce'); ?></label>
									<span class="woocommerce-help-tip" tabindex="0" aria-label="Specify by how many products quantity will increase or decrease when a customer adds the product to the cart for purchase by <b><?php echo esc_attr( $key ); ?></b>. Leave blank if products can be added one by one."></span><input type="number" step="1" name="<?php echo esc_attr( $fieldName ); ?>" id="_rcbp_role_group_of[<?php echo esc_attr($key); ?>]" value="<?php echo esc_attr( $value['group_of'] ); ?>">
						        </p>
						    </div>
						</div>
					</div>
				</div>

		   	<?php
		    }
		}
		return ob_get_clean();
	}

	public function bytes_search_customer_for_rule(){

		if( ! isset( $_GET[ 'security' ] ) || ! wp_verify_nonce(  sanitize_text_field( wp_unslash ( $_GET[ 'security' ] ) ), "search-products" ) ){
			$response = array( "success" => false, "html" => __( "Nonce fail to validate. Please try again.", "bytes-role-and-customer-based-pricing-for-woocommerce") );
    		echo esc_html( wp_send_json( $response ) );
    		return;
    	}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json( array() );
		}

		$term = isset( $_GET[ 'term' ] ) ? sanitize_text_field( $_GET[ 'term' ] ) : false;
		$exclude = isset( $_GET[ 'exclude' ] ) ? array_map( "sanitize_key", $_GET[ 'exclude' ] ) : array();
		
		if ( $term ) {

			$wp_user_query = new WP_User_Query(
				array(
					'search'         => '*' . $term . '*',
					'search_columns' => array(
						'user_login',
						'user_nicename',
						'user_email',
					),
					'fields'         => 'ID',
					'exclude'   => $exclude, // user exclude
				) );
			$users = $wp_user_query->get_results();

			if ( $users ) {
				$_users = array();

				foreach ( bytes_brcbp_sanitize_array( $users ) as $userId ) {
					try {
						$customer = new WC_Customer( $userId );
					} catch ( Exception $e ) {
						continue;
					}

					if ( $customer instanceof WC_Customer ) {
						$_users[ $userId ] = $this->formatCustomerString( $customer );
					}
				}
				wp_send_json( $_users );
			}
		}
		wp_send_json( array() );
	}

    public static function formatCustomerString( WC_Customer $customer, $link = false ) {
		$firstName = $customer->get_billing_first_name() ? $customer->get_billing_first_name() : $customer->get_shipping_first_name();
		$lastName  = $customer->get_billing_last_name() ? $customer->get_billing_last_name() : $customer->get_shipping_last_name();
		$email     = $customer->get_billing_email() ? $customer->get_billing_email() : $customer->get_email();

		if ( ! $email ) {
			return 'Undefined';
		}

		$name = sprintf( '%s %s (%s)', $firstName, $lastName, $email );

		if ( $link ) {
			return sprintf( '<a href="%s">%s</a>', get_edit_user_link( $customer->get_id() ), $name );
		}
		return $name;
	}
} 
new Bytes_Product_Tab_For_Role_Customer_Based_Price;