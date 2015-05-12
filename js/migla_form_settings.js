var changed_fields = [];

//This is for reset the event binding
jQuery.fn.once = function(a, b) {
    return this.each(function() {
        jQuery(this).off(a).on(a,b);
    });
};

function mg_getRandomInt(min, max) {
    return Math.floor(Math.random() * (max - min + 1)) + min;
}

function mg_random(){
  var date = new Date();
  var day        = String( date.getDate() );
  var monthIndex = String( date.getMonth() );
  var year       = String( date.getFullYear() );
  var hours      = String( date.getHours() );
  var minutes    = String( date.getMinutes() );
  var seconds    = String( date.getSeconds() );
  var rand_value = year + monthIndex + day + hours + minutes + seconds  + "_" + String(  mg_getRandomInt( 100000, 100000000) ); 
  return rand_value;
}

function mg_generate_uid(){
  var date = new Date();
  var day        = String( date.getDate() );
  var monthIndex = String( date.getMonth() );
  var year       = String( date.getFullYear() );
  var hours      = String( date.getHours() );
  var minutes    = String( date.getMinutes() );
  var seconds    = String( date.getSeconds() );

  var dd = "f" + year + monthIndex + day + hours + minutes + seconds  + "_" + String(  mg_getRandomInt( 100000, 100000000) ); 

  return dd;
}

/******PLANS**********************************************************************************************/
function mg_get_structure(){
  var list = []; var i = 0;

  jQuery('#section4').find('li.mg_reoccuring-field').each(function(){
    var item = {};
         item.name           = jQuery(this).find(".name").val();
         item.id             = jQuery(this).find(".old_id").val();
         item.interval_count = jQuery(this).find(".time").val();
         item.interval       = jQuery(this).find(".period").val();

         var pid             = "#" + jQuery(this).find(".method").attr('id');
         item.payment_method = jQuery(pid).find(":selected").val();

         item.status         = jQuery(this).find("input[name='status" + item.id + "']:checked").val();
         list.push(item);
    i++;
  });
  return list;
}

function remove_plan(){

  jQuery('.removePlanField').click( function(){

   var parent  = jQuery(this).closest('li.mg_reoccuring-field');
   var plan_id = parent.find('.old_id').val();
   
    var answer = true;
    var method    = parent.find(".method").val();

  //This is change on interval and interval count
    if( method == 'paypal-stripe' || method == 'stripe' ){
       answer = confirm( jQuery('#mg_recurring_warning2').html() );
    }

   if( answer )
   {
      if( method == 'paypal-stripe' || method == 'stripe' )
      {
         //Delete the current plan
         jQuery.ajax({
             type : "post",
             url :  miglaAdminAjax.ajaxurl, 
             data : {action: "miglaA_stripe_deletePlan", id:plan_id },
             success: function(msg1) {
                 if( msg1 == "1" || ( msg1.search("No such plan") >= 0 ) ){

                     parent.remove();
                      //Ok save the structure
                      var send_list = [];
                      send_list = mg_get_structure();

                      //alert( JSON.stringify(send_list) );

                     jQuery.ajax({
                         type : "post",
                         url :  miglaAdminAjax.ajaxurl, 
                         data : {action: "miglaA_update_me", key:'migla_recurring_plans' , value:send_list },
                         success: function(msg2) { 
                         }
                     }); //ajax 

                 }else{
                      alert( msg1 );
                 }
             }
          }); //ajax
 
      }else{

        parent.remove();
         //Ok save the structure
         var send_list = [];
         send_list = mg_get_structure();

         //alert( JSON.stringify(send_list) );

          jQuery.ajax({
              type : "post",
              url :  miglaAdminAjax.ajaxurl, 
              data : {action: "miglaA_update_me", key:'migla_recurring_plans' , value:send_list },
              success: function(msg2) { 
              }
          }); //ajax 
      }
  }//If answer yes


  }); //Click
}


function mg_save_row_plan(){

  jQuery(".migla_save_row_plan").bind( "click", function(){

      var this_id   = '#' + jQuery(this).attr('id');
      var parent    = jQuery(this).closest('li.mg_reoccuring-field');

      var name      = parent.find('.name').val();
      var planid    = parent.find('.old_id').val();
      var time      = parent.find('.time').val();
      var period    = parent.find(".period").val();
      var status    = parent.find("input[name='status" + planid + "']:checked").val();

        var pid       = "#" + parent.find(".method").attr('id');
      var method    = jQuery(pid).val();


      var _name     = parent.find('.old_name').val();
      var _id       = parent.find('.old_id').val();
      var _time     = parent.find('.old_time').val();
      var _period   = parent.find('.old_period').val();
      var _status   = parent.find('.old_status').val();
      var _method   = parent.find('.old_method').val();
    
      var send_list = [];
      send_list = mg_get_structure();

    //alert( name+planid+time+period+method+status +" "+_name+_id+_time+_period+_method+_status );

    var answer = true;

    //This is change on interval and interval count
    if( _method == 'paypal-stripe' && method == 'paypal-stripe' ){
         if( ( _time != time ) || period != _period ){
             answer = confirm( jQuery('#mg_recurring_warning3').html() );
         }else if(  _name != name ){
             answer = confirm( jQuery('#mg_recurring_warning4').html() );
         }
    }
    if( _method == 'stripe' && method == 'stripe' ){
         if( ( _time != time ) || period != _period ){
             answer = confirm( jQuery('#mg_recurring_warning3').html() );
         }else if(  _name != name ){
             answer = confirm( jQuery('#mg_recurring_warning4').html() );
         }
    }

    //From paypal to stripe addition
    if( _method == 'paypal' && method == 'paypal-stripe' ){
           answer = confirm( jQuery('#mg_recurring_warning1').html() );
    }

    if( _method == 'paypal' && method == 'stripe' ){
           answer = confirm( jQuery('#mg_recurring_warning1').html() );
    }

    //From stripe to paypal only
    if( _method == 'paypal-stripe' && method == 'paypal' ){
            answer = confirm( jQuery('#mg_recurring_warning2').html() );
    }
    if( _method == 'stripe' && method == 'paypal' ){
            answer = confirm( jQuery('#mg_recurring_warning2').html() );
    }
      
   if( answer )
   {
      jQuery.ajax({
        type : "post",
        url :  miglaAdminAjax.ajaxurl, 
        data : {action: "miglaA_update_recurring_plans",
                new_id       : planid        ,  old_id       : _id,
                new_name     : name          ,  old_name     : _name ,
                new_interval : period        ,  old_interval : _period,
                new_interval_count : time    ,  old_interval_count : _time ,
                new_payment_method : method ,  old_payment_method : _method ,
                new_status : status          ,  old_status   : _status,
                list:send_list 
               },
        success: function(msg) { 
              if( msg == "1" ){
                 //alert("Data is updated");  
                 parent.find('.old_name').val( name );
                 parent.find('.old_time').val( time );
                 parent.find('.old_period').val( period );
                 parent.find('.old_method').val( method );
                 parent.find('.old_status').val( _status );

                 jQuery(this_id).html('Saved');
                      setTimeout(function (){
                      jQuery(this_id).html("<i class='fa fa-fw fa-save'></i> save" );
                 }, 800);

                 if( method == 'paypal-stripe' || method == 'stripe'){
                      parent.find('.time').prop('disabled', true);
                      parent.find('.spinner-down').prop('disabled', true);
                      parent.find('.spinner-up').prop('disabled', true);
                      parent.find(".period").prop('disabled', true);
                      parent.find(".method").prop('disabled', true);
                 }
              }else{
                 jQuery(this_id).html('Canceled');
                      setTimeout(function (){
                      jQuery(this_id).html("<i class='fa fa-fw fa-save'></i> save" );
                 }, 800);

                 alert(msg);
              } 
        }
      }); //ajax 

   }else{
      parent.find('.name').val( _name );
      parent.find('.time').val( _time );
      parent.find(".period").val( _period );

      var pid2       = "#" + parent.find(".method").attr('id');
      jQuery( pid2 + " option[value='"+ _method +"']").attr('selected', 'selected'); 
       
      parent.find(".status[value='"+ _status +"']").attr("checked","checked");   

                 jQuery(this_id).html('Canceled');
                      setTimeout(function (){
                      jQuery(this_id).html("<i class='fa fa-fw fa-save'></i> save" );
                 }, 800); 
   }

 }); 
}

/*****************************************************************************************************/


var radioState = {}; var currencies = []; var showDec ;
var tempid = -1; var btnid = 0;


/************ CUSTOM LIST  ***************************/
 function mg_save_custom_list( flag ){

    var data_obj = ""; //alert(jQuery('.mg_list_row').length);

   if( jQuery('.mg_custom_list_row').length > 0  ){

     jQuery('.mg_custom_list_row').each(function(){
          data_obj = data_obj + jQuery(this).find('.mg_customlist_key').val() + "::" + jQuery(this).find('.mg_customlist_val').val() + ";";
     });   

     //data_obj = data_obj.slice(0, data_obj.length - 1 );

     //alert( JSON.stringify(data_obj) );

     jQuery.ajax({
        type : "post",
        url :  miglaAdminAjax.ajaxurl, 
        data : {action: "miglaA_update_postmeta", key:jQuery('#mg_id_custom_values_edit').text(), id:jQuery('#migla_custom_values_id').val(),
             value:data_obj },
        success: function() {  

        }
     }); //ajax

   }else{

    jQuery.ajax({
        type : "post",
        url :  miglaAdminAjax.ajaxurl, 
        data : {action: "miglaA_update_postmeta", key:jQuery('#mg_id_custom_values_edit').text(), id:jQuery('#migla_custom_values_id').val(),
             value:"" },
        success: function() { 

        }
     }); //ajax

   }
 }

function mg_delete_custom_list(){
   jQuery('.mg_customlist_remove').bind( "click", function() {
       var par = jQuery(this).closest(".mg_custom_list_row"); 
       par.remove();
   });
}

///////////////////////////////////////////////////////////////////////////////////////////////////
function drawLevel( key, amount ){
   var str = '';
   var decimal = jQuery('#sep2').text();
   str = str + "<p id='amount"+key+"'>";
   str = str + "<input class='value' type=hidden id='"+ amount +"' value='"+ amount +"' />";
   str = str + "<label>" + amount.replace(".", decimal ) + "</label>";			   
   str = str + "<button name='miglaAmounts' class='miglaRemoveLevel obutton'><i class='fa fa-times'></i></button>";
   str = str + "</p>";
  return str;
}

function remove(){
	jQuery('.miglaRemoveLevel').click( function() {
	  var parent = "#" + jQuery(this).closest('p').attr("id");
          var del = jQuery(parent).find(".value").attr("id");
         // alert(del);

	  jQuery.ajax({
        type : "post",
        url :  miglaAdminAjax.ajaxurl, 
        data : {action: "miglaA_remove_options", option_name: "migla_amounts" , key : del},
        success: function(msg) {  
	       jQuery('#miglaAmountTable').empty();
             		  var list = JSON.parse(msg);
               var htmlstruct = "";  
               for (var i = 0; i < list.length; i++) {
                  htmlstruct = htmlstruct +  drawLevel( i , list[i] );
               }
 jQuery(htmlstruct).appendTo( jQuery('#miglaAmountTable'));
		   remove();
		   if( jQuery('#miglaAmountTable').text() == '' ){
                     jQuery('#warningEmptyAmounts').show();
                    }
        }
      })  ; //ajax    
     });
 
}

function add(){

	jQuery('#miglaAddAmountButton').click(function() {
	  var newVal = jQuery('#miglaAddAmount').val();
          var newValue = newVal.replace( jQuery('#sep2').text(), "." );
	  if( newValue != '')
	  {
	   var added = "<p>"+newValue+"<input id='"+newValue+"' class='miglaDeleteAmountButton' type='button' value='remove' ></p>";
	   jQuery.ajax({
        type : "post",
        url :  miglaAdminAjax.ajaxurl, 
        data : {action: "miglaA_add_options", option_name: "migla_amounts" , key : newValue, value : newValue},
        success: function(msg) {
                  jQuery('#warningEmptyAmounts').hide(); 	
		  jQuery('#miglaAmountTable').empty();
             		  var list = JSON.parse(msg);
               var htmlstruct = "";  
               for (var i = 0; i < list.length; i++) {
                  htmlstruct = htmlstruct +  drawLevel( i , list[i] );
               }
		  jQuery(htmlstruct).appendTo( jQuery('#miglaAmountTable'));
		  jQuery('#miglaAddAmount').val('');
                  remove();  
        }
       }); //ajax
	  }else{
	    alert('no empty amount dude');
	  }
	});
 
}

///////////////////////////// SORTABLE ///////////////////////////////////////////////
function ohDrag(){
  jQuery('#section3').find("ul.containers").sortable({
    placeholder: "ui-state-highlight-container",
    revert: true,
    forcePlaceholderSize: true,
    axis: 'y',
    update: function (e, ui) {
        //alert("updated");
        //save();
    },
    start: function (e, ui) {
    }
  }).bind('sortstop', function (event, ui) {
     jQuery("ul.rows").find('input[type="radio"]').each(function() {
       if(  radioState[ jQuery(this).attr('name') ] === jQuery(this).val() ){
         jQuery(this).prop('checked', true);
       }
     });
  });

//jQuery("ul.containers").disableSelection();

function SetSortableRows(rows)
{
    rows.sortable({
    placeholder: "ui-state-highlight-row",
    connectWith: "ul.rows:not(.containers)",
    containment: "ul.containers",
    helper: "clone",
    revert: true,
    forcePlaceholderSize: true,
    axis: 'y',
    start: function (e, ui) {
        jQuery(this).find('input[type="radio"]').each(function() {
          if( jQuery(this).is(':checked') ){
            radioState[ jQuery(this).attr('name') ] = jQuery(this).val();
          }
        });
    },
    update: function (e, ui) {

        //alert("updated");
        //save();
    },
    stop: function(e, ui){

    },
    received: function(e, ui){
    }
   }).bind('sortstop', function (event, ui) {
     jQuery(this).find('input[type="radio"]').each(function() {
       if(  radioState[ jQuery(this).attr('name') ] === jQuery(this).val() ){
         jQuery(this).prop('checked', true);
       }
     });
           checkGroup();
  }); 
}
  SetSortableRows(jQuery("ul.rows"));
//jQuery("ul.rows").disableSelection();
}


/////////////// DELETE GROUP ENABLE //////////////////////////
function checkGroup(){
  jQuery('#section3').find('li.formheader').each(function(){
   var div = jQuery(this).find('.mDeleteGroup');
     if( jQuery(this).find('ul.rows').children('li').length == 0 ){ 
        if( div.hasClass('disabled') ){ div.removeClass('disabled'); }  
     }else{
        if( div.hasClass('disabled') ){ }else{ div.addClass('disabled'); }  
     }    
  });
}

/////////GET RID THE FORBIDDEN CHAR/////////////
function getRidForbiddenChars(){
  jQuery('#section3').find('li.formfield').each(function() { 
     var lbl = jQuery(this).find("input[name=label]").val();
     var r = lbl.replace("[q]","'");
     jQuery(this).find(".labelChange").val( r ); 

     var target = jQuery(this).find("input[name=target]").val();
     jQuery(this).find(".targetChange").val( target ); 

     var show = jQuery(this).find("input[name=show]").val();
     jQuery(this).find("input[value='"+show+"']").prop('checked',true); 

  });
}


/////////GET FORM SETTINGS//////////////
function getFormStructure()
{
   var fields = [];
   changed_fields.length = 0;

   jQuery('#section3').find('li.formheader').each(function(){
      var group = {};
      var t = jQuery(this).find("input[name='grouptitle']").val();
      group.title = t.replace("'","[q]");
      group.parent_id = 'NULL';

      if ( jQuery(this).find(".toggle").is( ":checked" ) )
      {
        group.toggle = '1';
      }else{
        group.toggle = '0';
      }

      var leaf = -1;
      var children = []; 
      var i = 0;
      jQuery(this).find('li.formfield').each(function() { 
        var child = {}; var changed = [];
        leaf = leaf + 1; 

        var lbl = jQuery(this).find(".labelChange").val();
        child.label = lbl.replace("'","[q]");

        child.code = jQuery(this).find("input[name='code']").val();
         
        child.id =  jQuery(this).find("input[name='id']").val(); 

        if( child.code == 'miglac_' )
        {
          var new_id = lbl.replace("'","[q]");
          new_id     = new_id.replace(" ", "");

          var old_id = jQuery(this).find("input[name='id']").val();          

          if( new_id != old_id )
          {            
            changed[0] = old_id;
            changed[1] = new_id;
            changed_fields.push( changed );

            jQuery(this).find("input[name='id']").val(new_id);
            child.id =  new_id; 
          }
        }

        child.type = jQuery(this).find("select[name=typeChange] option:selected").val();
        jQuery(this).find("input[name='type']").val( child.type );
        //alert(child.type);
        
        var status = "1";
        jQuery(this).find("input[type=radio]").each(function(){
           if( jQuery(this).is(':checked') ){
             status = jQuery(this).val();
           }
        });
        child.status = status;

        child.uid = jQuery(this).find("input[name='uid']").val();

        if( (child.code == 'miglad_') && (child.status == '2') ){ child.status = '3' }     
  
        children.push(child);
      });
      
      group.depth = leaf;
      group.child = children;
      
      fields.push(group);
   });
   //alert( fields[0]['title'] );
   
  //alert( JSON.stringify(changed_fields) );

   return fields;
}

////////////KEYUP AND CHANGE/////////////////
function labelChanged(){
  jQuery('.labelChange').bind("keyup", function(e) {
    var p = jQuery(this).closest('li.formfield');
    var val = jQuery(this).val();

    var val1     = val.replace("'", "[q]");
    p.find("input[name=label]").val( val1 );

    alert( p.find("input[name=code]").val());

  });
}
/////////////////////////////////////////////////

////////////DEACTIVED & ACTIVED/////////////////
function disFormfield(){
  jQuery('body').find('li.formfield').each(function(){
    if( jQuery(this).hasClass('justAdded') ){
      //skipp
    }else{
       jQuery(this).find('input').addClass('disabled');
    }
  });
}

function enFormfield(){
  jQuery('li.formfield').each(function(){
      jQuery(this).find('input').removeClass('disabled');
  });
}
////////////////////////////////////////////////////////

function findDuplicateTitle( checkvalue ){
    var trimVal = checkvalue.replace("'", "[q]"); 
    return jQuery(".mHiddenTitle[value='" + trimVal + "']").length ;
}

function findDuplicateLabel( checkvalue ){
    var trimVal = checkvalue.replace("'", "[q]");  
    return jQuery(".mHiddenLabel[value='" + trimVal + "']").length ;
}


function isFieldValid(){
  var isValid = true;
  var BreakException= {};

  try {
    jQuery('#section3').find('li.formheader').each(function(){
        var title = jQuery(this).find("input[name='grouptitle']").val();
        //alert( "title " + title + ":" + findDuplicateTitle(title) );
    
        if( title == '' || findDuplicateTitle(title) > 1 ){ 
	   //alert('title' + title);
           isValid = false; throw BreakException;
        }
    
       var row = jQuery(this).find('ul.rows');
       row.find('li.formfield').each(function(){
          var label = jQuery(this).find('.labelChange').val();
          //alert( "label " + label + ":" + findDuplicateLabel(label) );

          if( label == '' || findDuplicateLabel(label) > 1 ){
	    //alert('label ' + label);
            isValid = false; throw BreakException;
          }
       });

    });
  } catch(e) {
    if (e!==BreakException) throw e;
  }  
  return isValid;
}


function removeField(){
 
jQuery('.removeField').click( function(){

    var parent =  jQuery(this).closest('li');
    var group =  parent.closest('ul.rows');
    group = group.closest('li.formheader');
   
    //alert(parent.attr('class'));
    if( parent.find("input[name='code']").val() === 'miglad_' ){
        alert("You can not remove default field !");
        return false;
    }else{
        var type_ = parent.find("input[name='type']").val();
        //alert(type_);
        if( type_ === 'select' || type_ === 'radio' || type_ === 'multiplecheckbox' )
        {
           var recId  = "mgval_" + parent.find("input[name='uid']").val();
           //alert( recId + " " + jQuery('#migla_custom_values_id').val() );
           jQuery.ajax({
               type : "post",
               url : miglaAdminAjax.ajaxurl, 
               data : {action: "miglaA_delete_postmeta", key : recId , id : jQuery('#migla_custom_values_id').val() },
               success: function(msg) { 
               }, asycn : true
           })  ; //ajax	
        }

          jQuery(this).closest('li').remove();
          jQuery.ajax({
              type : "post",
              url : miglaAdminAjax.ajaxurl, 
              data : {action: "miglaA_update_form", values:getFormStructure() , changes:changed_fields },
              success: function(msg) { 
                         var count = calcChildren( group );
                         if( Number(count) < 1  ){ group.find('.mDeleteGroup').removeClass('disabled');  }

             }
         })  ; //ajax	
    } 
 });

}

function writeList( tempid )
{

   var  newComer = "";  var random_uid =  mg_generate_uid();
   newComer = newComer + "<li class='ui-state-default formfield clearfix justAdded'>";
   
   newComer = newComer + "<input class='mHiddenLabel' type='hidden' name='label' value='' />";
   newComer = newComer + "<input type='hidden' name='type' value='text' />";
   newComer = newComer + "<input type='hidden' name='id' value='' />";
   newComer = newComer + "<input type='hidden' name='code' value='miglac_' />";
   newComer = newComer + "<input type='hidden' name='status' value='1' />";
   newComer = newComer + "<input type='hidden' name='uid' value='" + random_uid + "' />";

   newComer = newComer + "<div class='clabel col-sm-1 hidden-xs'><label class='control-label'>Label:</label></div>";
   newComer = newComer + "<div class='col-sm-3 col-xs-12'><input type='text' class='labelChange' name='labelChange' placeholder='";
   newComer = newComer + "' value='' /></div>";

   newComer = newComer + "<div class='ctype col-sm-2 col-xs-12'>";

   newComer = newComer + "<select class='typeChange' name='typeChange'>";

   newComer = newComer + "<option value='text'>text</option>";
   newComer = newComer + "<option value='checkbox'>checkbox</option>";
   newComer = newComer + "<option value='textarea'>textarea</option>";
   newComer = newComer + "<option value='select'>select</option>";
   newComer = newComer + "<option value='radio'>radio</option>";
   newComer = newComer + "<option value='multiplecheckbox'>multiple checkbox</option>";

   newComer = newComer + "</select>";

   newComer = newComer + "</div>";
   newComer = newComer + "<div class='ccode' style='display:none'>miglac_</div>";

   newComer = newComer + "<div class='control-radio-sortable col-sm-4 col-xs-12'>";

   newComer = newComer + "<span><label><input type='radio' name='r"+tempid+"'  value='1' checked > Show</label></span>";
   newComer = newComer + "<span><label><input type='radio' name='r"+tempid+"'  value='0' > hide</label></span>";
   newComer = newComer + "<span><label><input type='radio' name='r"+tempid+"'  value='2' > mandatory</label></span>";
   newComer = newComer + "<span><button class='removeField'><i class='fa fa-fw fa-trash'></i></button></span></div>";


   newComer = newComer + "<div class='row rowsavenewcomer'>";
   newComer = newComer + "<div class='addButton col-sm-12 '>";
   newComer = newComer + "<button id='' class='btn btn-default mbutton cancelAddField' type='button'>Cancel</button>";
   newComer = newComer + "<button id='saveNewField' class='btn btn-info pbutton AddNewComer' type='button'>";
   newComer = newComer + "<i class='fa fa-fw fa-save'></i>save field</button>";
   newComer = newComer + "</div>";
   newComer = newComer + "</div>";

   newComer = newComer + "</li>";

 return newComer;
}



function addField()
{
   var currentRow ;
   var currentRowid;

   jQuery('.mAddField').click(function(){

   parent = jQuery(this).closest('.formheader'); //group header
   currentRow = parent.find('ul.rows'); //check the children list

  if( jQuery('body').find('li.justAdded').length > 0 )
  {  
  }else{

    disFormfield();

    tempid = tempid + 1;
    var parent ;

    var newlist = "";
    newlist = writeList( tempid );

    jQuery(newlist).prependTo( currentRow );

    if( !parent.find('.mDeleteGroup').hasClass('disabled') ) { parent.find('.mDeleteGroup').addClass('disabled'); }

    labelChanged();

    ////CANCEL//////////////
    jQuery('.cancelAddField').click(function(){

       enFormfield();

       jQuery('#section3').find('li.formheader').each(function(){
          var currow = jQuery(this).find('ul.rows'); 
          currow.find('li.justAdded').each(function(){ jQuery(this).fadeOut('slow').remove()});
          tempid = -1;
          if( currow.children('li').length > 0 ){ parent.find('.mDeleteGroup').removeClass('disabled');  }
       });
    });

 }

 jQuery('#saveNewField').click(function(){

      var me = jQuery(this); 
      me.data( 'oldtext', me.html() );
      me.text('Saving...'); jQuery("<i class='fa fa-fw fa-spinner fa-spin'></i>" ).prependTo( me ); 

     var curFormField = jQuery(this).closest('li.justAdded'); // formfield
     var newLabel = curFormField.find('.labelChange');
     var newList = [];

     //CHEK VALID/////////////////
     var isValid = true;
     var BreakException= {};

    try {    
       //alert( findDuplicateLabel(  newLabel.val() ) );
       if( newLabel.val() == '' || findDuplicateLabel(  newLabel.val() ) > 1 ){
         isValid = false; throw BreakException;
       }
    } catch(e) {
       if (e!==BreakException) throw e;
    } 


   if( isValid )
   {
     jQuery('#section3').find('li.justAdded').each(function(){
         var x = jQuery(this).find("input.labelChange").val();
         n = x.replace(" ","");
         x = n.replace("'","");

        jQuery(this).find("input[name='id']").val(x);
 
        jQuery(this).find("input[type=radio]").each(function(){
           jQuery(this).attr('name', (x+'st') );
        });

        var new_type = jQuery(this).find("select[name='typeChange'] option:selected").val();
        jQuery(this).find("input[name='type']").val( new_type );

       /******** Editable *************************************************************/
       if( new_type == 'select' || new_type == 'radio' || new_type == 'multiplecheckbox' ){
            var me = jQuery(this).find(".ctype");
            if( me.find('.edit_select_value').length == 0 ){
                 
                  var html = "<div class='col-sm-2 col-xs-12'><button class='mbutton edit_select_value' >Enter Values</button></div>";
                  jQuery( html ).insertAfter( me );
            }
      }

      jQuery(".edit_select_value").click(function(e){
          e.preventDefault();
 	  var parent = jQuery(this).closest('li.formfield');
          var recId  = "mgval_" + parent.find("input[name='uid']").val();

          jQuery("#mg_id_custom_values_edit").text("");
	  jQuery("#mg_id_custom_values_edit").text(recId);
	  jQuery('#mg_add_values').modal('show');
      });

      /******** Editable *************************************************************/

      jQuery(this).removeClass('justAdded');

    });

      jQuery.ajax({
       type : "post",
       url : miglaAdminAjax.ajaxurl, 
       data : {action: "miglaA_update_form", values:getFormStructure(), changes:changed_fields },
       success: function(msg) { 
         saved("#saveNewField"); 
         jQuery('body').find('.rowsavenewcomer').remove();
         removeField(); 
         ohDrag();
       }
     })  ; //ajax	 

     enFormfield(); field_type_change();

   }else{
      alert("No empty values please or duplicate label !");
      canceledLoser( "#saveNewField", "<i class='fa fa-fw fa-save'></i> Save field");
   }

 })


 });

}

function deleteGroup(){
jQuery('.mDeleteGroup').click(function() {
  var parent = jQuery(this).closest('.formheader');
  
  if( parent.find('li.formfield').length > 0 ){
    alert("you can not remove the group because it has fields");
  }else{
    parent.remove();
    jQuery.ajax({
      type : "post",
      url : miglaAdminAjax.ajaxurl, 
      data : {action: "miglaA_update_form", values:getFormStructure(), changes:changed_fields},
      success: function(msg) {  
     }
    })  ; //ajax	
  }
});
}

function calcChildren( group ){
  var count = 0;
  group.find('li.formfield').each(function() {
    count = count + 1; 
  });
  return count;
}

function clearLeftover(){
  jQuery('li.formheader').each(function(){
    jQuery(this).find('.titleChange').val( jQuery(this).find('.mHiddenTitle').val() );

    jQuery('li.formfield').each(function(){
    // alert(jQuery(this).find('input[name=label]').val());
     jQuery(this).find('.labelChange').val( jQuery(this).find('input[name=label]').val() );

     var s = jQuery(this).find('input[name=type]').val();
      jQuery(this).find(".typeChange option[value='" + s + "']").attr("selected","selected");
    });
  })
}


////////////KEYUP AND CHANGE/////////////////
function labelChanged(){
  jQuery('.labelChange').bind("keyup change", function(e) {
   var p = jQuery(this).closest('li.formfield');

   var val = jQuery(this).val().replace("'", "[q]");
   p.find("input[name=label]").val( val );
  });
}

function targetChanged(){
  jQuery('.targetChange').bind("keyup change", function(e) {
   var p = jQuery(this).closest('li.formfield');

   p.find("input[name=target]").val( val );
  });
}
/////////////////////////////////////////////////

function numberExample( t, d ){
 var n = 10000; var nf = "";

 if ( jQuery('#showDecimal').text() == 'yes' ){ showDec = 2;}

 jQuery('#sep1').text( t );
 jQuery('#sep2').text( d );

 nf = n.formatMoney(showDec, t , d )  ;

 jQuery('#miglanum').text(nf);
}



/**************************************************************************************************************************/
/*                                             Document Ready                                                       */
/**************************************************************************************************************************/

jQuery(document).ready(function() {
//alert('load');

clearLeftover();
getRidForbiddenChars(); labelChanged();
add();
remove();

jQuery('#miglaAddAmount').val('0');

//For making format number
Number.prototype.formatMoney = function(decPlaces, thouSeparator, decSeparator) {
    var n = this,
        decPlaces = isNaN(decPlaces = Math.abs(decPlaces)) ? 2 : decPlaces,
        decSeparator = decSeparator == undefined ? "." : decSeparator,
        thouSeparator = thouSeparator == undefined ? "," : thouSeparator,
        sign = n < 0 ? "-" : "",
        i = parseInt(n = Math.abs(+n || 0).toFixed(decPlaces)) + "",
        j = (j = i.length) > 3 ? j % 3 : 0;
    var result = sign + (j ? i.substr(0, j) + thouSeparator : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" 
            + thouSeparator) + (decPlaces ? decSeparator + Math.abs(n - i).toFixed(decPlaces).slice(2) : "");
    return result;
};

   jQuery('.miglaNAD2').on('keypress', function (e){ 
     var str = jQuery(this).val(); 
     var separator = jQuery('#sep2').text();
     var key = String.fromCharCode(e.which);
     console.log(String.fromCharCode(e.which) + e.keycode + e.which );

   if(jQuery('#showDecimal').text() == 'yes'){	 
     // Allow: backspace, delete, escape, enter	 
     if (jQuery.inArray( e.which, [ 8, 0, 27, 13]) !== -1 ||
        jQuery.inArray( key, [ '0', '1', '2', '3', '4', '5', '6', '7', '8', '9' ]) !== -1 ||
        ( key == separator )
     )
     {
        if( key == separator  && ( str.indexOf(separator) >= 0 ))
        {
          e.preventDefault();
        }else{
          return;
        }
     }else{
        e.preventDefault();
     } 
   }else{
     // Allow: backspace, delete, escape, enter	 
     if (jQuery.inArray( e.which, [ 8, 0, 27, 13]) !== -1 ||
        jQuery.inArray( key, [ '0', '1', '2', '3', '4', '5', '6', '7', '8', '9' ]) !== -1 
     )
     {

     }else{
        e.preventDefault();
     }    
   }	 
  });

if( jQuery('#miglaAmountTable').text() == '' ){
  jQuery('#warningEmptyAmounts').show();
}
 
/////////CURRENCY/////////////////////////////
 jQuery.ajax({
	type : "post",
	url : miglaAdminAjax.ajaxurl, 
	data : {action: "miglaA_get_option", option:'migla_currencies'},
	success: function(msg) {
        //alert( msg );
        var c  = "";
        currencies = JSON.parse( msg );
  }
 })  ; //ajax	

function getsymbol( code ){
  var s = '';
for(key in currencies){
    if( currencies[key]['code'] == code)
    {
     if( currencies[key]['faicon']  != ''){ s = "<i class='fa " + currencies[key]['faicon'] + "'></i>"; 
     }else{ s = "<label>" + currencies[key]['symbol'] + "</label>"; }   
    }
  }
  return s;
}

jQuery('#miglaDefaultCurrency').change(function(){
   //alert(jQuery(this).val());
   var str = getsymbol( jQuery(this).val() ) ;
   jQuery('#icon').html(str);
   if( jQuery('#placement').text() == 'before' ){
    jQuery('#miglabefore').html(str);jQuery('#miglaafter').html("");
   }else{
    jQuery('#miglaafter').html(str);jQuery('#miglabefore').html("");   
   }
});

jQuery('#miglaDefaultPlacement').change(function(){
   var placement = jQuery(this).val();
   var icon = jQuery('#icon').html();
   jQuery('#placement').text( placement );
   if( placement == 'before'){
     jQuery('#miglabefore').html(icon);jQuery('#miglaafter').html("");
   }else{
     jQuery('#miglaafter').html(icon);jQuery('#miglabefore').html("");
   }
});

showDec = 0;
if ( jQuery('#showDecimal').text() == 'yes' ){ showDec = 2; }

/////////// SEPARATOR ////////////////////////////////////////
jQuery('#thousandSep').val( jQuery('#sep1').text() );
jQuery('#decimalSep').val( jQuery('#sep2').text() );

jQuery('#thousandSep').change(function(){
 numberExample( jQuery(this).val() , jQuery('#sep2').text() );
})
	
jQuery('#decimalSep').change(function(){
 numberExample( jQuery('#sep1').text(),  jQuery(this).val() );
})

jQuery('#mHideDecimalCheck').click(function(){
showDec = 0;

  if( jQuery(this).is(':checked') ){
    jQuery('#showDecimal').text('yes');
    showDec = 2;
  }else{
    jQuery('#showDecimal').text('no');
  }

  numberExample( jQuery('#sep1').text(),  jQuery('#sep2').text() );
});

/////////// END SEPARATOR ///////////////////////////////////

    jQuery('#miglaSetCurrencyButton').click(function() {
      var id = '#' + jQuery(this).attr('id');
          //alert( jQuery('input[name=thousandSep]').val() + " " + jQuery('input[name=decimalSep]').val());
	  jQuery.ajax({
		type : "post",
		url :  miglaAdminAjax.ajaxurl, 
		data : {action: "miglaA_update_me", key:'migla_thousandSep', 
		    value: jQuery('input[name=thousandSep]').val() },
		success: function(msg) { 
                   // saved(id); 
		}
	  })  ; //ajax	

	  jQuery.ajax({
		type : "post",
		url :  miglaAdminAjax.ajaxurl, 
		data : {action: "miglaA_update_me", key:'migla_decimalSep', 
		    value: jQuery('input[name=decimalSep]').val() },
		success: function(msg) { 
                   // saved(id); 
		}
	  })  ; //ajax	

	  jQuery.ajax({
		type : "post",
		url :  miglaAdminAjax.ajaxurl, 
		data : {action: "miglaA_update_me", key:'migla_curplacement', 
		    value: jQuery('select[name=miglaDefaultplacement] option:selected').val() },
		success: function(msg) { 
                   // saved(id); 
		}
	  })  ; //ajax	


          var show = "no";
          if( jQuery('#mHideDecimalCheck').is(":checked")  ) { show = "yes"; }
	  jQuery.ajax({
		type : "post",
		url :  miglaAdminAjax.ajaxurl, 
		data : {action: "miglaA_update_me", key:'migla_showDecimalSep', 
		    value:show },
		success: function(msg) { 
                   // saved(id); 
		}
	  })  ; //ajax	

	  jQuery.ajax({
		type : "post",
		url :  miglaAdminAjax.ajaxurl, 
		data : {action: "miglaA_update_me", key:'migla_default_currency', 
		    value: jQuery('select[name=miglaDefaultCurrency] option:selected').val() },
		success: function(msg) { 
                    saved(id); 
		}
	  })  ; //ajax		  
    });		

    jQuery('#miglaSetCountryButton').click(function() {
      var id = '#' + jQuery(this).attr('id');
	  //alert(jQuery('select[name=miglaDefaultCountry] option:selected').text());
	  jQuery.ajax({
		type : "post",
		url : miglaAdminAjax.ajaxurl, 
		data : {action: "miglaA_update_me", key:'migla_default_country', 
		    value: jQuery('select[name=miglaDefaultCountry] option:selected').text() },
		success: function(msg) {  
                    saved(id);
		}
	  })  ; //ajax		
    });	
/////////END OF CURRENCY/////////////////////////////

/////////SAVE FORM//////////////////////////////////

jQuery('.miglaSaveForm').click(function() {

var f = []; var id = '#' + jQuery(this).attr('id'); 

if(  isFieldValid() )
{
  f = getFormStructure();
  jQuery.ajax({
    type : "post",
    url : miglaAdminAjax.ajaxurl, 
    data : {action: "miglaA_update_form", values: f , changes:changed_fields },
    success: function(msg) {                 
     //alert( "done" );
     saved( id );
     clearLeftover(); getRidForbiddenChars();
    }
  })  ; //ajax

  jQuery.ajax({
    type : "post",
    url : miglaAdminAjax.ajaxurl, 
    data : {action: "miglaA_update_me", key:'migla_custamounttext', value:jQuery('#migla_custAmountTxt').val() },
    success: function(msg) {                 
    }
  })  ; //ajax

}else{
  alert("No empty values or duplicate values");
  canceled(id);
}
	   
/*
  alert("under construction");
  canceled(id);
*/

});

/////// change field label ////////////////////////
labelChanged();

jQuery('.titleChange').bind("keyup change", function(e) {
  var p = jQuery(this).closest('li.formheader');
  var val = jQuery(this).val().replace("'", "[q]");
  p.find("input[name=title]").val( val );
});

////////////ADD FIELDS//////////////////////////////
addField();

////////////////////REMOVE FIELD////////////////////////////////
removeField();

/////////////////FIELD GROUP/////////////////////////

//////////// DELETE GROUP //////////////////
deleteGroup();

///////// ADD GROUP ////////////////////////
jQuery('.mAddGroup').click(function() {
   jQuery('#divAddGroup').toggle();
});

jQuery('#cancelAddGroup').click(function() {
   jQuery('#divAddGroup').toggle();
  jQuery('#labelNewGroup').val('');
});

jQuery('#saveAddGroup').click(function() {
   var title = jQuery('#labelNewGroup').val();
   var ulid = "";
   var newG = "";
   var idGroup = Number(jQuery('ul.containers').children('li').length) + 1;

//CHEK VALID/////////////////
  var isValid = true;
  var BreakException= {};

  try {    
      if( title == '' || findDuplicateTitle(  title ) > 0 ){
        isValid = false; throw BreakException;
      }
  } catch(e) {
    if (e!==BreakException) throw e;
  } 
////////////////////////////

if( isValid )
{
  newG = newG + "<li class='title formheader'>";
   newG = newG + "<div class='row'>";

   newG = newG + "<div class='col-sm-4'>";
   newG = newG + "<div class='row'>";
   newG = newG + "<div class='col-sm-2'> <i class='fa fa-bars bar-icon-styling'></i></div>";
  newG = newG + "<div class='col-sm-10'> ";
  newG = newG + "<input type='text' class='miglaNQ'  placeholder='"+title+"' name='grouptitle' value='"+title+"'> ";
  newG = newG + "</div>";
  newG = newG + "</div></div>";

  newG = newG + "<div class='col-sm-4'>";
  newG = newG + "<div class='col-sm-5'>";

if( jQuery('#toggleNewGroup').is(':checked') ){
  newG = newG + "<input type='checkbox' id='t" + idGroup + "' class='toggle' checked='checked' /><label>Toggle</label>";
}else{
  newG = newG + "<input type='checkbox' id='t" + idGroup + "' class='toggle' /><label>Toggle</label>";
}

  newG = newG + "</div>";
  newG = newG + "<button value='add' class='btn btn-info obutton mAddField addfield-button-control' style='display:none'>";
  newG = newG + "<i class='fa fa-fw fa-plus-square-o'></i>Add Field</button>";
  newG = newG + "</div>";

  newG = newG + "<div class='col-sm-4 text-right-sm text-center-xs divDelGroup'>";
  newG = newG + "<button value='add' class='rbutton btn btn-danger mDeleteGroup pull-right'>";
  newG = newG + "<i class='fa fa-fw fa-trash'></i>Delete Group</button>";
  newG = newG + "</div>";

  newG = newG + "</div>";

  newG = newG + "<input type='hidden' name='title' value='"+title+"' />";
  newG = newG +"<input type='hidden' name='child' value='NULL' />";
  newG = newG +"<input type='hidden' name='parent_id' value='NULL' />";
  newG = newG +"<input type='hidden' name='depth' value='0' />";

  ulid = title.replace(" ", "");
  newG = newG + "<ul class='rows' id='"+ulid+"'>";

  newG = newG + "</ul>";
  newG = newG + "</li>";

  jQuery(newG).prependTo( jQuery('ul.containers') );
  jQuery('#labelNewGroup').val('');
  addField(); deleteGroup(); ohDrag();

  var fielddata = [];
  fielddata = getFormStructure(); 
  //alert( JSON.stringify(fielddata) );


   jQuery.ajax({
     type : "post",
     url : miglaAdminAjax.ajaxurl, 
     data : {action: "miglaA_update_form", values:fielddata , changes:changed_fields },
     success: function(msg) {  
      jQuery('#divAddGroup').toggle();
      jQuery('.mAddField').show();
      saved('#saveAddGroup');
    }
   })  ; //ajax


}else{
   alert("data can not be empty or duplicate title !");
   canceled('#saveAddGroup');
}

});

////////////RESET/////////////////////////////////
jQuery('#miglaRestore').click(function(){
 jQuery.ajax({
	type : "post",
	url : miglaAdminAjax.ajaxurl, 
	data : {action: "miglaA_reset_form"},
	success: function(msg) {
          //alert( msg );  
	  location.reload(true);
          clearLeftover();
          getRidForbiddenChars();
	}
 })  ; //ajax	   
});


/////////////SORTABLE 2/////////////////////////////
ohDrag();

//////////// Undesignated Label April 8th, 2015//////////////////////
 jQuery('#miglaUnLabelChange').click(function(){
 
   var label = jQuery('#mg-undesignated-default').val();
   jQuery.ajax({
        type : "post",
        url :  miglaAdminAjax.ajaxurl, 
        data : {action: "miglaA_updateUndesignated", old:jQuery('#mg_oldUnLabel').val(), new:label },
        success: function(msg) {  
           jQuery('#mg_oldUnLabel').val(label);
        }
   }); //ajax
 
    var val = 'no';
    if ( jQuery('#mHideUndesignatedCheck').is(":checked") ){
        val = 'yes';
        //alert( 'yes' );
    }

   jQuery.ajax({
        type : "post",
        url :  miglaAdminAjax.ajaxurl, 
        data : {action: "miglaA_update_me", key:'migla_hideUndesignated', value:val },
        success: function(msg) {  
        }
   }); //ajax

      var valbar = 'yes';
      if( jQuery('#mHideProgressBarCheck').is(':checked') ) {  valbar = 'no'; }     
      jQuery.ajax({
	  type : "post",
	  url : miglaAdminAjax.ajaxurl, 
	  data : {action: "miglaA_update_me", key:'migla_show_bar', value:valbar },
	  success: function(msg) {    }
      })  ; //ajax	

      var valsort = 'sort';
      if( jQuery('#mReverseOrderCheck').is(':checked') ) {  valsort = 'rsort'; }     
      jQuery.ajax({
	  type : "post",
	  url : miglaAdminAjax.ajaxurl, 
	  data : {action: "miglaA_update_me", key:'migla_sort_level', value:valsort },
	  success: function(msg) {  saved( '#miglaUnLabelChange' );  }
      })  ; //ajax	

 }) //miglaUnLabelChange clicked

 jQuery('.typeChange').change(function(){
     var optionSelected = jQuery(this).find("option:selected");
     var valueSelected  = optionSelected.val();
     var textSelected   = optionSelected.text();
     //alert( valueSelected + textSelected );
    
   jQuery(this).find("option").each(function(){
     if( jQuery(this).val() == valueSelected ){
       jQuery(this).attr('selected', 'selected');
     }else{
       jQuery(this).removeAttr('selected');
     }
   });

 });



 jQuery("input[name='campaignst']").each(function(){
     if( jQuery(this).is(':checked') ){
        if( jQuery(this).val() == '0' ){
           jQuery("#miglaform_campaign").removeAttr('disabled');
           jQuery("#miglaform_campaign").show();
        }else{
	   jQuery("#miglaform_campaign").hide();
        }
     }
 });

/////Hide Campaign
 jQuery("input[name='campaignst']").change(function(){
   if( jQuery(this).val() == '0' ){
      jQuery("#miglaform_campaign").removeAttr('disabled');
      jQuery("#miglaform_campaign").show();
   }else{
      jQuery("#miglaform_campaign").attr('disabled', 'disabled');
      jQuery("#miglaform_campaign").hide();
   }
 });

 jQuery("#miglaform_campaign").change(function(){
   //alert(jQuery("#miglaform_campaign").val());
   jQuery.ajax({
        type : "post",
        url :  miglaAdminAjax.ajaxurl, 
        data : {action: "miglaA_update_me", key:'migla_selectedCampaign', value:jQuery("#miglaform_campaign").val()},
        success: function() {  
        }
   }); //ajax
 });



/****************** New March 25th *************************************/


//add to list
 jQuery('#miglaAddCustomValueForm').click(function(){
   
    if( jQuery( '#mg_custom_list_container' ).find('.mg_custom_list_row'.length) <= 0 ){ jQuery( '#mg_custom_list_container' ).empty() };
    
    var content = "";
                    content = content + "<div class='mg_custom_list_row'>";

                    content = content + "<div class='form-group mg_custom_list'><div class='col-sm-3'><label class='control-label' for=''>Value</label></div>";
                    content = content + "<div class='col-sm-6'><input type='text'  value='" + jQuery('#mg_add_value').val();
                    content = content + "' class='mg_customlist_key form-control'></div>";
                    content = content + "<div class='col-sm-3'></div>";
                    content = content + "</div>";

                    content = content + "<div class='form-group mg_custom_list'><div class='col-sm-3'><label class='control-label' for=''>Label</label></div>";
                    content = content + "<div class='col-sm-6'><input type='text'  value='" + jQuery('#mg_add_label').val();
                    content = content + "' class='form-control touch-bottom mg_customlist_val'></div>";
                    content = content + "<div class='col-sm-3'><button class='mg_customlist_remove btn obutton alignleft'><i class='fa fa-fw fa-trash'></i> </button></div>";
                    content = content + "</div>";

                    content = content + "</div>";

    jQuery( content ).appendTo( '#mg_custom_list_container' ) ;

    mg_delete_custom_list();

    jQuery('#mg_add_value').val(''); jQuery('#mg_add_label').val('');

 });
 
   jQuery('#miglaAddCustomValues').click(function(){

     mg_save_custom_list('');
     jQuery('#mg_add_values').find('.close').trigger('click');
   
   });
 
  jQuery(".edit_select_value").click(function(e){
        e.preventDefault();
 	var parent = jQuery(this).closest('li.formfield');
        var recId  = "mgval_" + parent.find("input[name='uid']").val();

        jQuery("#mg_id_custom_values_edit").text("");
	jQuery("#mg_id_custom_values_edit").text(recId);
	jQuery('#mg_add_values').modal('show');
  });


 jQuery('#mg_add_values').on('show.bs.modal', function(e) {
  
   jQuery( '#mg_custom_list_container' ).empty(); //jQuery('#mg_add_val_load').show();

   //alert( jQuery('#mg_id_custom_values_edit').text() + jQuery('#migla_custom_values_id').val() );

    jQuery.ajax({
        type : "post",
        url :  miglaAdminAjax.ajaxurl, 
        data : {action: "miglaA_get_postmeta", key:jQuery('#mg_id_custom_values_edit').text(), id:jQuery('#migla_custom_values_id').val() },
        success: function( msg ) {  

          //alert(msg);

          jQuery('#miglaAddCustomValues').removeAttr('disabled');

          if( msg == "-1" || msg == ""){

            jQuery('#mg_add_values').find('.fa-spinner').hide();
            jQuery( '#mg_custom_list_container' ).html('');

          }else{
             
              jQuery('#mg_add_values').find('.fa-spinner').hide();
              jQuery( '#mg_custom_list_container' ).html('');

              var content = "";
              var res = msg.split(";");

             for( key in res ){
               if( res[key] !== ''  ){
                  var res2 = res[key].split("::");
                  if( typeof res2[1] !== 'undefined' && res2[1] !== '' ){ 

                    content = content + "<div class='mg_custom_list_row'>";

                    content = content + "<div class='form-group mg_custom_list'><div class='col-sm-3'><label class='control-label' for=''>Value</label></div>";
                    content = content + "<div class='col-sm-6'><input type='text'  value='" + res2[0]
                    content = content + "' class='mg_customlist_key form-control touch-top'></div>";
                    content = content + "<div class='col-sm-3'></div>";
                    content = content + "</div>";

                    content = content + "<div class='form-group mg_custom_list'><div class='col-sm-3'><label class='control-label' for=''>Label</label></div>";
                    content = content + "<div class='col-sm-6'><input type='text'  value='" + res2[1];
                    content = content + "' class='form-control  mg_customlist_val'></div>";
                    content = content + "<div class='col-sm-3'><button class='mg_customlist_remove btn obutton alignleft'><i class='fa fa-fw fa-trash'></i> </button></div>";
                    content = content + "</div>";

                    content = content + "</div>";

                }
              }

             }

             jQuery( content ).appendTo( '#mg_custom_list_container' ) ;

             mg_delete_custom_list();

          }

        }
   }); //ajax

 });


/********************************************************************************************************/
   //Init Variables
   jQuery('#migla_planName').val(''); jQuery('#migla_planTime').val('1');


   jQuery('#miglaAddPlan').click(function(){

      var lines = "";
      var Pname = jQuery('#migla_planName').val();
      var Pid   =  "BasicTD_" + mg_random();
      var Pt    = jQuery('#migla_planTime').val();
      var Pp    = jQuery('#migla_planPeriod').val();
      var Pmethod = jQuery("#migla_planMethod").find(":selected").val();
      var row   =  jQuery('li.mg_reoccuring-field').length ;
      var isThisEnabled = '';
      
      if( Pmethod == 'paypal-stripe' || Pmethod == 'stripe' ){ isThisEnabled = 'disabled'; }

      var uid_row = "planbtn" + mg_random();

    if( Pname != ''){

     lines = lines + "<li class='mg_reoccuring-field clearfix title formheader ui-sortable-handle rec_just_added'> ";
     lines = lines + "<input type='hidden' class='old_name' value='"+ Pname +"'>";
     lines = lines + "<input type='hidden' class='old_id' value='"+ Pid +"'>";
     lines = lines + "<input type='hidden' class='old_time' value='" + Pt +"'>";
     lines = lines + "<input type='hidden' class='old_period' value='"+ Pp +"'>";
     lines = lines + "<input type='hidden' class='old_status' value='1'>";
     lines = lines + "<input type='hidden' class='old_method' value='"+ Pmethod +"'>";

     lines = lines + "<div class='rows'> <div class='col-sm-1 clabel'>";
     lines = lines + "<label class='control-label '>Label</label>";
     lines = lines + "</div>";
     lines = lines + "<div class='col-sm-2 col-xs-12'><input type='text' class='name' name='' placeholder='' value='"+ Pname +"'></div>";
     lines = lines + "<div class='col-sm-1 hidden-xs'><label class='control-label'>Interval</label></div>";
            
     lines = lines + "<div class='col-sm-2 col-xs-12 text-right-sm text-center-xs'>";
     lines = lines + "<div data-plugin-spinner='' data-plugin-options='{ }'>";
     lines = lines + "<div class='input-group' style=''><input type='text' value='"+ Pt +"' class='spinner-input form-control time' maxlength='2' "+isThisEnabled+">";
     lines = lines + "<div class='spinner-buttons input-group-btn'>";
     lines = lines + "<button type='button' class='btn btn-default spinner-up' "+isThisEnabled+">";
     lines = lines + "<i class='fa fa-angle-up'></i>";
     lines = lines + "</button>";
	 lines = lines + "<button type='button' class='btn btn-default spinner-down' "+isThisEnabled+">";
	 lines = lines + "<i class='fa fa-angle-down'></i>";
	 lines = lines + "</button>";
	 lines = lines + "</div>";
	 lines = lines + "</div>";
															
	 lines = lines + "</div>";
	 lines = lines + "</div>";

    var checked = { day:"" , week:"", month:"" , year:"" };
    switch( Pp ){
      case 'day' : checked.day = 'selected'; break;
      case 'week' : checked.week = 'selected'; break;
      case 'month' : checked.month = 'selected'; break;
      case 'year' : checked.year = 'selected'; break;
    }

      lines = lines + "<div class='col-sm-2 col-xs-12  '>";
       lines = lines + "<select id='period"+Pid+"' class='period' name='' "+isThisEnabled+">";                 
      lines = lines + "<option value='day' "+ checked.day +">Day(s)</option>";
      lines = lines + "<option value='week' "+ checked.week +">Week(s)</option> ";
      lines = lines + "<option value='month' "+ checked.month +">Month(s)</option> ";
      lines = lines + "<option value='year' "+ checked.year +">Year(s)</option> ";
      lines = lines + "</select>";
      lines = lines + "</div>";

     lines = lines + "<div class='control-radio-sortable col-sm-3 col-xs-12 form-group touching'><span><label>";
	 lines = lines + "<input type='radio' class='status' value='1' name='status"+ Pid +"' checked> Show</label></span>";
	 lines = lines + "<span><label><input type='radio' class='status' value='0' name='status"+ Pid +"'> Deactivate</label></span>";
	 lines = lines + "<span><button class='removePlanField'><i class='fa fa-fw fa-trash'></i></button></span></div>";
         lines = lines + "</div> ";

        lines = lines + "<div class='rows'><div class='col-sm-1 '></div>";
        lines = lines + "<div class='col-sm-2 col-xs-12'></div>";
        lines = lines + "<div class='col-sm-1 hidden-xs'><label class='control-label'>Gateways</label></div>";               
        lines = lines + "<div class='col-sm-4 col-xs-12  '>";
            
      if( Pmethod == 'paypal' ){
          lines = lines + "<select id='method"+ Pid +"' class='method' > ";                
          lines = lines + "<option value='paypal' selected>PayPal Only</option> <option value='stripe' >Stripe Only</option>";
          lines = lines + "<option value='paypal-stripe'>PayPal or Stripe</option>";
          lines = lines + " </select>";
      }else if( Pmethod == 'stripe' ){
          lines = lines + "<select id='method"+ Pid +"' class='method' "+isThisEnabled+"> ";                
          lines = lines + "<option value='paypal'>PayPal Only</option> <option value='stripe' selected>Stripe Only</option>";
          lines = lines + "<option value='paypal-stripe'>PayPal or Stripe</option>";
          lines = lines + " </select>";
      }else{
          lines = lines + "<select id='method"+ Pid +"' class='method' "+isThisEnabled+">  ";               
          lines = lines + "<option value='paypal'>PayPal Only</option><option value='stripe' >Stripe Only</option>";
          lines = lines + "<option value='paypal-stripe' selected>PayPal or Stripe</option>";
          lines = lines + " </select>";
       }

     lines = lines + "</div>";
     lines = lines + "<div class='control-radio-sortable col-sm-3 col-xs-12 '>";
     lines = lines + "<button value='save' class='btn btn-info pbutton migla_save_row_plan' id='"+ uid_row +"'><i class='fa fa-fw fa-save'></i> save</button>";
     lines = lines + "</div></div></li>";

     if( jQuery('ul.mg_recurring_row').find('li.mg_reoccuring-field').length <= 0 ){
         jQuery('#mg_plan_list_info').remove();
         jQuery(lines).prependTo( jQuery('ul.mg_recurring_row') );
     }else{
         jQuery(lines).prependTo( jQuery('ul.mg_recurring_row') );
     }

      var send_list = [];
      send_list = mg_get_structure();
      var result = "";
     //alert( JSON.stringify(send_list) );

      //Test if this stripe addition
      if( Pmethod == "paypal-stripe" ||  Pmethod == "stripe" ){
    	     jQuery.ajax({
		 type : "post",
 		 url : miglaAdminAjax.ajaxurl, 
		 data : {action: "miglaA_stripe_addBasicPlan", 
                         amount:1,
                         interval:Pp,
                         interval_count:Pt,
                         name:Pname,
                         id:Pid
                        },
		success: function( msg_add ) { 
                   if( msg_add == "1" ){
                       jQuery.ajax({
                          type : "post",
                          url  :  miglaAdminAjax.ajaxurl, 
                          data : {action: "miglaA_update_me", key:'migla_recurring_plans' , value:send_list },
                              success: function(msg_update) 
                              {
                                      //mg_save_row_plan();  remove_plan();
                                      //init_rec_section();  ohDrag_4();
                                      saved('#miglaAddPlan'); 
                                      jQuery('li.rec_just_added').removeClass('rec_just_added');
                              } , asycn : true
                       }); //ajax                       
                   }else{
                        canceled('#miglaAddPlan'); jQuery('li.rec_just_added').remove();
                        alert( msg_add  );
                   }
		}
	     })  ; //ajax	
      }else{

          jQuery.ajax({
             type : "post",
             url  :  miglaAdminAjax.ajaxurl, 
             data :  {  action: "miglaA_update_me", key:'migla_recurring_plans' , value:send_list  },
             success: function( msg_update2 ) { 
                         // mg_save_row_plan();  remove_plan();
                         //init_rec_section();  ohDrag_4();
                         saved('#miglaAddPlan');
                         jQuery('li.rec_just_added').removeClass('rec_just_added');
             } , asycn : true
          }); //ajax
       }


     remove_plan();
     mg_save_row_plan();
     init_rec_section();
     ohDrag_4();

   }else{
      alert('Data is not complete');
   }

   });

   remove_plan();
   mg_save_row_plan();
   init_rec_section();
   ohDrag_4();

field_type_change();

}); //ON READY


function init_rec_section(){

  jQuery('.spinner-up').once('click', function(e){
        var input = jQuery(this).closest('.input-group').find('.spinner-input');
        var i = parseInt(input.val()) + 1;
        input.val( i );
  });

  jQuery('.spinner-down').once('click', function(e){
        var input = jQuery(this).closest('.input-group').find('.spinner-input');
      if( parseInt(input.val()) > 1 ){
          var i = parseInt(input.val()) - 1;
          input.val( i );
      }
  });

   jQuery('#mg_none_rec_radiobtn').click(function(){
      jQuery.ajax({
         type : "post",
         url :  miglaAdminAjax.ajaxurl, 
         data : {action: "miglaA_update_me", key:"migla_none_rec_radiobtn_text", value:jQuery('#mg_none_rec_radiobtn_text').val() },
         success: function(msg) {
             saved('#mg_none_rec_radiobtn');
         }, asycn : true
      }); //ajax 
   });
}

function ohDrag_4(){

  jQuery('#section4').find("ul.containers").sortable({
      placeholder: "ui-state-highlight-container",
      revert: true,
      forcePlaceholderSize: true,
      axis: 'y',
      update: function (e, ui) {
      },
      start: function (e, ui) {
        //Ok lets revert :) this only save the order
        var parent    = jQuery(ui.item);
        var pid2       = "#" + parent.find(".method").attr('id');
        var _method   = parent.find('.old_method').val();

        if(  _method === 'paypal' )
        {
          var _name     = parent.find('.old_name').val();
          var _id       = parent.find('.old_id').val();
          var _time     = parent.find('.old_time').val();
          var _period   = parent.find('.old_period').val();
          var _status   = parent.find('.old_status').val();

          parent.find('.name').val( _name );
          parent.find('.time').val( _time );
          parent.find(".period").val( _period );
          jQuery( pid2 + " option[value='"+ _method +"']").attr('selected', 'selected'); 
          parent.find(".status[value='"+ _status +"']").attr("checked","checked");  
        }
      }
  }).bind('sortstop', function (event, ui) {

      //Send it to database using ajax
      var send_list = [];
      send_list = mg_get_structure();

       jQuery.ajax({
             type : "post",
             url  :  miglaAdminAjax.ajaxurl, 
             data :  {  action: "miglaA_update_me", key:'migla_recurring_plans' , value:send_list  },
             success: function( msg_update2 ) { 
                     //mg_save_row_plan();  remove_plan();
             }, asycn:true
       }); //ajax

       init_rec_section();

  });
}

function field_type_change()
{
  jQuery('.typeChange').once('click', function(e){

     var p = jQuery(this).closest('li.formfield');
     var type_val = jQuery(this).val() ;
     p.find("input[name='type']").val(type_val);

    if( type_val=='select' || type_val=='radio' || type_val=='multiplecheckbox' )
    {
      if( p.find('.edit_select_value').length == 0 ){
        jQuery("<div class='col-sm-2 col-xs-12'><button class='mbutton edit_select_value' >Enter Values</button></div>").insertAfter( p.find('.ctype') );
        
        jQuery(".edit_select_value").click(function(e){
        e.preventDefault();
 	var parent = jQuery(this).closest('li.formfield');
        var recId  = "mgval_" + parent.find("input[name='uid']").val();

        jQuery("#mg_id_custom_values_edit").text("");
	jQuery("#mg_id_custom_values_edit").text(recId);
	jQuery('#mg_add_values').modal('show');
  });
      }
    }else{
         p.find('.edit_select_value').remove();  
    }

  });
}


