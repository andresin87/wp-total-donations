jQuery(document).ready(function() {	

    jQuery('#miglaUpdatePaypalSettings').click(function() {

         var isShown = 'no';
         if( jQuery('#migla_show_paypal').is(':checked') ){ isShown = 'yes';  }

		  jQuery.ajax({
			type : "post",
			url :  miglaAdminAjax.ajaxurl, 
			data : {action: "miglaA_update_me", key:'migla_show_paypal', value: isShown },
			success: function(msg) {  
                          
			}
		  })  ; //ajax	

		  jQuery.ajax({
			type : "post",
			url :  miglaAdminAjax.ajaxurl, 
			data : {action: "miglaA_update_me", key:'migla_paypal_emails', value: jQuery('#miglaPaypalEmails').val() },
			success: function(msg) {  
                          
			}
		  })  ; //ajax	

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
			data : {action: "miglaA_update_me", key:'migla_paypalitem', value: jQuery('#miglaPaypalItem').val() },
			success: function(msg) {  
                          //saved('#miglaUpdatePaypalItem');
			}
		  })  ; //ajax	

		  jQuery.ajax({
			type : "post",
			url :  miglaAdminAjax.ajaxurl, 
			data : {action: "miglaA_update_me", key:'migla_paymentcmd', value: jQuery("input[name='miglaPaypalcmd']:checked").val() },
			success: function(msg) { 
                          saved('#miglaUpdatePaypalCmdSettings');
			}
		  })  ; //ajax	
      	
    });	


//// PAYPAL BUTTON CHOICE ////////////////////
jQuery('#miglaUploadPaypalBtn').click(function() {
 formfield = jQuery('#mg_upload_image').attr('name');
 tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
 return false;
});
 
window.send_to_editor = function(html) {
 imgurl = jQuery('img',html).attr('src');
 jQuery('#mg_upload_image').val(imgurl);
 tb_remove();
}

jQuery('#miglaSavePaypalBtnUrl').click(function(){
   jQuery.ajax({
        type : "post",
        url :  miglaAdminAjax.ajaxurl, 
        data : {action: "miglaA_update_me", key:'miglaPayPalButtonChoice', value:'imageUpload' },
        success: function(msg) {  
        }
   }); //ajax
   jQuery.ajax({
        type : "post",
        url :  miglaAdminAjax.ajaxurl, 
        data : {action: "miglaA_update_me", key:'migla_paypalbuttonurl', value:jQuery('#mg_upload_image').val() },
        success: function(msg) {  
          saved('#miglaSavePaypalBtnUrl');
        }
   }); //ajax
});

jQuery('#miglaSavePayPalButtonPicker').click(function(){
   jQuery.ajax({
        type : "post",
        url :  miglaAdminAjax.ajaxurl, 
        data : {action: "miglaA_update_me", key:'miglaPayPalButtonChoice', value:'paypalButton' },
        success: function(msg) {  
        }
   }); //ajax
   var lang = jQuery("#miglaPayPalButtonPicker").val(); 
   jQuery.ajax({
        type : "post",
        url :  miglaAdminAjax.ajaxurl, 
        data : {action: "miglaA_update_me", key:'migla_paypalbutton', value:lang },
        success: function(msg) {  
          saved('#miglaSavePayPalButtonPicker');
        }
   }); //ajax
});

jQuery('#miglaCSSButtonPickerSave').click(function(){
   jQuery.ajax({
        type : "post",
        url :  miglaAdminAjax.ajaxurl, 
        data : {action: "miglaA_update_me", key:'miglaPayPalButtonChoice', value:'cssButton' },
        success: function(msg) {  
        }
   }); //ajax
   jQuery.ajax({
        type : "post",
        url :  miglaAdminAjax.ajaxurl, 
        data : {action: "miglaA_update_me", key:'migla_paypalcssbtnstyle', value:jQuery('#mg_CSSButtonPicker').val() },
        success: function(msg) {  
        }
   }); //ajax
   jQuery.ajax({
        type : "post",
        url :  miglaAdminAjax.ajaxurl, 
        data : {action: "miglaA_update_me", key:'migla_paypalcssbtntext', value:jQuery('#mg_CSSButtonText').val() },
        success: function(msg) {  
        }
   }); //ajax
   jQuery.ajax({
        type : "post",
        url :  miglaAdminAjax.ajaxurl, 
        data : {action: "miglaA_update_me", key:'migla_paypalcssbtnclass', value:jQuery('#mg_CSSButtonClass').val()},
        success: function(msg) {  
          saved('#miglaCSSButtonPickerSave');
        }
   }); //ajax
});


});