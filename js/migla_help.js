jQuery(document).ready(function() {

 jQuery('.mg-settings').change(function(){

   var id = jQuery(this).attr('id');
   var msg = '#' + id + '_';
   var inputValue = 'no';

   if( jQuery(this).is(':checked') ){ inputValue = 'yes' }

   jQuery.ajax({
	type : "post",
	url : miglaAdminAjax.ajaxurl, 
	data : {action: "miglaA_update_me", key:id, value:inputValue },
	success: function() {
         
         jQuery(msg).text('Saved'); 
         setTimeout(function (){
           jQuery(msg).text(''); 
         }, 1000);

	}
    })  ; //ajax	   

 });

 jQuery('#migla_ajax_caller_setting').change(function(){

   var inputValue = 'td';
   if( jQuery(this).is(':checked') ){ inputValue = 'wp' }

   jQuery.ajax({
	type : "post",
	url : miglaAdminAjax.ajaxurl, 
	data : {action: "miglaA_update_me", key:'migla_ajax_caller', value:inputValue },
	success: function() {
           jQuery('#migla_ajax_caller_setting_').text('Saved'); 
           setTimeout(function (){
               jQuery('#migla_ajax_caller_setting_').text(''); 
           }, 1000);
	}
    })  ; //ajax	   

 });

 jQuery('#migla_allow_cors_setting').change(function(){
   var inputValue = 'no';
   if( jQuery(this).is(':checked') ){ inputValue = 'yes' }

   jQuery.ajax({
	type : "post",
	url : miglaAdminAjax.ajaxurl, 
	data : {action: "miglaA_update_me", key:'migla_allow_cors', value:inputValue },
	success: function() {
           jQuery('#migla_allow_cors_setting_').text('Saved'); 
           setTimeout(function (){
               jQuery('#migla_allow_cors_setting_').text(''); 
           }, 1000);
	}
    })  ; //ajax	
 });

 jQuery('#miglaEraseCache').click(function(){

      var me = jQuery(this); 
      me.data( 'oldtext', me.html() );

   jQuery.ajax({
	type : "post",
	url : miglaAdminAjax.ajaxurl, 
	data : {action: "miglaA_purgeCache" },
	success: function(m) {
           alert(m);
          
              jQuery(me).html('Saved');
                setTimeout(function (){
                jQuery(me).html(' ' + jQuery(me).data('oldtext') );
              }, 800);
	}
    })  ; //ajax	
 });

});