//jQuery = jQuery.noConflict();

var radioState = {}; var currencies = []; var showDec ;
var tempid = -1; var btnid = 0;

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
//////////////FORM////////////////////////////////////////////////////////////////////
///////////////////////////// SORTABLE ///////////////////////////////////////////////

function ohDrag(){
jQuery("ul.containers").sortable({
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
  jQuery('li.formheader').each(function(){
   var div = jQuery(this).find('.mDeleteGroup');
     if( jQuery(this).find('ul.rows').children('li').length == 0 ){ 
        if( div.hasClass('disabled') ){ div.removeClass('disabled'); }  
     }else{
        if( div.hasClass('disabled') ){ }else{ div.addClass('disabled'); }  
     }    
  });
}

/////////GET RID THE FORBIDDEN CHAR/////////////
/*
function getRidForbiddenChars(){
   jQuery('.formheader').each(function(){
      var t = jQuery(this).find("input[name='grouptitle']").val();
      jQuery(this).find("input[name='grouptitle']").val( t.replace("[q]","'") );

      jQuery(this).find('.formfield').each(function() { 
        var lbl = jQuery(this).find(".labelChange").val();
        lbl = lbl.replace("[q]","'");
        jQuery(this).find(".labelChange").val( lbl ); 
      });

   });
}
*/

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


/////////GET FORM SETTINGS//////////////
function getFormStructure(){
   var fields = [];
   jQuery('li.formheader').each(function(){
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
        var child = {};
        leaf = leaf + 1; 

        var id = jQuery(this).find("input[name='id']").val();
        child.id = id;

        var lbl = jQuery(this).find(".labelChange").val();
        child.label = lbl.replace("'","[q]");

        child.type = jQuery(this).find("select[name=typeChange] option:selected").val();
        jQuery(this).find("input[name='type']").val( child.type );
        //alert(child.type);

        child.code = jQuery(this).find("input[name='code']").val();
        
        var status = "1";
        jQuery(this).find("input[type=radio]").each(function(){
           if( jQuery(this).is(':checked') ){
             status = jQuery(this).val();
           }
        });
        child.status = status;

        if( (child.code == 'miglad_') && (child.status == '2') ){ child.status = '3' }     
  
        children.push(child);
      });
      
      group.depth = leaf;
      group.child = children;
      
      fields.push(group);
   });
   //alert( fields[0]['title'] );
   return fields;
}

////////////KEYUP AND CHANGE/////////////////
function labelChanged(){
  jQuery('.labelChange').bind("keyup change", function(e) {
   var p = jQuery(this).closest('li.formfield');

   var val = jQuery(this).val().replace("'", "[q]");
   p.find("input[name=label]").val( val );

   if( p.find("input[name=code]").val() == 'miglac_' ){
    var id = val.replace(" ", "");
    p.find("input[name=id]").val( id );
   }
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

function findDuplicateTitle(value){
    var trimVal = value.replace("'", "[q]"); 
    return jQuery(".mHiddenTitle[value='" + trimVal + "']").length ;
}

function findDuplicateLabel(value){
    var trimVal = value.replace("'", "[q]");  
    return jQuery(".mHiddenLabel[value='" + trimVal + "']").length ;
}


function isFieldValid(){
  var isValid = true;
  var BreakException= {};

  try {
    jQuery('body').find('li.formheader').each(function(){
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
    jQuery(this).closest('li').remove();
    jQuery.ajax({
     type : "post",
     url : miglaAdminAjax.ajaxurl, 
     data : {action: "miglaA_update_form", values:getFormStructure() },
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

   var  newComer = "";
   newComer = newComer + "<li class='ui-state-default formfield clearfix justAdded'>";


   newComer = newComer + "<input class='mHiddenLabel' type='hidden' name='label' value='' />";
   newComer = newComer + "<input type='hidden' name='type' value='text' />";
   newComer = newComer + "<input type='hidden' name='id' value='' />";
   newComer = newComer + "<input type='hidden' name='code' value='miglac_' />";
   newComer = newComer + "<input type='hidden' name='status' value='1' />";

   newComer = newComer + "<div class='clabel col-sm-1 hidden-xs'><label class='control-label'>Label:</label></div>";
   newComer = newComer + "<div class='col-sm-3 col-xs-12'><input type='text' class='labelChange' name='labelChange' placeholder='";
   newComer = newComer + "' value='' /></div>";

   newComer = newComer + "<div class='ctype col-sm-2 col-xs-12'>";

  // alert(jQuery('select#addType option:selected').index());

   newComer = newComer + "<select class='typeChange' name='typeChange'>";

     newComer = newComer + "<option value='text'>text</option>";
     newComer = newComer + "<option value='checkbox'>checkbox</option>";
   newComer = newComer + "<option value='textarea'>textarea</option>";

   newComer = newComer + "</select>";

   newComer = newComer + "</div>";
   newComer = newComer + "<div class='cid col-sm-2 hidden-xs'><label></label></div>";
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



function addField(){

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

 jQuery('li.formheader').each(function(){
   var currow = jQuery(this).find('ul.rows'); 
   currow.find('li.justAdded').each(function(){ jQuery(this).fadeOut('slow').remove()});
   tempid = -1;
   if( currow.children('li').length > 0 ){ parent.find('.mDeleteGroup').removeClass('disabled');  }
 });
 ////CANCEL//////////////

 });

}

jQuery('#saveNewField').click(function(){

      var me = jQuery(this); 
      //alert( me.html() );
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
////////////////////////////


 if( isValid )
 {
  jQuery('body').find('li.justAdded').each(function(){
    var x = jQuery(this).find("input.labelChange").val();
    n = x.replace(" ","");
    x = n.replace("'","");

    jQuery(this).find("input[name='id']").val(x);
 
    jQuery(this).find("input[type=radio]").each(function(){
      jQuery(this).attr('name', (x+'st') );
    });

    jQuery(this).find("input[name='type']").val( jQuery(this).find("select[name='typeChange'] option:selected").val() );

    jQuery(this).removeClass('justAdded');

  });

     jQuery.ajax({
      type : "post",
      url : miglaAdminAjax.ajaxurl, 
      data : {action: "miglaA_update_form", values:getFormStructure()},
      success: function(msg) { 
       saved("#saveNewField"); 

       jQuery('body').find('.rowsavenewcomer').remove();

       removeField(); 
       ohDrag();
     }
    })  ; //ajax	 

 enFormfield();

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
      data : {action: "miglaA_update_form", values:getFormStructure() },
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
      jQuery(this).find(".typeChange option[value=" + s + "]").attr("selected","selected");
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

///////////////////////////////////// ON LOAD ////////////////////////////////////////////////////

jQuery(document).ready(
function() {
//alert('load');
clearLeftover();
getRidForbiddenChars();

jQuery('#miglaAddAmount').val('0');

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

add();
remove();

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
    data : {action: "miglaA_update_form", values: f },
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
     data : {action: "miglaA_update_form", values:fielddata },
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


//////////// Undesignated Label //////////////////////
 jQuery('#miglaUnLabelChange').click(function(){
 
   var label = jQuery('#mg-undesignated-default').val();
   jQuery.ajax({
        type : "post",
        url :  miglaAdminAjax.ajaxurl, 
        data : {action: "miglaA_updateUndesignated", old:jQuery('#mg_oldUnLabel').val(), new:label },
        success: function(msg) {  
         saved( '#miglaUnLabelChange' );
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

});


    

