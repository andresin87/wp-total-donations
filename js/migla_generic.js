//jQuery = jQuery.noConflict();

function saved( me )
{
   jQuery(me).html('Saved');
   setTimeout(function (){
     jQuery(me).html(' ' + jQuery(me).data('oldtext') );
   }, 800);
} 

function canceled( me )
{
   jQuery(me).html('canceled');
   setTimeout(function (){
     jQuery(me).html(' ' + jQuery(me).data('oldtext') );
   }, 800);
} 

function canceledLoser( me , oldtext)
{
   jQuery(me).html('canceled');
   setTimeout(function (){
     jQuery(me).html( oldtext );
   }, 800);
} 


jQuery(document).ready(
function() { 

jQuery('.fa-caret-down').click( function(){
  var p = jQuery(this).closest('section');
  //p.find('.panel-body').slideToggle( 'slow' );
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


   jQuery('.miglaNAD').on('keydown', function (e){

       // Allow: backspace, delete, tab, escape, enter and .
        if (jQuery.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
             // Allow: Ctrl+A
            (e.keyCode == 65 && e.ctrlKey === true) || 
             // Allow: home, end, left, right
            (e.keyCode >= 35 && e.keyCode <= 39)) 
        {
             // let it happen, don't do anything
             if( (e.keyCode==190) && ( str.indexOf(".") >= 0 ) )
             { 
               e.preventDefault(); 
             }
                 return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
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
        if ( (e.keyCode >= 33 && e.keyCode <= 39) || (e.keyCode == 60) ||  (e.keyCode == 62) || (e.keyCode == 92) 
         ) 
        {
            e.preventDefault();
        }
  });


   jQuery('.pbutton').click(function(e){
      var me = jQuery(this); 
      //alert( me.html() );
      me.data( 'oldtext', me.html() );
      me.text('Saving...'); jQuery("<i class='fa fa-fw fa-spinner fa-spin'></i>" ).prependTo( me ); 
   })

});
