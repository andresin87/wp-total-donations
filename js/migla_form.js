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

jQuery(document).ready(
function() {

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

   jQuery('.miglaNAN').on('keydown', function (e){

       // Allow: backspace, delete, tab, escape, enter and .
        if (jQuery.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
             // Allow: Ctrl+A
            (e.keyCode == 65 && e.ctrlKey === true) || 
             // Allow: home, end, left, right
            (e.keyCode >= 35 && e.keyCode <= 39)) 
        {
                 // let it happen, don't do anything
                 return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
  });

var separator = String(jQuery('#miglaDecimalSep').val());
var separatorCode = separator.charCodeAt(0);
if( separatorCode == 46 ){ separatorCode = 190; }
//alert( separator  + " " + separatorCode);

   jQuery('.miglaNAD').on('keydown', function (e){
     var key = String.fromCharCode(e.which);
     var str = jQuery(this).val(); 

if( jQuery('#miglaShowDecimal').val() == 'yes' ){
       // Allow: backspace, delete, tab, escape, enter and .
        if (jQuery.inArray(e.keyCode, [ separatorCode , 8, 9, 27, 13 ]) !== -1 ||
             // Allow: Ctrl+A
            (e.keyCode == 65 && e.ctrlKey === true) || 
             // Allow: home, end, left, right
            (e.keyCode >= 35 && e.keyCode <= 39)) 
        {
                 // let it happen, don't do anything
             if( e.keyCode == separatorCode && str.indexOf(separator) >= 0  )
             { 
               e.preventDefault(); 
             }
                 return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
}else{
       // Allow: backspace, delete, tab, escape, enter and .
        if (jQuery.inArray(e.keyCode, [ 8, 9, 27, 13]) !== -1 ||
             // Allow: Ctrl+A
            (e.keyCode == 65 && e.ctrlKey === true) || 
             // Allow: home, end, left, right
            (e.keyCode >= 35 && e.keyCode <= 39)) 
        {
                 // let it happen, don't do anything
                 return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
}
  });

   jQuery('.miglaNQ').on('keydown', function (e){
     var key = String.fromCharCode(e.which);
     var str = jQuery(this).val(); 

       // Allow: backspace, delete, tab, escape, enter and .
        if (jQuery.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
             // Allow: Ctrl+A
            (e.keyCode == 65 && e.ctrlKey === true) || 
             // Allow: home, end, left, right
            (e.keyCode >= 35 && e.keyCode <= 39)) 
        {

                 return;
        }
        // Stop it when user press ' " / \
        if ( (e.keyCode==222) || (e.keyCode==220) || (e.keyCode==191)) 
        {
            e.preventDefault();
        }
  });

 //only accept numbers and aplhabet
  jQuery('.miglaNumAZ').on('keydown', function (e){
//alert('hi');
       // Allow: backspace, delete, tab, escape, enter and .
        if (jQuery.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
             // Allow: Ctrl+A
            (e.keyCode == 65 && e.ctrlKey === true) || 
             // Allow: home, end, left, right
            (e.keyCode >= 35 && e.keyCode <= 39)) 
        {
                 // let it happen, don't do anything
                 return;
        }
        // Ensure that it is a number and stop the keypress
        if ( (e.keyCode==222) || (e.keyCode==220) || (e.keyCode==191) 
         ) 
        {
            e.preventDefault();
        }
  });

})