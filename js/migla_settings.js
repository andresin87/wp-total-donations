function get_tinymce_content(){
    if (jQuery("#wp-migla_editor-wrap").hasClass("tmce-active")){
        return tinyMCE.activeEditor.getContent();
    }else if (jQuery("#wp-migla_editor-wrap").hasClass("html-active")){
        return jQuery('#migla_editor').val();
    }
}

jQuery(document).ready(
function() {	


////////// Thank You Page ///////////////////////
    jQuery('#miglaThankPage').click(function() {

    jQuery.ajax({
	type : "post",
	url :  miglaAdminAjax.ajaxurl, 
	data : {action: "miglaA_update_me", key:'migla_thankyoupage', 
                value:  get_tinymce_content() },
	success: function(msg) { 
          saved( '#miglaThankPage' );
        }
      })  ; //ajax	
    });	
	
////////// Thank You Page Preview ///////////////////////
jQuery('#miglaThankPagePrev').click(function(){
    if( jQuery('div#migla_urlshortcode').text() === '' ){
	   alert('You must make a shortcode in page');
	}else{
	  // alert('submit');
	   jQuery('#miglaFormPreviewThank').submit();
	}
});
    
////////// Thank You email ///////////////////////
    jQuery('#miglaReplyTo').click(function() {

		  jQuery.ajax({
			type : "post",
			url :  miglaAdminAjax.ajaxurl, 
			data : {action: "miglaA_update_me", key:'migla_replyTo', 
                           value: jQuery('#miglaReplyToTxt').val()  
				   },
		        success: function(msg) { 
                          saved( '#miglaReplyTo' ); 
			}
		   })  ; //ajax		
    });	

    jQuery('#miglaReplyToName').click(function() {
  jQuery.ajax({
			type : "post",
			url :  miglaAdminAjax.ajaxurl, 
			data : {action: "miglaA_update_me", key:'migla_replyToName', 
                           value: jQuery('#miglaReplyToNameTxt').val()  },
			success: function(msg) {  
                           saved( '#miglaReplyToName' );
			}
		  })  ; //ajax		
    });	


    jQuery('#miglaThankEmail').click(function() {
       jQuery.ajax({type : "post",url :  miglaAdminAjax.ajaxurl, 
		data : {action: "miglaA_update_me", key:'migla_thankSbj', 
                value: jQuery("input[name='migla_thankSbj']").val()  },
		success: function(msg) {  }
      })  ; //ajax	
       jQuery.ajax({type : "post",url :  miglaAdminAjax.ajaxurl, 
		data : {action: "miglaA_update_me", key:'migla_thankBody', 
                value: jQuery('#miglaThankBody').val()  },
		success: function(msg) {  }
      })  ; //ajax
       jQuery.ajax({type : "post",url :  miglaAdminAjax.ajaxurl, 
		data : {action: "miglaA_update_me", key:'migla_thankRepeat', 
                value: jQuery("input[name='migla_thankRepeat']").val()  },
		success: function(msg) {  }
      })  ; //ajax
       jQuery.ajax({type : "post",url :  miglaAdminAjax.ajaxurl, 
		data : {action: "miglaA_update_me", key:'migla_thankAnon', 
                value: jQuery("input[name='migla_thankAnon']").val()  },
		success: function(msg) {  }
      })  ; //ajax	
       jQuery.ajax({type : "post",url :  miglaAdminAjax.ajaxurl, 
		data : {action: "miglaA_update_me", key:'migla_thankSig', 
                value: jQuery("input[name='migla_thankSig']").val()  },
		success: function(msg) { saved( '#miglaThankEmail' ); }
      })  ; //ajax

      	

    });	

///////// HONOREE EMAIL //////////////////////

    jQuery('#miglaHonoreEmail').click(function() {
       jQuery.ajax({type : "post",url :  miglaAdminAjax.ajaxurl, 
		data : {action: "miglaA_update_me", key:'migla_honoreESbj', 
                value: jQuery("input[name='migla_honoreESbj']").val()  },
		success: function(msg) {  }
      })  ; //ajax	
       jQuery.ajax({type : "post",url :  miglaAdminAjax.ajaxurl, 
		data : {action: "miglaA_update_me", key:'migla_honoreEBody', 
                value: jQuery('#migla_honoreEBody').val()  },
		success: function(msg) {  }
      })  ; //ajax
       jQuery.ajax({type : "post",url :  miglaAdminAjax.ajaxurl, 
		data : {action: "miglaA_update_me", key:'migla_honoreERepeat', 
                value: jQuery("input[name='migla_honoreERepeat']").val()  },
		success: function(msg) {  }
      })  ; //ajax
       jQuery.ajax({type : "post",url :  miglaAdminAjax.ajaxurl, 
		data : {action: "miglaA_update_me", key:'migla_honoreECustomIntro', 
                value: jQuery("input[name='migla_honoreECustomIntro']").val()  },
		success: function(msg) {  }
      })  ; //ajax
       jQuery.ajax({type : "post",url :  miglaAdminAjax.ajaxurl, 
		data : {action: "miglaA_update_me", key:'migla_honoreEAnon', 
                value: jQuery("input[name='migla_honoreEAnon']").val()  },
		success: function(msg) {  }
      })  ; //ajax	
       jQuery.ajax({type : "post",url :  miglaAdminAjax.ajaxurl, 
		data : {action: "miglaA_update_me", key:'migla_honoreESig', 
                value: jQuery("input[name='migla_honoreESig']").val()  },
		success: function(msg) { saved( '#miglaHonoreEmail' ); }
      })  ; //ajax	
      
       

    });	

////////// Test emails ///////////////////////
    jQuery('#miglaTestEmail').click(function() {
      if( jQuery('#miglaTestEmailAdd').val() == '' ){ alert("Please input the email address"); }else{
		  jQuery.ajax({
			type : "post",
			url :  miglaAdminAjax.ajaxurl, 
			data : {action: "miglaA_test_email" , email:jQuery('#miglaReplyToTxt').val(),
                                emailname:jQuery('#miglaReplyToNameTxt').val() ,
                                testemail:jQuery('#miglaTestEmailAdd').val() ,
                        },
			success: function(msg) { 
                          alert(msg); 
                        }
		  })  ; //ajax		
     }
    });	    

    jQuery('#miglaTestHEmail').click(function() {
      if( jQuery('#miglaTestHEmailAdd').val() == '' ){ alert("Please input the email address"); }else{
		  jQuery.ajax({
			type : "post",
			url :  miglaAdminAjax.ajaxurl, 
			data : {action: "miglaA_test_hEmail" , email:jQuery('#miglaReplyToTxt').val(),
                                emailname:jQuery('#miglaReplyToNameTxt').val() ,
                                testemail:jQuery('#miglaTestHEmailAdd').val() ,
                        },
			success: function(msg) { 
                          alert(msg); 
                        }
		  })  ; //ajax		
     }
    });	 
    
////////// Notification emails ///////////////////////

    jQuery('#miglaUpdateNotifEmails').click(function() {
		  jQuery.ajax({
			type : "post",
			url :  miglaAdminAjax.ajaxurl, 
			data : {action: "miglaA_update_me", key:'migla_notif_emails', value: jQuery('#miglaNotifEmails').val() },
			success: function(msg) {  
                         saved('#miglaUpdateNotifEmails');
			}
		  })  ; //ajax	
       	
    });	




    jQuery('#miglaSetTimezoneButton').click(function() {
      var id = '#' + jQuery(this).attr('id');
	  //alert(jQuery('select[name=miglaDefaultTimezone] option:selected').val());
	  jQuery.ajax({
		type : "post",
		url : miglaAdminAjax.ajaxurl, 
		data : {action: "miglaA_update_me", key:'migla_default_timezone', 
		    value: jQuery('select[name=miglaDefaultTimezone] option:selected').val() },
		success: function(msg) {  
                    saved(id);
		}
	  })  ; //ajax	
	
	  jQuery.ajax({
		type : "post",
		url : miglaAdminAjax.ajaxurl, 
		data : {action: "miglaA_currentTime", timezone: jQuery('select[name=miglaDefaultTimezone] option:selected').val() },
		success: function(msg) { 
                    jQuery('#migla_current_time').text(""); 
                    jQuery('#migla_current_time').text(msg); 
                    //saved(id);
		}
	  })  ; //ajax	
    });	


});

