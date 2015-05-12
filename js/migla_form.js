//jQuery = jQuery.noConflict();

function getRidForbidden(){
 jQuery('#migla_donation_form').find('.migla-panel').each(function(){
  jQuery(this).find('.form-group').each(function(){
     var type = jQuery(this).find("input").attr('type'); 
     var val = "";
          
     if( type == 'text' ){ //text
       val = jQuery(this).find("input").attr('placeholder');
       jQuery(this).find("input").attr('placeholder', val.replace("[q]", "'") );
     }
  });
 });
}

var tab_bgcolor; var tab_border;

jQuery(document).ready( function() {


//This is for tab yeah
  tab_bgcolor = jQuery('.mg_tab-content').find('.mg_active').css('background-color');
  tab_border  = jQuery('.mg_tab-content').find('.mg_active').css('border');
  tabcolor1  = jQuery('#_sectionPaypal').css('background-color');
  tabcolor2  = jQuery('#_sectionStripe').css('background-color');

  jQuery('.mg_nav li').click(function(){

      var id = jQuery(this).find('a').attr('id');
      var $this = jQuery(this);
     // alert(id);

          jQuery('.mg_nav li').each(function(){
               jQuery(this).removeClass('mg_active');
          });

      if( id == '_sectionStripe' ){
          $this.addClass('mg_active');
          jQuery('.mg_tab-content').find('#sectionStripe').addClass('mg_active');
          jQuery('.mg_tab-content').find('#sectionStripe').css('background-color', tab_bgcolor);
          jQuery('.mg_tab-content').find('#sectionStripe').css('border', tab_border);

          jQuery('#_sectionStripe').css('background-color', tabcolor1);
          jQuery('#_sectionPaypal').css('background-color', tabcolor2);

          jQuery('.mg_tab-content').find('#sectionPaypal').removeClass('mg_active');
      }else{
          $this.addClass('mg_active');
          jQuery('.mg_tab-content').find('#sectionStripe').removeClass('mg_active');

          jQuery('.mg_tab-content').find('#sectionPaypal').addClass('mg_active');
          jQuery('.mg_tab-content').find('#sectionPaypal').css('background-color', tab_bgcolor);
          jQuery('.mg_tab-content').find('#sectionPaypal').css('border', tab_border);

          jQuery('#_sectionStripe').css('background-color', tabcolor2);
          jQuery('#_sectionPaypal').css('background-color', tabcolor1);
      }
  });



getRidForbidden();

   jQuery('.miglaNAD2').on('keypress', function (e){ 
     var str = jQuery(this).val(); 
     var separator = jQuery('#miglaDecimalSep').val();
     var key = String.fromCharCode(e.which);

     // Allow: backspace, delete, escape, enter
     if (jQuery.inArray( e.which, [ 8, 0, 27, 13]) !== -1 ||
        jQuery.inArray( key, [ '0', '1', '2', '3', '4', '5', '6', '7', '8', '9' ]) !== -1 ||
        ( key == separator )
     )
     {
        if( key == separator  ){
          
           if(jQuery('#miglaShowDecimal').val()=='yes'){
             if( ( str.indexOf(separator) >= 0 ) ){
               e.preventDefault();
             }else{
               return;
             }
           }else{
              e.preventDefault();
           }
        }

     }else{
        e.preventDefault();
     }
  });

//someone click custom amount
jQuery('.migla_amount_choice').click(function(){
   if( jQuery(this).val() == 'custom' ){
     jQuery('#miglaCustomAmount').focus();
   }
});

jQuery('#miglaCustomAmount').click(function(){
    jQuery('.migla_custom_amount').attr("checked", "checked");
});

//honoree
jQuery('#memorialgift').click(function(){
  if( jQuery('#memorialgift').is(':checked') ){
     jQuery('#honoreeemail').attr("disabled", "disabled"); 
     jQuery('#honoreeletter').attr("disabled", "disabled"); 
     jQuery('#honoreeaddress').attr("disabled", "disabled");
     jQuery('#honoreecountry').attr("disabled", "disabled"); 
     jQuery('select[name=miglad_honoreestate]').attr("disabled", "disabled"); 
     jQuery('select[name=miglad_honoreeprovince]').attr("disabled", "disabled"); 
     jQuery('#honoreecity').attr("disabled", "disabled"); 
     jQuery('#honoreepostalcode').attr("disabled", "disabled");
 
     jQuery('#honoreeemail').val(""); 
     jQuery('#honoreeletter').val(""); 
     jQuery('#honoreeaddress').val("");
     jQuery('#honoreecity').val(""); 
     jQuery('#honoreepostalcode').val(""); 

  }else{
     jQuery('#honoreeemail').removeAttr("disabled"); 
     jQuery('#honoreeletter').removeAttr("disabled"); 
     jQuery('#honoreecountry').removeAttr("disabled");
     jQuery('#honoreeaddress').removeAttr("disabled");
     jQuery('select[name=miglad_honoreestate]').removeAttr("disabled"); 
     jQuery('select[name=miglad_honoreeprovince]').removeAttr("disabled");
     jQuery('#honoreecity').removeAttr("disabled"); 
     jQuery('#honoreepostalcode').removeAttr("disabled");
  }
}); 

//DONOR
	  if( jQuery('select[name=miglad_country] option:selected').text() == 'United States' ){
	    jQuery('#state').show();
	    jQuery('#province').hide();
	  }else{
	    if( jQuery('#state').is(':visible') ) 
		{ 
	      jQuery('#state').hide();	  
		}
	  }
	  
          if( jQuery('select[name=miglad_country] option:selected').text() == 'Canada' ){
	    jQuery('#state').hide();
		jQuery('#province').show();
	  }else{
	    if( jQuery('#province').is(':visible') ) 
		{ 
	      jQuery('#province').hide();	  
		}
	  }

	jQuery('#country').change(function (e){
	  if( jQuery('select[name=miglad_country] option:selected').text() == 'United States' ){
	    jQuery('#state').show();
	    jQuery('#province').hide();
	  }else{
	    if( jQuery('#state').is(':visible') ) 
		{ 
	      jQuery('#state').hide();	  
		}
	  }
	  
          if( jQuery('select[name=miglad_country] option:selected').text() == 'Canada' ){
	    jQuery('#state').hide();
		jQuery('#province').show();
	  }else{
	    if( jQuery('#province').is(':visible') ) 
		{ 
	      jQuery('#province').hide();	  
		}
	  }
	 });	
	 
//HONOREE
	  if( jQuery('select[name=miglad_honoreecountry] option:selected').text() == 'United States' ){
	    jQuery('#honoreestate').show();
	    jQuery('#honoreeprovince').hide();
	  }else{
	    if( jQuery('#honoreestate').is(':visible') ) 
		{ 
	      jQuery('#honoreestate').hide();	  
		}
	  }
	  
          if( jQuery('select[name=miglad_honoreecountry] option:selected').text() == 'Canada' ){
	    jQuery('#honoreestate').hide();
		jQuery('#honoreeprovince').show();
	  }else{
	    if( jQuery('#honoreeprovince').is(':visible') ) 
		{ 
	      jQuery('#honoreeprovince').hide();	  
		}
	  }

	jQuery('#honoreecountry').change(function (e){
	  if( jQuery('select[name=miglad_honoreecountry] option:selected').text() == 'United States' ){
	    jQuery('#honoreestate').show();
	    jQuery('#honoreeprovince').hide();
	  }else{
	    if( jQuery('#honoreestate').is(':visible') ) 
		{ 
	      jQuery('#honoreestate').hide();	  
		}
	  }
	  
          if( jQuery('select[name=miglad_honoreecountry] option:selected').text() == 'Canada' ){
	    jQuery('#honoreestate').hide();
		jQuery('#honoreeprovince').show();
	  }else{
	    if( jQuery('#honoreeprovince').is(':visible') ) 
		{ 
	      jQuery('#honoreeprovince').hide();	  
		}
	  }
	 });		  
	  
//Campaign
jQuery('#miglaform_campaign').change(function(){
    var c = jQuery('select[name=campaign] option:selected').text();
    jQuery('#migla_bar').empty();

    //if( c == 'Undesignated' ){ }else{
    //alert(c);
    var temp = c.replace("'", "[q]");
    
    jQuery.ajax({
        type : "post",
        url :  miglaAdminAjax.ajaxurl, 
        data : {action: "miglaA_draw_progress_bar", cname:temp, posttype:""},
        success: function(msg) {
             //alert(msg);    
            jQuery(msg).appendTo( jQuery('#migla_bar'));
        }
       }); //ajax 
    //} //else
 });

    var c = jQuery('select[name=campaign] option:selected').text();
    jQuery('#migla_bar').empty();
    if( c == 'Undesignated' ){ }else{
    //alert(c);
    var temp = c.replace("'", "[q]");
    
    jQuery.ajax({
        type : "post",
        url :  miglaAdminAjax.ajaxurl, 
        data : {action: "miglaA_draw_progress_bar", cname:temp, posttype:""},
        success: function(msg) {
             //alert(msg);    
            jQuery(msg).appendTo( jQuery('#migla_bar'));
        }
       }); //ajax 
    }

//Toggle
jQuery('.mtoggle').each(function(){
  jQuery(this).prop("checked", false);
});

jQuery('input[type=text]').each(function(){
  jQuery(this).val('');
});

jQuery('input[type=textarea]').each(function(){
  jQuery(this).val('');
});


jQuery('.mtoggle').click(function(){
   var p = jQuery(this).closest('.migla-panel');
   p.find('.migla-panel-body').toggle();
});


  //Detect credit card number
  jQuery('#mg_stripe_card_number').on('keyup', function (e){ 
     var key = jQuery('#mg_stripe_card_number').val();
     var firstDigit = key.slice( 0, 1 );
         
      if( firstDigit == "3") {
          jQuery('#mg_cc_number').html('travel/entertainment cards');
      }else if( firstDigit == "4"){
          jQuery('#mg_cc_number').html('Visa');
      }else if( firstDigit == "5" ){
          jQuery('#mg_cc_number').html('Master Card');
      }else if( firstDigit == "6" ){
          jQuery('#mg_cc_number').html('Discover Card');
      }
  });


})