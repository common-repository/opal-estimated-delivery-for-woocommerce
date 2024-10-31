/*------------------------- 
Frontend related javascript
-------------------------*/

(function( $ ) {

	"use strict";

    function oedw_update_est_date_by_shipping_method($shipping_method) {
        $.ajax({ 
            url: oedw_script.ajaxurl,
            type: "post", 
            dataType: 'json', 
            data: {
                action: 'oedw_update_est_date_cart',
                shipping_method: $shipping_method,
                ajax_nonce_parameter: oedw_script.security_nonce,
            }, 
            success: function(data) { 
                console.log(data);
                
            }, 
            error: function() { 
                alert("An error occured, please try again.");          
            } 
        }); 
    }

    $(document).ready( function() {
        $( document.body ).on( 'updated_checkout', function(data) {
            if ($('#shipping_method .shipping_method').length) {
                var shipping_method = $('#shipping_method .shipping_method:checked').val();
    
                // Update 
                oedw_update_est_date_by_shipping_method(shipping_method);
            }
        } );

        if ($('#oedw_detail_zone').length) {
            $('#oedw_detail_zone').on('click', function(e) {
                e.preventDefault();
    
                $(this).closest('.oedw-est-box').next('.oedw-est-shipping-method').slideToggle();
            })
        }
    });
    // $.fn.oedw = oedw;

})( jQuery );
