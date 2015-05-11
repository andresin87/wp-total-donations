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