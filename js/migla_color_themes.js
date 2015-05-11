//THEME SETTINGS

function convertHex(hexa,opacity){
    var hexa = hexa.replace('#','');
    r = parseInt(hexa.substring(0,2), 16);
    g = parseInt(hexa.substring(2,4), 16);
    b = parseInt(hexa.substring(4,6), 16);

    result = 'rgba('+r+','+g+','+b+','+opacity/100+')';
    return result;
}

function changeBoxShadow(hshadow, vshadow, blur, spread, hex, opacity)
{
  var style = "";
  style = style + hshadow +"px "+ vshadow +"px "+ blur +"px "+ spread +"px " ;
  //style = style + convertHex(hex,opacity) + " inset" ;
  style = style + hex + " inset" ;
  return style;
}

jQuery(document).ready(
function() {
var effect = [ "yes", "yes", "yes", "yes"];

//alert("load ok");

jQuery('.spinner-up').click(function(){
  var parent = jQuery(this).closest('.input-group');
  var num = Number(parent.find('.spinner-input').val());
  if(  num < 10 ){
     num = num + 1;
     parent.find('.spinner-input').val(num);
     parent.find('.spinner-input').trigger('change');
  }
});

jQuery('.spinner-up2').click(function(){
  var parent = jQuery(this).closest('.input-group');
  var num = Number(parent.find('.spinner-input').val());
  if(  num < 10 ){
     num = num + 1;
     parent.find('.spinner-input').val(num);parent.find('.spinner-input').trigger('change');
  }
});


jQuery('.spinner-down').click(function(){
  var parent = jQuery(this).closest('.input-group');
  var num = Number(parent.find('.spinner-input').val());
  if(  num > 0 ){
     num = num - 1;
     parent.find('.spinner-input').val(num);parent.find('.spinner-input').trigger('change');
  }
});

//Changing the spinner
jQuery('#migla_radiustopleft').change(function(){
   jQuery('#me').css( '-webkit-border-top-left-radius' , jQuery(this).val()+"px" );
   jQuery('#me').css( '-moz-border-radius-topleft' , jQuery(this).val()+"px" );
   jQuery('#me').css( 'border-top-left-radius' , jQuery(this).val()+"px" );
});

jQuery('#migla_radiustopright').change(function(){
   jQuery('#me').css( '-webkit-border-top-right-radius' , jQuery(this).val()+"px" );
   jQuery('#me').css( '-moz-border-radius-topright' , jQuery(this).val()+"px" );
   jQuery('#me').css( 'border-top-right-radius' , jQuery(this).val()+"px" );
});

jQuery('#migla_radiusbottomleft').change(function(){
   jQuery('#me').css( '-webkit-border-bottom-left-radius' , jQuery(this).val()+"px" );
   jQuery('#me').css( '-moz-border-radius-bottomleft' , jQuery(this).val()+"px" );
   jQuery('#me').css( 'border-bottom-left-radius' , jQuery(this).val()+"px" );
});

jQuery('#migla_radiusbottomright').change(function(){
   jQuery('#me').css( '-webkit-border-bottom-right-radius' , jQuery(this).val()+"px" );
   jQuery('#me').css( '-moz-border-radius-bottomright' , jQuery(this).val()+"px" );
   jQuery('#me').css( 'border-bottom-right-radius' , jQuery(this).val()+"px" );
});

//Changing the color
jQuery('#migla_backgroundcolor').change(function(){
   var parent = jQuery(this).closest('div.row');
   jQuery(parent).find('#currentColor').css('background-color', jQuery(this).val());	 
});

jQuery('#migla_panelborder').change(function(){
   var parent = jQuery(this).closest('div.row');
   jQuery(parent).find('#currentColor').css('background-color', jQuery(this).val());	 
});

jQuery('#migla_bglevelcolor').change(function(){
   var parent = jQuery(this).closest('div.row');
   jQuery(parent).find('#currentColor').css('background-color', jQuery(this).val());	 
});

jQuery('#migla_borderlevelcolor').change(function(){
   var parent = jQuery(this).closest('div.row');
   jQuery(parent).find('#currentColor').css('background-color', jQuery(this).val());	 
});

jQuery('#migla_barcolor').change(function(){
   var parent = jQuery(this).closest('div.row');
   jQuery(parent).find('#currentColor').css('background-color', jQuery(this).val());	 
   jQuery('#div2previewbar').css( 'background-color' , jQuery(this).val() );
});

jQuery('#migla_wellcolor').change(function(){
   var parent = jQuery(this).closest('div.row');
   jQuery(parent).find('#currentColor').css('background-color', jQuery(this).val());	 
   jQuery('#me').css( 'background-color' , jQuery(this).val() );
});

jQuery('#migla_wellshadow').change(function(){
   var parent = jQuery(this).closest('div.row');
   jQuery(parent).find('#currentColor').css('background-color', jQuery(this).val());

   var newStyle = changeBoxShadow( jQuery('#migla_hshadow').val(), jQuery('#migla_vshadow').val(), 
               jQuery('#migla_blur').val(),  jQuery('#migla_spread').val(), jQuery(this).val(), 100);

   jQuery('#me').css( 'box-shadow' , newStyle );	
 
});

jQuery('#migla_hshadow').change(function(){
  var newStyle = changeBoxShadow( jQuery(this).val(), jQuery('#migla_vshadow').val(), jQuery('#migla_blur').val(), 
                jQuery('#migla_spread').val(), jQuery('#migla_wellshadow').val(), 100);
  jQuery('#me').css( 'box-shadow' , newStyle );
});

jQuery('#migla_vshadow').change(function(){
  var newStyle = changeBoxShadow( jQuery('#migla_hshadow').val(), jQuery(this).val(), jQuery('#migla_blur').val(), 
                jQuery('#migla_spread').val(), jQuery('#migla_wellshadow').val(), 100);
  jQuery('#me').css( 'box-shadow' , newStyle );
});

jQuery('#migla_blur').change(function(){
  var newStyle = changeBoxShadow( jQuery('#migla_hshadow').val(), jQuery('#migla_vshadow').val(), jQuery(this).val(), 
                jQuery('#migla_spread').val(), jQuery('#migla_wellshadow').val(), 100);
  jQuery('#me').css( 'box-shadow' , newStyle );
});

jQuery('#migla_spread').change(function(){
  var newStyle = changeBoxShadow( jQuery('#migla_hshadow').val(), jQuery('#migla_vshadow').val(), jQuery('#migla_blur').val(), 
                jQuery(this).val(), jQuery('#migla_wellshadow').val(), 100);
  jQuery('#me').css( 'box-shadow' , newStyle );
});

//Save changes
jQuery('.msave').click(function() {
    	        var id = '#' + jQuery(this).attr('id');
		var parent = jQuery(this).closest('div.row');
		var ColorCode = jQuery(parent).find('.rgba_value').val();
		//alert(ColorCode);

	   jQuery.ajax({
           type : "post",
           url :miglaAdminAjax.ajaxurl,
           data : {action: "miglaA_update_me", key:jQuery(this).attr('name'), value:ColorCode},
           success: function(msg) { 
             jQuery(parent).find('#currentColor').css('background-color', jQuery(parent).find('.mg-color-field').val());	 
             saved( id );
          }
      })  ; //ajax	 	
	
	});

jQuery('#migla_2ndbgcolorb').click(function(){
    var parent = jQuery(this).closest('div.row');
    var ColorCode = parent.find('.rgba_value').val() + "," + parent.find('.spinner-input').val();

    jQuery.ajax({
           type : "post",
           url :miglaAdminAjax.ajaxurl,
           data : {action: "miglaA_update_me", key:jQuery(this).attr('name'), value:ColorCode},
           success: function(msg) { 
             jQuery(parent).find('#currentColor').css('background-color', jQuery(parent).find('.mg-color-field').val());		 
             saved( '#migla_2ndbgcolorb' );
          }
      })  ; //ajax	 	

});

jQuery('#migla_borderRadius').click(function(){
    var parent = jQuery(this).closest('div.row');
    var border = "";
    border = border + parent.find('input[name=topleft]').val() + ",";
    border = border + parent.find('input[name=topright]').val() + ",";
    border = border + parent.find('input[name=bottomleft]').val() + ",";
    border = border + parent.find('input[name=bottomright]').val();

    jQuery.ajax({
           type : "post",
           url :miglaAdminAjax.ajaxurl,
           data : {action: "miglaA_update_me", key:'migla_borderRadius', value:border},
           success: function(msg) {  
             saved( '#migla_borderRadius' );
          }
      })  ; //ajax	

});

jQuery('#migla_wellboxshadow').click(function(){
    var parent = jQuery(this).closest('div.row');
    var well = "";
    well = parent.find('.rgba_value').val() + ",";
    well = well + parent.find('input[name=hshadow]').val() + ",";
    well = well + parent.find('input[name=vshadow]').val() + ",";
    well = well + parent.find('input[name=blur]').val() + ",";
    well = well + parent.find('input[name=spread]').val();

//alert(well);

    jQuery.ajax({
           type : "post",
           url :miglaAdminAjax.ajaxurl,
           data : {action: "miglaA_update_me", key:'migla_wellboxshadow', value:well},
           success: function(msg) {  
            jQuery(parent).find('#currentColor').css('background-color', jQuery(parent).find('.mg-color-field').val());	
            saved( '#migla_wellboxshadow' );
          }
      })  ; //ajax

});

	jQuery('#migla_progressbar_info').click(function() {

	   jQuery.ajax({
           type : "post",
           url :miglaAdminAjax.ajaxurl,
           data : {action: "miglaA_update_barinfo", key:'migla_progbar_info', value:jQuery('#migla_progressbar_text').val()},
           success: function(msg) { 
             saved('#migla_progressbar_info');
          }
          })  ; //ajax	 	
	
	});


     jQuery('.meffects').click(function() {
     
          var id = jQuery(this).attr('id');
          if( id == "inlineCheckbox1" ){
            jQuery('div.progress').toggleClass("striped");
          }
          if( id == "inlineCheckbox2" ){
            jQuery('div.progress').toggleClass("mg_pulse");
          }
          if( id == "inlineCheckbox3" ){
            jQuery('div.progress').toggleClass("animated-striped");
            jQuery('div.progress').toggleClass("active");
          }
          if( id == "inlineCheckbox4" ){
            jQuery('div.progress').toggleClass("mg_percentage");
          }
          
          var s = "no";var ps = "no"; var as = "no"; var pc = "no";

          if( jQuery("#inlineCheckbox1").is(":checked") ){
             s = "yes";
          }
          if( jQuery("#inlineCheckbox2").is(":checked") ){
             ps = "yes";
          }
          if( jQuery("#inlineCheckbox3").is(":checked") ){
             as = "yes";
          }
          if( jQuery("#inlineCheckbox4").is(":checked") ){
             pc = "yes";
          }


	  jQuery.ajax({
           type : "post",
           url :miglaAdminAjax.ajaxurl,
           data : {action: "miglaA_update_us", Stripes:s, Pulse:ps, AnimatedStripes:as, Percentage:pc},
            success: function(msg) { 

           }
          })  ; //ajax	
      });


jQuery('.mg-color-field').each(function(){
var rgb = ""; alpha = "";
var row = jQuery(this).closest('div.row');

                jQuery(this).minicolors({
                    control: jQuery(this).attr('data-control') || 'hue',
                    defaultValue: jQuery(this).attr('data-defaultValue') || '',
                    inline: jQuery(this).attr('data-inline') === 'true',
                    letterCase: jQuery(this).attr('data-letterCase') || 'lowercase',
                    opacity: jQuery(this).attr('data-opacity'),
                    position: jQuery(this).attr('data-position') || 'bottom left',
                    change: function(hex, opacity) {
                        if( !hex ) return;
                        if( opacity ) { 
                          hex += ',' + opacity; 
                          row.find('.rgba_value').val(hex);
                        }
                        if( typeof console === 'object' ) {
                            console.log(hex);
                        }
                    },
                    theme: 'bootstrap'
                });

});

////////////RESTORE/////////////////////////////////
jQuery('#miglaRestore').click(function(){
 jQuery.ajax({
	type : "post",
	url : miglaAdminAjax.ajaxurl, 
	data : {action: "miglaA_reset_theme"},
	success: function(msg) {
          //alert( msg );  
	  location.reload(true);
	}
 })  ; //ajax	   
});


 ////LEVEL SECTION
 jQuery('#migla_bgcolorLevelsSave').click(function(){

    var parent = jQuery(this).closest('div.row');
    var ColorCode = parent.find('#migla_bglevelcolor').val() ;

   jQuery.ajax({
	type : "post",
	url : miglaAdminAjax.ajaxurl, 
	data : {action: "miglaA_update_me", key:'migla_bglevelcolor', value:ColorCode },
	success: function(msg) {
          saved('#migla_bgcolorLevelsSave');
	}
    })  ; //ajax	
 });

 //LEVEL SECTION
 jQuery('#migla_borderlevelsave').click(function(){

    var parent = jQuery(this).closest('div.row');
    var ColorCode = parent.find('#migla_borderlevelcolor').val() ;
    var spinner = parent.find('.spinner-input').val();

   jQuery.ajax({
	type : "post",
	url : miglaAdminAjax.ajaxurl, 
	data : {action: "miglaA_update_me", key:'migla_borderlevelcolor', value:ColorCode },
	success: function(msg) {
	}
    })  ; //ajax

   jQuery.ajax({
	type : "post",
	url : miglaAdminAjax.ajaxurl, 
	data : {action: "miglaA_update_me", key:'migla_borderlevel', value:spinner  },
	success: function(msg) {
           saved('#migla_borderlevelsave');
	}
    })  ; //ajax	
 });


});