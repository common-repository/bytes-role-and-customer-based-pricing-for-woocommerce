jQuery(document).ready(function ($) {   
    jQuery(document).find(".rcbp-add-new-rule-form").on('click, change, blur','.select2-container.select2-container--open', function() {
        let UserIds = [];
        jQuery(".bytes-pricing-rule-block .rcbp-pricing-rule.customer").each(function(){
            UserIds.push(jQuery(this).attr('data-userid'));
        });
        
        jQuery(".bytes-add-new-rule.rcbp-add-new-rule-form .rcbp-add-new-rule-role").attr("data-exclude", "["+UserIds+"]");
    });
    
    jQuery('body').on("click","a.bytes-add-new-rule-form-add-button", function() {
        
        let identifier = jQuery(this).parent().children(".bytes-add-new-rule-form__identifier-selector").val();
        let type = jQuery(this).attr("data-type");
        let ajaxurl = ajax_object.ajax_url;
        let loop = jQuery(this).parent().children(".rcbp-add-new-rule-role").attr("data-loop");
        let productID = jQuery(this).parent().children(".rcbp-add-new-rule-role").attr("data-product-id");
        
        if( ( type == "customer" && identifier == null ) || ( type != "customer" && identifier == null ) ){
            return false;
        }
        
        action = "bytes_get_product_rule_row_html";
        
        jQuery.ajax({
            method: 'POST',
            url: ajax_object.ajax_url,
            data: {
                action: action,
                identifier : identifier,
                type : type,
                loop : loop,
                productID : productID,
                ajax_nonce : ajax_object.brcbp_ajax_nonce
            },
            success: function (response) {
                let id = "#bytes-pricing-rule-block-customer--" + productID;
                if( type == "role" ){ 
                    id = "#bytes-pricing-rule-block-role--" + productID;
                }
                
                jQuery(id + " .bytes-add-new-rule-form__identifier-selector").find('[value="' + identifier + '"]').attr('disabled','disabled');
                jQuery(id + " .bytes-pricing-rule-block-content .rcbp-pricing-rules .rcbp-no-rules").css('display','none');
                jQuery(id + " .bytes-pricing-rule-block-content .rcbp-pricing-rules").append(response.html);
            }
        });
    });

    jQuery('body').on("click",".panel-wrap.product_data .bytes-role-specific-pricing_options", function(){
        jQuery(".panel-wrap.product_data #bytes-role-specific-pricing").css("display","block");
    });
    jQuery('body').on("click",".panel-wrap.product_data .variations_options", function(){
        jQuery(".panel-wrap.product_data .woocommerce_variation .variable_pricing #bytes-role-specific-pricing").css("display","block");
    });
    
    jQuery('body').on("click",".rcbp-pricing-rule-form__pricing-type .rcbp-pricing-type-input",function() {
        let selectValue = jQuery(this).val();
        let mainDiv = jQuery(this).parent().parent().parent();

        console.log(jQuery(this));
        
        if( selectValue == "percentage" ){
            mainDiv.children(".rcbp-pricing-rule-form__flat_prices").css("display","none");
            mainDiv.children(".rcbp-pricing-rule-form__percentage_discount").css("display","block");
        }
        if( selectValue == "flat" ){
            mainDiv.children( ".rcbp-pricing-rule-form__flat_prices" ).css( "display", "block" );
            mainDiv.children( ".rcbp-pricing-rule-form__percentage_discount" ).css("display", "none" );
        }
    });

    jQuery('body').on("click",".rcbp-pricing-rule__header",function(e) {
        e.preventDefault();
        var $element = $(e.target);
        if ($element.hasClass('rcbp-pricing-rule-action--delete')) {
            return;
        }
        
        let $this = jQuery(this);
        jQuery($this).next().slideToggle();
        let close = jQuery('.rcbp-pricing-rule__header .rcbp-pricing-rule__actions span').hasClass('rcbp-pricing-rule__action-toggle-view--close');
        if(close == true){
            jQuery(this).find('.rcbp-pricing-rule__actions span').removeClass('rcbp-pricing-rule__action-toggle-view--close').addClass('rcbp-pricing-rule__action-toggle-view--open');
        }else{
            jQuery(this).find('.rcbp-pricing-rule__actions span').addClass('rcbp-pricing-rule__action-toggle-view--close').removeClass('rcbp-pricing-rule__action-toggle-view--open');
        }
    });

    jQuery('body').on("click",".rcbp-pricing-rule-action--delete",function(e) {
        e.preventDefault();
        if( confirm( "Are you sure to delete pricing rule?" ) ) {
            let $roleToRemove = $(e.target).closest('.rcbp-pricing-rule');
            let roleSlug = $roleToRemove.attr('data-identifier-slug');
            let type = $roleToRemove.attr('data-type');

            $roleToRemove.slideUp(300, function () {
                $roleToRemove.remove();
            });
            
            if(type == 'role'){
                let id = $roleToRemove.attr("id");
                jQuery(".rcbp-add-new-rule-role").find('[value="' + jQuery(this).attr('data-identifier') + '"]').removeAttr('disabled');
            }
        }
    });
    
    /* For Tool-tip Comment */
    jQuery('.woocommerce-help-tip').tipTip({
        'attribute': 'aria-label',
        'fadeIn':    50,
        'fadeOut':   50,
        'delay':     200
    });
});     