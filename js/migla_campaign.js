//jQuery = jQuery.noConflict();

var del;
var updatedList = [];

function countAll(){
var c = -1;
jQuery('li.formfield').each(function(){ c = c + 1; });
return c;
}

function addCampaign( label, target ){

   var newComer = "";
   if( target == '' ){
     target = 0;
   }
   var lbl = label.replace("'", "[q]");

   newComer = newComer + "<li class='ui-state-default formfield clearfix'>";

	newComer = newComer + "<input type='hidden' name='oldlabel' value='"+lbl+"' />";
	newComer = newComer + "<input type='hidden' name='label' value='"+lbl+"' />";
	newComer = newComer + "<input type='hidden' name='target' value='"+target+"' />";
	newComer = newComer + "<input type='hidden' name='show'  value='1' />";

   newComer = newComer + "<div class='col-sm-1 hidden-xs'><label  class='control-label'>Campaign</label></div>";
   newComer = newComer + "<div class='col-sm-3 col-xs-12'><input type='text' class='labelChange' name='' placeholder='";

   newComer = newComer + lbl + "' value='" + lbl + "' /></div>";

   newComer = newComer + "<div class='col-sm-1 hidden-xs'><label  class='control-label'>Target</label></div>";
   newComer = newComer + "<div class='col-sm-2 col-xs-12'><input type='text' class='targetChange miglaNAD' name='' placeholder='";
   newComer = newComer + target + "' value='" + target + "' /></div>";
   var c = countAll(); c = c + 1;
   newComer = newComer + "<div class='control-radio-sortable col-sm-5 col-xs-12'>";
   newComer = newComer + "<span><label><input type='radio' name=r'"+c+"'  value='1' checked='checked' > Show</label></span>";
   newComer = newComer + "<span><label><input type='radio' name=r'"+c+"'  value='-1' > Deactived</label></span>";

   newComer = newComer + "<span><button class='removeField' data-toggle='modal' data-target='#confirm-delete'><i class='fa fa-fw fa-trash'></i></button></span>";
   newComer = newComer + "</div>";

   newComer = newComer + "</li>";

   return newComer;
}

/////////GET RID THE FORBIDDEN CHAR/////////////
function getRidForbiddenChars(){
  jQuery('li.formfield').each(function() { 
     var lbl = jQuery(this).find("input[name=label]").val();
     var r = lbl.replace("[q]","'");
     jQuery(this).find(".labelChange").val( r ); 

     var target = jQuery(this).find("input[name=target]").val();
     jQuery(this).find(".targetChange").val( target ); 

     var show = jQuery(this).find("input[name=show]").val();
     jQuery(this).find("input[value='"+show+"']").prop('checked',true); 

  });
}

function getFormStructure(){
   var fields = []; updatedList.length = 0;
   var c = 0;

   jQuery('li.formfield').each(function(){
      var item = {};

      var temp = jQuery(this).find('.labelChange').val();      
      item.name = temp.replace("'", "[q]");
      var target = String(jQuery(this).find('.targetChange').val());
      if( target == '' ){
        jQuery(this).find('.targetChange').val('0');
        target = 0;
      }
      item.target = target; 
      item.show = jQuery(this).find("input[type='radio']:checked").val();  
      
      if( item.name != jQuery(this).find('input[name=oldlabel]').val() ){
         updatedList.push( jQuery(this).find('input[name=oldlabel]').val()+"-**-"+item.name );
      }

      fields.push(item);
      //alert(item.show);
      c = c + 1;
  });
   
//alert( JSON.stringify(fields) );

   return fields;
}

function remove(){
  jQuery('.removeField').click(function(){
    del = jQuery(this).closest('li.formfield');   
  });

  jQuery('#mRemove').click(function(){
     del.remove();
  
      var list = getFormStructure();    
       jQuery.ajax({
        type : "post",
        url :  miglaAdminAjax.ajaxurl, 
        data : {action: "miglaA_save_campaign", values:list},
        success: function(msg) {  
            jQuery( ".close" ).trigger( "click" );
         }
       }) ; //ajax

  });
}

////////////KEYUP AND CHANGE/////////////////
function labelChanged(){
  jQuery('.labelChange').bind("keyup change", function(e) {
   var p = jQuery(this).closest('li.formfield');

   var val = jQuery(this).val().replace("'", "[q]");
   p.find("input[name='label']").val( val );
  });
}

function targetChanged(){
  jQuery('.targetChange').bind("keyup", function(e) {
   var p = jQuery(this).closest('li.formfield');

   p.find("input[name='target']").val( val );
  });
}
/////////////////////////////////////////////////

jQuery(document).ready(
function() {    

//alert("Load OK");

   //Open Tab
    jQuery('#campaign-fa').click(function() {
       jQuery('#panel-addcampaign').toggle();
    });	 

getRidForbiddenChars();
labelChanged(); targetChanged();
remove();

jQuery('#mName').val('');
jQuery('#mAmount').val('0');

jQuery('#miglaAddCampaign').click(function() {
  
  var name = jQuery('#mName').val();
  var target = jQuery('#mAmount').val();
  var str = addCampaign( name , target );

  if( countAll() < 0 ){
     jQuery('ul.rows').empty();
  }
  jQuery(str).prependTo( jQuery('ul.rows') );

  var t = getFormStructure();

//alert(JSON.stringify(t));
    
   jQuery.ajax({
        type : "post",
        url :  miglaAdminAjax.ajaxurl, 
        data : {action: "miglaA_save_campaign", values:t, update:updatedList},
        success: function(msg) {  
          //alert(JSON.stringify(msg));
          remove();
          getRidForbiddenChars();
          labelChanged(); targetChanged();
          saved( '#miglaAddCampaign' );
        }
   })  ; //ajax

});

//Add Campaign
jQuery('#miglaSaveCampaign').click(function() {
  var list = getFormStructure();   

  //alert( JSON.stringify(updatedList) ); 

   jQuery.ajax({
        type : "post",
        url :  miglaAdminAjax.ajaxurl, 
        data : {action: "miglaA_save_campaign", values:list, update:updatedList},
        success: function(msg) {  
          //alert(msg); 
          labelChanged(); targetChanged();
         saved( '#miglaSaveCampaign' );
        }
   })  ; //ajax
});


jQuery("input[type='radio']").each(function(){
var parent = jQuery(this).closest('.formfield');
 
   jQuery(this).click(function(){
      if( jQuery(this).val()== "-1" )
      {
        //alert("clicked");
        parent.addClass('pink-highlight');
      }else{
        parent.removeClass('pink-highlight');
      }
   });

})

jQuery(("input[type='radio']:checked")).each(function(){
var parent = jQuery(this).closest('.formfield');
      if( jQuery(this).val()== "-1" )
      {
        parent.addClass('pink-highlight');
      }else{
        parent.removeClass('pink-highlight');
      }
})

	
});

