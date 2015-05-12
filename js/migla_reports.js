	//////////////////////////////////////////
	////////// ONLINE DONATIONS REPORT ///////////////////////
//jQuery = jQuery.noConflict();

var dataArr;
var inQuery;
var ajaxData = {}; var form = {}; var orphan = {}; var alldata = {}; var fieldType = {}; var campaigns = {};
var custom_types = {};
var undesignatedLabel; var recurring = {};
var countries = {}; var states = {}; var provinces = {};
var oTable; 
var removeList = new Array(); 
var removeMessage = new Array();
var removeRow = new Array();
var displayID = new Array();

var mapKey = ['FirstName','LastName','campaign','Amount','Country','startdate','endddate'];
var mapFilter = ['','','','','','',''];
var before = ''; var after = '';
var thouSep = ''; var decSep = ''; var showDec;
var allAmount = 0;

function mdataTable( x ){

   var table;
   table = jQuery('#miglaReportTable').DataTable(
      {
      "scrollX": true ,
      "data": x,
      "columns" : [
            {
                "orderable":      false,
                "data":           'remove',
                "class" : 'removeColumn',
                "defaultContent": '',
                
            },            
            {
                "class":          'details-control sorting_disable',
                "orderable":      false,
                "orderable":      false,
                "data":           'detail',
                "defaultContent": ''                
            },            
            { "data": 'miglad_date' , sDefaultContent: "" },
            { "data": 'miglad_firstname', sDefaultContent: "" },
            { "data": 'miglad_lastname' , sDefaultContent: ""},
            { "data": 'miglad_campaign' , sDefaultContent: "" },
            { "data": 'miglad_amount' , sDefaultContent: ""},
            { "data": 'miglad_country' , sDefaultContent: ""},
            { "data": function ( row, type, val, meta ) {
                        var r = row.miglad_transactionType ;
						if( r == 'web_accept' ){
						    r = 'One time (Paypal)';
						}else if( r == 'subscr_payment' ){
						    r = 'Recurring (Paypal)';
						}
                  		r = r + " <i class='fa fa-check-circle'></i>";
                        if( row.miglad_charge_dispute == 'dispute' ){ r = row.miglad_transactionType + " <i class='fa fa-exclamation-triangle'></i>"; }
                        return r;
                     }
            },
            { "data": 'id' }
            ],
        "columnDefs": [            
                { "targets": [ 1 ], "searchable": false}
                ,
                { "targets": [ 9 ], "visible": false}
         ],
        "createdRow": function ( row, data, index ) {
           },
"fnFooterCallback": function ( nRow, aaData, iStart, iEnd, aiDisplay )
       {
                        /* Calculate the market share for browsers on this page  */
                        var iPage = 0;  displayID.length = 0;
                        for ( var i=0 ; i<aiDisplay.length ; i++ )
                        {
                            iPage += Number( aaData[ aiDisplay[i] ]['miglad_amount'] );
                            displayID.push( aaData[ aiDisplay[i] ]['id'] );

                        }
						
document.getElementById("miglaOnTotalAmount2").innerHTML = before +" "+ iPage.formatMoney(showDec , thouSep , decSep  ) + after; 
                         
                    },
        "language": {
			 "lengthMenu": '<label>Show  Entries<select>'+
			  '<option value="10">10</option>'+
			 '<option value="20">20</option>'+
			 '<option value="30">30</option>'+
			 '<option value="40">40</option>'+
			 '<option value="50">50</option>'+
			 '<option value="-1">All</option>'+
			 '</select></label>'
	},
       "fnDrawCallback": function( oSettings ) {
         var rows = jQuery('#miglaReportTable').dataTable().fnGetNodes();
         for(var i=0;i<rows.length;i++)
         {
           var r = rows[i];
           jQuery(r).removeClass('shown');
         }
        }
      });
         
  jQuery('th.detailsHeader').removeClass('sorting_asc');
  jQuery('th.detailsHeader').removeClass('sorting_desc');  
  jQuery('th.removeColumn').removeClass('sorting_asc');
  jQuery('th.removeColumn').removeClass('sorting_desc');  
   
    return table;
}

function mexport(){
   jQuery('#miglaExportAll').click(function(){
      alert("this might take a while if your dataset is large");
      jQuery("input[name='miglaFilters']").val("");
      jQuery('#miglaExportTable').submit();
   });

   jQuery('#exportTable').click(function(){
      if( displayID.length > 0 ){
        jQuery("input[name='miglaFilters']").val(displayID);
      }

      //alert( jQuery("input[name='miglaFilters']").val() );
      jQuery('#miglaExportTable').submit();
   });
}



function getIndex( id ){
  var idx = 0;
  for( var i = 0; i < ajaxData.length; i++)
  {  
    if( Number(id) == Number(ajaxData[i]['id']) ){
        idx = i;
    }
  }
  return idx;
}


function celClick(){

          var rows = jQuery('#miglaReportTable').dataTable().fnGetNodes();
          for(var i=0;i<rows.length;i++)
         {
              var r = rows[i];
               jQuery(r).removeClass('selectedrow');
          }

       var tr = jQuery(this).closest('tr');
        if ( tr.hasClass('selectedrow') ) {
            tr.removeClass('selectedrow');
        }
        else {
            tr.addClass('selectedrow');
        }

  if ( jQuery(this).hasClass('removeColumn') )
  {
       var parent = jQuery(this).closest('tr');
       var him = jQuery(this).find('.removeRow');
       var name = him.attr('name');

       if( jQuery(parent).hasClass('removed') ){
         removeList.remove(name); 
         jQuery(parent).closest("tr").removeClass('pink-highlight');
         jQuery(parent).removeClass('removed'); 
         
       }else {
         removeList.push( name );
         jQuery(parent).closest("tr").addClass('pink-highlight');
         jQuery(parent).addClass('removed'); 
       }

  }

  if( jQuery(this).hasClass('details-control') )
  {
         var tr = jQuery(this).closest('tr');
         var tt = jQuery(this).next();
         var aData = oTable.cell('.selectedrow', 9).data();
         //alert( getIndex( aData ) );

         if( tr.hasClass('shown') )
         {
         
          tr.removeClass('shown');
          var n = tr.next();
          n.remove();         
         }else{
          tr.addClass('shown');
          jQuery( format( ajaxData, getIndex( aData ) , form) ).insertAfter(tr);

          //let's get little table
          
         }   
  } 
}

function convertDate( theDate ){
  var str; var m; var d; var y;
  var field = theDate.split("/");
 switch ( Number(field[0]) ) {
    case 1:
        m = "Jan";
        break;
    case 2:
        m = "Feb";
        break;
    case 3:
        m = "March";
        break;
    case 4:
        m = "April";
        break;
    case 5:
        m = "May";
        break;
    case 6:
        m = "June";
        break;
    case 7:
        m = "July";
        break;
    case 8:
        m = "Aug";
        break;
    case 9:
        m = "Sept";
        break;
    case 10:
        m = "Oct";
        break;
    case 11:
        m = "Nov";
        break;
    case 12:
        m = "Dec";
        break;
  }

var dd = field[1].slice(-1); var something;
if( dd == "1" ){ something = "st"; }
else if( dd == "2" ){ something = "nd"; }
else{ something = "th"; } 

str = m + " " + String(Number(field[1])) +  something +", " + field[2];
return str;

}

function getData( data, val){
  var value = "";
  for ( key in data ) {
     if(  key == val ){ value = data[key]; }
  } 
  return value; 
}

function format ( d, idx  , form ) {
    var check = 0;
    var str = '';

    str = str + '<tr class="det"><td colspan="9">';
    str = str + '<div class="col-sm-6">';
    str = str + '<table class="table-hover" cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">';


       str = str + '<tr><td >Date : ' +  convertDate( d[idx]['miglad_date'] ) + '</td></tr>';
       str = str +  '<tr><td >Time : ' + d[idx]['miglad_time'] +  '</td></tr>';
       str = str +  '<tr><td >Time Zone : ' + d[idx]['miglad_timezone'] + ' ' + '</td></tr>';

 for ( key in form ){
  if( key=='parent_id' || key=='toggle' || key=='depth' || key=='remove' || typeof key === 'undefined')
  { }else{

    var title = String(form[key]['title']);
    
    var child = form[key]['child'];
    for ( key2 in child ) {
      var thecode = child[key2]['code'] + child[key2]['id'];
      var dataValue = getData( d[idx], thecode );

      if( dataValue == "" || typeof key2 === 'undefined'){

      }else{
        if( check == 0 ){
         str = str + "<tr class='reportGroupHeader'>" ;
         str = str + "<td >" + title.replace("[q]", "'") + " " + "</td>"+"</tr>";
        }

        var label = String(child[key2]['label']);
        str = str + '<tr>'+'<td >'  + label.replace("[q]", "'")  + ' : ' + dataValue + '</td>'+'</tr>';
        if( label == "Country" && dataValue == "Canada"){
           str = str + '<tr>'+'<td >Province : ' + getData( d[idx], "miglad_province") + '</td>'+'</tr>';     
        }
        if( label == "Country" && dataValue == "United States"){
           str = str + '<tr>'+'<td >Province : ' + getData( d[idx], "miglad_state") + '</td>'+'</tr>';     
        }
        ////////HONOREE's Country
        if( String(child[key2]['label']) == "Honoree[q]s Country" && dataValue == "Canada"){
           str = str + '<tr>'+'<td >Province : ' + getData( d[idx], "miglad_honoreeprovince") + '</td>'+'</tr>';     
        }
        if( String(child[key2]['label']) == "Honoree[q]s Country" && dataValue == "United States"){
           str = str + '<tr>'+'<td >Province : ' + getData( d[idx], "miglad_honoreestate") + '</td>'+'</tr>';     
        }

      }
      check = check + 1;
    }//for child

    check = 0;
  }//if else

 }//for form

   /////CUSTOM FIELD
   check = 0;   
   for( key4 in orphan[idx] )
   {
     if( orphan[idx][key4] != "" ){
          check = check + 1;
      }
   }

  if( check != 0 ){
         str = str + "<tr class='reportGroupHeader'> <td >User Custom Fields</td></tr>";
  }
   for( key4 in orphan[idx] )
   {
     if( orphan[idx][key4] != "" ){
       str = str + "<tr>" + "<td >" + key4.slice(7) + " : " + orphan[idx][key4] + "</td>" + "</tr>";
     }//if orphan not empty
   }
  
  ///PAYMENT Information
  str = str + "<tr class='reportGroupHeader'> <td >Payment Information</td></tr>";
  //str = str + "<tr>" + "<td >Session ID : " + getData( d[idx], "miglad_session_id") + "</td>" + "</tr>";

  var _status_payment = getData( d[idx], "miglad_charge_dispute");
  if( _status_payment == '' ){  
     str = str + "<tr>" + "<td >Status : Completed</td>" + "</tr>";
  }else{
     str = str + "<tr>" + "<td >Status : " + _status_payment + "</td>" + "</tr>";
  }
  str = str + "<tr>" + "<td >Payment Method : " + getData( d[idx], "miglad_paymentmethod") + "</td>" + "</tr>";

  var transType = getData( d[idx], "miglad_transactionType");

  str = str + "<tr>" + "<td >Transaction Type : " + transType + "</td>" + "</tr>";
  str = str + "<tr>" + "<td >Transaction ID : " + getData( d[idx], "miglad_transactionId") + "</td>" + "</tr>";

  if( transType.search( 'Recurring' ) >= 0 ){
     str = str + "<tr>" + "<td >Subscription ID : " + getData( d[idx], "miglad_subscription_id") + "</td>" + "</tr>";
  }
  str = str +  '</table>';

  str = str +  '<br>';

   str = str + '<a id="'+d[idx]['id']+'" title="Edit this record" class="mg_editrecord btn btn-primary obutton" href="#mg-edit-record">Edit this record (ID:'
   str = str + d[idx]['id']+')</a>';

  
 str = str + "</div>";

  if( transType == 'Recurring (Stripe)' || transType == 'Recurring (Paypal)' )
  {
    var subcr_id     = getData( d[idx], "miglad_subscription_id");
    var amount_each  = getData( d[idx], "miglad_amount");
    var total_amount = 0;
    if( subcr_id != null )
    {
      var rec_info = recurring[ subcr_id ];
      str = str +  '<div class="col-sm-6"><h4>Reoccuring Donations </h4>';
      str = str +  '<h6>Subscription ID : ' + subcr_id + '</h6>';
      str = str + '<table cellspacing="0" cellpadding="5" border="0" style="padding-left:50px;"><tbody><tr><td>Date : </td><td>Time</td><td>Amount</td></tr>';
      for( key3 in rec_info){
         if( typeof rec_info[key3]['date'] != 'undefined' ){
           str = str + '<tr><td>' + rec_info[key3]['date'] + '</td><td>'+rec_info[key3]['time']+'</td>'+'<td>'+amount_each+'</td>'+'</tr>';
           total_amount = total_amount + Number(amount_each);
         }
      } 
      str = str + '<tr><td></td><td>Total Amount : </td>'+'<td>'+total_amount+'</td>'+'</tr>';
      str = str + '</tbody></table>';
      str = str + '</div> <!-- / col-sm-6 -->';
    }
  }

  str = str + '</td>';
  str = str + '</tr>';

            
  return str;
}

function findWithAttr(array, attr, value) {
  var out = []; out[0] = ""; out[1] = false;
    for(var i = 0; i < array.length; i += 1) {
        if(array[i][attr] === value) {
            var r = "";
            r = r + "<tr><td width=''>"+array[i]['miglad_date']+"</td><td width='' align='center'>";
            r = r + array[i]['miglad_firstname']+"</td><td width=''>";
            r = r + array[i]['miglad_lastname']+"</td>";

            var status = "One time donation";
            var trans  = new String(array[i]['miglad_transactionType']);
            if( trans == 'subscr_payment' ||  trans == 'Recurring (Paypal)' || trans == "Recurring (Stripe)" )
            { 
               status = "Recurring Payment";  out[1] = true; 
            }

            r = r + "<td>" + status +"</td>";
            r = r + "<td width=''>"+array[i]['miglad_amount']+"</td>";
            r = r + "</tr>";

            out[0] = r;
            
            return out;
        }
    }
}
  
function isValid(){
  var isVal = true;
  jQuery('input.required').each(function(){
     if( jQuery(this).val() == '' ){
       jQuery(this).addClass('pink-highlight'); isVal = false;
     }
  });
  return isVal;
}

function getBack(){
  jQuery('input.required').each(function(){
     jQuery(this).removeClass('pink-highlight');
  });
}

function calcAmount(){
var num = 0;
    for(var i = 0; i < ajaxData.length; i += 1) {
        if( removeList.indexOf( ajaxData[i]['id'] ) > -1 ) {
          num = num + Number( ajaxData[i]['amount'] );
        }
    }
return num;	
}

jQuery(document).ready( function() {

  if( jQuery('#placement').text() == 'before'){ before =jQuery('div#symbol').html();after=''; }else{ after =jQuery('div#symbol').html();before=''; } 
  thouSep = jQuery('#thousandSep').text(); decSep = jQuery('#decimalSep').text();
  showDec = 0;
  if( jQuery('#showDecimal').text() == 'yes' ){ showDec = 2; }

  jQuery('#confirm-delete').modal({show: false});

  jQuery('#sdate, #edate').val("");

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

       jQuery('input[type=checkbox]').each(function () {
         jQuery(this).checked = false;
       });

 Array.prototype.remove = function(value) {
    var idx = this.indexOf(value);
    if (idx != -1) {
       return this.splice(idx, 1); // The second parameter is the number of elements to remove.
    }
   return false;
 }


/////RETRIEVE ALL RECORD///////////////////////////////

jQuery.ajax({
   type : "post",
   url :  miglaAdminAjax.ajaxurl,  
   data :  { action:'miglaA_report' },
     success: function(msg) {
      var output = JSON.parse(msg);
      alldata = output[0] ; form = output[1]; orphan = output[2]; fieldType = output[3]; campaigns = output[4]; undesignatedLabel = output[5];
      recurring = output[6];

    if( alldata.length > 0){ 
       alldata.sort(function (a, b) {
         return (new Date(b.miglad_date + ' ' + b.miglad_time) - new Date(a.miglad_date + ' ' + a.miglad_time) );
       });
     }
/*
    var list_ = "";
    for(key in alldata){
     //if( alldata[key]['miglad_firstname'] == 'Johny' ){
       for(key2 in alldata[key] ){
          list_ = list_ + " " + alldata[key][key2] + " ; ";
       }
       list_ = list_ + "<br>";
      //}
    }
    jQuery(list_).appendTo( jQuery('#mg_list') );
*/
     ajaxData = alldata;

            jQuery('#miglaReportTable tfoot th').each( function () {
               var title = jQuery('#miglaReportTable thead th').eq( (jQuery(this).index()+2) ).text();
               if ( title != 'Detail' && title != 'Date' ) {
                 jQuery(this).html( '<input type="text" placeholder="'+title+'" name="'+title+'" />' );
               }
             } ); //

      oTable = mdataTable( ajaxData );

//alert(ajaxData.length);

jQuery.fn.dataTable.ext.search.push(
    function( settings, data, dataIndex ) {
        var min = Date.parse( jQuery('#sdate').val() ) ;
        var max = Date.parse( jQuery('#edate').val() );
        var age = Date.parse( data[2] );
 
        if (  ( isNaN( min ) && isNaN( max ) ) ||
             ( isNaN( min ) && age <= max ) ||
             ( min <= age   && isNaN( max ) ) ||
             ( min <= age   && age <= max ) 
        )
        {
            return true;
        }
        return false;
});  

    // Event listener to the two range filtering inputs to redraw on input
    jQuery('#sdate, #edate').on( 'keyup change', function () {
      jQuery('#miglaReportTable').DataTable().draw();
      jQuery('.miglaOffdate').datepicker('hide');
    } );

        //Search on footer
         jQuery( 'input' ).on( 'keyup change', function () {
           //alert( jQuery(this).val() );
            var p = jQuery(this).parent();
            var col = p.attr("id");
            col = col.slice(1);
            
            jQuery('#miglaReportTable').DataTable().column( col ).search(
               jQuery(this).val(),
               jQuery(this).prop('checked'),
               jQuery(this).prop('checked')
         ).draw(); 

         });


      jQuery('#miglaReportTable tbody').delegate("td", "click", celClick);

     jQuery('.sorting').click(function(){
        var n = jQuery('tr.det');
        var m = jQuery('.shown');
        m.removeClass('shown');
        n.remove();
     })
 
      jQuery('th.detailsHeader').removeClass('sorting_asc');
      jQuery('th.detailsHeader').removeClass('sorting_desc');                 
             
    },
    async : false
}); //ajax  


////////////RETRIEVE ALL RECORDS END HERE////////////////////////


/////////// RETRIEVE DATA NEEDED FOR EDIT FORM /////////////////

  jQuery.ajax({
     type : "post",
     url :  miglaAdminAjax.ajaxurl, 
     data : {action: "miglaA_get_data_for_edit_form" },
	success: function(msg) {
           var output = eval(msg);
           countries = output[0] ; states = output[1]; provinces = output[2]; custom_types = output[3];
	}//success
        
     })  ; //ajax       


   // Open Tab
       jQuery('.fa-caret-blah').click(function() {
       jQuery('#row-upper').toggle();
    });	 
    

///////////////////// GET THE TOTAL /////////////////////////////
    jQuery.ajax({
     type : "post",
     url :  miglaAdminAjax.ajaxurl, 
     data : {action: "miglaA_totalAll" },
	success: function(msg) {
          d = eval( msg );
          document.getElementById("miglaOnTotalAmount").innerHTML = before +" "+ (d[0]).formatMoney(showDec , thouSep , decSep  ) + after;        
	}//success
     })  ; //ajax        



//datepicker
jQuery('.miglaOffdate').datepicker({
 dateFormat : 'mm/dd/yy', 
 onSelect: function() { 
   jQuery(".ui-datepicker a").removeAttr("href");
   jQuery('#sdate, #edate').trigger('change');
  } 
});

jQuery('#miglaAddOffline').click(function() {
 if ( isValid() )
 {
   add();
   getBack();
 }else{
   alert("Please input all required fields !!");
   canceled('#miglaAddOffline');
 }
});	  

jQuery('#miglaUnselect').click(function(){
  removeList.length = 0; 
  var rows = jQuery('#miglaReportTable').dataTable().fnGetNodes();
  for(var i=0;i<rows.length;i++)
  {
      var r = rows[i];
      jQuery(r).removeClass('pink-highlight');
      jQuery(r).removeClass('removed');
  }
});

jQuery('#miglaRemove').click( function(){
jQuery('#confirm-delete').show();
});

mexport();

/////////////////// DELETE RECORD /////////////////////////////////////////////////
jQuery('.danger').click( function(){

 if( removeList.length > 0 )
 {
  jQuery.ajax({
     type : "post",
     url :  miglaAdminAjax.ajaxurl,  
     data :  { action:'miglaA_remove_donation', list:inQuery },
     success: function() {
	   allAmount = allAmount - calcAmount();
		document.getElementById("miglaOnTotalAmount").innerHTML = before +" "+ (allAmount).formatMoney(showDec , thouSep , decSep ) + after; 
	 
       oTable.row('.removed').remove().draw( false );
       removeList.length = 0;    
       jQuery( ".close" ).trigger( "click" );
     }
  }); //ajax  
 }

//alert("hi"); 
});

jQuery('#confirm-delete').on('show.bs.modal', function(e) {
 var msg = "";
 var l = "";        inQuery = "";     var output = []; var isExistRepeat = false;
 
  jQuery(this).find('.modal-body').empty();
  
  if( removeList.length > 0 )
  {
    inQuery = "( " + removeList[0]; 

    output  = findWithAttr(ajaxData, "id", removeList[0]);
    l = l + "<table>" + output[0];

    isExistRepeat = isExistRepeat || output[1];

    output.length = 0;

  for(var i = 1; i < removeList.length; i++){

     isExistRepeat = isExistRepeat || output[1];   

      inQuery = inQuery + ", " + removeList[i];
      output = findWithAttr(ajaxData, "id", removeList[i]) ;
      l = l + output[0];


  }
  inQuery = inQuery + ")";
  l = l + "</table>";

  msg = msg + "<p>" + jQuery('#mg-warningconfirm1').text() + "</p>" + l + "<p>" + jQuery('#mg-warningconfirm2').text() + "</p>";

  if(   isExistRepeat ){
    msg = msg + "<br><small>" + jQuery('#mg-warningconfirm3').text() + "</small>";
  }

  jQuery('.btn-danger').show();

}else{
  msg = "<p>Nothing selected for deletion</p>";
  jQuery('.btn-danger').hide();
}
  jQuery(msg).prependTo( jQuery(this).find('.modal-body') );
           
})





/////////////////// EDIT RECORD /////////////////////////////////////////////////

 jQuery(document).on("click", ".mg_editrecord", function (e) {

	e.preventDefault();

	var _self = jQuery(this);
	var recId = _self.attr('id');

	jQuery("#recordID").val(recId);

	jQuery(_self.attr('href')).modal('show');

 });


 jQuery('#mg-edit-record').on('show.bs.modal', function(e) {

   //alert( JSON.stringify(custom_types) );

   var id    = jQuery("#recordID").val() ;
   var index = getIndex( Number(id) );
   var form  = "";
   var edited = ajaxData[index]; var typeOfField = fieldType[index];
   var curCountry = ''; var curState = ''; var curProvince = '';
   var curHCountry = ''; var curHState = ''; var curHProvince = '';

   jQuery(this).find('#mgModalEditLabel').text( "Edit Form Record-" + id);
   jQuery(this).find('.modal-body').empty();

   form = form + "<div class='form-horizontal'>";
   form = form + "<input id='edit_record_ajaxindex' type='hidden' value='" + index + "'/>";

   for( key in edited ){

    if( key.substr(0,4) == 'uid:' ){

    }else if( key == 'miglad_campaign' ){

      form = form + "<div class='form-group touching'>";
      form = form + "<div class='col-sm-3 col-xs-12'><label class='control-label text-right-sm text-center-xs'>"+key.slice(7)+"</label></div>";
      form = form + "<div class='col-sm-6 col-xs-12 mg_field_to_edit_div'>";
      form = form + "<div style='display:none' class='mg_edit_old_value'>" + ajaxData[index][key] + "</div>";

      form = form + "<select id='" + key + "' class='mg_field_to_edit'>";
      var isthere = false;
      for( key2 in campaigns ){
         if(campaigns[key2]['name']!=''){ 
           var cname = new String( campaigns[key2]['name'] );

           var re = /[q]/g;
           cname = cname.replace(re, "'");
           //cname = cname.replace( "[q]", "'" );

           if( cname  == ajaxData[index][key] ){ 
              isthere = true;  form = form + "<option selected value='"+campaigns[key2]['name']+"'>" + cname + "</option>"; 
           }else{
              form = form + "<option value='"+campaigns[key2]['name']+"'>" + cname + "</option>"; 
           }
         }
      }
      if( !isthere ){ form = form + "<option selected value='"+ajaxData[index][key]+"' >" + ajaxData[index][key] + "</option>";  }
      form = form + "<option value='"+undesignatedLabel+"' >" + undesignatedLabel + "</option>";
      form = form + "</select>";
 
      form = form + "</div>";
      form = form + "<div class='col-sm-3 hidden-xs'></div></div>";

    }else if( key == 'miglad_country' || key == 'miglad_honoreecountry' ){
        var checkedThis = ''; var div_id = '';
        if( key == 'miglad_country'){ curCountry = ajaxData[index][key]; checkedThis = curCountry; div_id = 'miglad_country_div' ;
       }else{ curHCountry = ajaxData[index][key]; checkedThis = curHCountry; div_id = 'miglad_honoreecountry_div' }

        form = form + "<div class='form-group touching' id='"+div_id+"'>";
        form = form + "<div class='col-sm-3 col-xs-12'><label class='control-label text-right-sm text-center-xs'>"+key.slice(7)+"</label></div>";
        form = form + "<div class='col-sm-6 col-xs-12 mg_field_to_edit_div'>";
        form = form + "<div style='display:none' class='mg_edit_old_value'>" + curCountry + "</div>";

        form = form + "<select id='" + key + "' class='mg_field_to_edit'>";
        var isthere = false;
          for( keyC in countries ){
            if( checkedThis ==  countries[keyC] ){
              form = form + "<option selected>" + countries[keyC] + "</option>"; isthere = true;
            }else{
              form = form + "<option>" + countries[keyC] + "</option>"; 
            }
          }
        if( !isthere ){ form = form + "<option selected value=''>Please pick one</option>";  }else{
          form = form + "<option value=''>Please pick one</option>";  
        }
        form = form + "</select>";
   
        form = form + "</div>";
        form = form + "<div class='col-sm-3 hidden-xs'></div></div>";
        
    }else if( key == 'miglad_state' ){
        curState = ajaxData[index][key];

    }else if( key == 'miglad_province' ){
        curProvince = ajaxData[index][key];

    }else if( key == 'miglad_honoreestate' ){
        curHState = ajaxData[index][key];

    }else if( key == 'miglad_honoreeprovince' ){
        curHProvince = ajaxData[index][key];

    }else{
      if( key=='remove' || key=='detail' ||  key=='id' || key=='miglad_session_id' || key=='miglad_paymentmethod' ||  
            key=='miglad_transactionType' || key == 'miglad_transactionId'  || key == 'miglad_timezone' || key == 'miglad_date' 
            || key == 'miglad_time' || key == 'paypaldata' || key == 'miglad_subscr_id' || key == 'miglad_repeating' || key == 'miglad_subscription_id'
       )
      {
          if( key=='miglad_session_id'){
             form = form + "<input id='edit_record_session' type='hidden' value='" + ajaxData[index][key] + "'/>";
          }
 
      }else if( key=='miglad_amount' ){

          var label = key.slice(7);
            form = form + "<div class='form-group touching'>";
            form = form + "<div class='col-sm-3 col-xs-12'><label class='control-label text-right-sm text-center-xs'>"+label+"</label></div>";
            form = form + "<div class='col-sm-6 col-xs-12 mg_field_to_edit_div'>";
            form = form + "<div style='display:none' class='mg_edit_old_value'>" + ajaxData[index][key] + "</div>";

            form = form + "<input disabled class='mg_field_to_edit disabled' type='text' id='" + key + "' placeholder='"+ajaxData[index][key]+"' value='"+ajaxData[index][key]+"' />" ;
            form = form + "</div>";
            form = form + "<div class='col-sm-3 hidden-xs'></div></div>";

      }else{
        var label = key.slice(7);
          if( fieldType[index][key] == 'checkbox' ){

            form = form + "<div class='form-group touching'>";
            form = form + "<div class='col-sm-3 col-xs-12'><label class='control-label text-right-sm text-center-xs'>"+label+"</label></div>";
            form = form + "<div class='col-sm-6 col-xs-12 mg_field_to_edit_div'>";
            form = form + "<div style='display:none' class='mg_edit_old_value'>" + ajaxData[index][key] + "</div>";
  
            if(  ajaxData[index][key] == 'yes' ){
               form = form + "<input class='mg_field_to_edit' name='"+key+"' type='checkbox' id='" + key + "' checked value='yes' />" ;
            }else{
               form = form + "<input class='mg_field_to_edit' name='"+key+"' type='checkbox' id='" + key + "' value='yes' />" ;
            }
            form = form + "</div>";
            form = form + "<div class='col-sm-3 hidden-xs'></div></div>";
          
          }else if( fieldType[index][key] == 'select' ){

            var _uid = 'mgval_' + ajaxData[index][('uid:' + key )];
            if( String(custom_types[ _uid ]) != '' && typeof custom_types[ _uid ] != 'undefined' )
            {
              var label = key.slice(7);
              form = form + "<div class='form-group touching'>";
              form = form + "<div class='col-sm-3 col-xs-12'><label class='control-label text-right-sm text-center-xs'>"+label+"</label></div>";
              form = form + "<div class='col-sm-6 col-xs-12 mg_field_to_edit_div'>";
              form = form + "<div style='display:none' class='mg_edit_old_value'>" + ajaxData[index][key] + "</div>";
       
              var _values = custom_types[ _uid ].split(";");
              var i = 0; var isthere = false;

               form = form + "<select class='mg_field_to_edit' id='"+key+"' >";
               for( _key in _values ){
                 if( i < (_values.length-1) )
                 {
                   var pair = _values[_key].split("::");
                   if( ajaxData[index][key] == pair[0] ){
                      form = form + "<option value='" + pair[0] + "' selected>" + pair[1] + "</option>";
                      isthere = true;
                   }else{
                      form = form + "<option value='" + pair[0] + "'>" + pair[1] + "</option>";
                   }
                 }
                 i++;
               }

               if( isthere ){
                   form = form + "<option value='' >Please Choose One</option>";
               }else{
                   form = form + "<option value='' selected>Please Choose One</option>";
               }

               form = form + "</select>";  

               form = form + "</div>";
               form = form + "<div class='col-sm-3 hidden-xs'></div></div>";
            }

          }else if( fieldType[index][key] == 'radio' ){

            var _uid = 'mgval_' + ajaxData[index][('uid:' + key )];
            if( String(custom_types[ _uid ]) != '' && typeof custom_types[ _uid ] != 'undefined' )
            {
              var label = key.slice(7);
              form = form + "<div class='form-group touching'>";
              form = form + "<div class='col-sm-3 col-xs-12'><label class='control-label text-right-sm text-center-xs'>"+label+"</label></div>";
              form = form + "<div class='col-sm-6 col-xs-12 mg_field_to_edit_div'>";
              form = form + "<div style='display:none' class='mg_edit_old_value'>" + ajaxData[index][key] + "</div>";
           
              var _values = custom_types[ _uid ].split(";");
              var i = 0; var isthere = false;
              for( _key in _values ){
                if( i < (_values.length-1) )
                {
                  var pair = _values[_key].split("::");
                  form = form + "<div class='radio'><label for='"+key+i+"'>";
                  if( ajaxData[index][key] == pair[0] ){
                     form = form + "<input name='"+key+"' id='"+key+i+"' value='" + pair[0] + "' checked='checked' type='radio' class='mg_field_to_edit'>";
                     isthere = true;
                  }else{
                     form = form + "<input name='"+key+"' id='"+key+i+"' value='" + pair[0] + "' type='radio' class='mg_field_to_edit'>";
                  }
                  form = form + pair[1];
                  form = form + "</label></div>";      
                }
                i++;
              }
              form = form + "<div class='radio'><label for='"+key+i+"' >";
              if( isthere ){
                   form = form + "<input name='"+key+"' id='"+key+i+"' value='' type='radio' class='mg_field_to_edit'>";
              }else{
                   form = form + "<input name='"+key+"' id='"+key+i+"' value='' checked='checked' type='radio' class='mg_field_to_edit'>";
              }
              form = form + 'none';
              form = form + "</label></div>";  

              form = form + "</div>";
              form = form + "<div class='col-sm-3 hidden-xs'></div></div>";
            }

          }else if( fieldType[index][key] == 'multiplecheckbox' ){

            var _uid = 'mgval_' + ajaxData[index][('uid:' + key )];
            if( String(custom_types[ _uid ]) != '' && typeof custom_types[ _uid ] != 'undefined' )
            {
              var label = key.slice(7);
               form = form + "<div class='form-group touching'>";
               form = form + "<div class='col-sm-3 col-xs-12'><label class='control-label text-right-sm text-center-xs'>"+label+"</label></div>";
               form = form + "<div class='col-sm-6 col-xs-12 mg_field_to_edit_div'>";
               form = form + "<div style='display:none' class='mg_edit_old_value'>" + ajaxData[index][key] + "</div>";

               var _values = custom_types[ _uid ].split(";");
               var i = 0; var isthere = false;
               for( _key in _values ){
                 if( i < (_values.length-1) )
                 {
                    var pair = _values[_key].split("::");
                    form = form + "<div class='checkbox'><label for='"+key+i+"' >";
                    if( ajaxData[index][key].search( pair[0] ) >= 0 && ajaxData[index][key] != '' )
                    {
                      form = form + "<input id='"+key+i+"' name='"+key+"' value='" + pair[0] + "' checked='checked' type='checkbox' class='mg_field_to_edit'>";
                      isthere = true;
                    }else{
                      form = form + "<input id='"+key+i+"' name='"+key+"' value='" + pair[0] + "' type='checkbox'  class='mg_field_to_edit'>";
                    }
                    form = form + pair[1];
                    form = form + "</label></div>";      
                 }
                 i++;
              }

              form = form + "</label></div>"; 
 
              form = form + "</div>";
              form = form + "<!--<div class='col-sm-3 hidden-xs'></div></div>-->";
            }

          }else {
            form = form + "<div class='form-group touching'>";
            form = form + "<div class='col-sm-3 col-xs-12'><label class='control-label text-right-sm text-center-xs'>"+label+"</label></div>";
            form = form + "<div class='col-sm-6 col-xs-12 mg_field_to_edit_div'>";
            form = form + "<div style='display:none' class='mg_edit_old_value'>" + ajaxData[index][key] + "</div>";

            form = form + "<input class='mg_field_to_edit' type='text' id='" + key + "' placeholder='"+ajaxData[index][key]+"' value='"+ajaxData[index][key]+"' />" ;
            form = form + "</div>";
            form = form + "<div class='col-sm-3 hidden-xs'></div></div>";
          }
      }
    }//campaign check

   }//for each

   form = form + "</div>";

   //gdisplay editable form
   jQuery( form ).prependTo( jQuery(this).find('.modal-body') );

///////////////////////////////////////////////////////////////////////////
//                      CHECK COUNTRY                                    //
///////////////////////////////////////////////////////////////////////////

   if( jQuery( "#miglad_country" ).val() == 'Canada' ){
           jQuery('#mg-edit-record').find('#miglad_province_div').remove();
           jQuery('#mg-edit-record').find('#miglad_state_div').remove();

           var r = get_provinces( 'miglad_province' , curProvince );
           jQuery( r ).insertAfter( jQuery('#miglad_country_div') );
   }
   if( jQuery( "#miglad_country" ).val() == 'United States' ){
           jQuery('#mg-edit-record').find('#miglad_province_div').remove();
           jQuery('#mg-edit-record').find('#miglad_state_div').remove();

           var r = get_states( 'miglad_state' , curState );
           jQuery( r ).insertAfter( jQuery('#miglad_country_div') );
   }

   if( jQuery( "#miglad_honoreecountry" ).val() == 'Canada' ){
           jQuery('#mg-edit-record').find('#miglad_honoreeprovince_div').remove();
           jQuery('#mg-edit-record').find('#miglad_honoreestate_div').remove();

           var r = get_provinces( 'miglad_honoreeprovince' , curHProvince );
           jQuery( r ).insertAfter( jQuery('#miglad_honoreecountry_div') );
   }
   if( jQuery( "#miglad_honoreecountry" ).val() == 'United States' ){
           jQuery('#mg-edit-record').find('#miglad_honoreeprovince_div').remove();
           jQuery('#mg-edit-record').find('#miglad_honoreestate_div').remove();

           var r = get_states( 'miglad_honoreestate' , curHState );
           jQuery( r ).insertAfter( jQuery('#miglad_honoreecountry_div') );
   }


   jQuery( "#miglad_country" ).bind( "change", function() {
           jQuery('#mg-edit-record').find('#miglad_province_div').remove();
           jQuery('#mg-edit-record').find('#miglad_state_div').remove();

        if( jQuery(this).val() == 'Canada' ){
           var r = get_provinces( 'miglad_province' , curProvince );
           jQuery( r ).insertAfter( jQuery('#miglad_country_div') );

        }else if( jQuery(this).val() == 'United States' ){
           var r = get_states( 'miglad_state' , curState );
           jQuery( r ).insertAfter( jQuery('#miglad_country_div') );

        }
   });

   jQuery( "#miglad_honoreecountry" ).bind( "change", function() {
           jQuery('#mg-edit-record').find('#miglad_honoreeprovince_div').remove();
           jQuery('#mg-edit-record').find('#miglad_honoreestate_div').remove();

        if( jQuery(this).val() == 'Canada' ){
           var r = get_provinces( 'miglad_honoreeprovince' , curHProvince );
           jQuery( r ).insertAfter( jQuery('#miglad_honoreecountry_div') );

        }else if( jQuery(this).val() == 'United States' ){
           var r = get_states( 'miglad_honoreestate' , curHState );
           jQuery( r ).insertAfter( jQuery('#miglad_honoreecountry_div') );

        }
   });

 });




///////////////////////////////////////////////////////////////////////////
//                              RESTORE                                   //
///////////////////////////////////////////////////////////////////////////
 jQuery('#mg_restore_record1').click(function(){

    jQuery.ajax({
      type : "post",
      url :  miglaAdminAjax.ajaxurl,  
      data :  { action:'miglaA_restore_donation1', post_id:jQuery("#recordID").val() , session_id:jQuery("#edit_record_session").val() },
      success: function(msg) {
       alert(msg);
       location.reload(); 
      }
    }); //ajax 

 });

 jQuery('#mg_restore_record2').click(function(){

    jQuery.ajax({
      type : "post",
      url :  miglaAdminAjax.ajaxurl,  
      data :  { action:'miglaA_restore_donation2', post_id:jQuery("#recordID").val() , session_id:jQuery("#edit_record_session").val() },
      success: function(msg) {
       alert(msg);
       location.reload(); 
      }
    }); //ajax 

 });

 jQuery('#mg_restore_record3').click(function(){

    jQuery.ajax({
      type : "post",
      url :  miglaAdminAjax.ajaxurl,  
      data :  { action:'miglaA_restore_donation3', post_id:jQuery("#recordID").val() , session_id:jQuery("#edit_record_session").val() },
      success: function(msg) {
       alert(msg);
       location.reload(); 
      }
    }); //ajax 

 });

///////////////////////////////////////////////////////////////////////////
//                              UPDATE                                  //
///////////////////////////////////////////////////////////////////////////
 jQuery('#mg_update_record').click(function(){
    var data_for_update = get_data_for_update();

    //alert( JSON.stringify(data_for_update) );

    jQuery.ajax({
      type : "post",
      url :  miglaAdminAjax.ajaxurl,  
      data :  { action:'miglaA_change_donation', post_id:jQuery("#recordID").val() , arrayData:data_for_update },
      success: function(msg) {
        //alert(msg);
        saved('#mg_update_record'); doRefresh(ajaxData);
        jQuery('#mg-edit-record-close').trigger('click');
      }
    }); //ajax 
 });


}); //Document ready



function get_data_for_update(){
  var updatedFields = []; var row = 0;

  jQuery('#mg-edit-record').find('.mg_field_to_edit_div').each(function(){

     var new_val = jQuery(this).find('.mg_field_to_edit');
     var val     = new_val.val();
     var id      = new_val.attr('id');
     var old_val = jQuery(this).find('.mg_edit_old_value').text();

     var ajaxIdx = jQuery('#edit_record_ajaxindex').val();

          ////////// PUSH IT ////////////////////////
          if( new_val.attr('type') == 'checkbox' )
          {
                val = ""; 
                if( new_val.length > 1 )
                {
                    id  = jQuery(this).find('.mg_field_to_edit:first').attr('name');
                    new_val.each(function(){
                        if( jQuery(this).is(':checked') )
                        {
                            val = val + jQuery(this).val() + ", ";
                            id  = jQuery(this).attr('name');
                        }
                    });
                }else{
                  id  = new_val.attr('name');
                  if( new_val.is(':checked') ){
                     val     = new_val.val();
                  }else{
                     val     = 'no';
                  }
                }

            //if( val != old_val ){
                val = new String( val );
                var re = /[q]/g;
                var cval = val.replace(re, "'");
                //var cval =  val.replace( "[q]", "'" );
                ajaxData[ajaxIdx][id] = cval;
             
                var e = [ id , val ];  
                updatedFields.push(e);
             //}
            
          }else if ( new_val.attr('type') == 'radio' )
          {  
              id  = new_val.attr('name');             
              val = jQuery(this).find("input[name='"+id+"']:checked").val();

            if( val != old_val ){
                val = new String( val );
                var re = /[q]/g;
                var cval = val.replace(re, "'");
                //var cval =  val.replace( "[q]", "'" );
                ajaxData[ajaxIdx][id] = cval;
             
                var e = [ id , val ];  
                updatedFields.push(e);
             }

          }else if( new_val.attr('type') == 'text' ){
            if( val != old_val ){
                val = new String( val );
                var re = /[q]/g;
                var cval = val.replace(re, "'");
                //var cval =  val.replace( "[q]", "'" );
                ajaxData[ajaxIdx][id] = cval;
             
                var e = [ id , val ];  
                updatedFields.push(e);
             }

          }else
          {
              val = jQuery( ('#' + id) ).val();

            if( val != old_val ){
                val = new String( val );
                var re = /[q]/g;
                var cval = val.replace(re, "'");
                //var cval =  val.replace( "[q]", "'" );
                ajaxData[ajaxIdx][id] = cval;
             
                var e = [ id , val ];  
                updatedFields.push(e);
             }

          }


  });
  return updatedFields;
}

function get_provinces(id, current){

   /////// check country ///////////////////////////////////////////
   var province_selection = "";   
        province_selection = province_selection + "<div class='form-group touching' id='"+id+"_div'>";
        province_selection = province_selection + "<div class='col-sm-3 col-xs-12'><label class='control-label text-right-sm text-center-xs'>Province</label></div>";
        province_selection = province_selection + "<div class='col-sm-6 col-xs-12 mg_field_to_edit_div'>";
        province_selection = province_selection + "<div style='display:none' class='mg_edit_old_value'>" + current + "</div>";

        province_selection = province_selection + "<select id='" + id + "' class='mg_field_to_edit'>";

        var isthere = false;
          for( keyP in provinces ){
            if( current ==  provinces[keyP] ){
              province_selection = province_selection + "<option selected value='"+provinces[keyP]+"'>" + provinces[keyP] + "</option>"; isthere = true;
            }else{
              province_selection = province_selection + "<option value='"+provinces[keyP]+"'>" + provinces[keyP] + "</option>"; 
            }
          }
        if( !isthere ){ province_selection = province_selection + "<option selected value=''>Please pick one</option>";  }else{
          province_selection = province_selection + "<option value=''>Please pick one</option>";  
        }
        province_selection = province_selection + "</select>";
   
        province_selection = province_selection + "</div>";
        province_selection = province_selection + "<div class='col-sm-3 hidden-xs'></div></div>";

   return province_selection;
}

function get_states(id, current){

   /////// cek country ///////////////////////////////////////////
   var province_selection = "";   
        province_selection = province_selection + "<div class='form-group touching' id='"+id+"_div'>";
        province_selection = province_selection + "<div class='col-sm-3 col-xs-12'><label class='control-label text-right-sm text-center-xs'>State</label></div>";
        province_selection = province_selection + "<div class='col-sm-6 col-xs-12 mg_field_to_edit_div'>";
        province_selection = province_selection + "<div style='display:none' class='mg_edit_old_value'>" + current + "</div>";

        province_selection = province_selection + "<select id='" + id + "' class='mg_field_to_edit'>";
        var isthere = false;
          for( keyP in states ){
            if( current ==  states[keyP] ){
              province_selection = province_selection + "<option selected value='"+states[keyP]+"'>" + states[keyP] + "</option>"; isthere = true;
            }else{
              province_selection = province_selection + "<option value='"+states[keyP]+"'>" + states[keyP] + "</option>"; 
            }
          }
        if( !isthere ){ province_selection = province_selection + "<option selected value=''>Please pick one</option>";  }else{
          province_selection = province_selection + "<option value=''>Please pick one</option>";  
        }
        province_selection = province_selection + "</select>";
   
        province_selection = province_selection + "</div>";
        province_selection = province_selection + "<div class='col-sm-3 hidden-xs'></div></div>";

   return province_selection;
}


function doRefresh(data) {
        var oTable = jQuery('#miglaReportTable').dataTable();
        oTable.fnClearTable();
        oTable.fnAddData(ajaxData);
        oTable.fnDraw();
}


