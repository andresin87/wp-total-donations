jQuery(document).ready(function() {	

////////// PAYPAL///////////////////////

    jQuery('#miglaUpdatePaypalEmails').click(function() {
		  jQuery.ajax({
			type : "post",
			url :  miglaAdminAjax.ajaxurl, 
			data : {action: "miglaA_update_me", key:'migla_paypal_emails', value: jQuery('#miglaPaypalEmails').val() },
			success: function(msg) {  
                          saved('#miglaUpdatePaypalEmails');
			}
		  })  ; //ajax	
      	
    });		


    jQuery('#miglaUpdatePaypalItem').click(function() {
		  jQuery.ajax({
			type : "post",
			url :  miglaAdminAjax.ajaxurl, 
			data : {action: "miglaA_update_me", key:'migla_paypalitem', value: jQuery('#miglaPaypalItem').val() },
			success: function(msg) {  
                          saved('#miglaUpdatePaypalItem');
			}
		  })  ; //ajax	
      	
    });		

    jQuery('#miglaUpdatePaypalSettings').click(function() {
		  jQuery.ajax({
			type : "post",
			url :  miglaAdminAjax.ajaxurl, 
			data : {action: "miglaA_update_me", key:'migla_payment', value: jQuery('input[name=miglaPaypal]:checked').val() },
			success: function(msg) { 
                          saved('#miglaUpdatePaypalSettings');
			}
		  })  ; //ajax	
      	
    });	

    jQuery('#miglaUpdatePaypalCmdSettings').click(function() {
		  jQuery.ajax({
			type : "post",
			url :  miglaAdminAjax.ajaxurl, 
			data : {action: "miglaA_update_me", key:'migla_paymentcmd', value: jQuery("input[name='miglaPaypalcmd']:checked").val() },
			success: function(msg) { 
                          saved('#miglaUpdatePaypalCmdSettings');
			}
		  })  ; //ajax	
      	
    });	

});