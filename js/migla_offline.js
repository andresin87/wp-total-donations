	//////////////////////////////////////////
	////////// OFFLINE DONATION REPORT///////////////////////
//jQuery = jQuery.noConflict();

var dataArr;
var inQuery;
var ajaxData = {}; 
var oTable; 
var removeList = new Array();
var removeMessage = new Array();
var removeRow = new Array();
var displayID = new Array();
var before = ''; var after = '';
var thouSep = ''; var decSep = ''; var showDec;
var allAmount = 0;

function mdataTable( x ){

   var table = jQuery('#miglaReportTable').DataTable(
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
            { "data": 'date' , sDefaultContent: "" },
            { "data": 'firstname', sDefaultContent: "" },
            { "data": 'lastname' , sDefaultContent: ""},
            { "data": 'campaign' , sDefaultContent: "" },
            { "data": 'amount' , sDefaultContent: ""},
            { "data": 'country' , sDefaultContent: ""},
            { "data": 'id' }
            ],
        "columnDefs": [            
                { "targets": [ 1 ], "searchable": false},
                { "targets": [ 8 ], "visible": false}
         ],
        "createdRow": function ( row, data, index ) {
           },
"fnFooterCallback": function ( nRow, aaData, iStart, iEnd, aiDisplay )
       {
                        /* Calculate the market share for browsers on this page  */
                        var iPage = 0;  displayID.length = 0;
                        for ( var i=0 ; i<aiDisplay.length ; i++ )
                        {
                            iPage += Number( aaData[ aiDisplay[i] ]['amount'] );
                            displayID.push( aaData[ aiDisplay[i] ]['id'] );

                        }
document.getElementById("miglaOnTotalAmount2").innerHTML =  before +" "+ iPage.formatMoney(showDec, thouSep , decSep  ) + after; 
                         
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

function init(){
       jQuery("input[name='miglad_firstname']").val('');
       jQuery("input[name='miglad_lastname']").val('');
       jQuery("input[name='miglad_amount']").val('');
       jQuery("input[name='miglad_anonymous']").checked = false ;
       jQuery("select[name='miglad_campaign']").attr("selectedIndex", 0);
       jQuery("input[name='miglad_date']").val('');
       jQuery("input[name='miglad_address']").val('');
       jQuery("input[name='miglad_email']").val('');
       jQuery("select[name='migla_donor_country']").attr("selectedIndex", 0);
       jQuery("select[name='migla_Canada_provinces']").attr("selectedIndex", 0);
       jQuery("select[name='migla_US_states']").attr("selectedIndex", 0);
       jQuery("input[name='miglad_zip']").val('');
       jQuery("input[name='miglad_orgname']").val('');
       jQuery("input[name='miglad_employer']").val('');
       jQuery("input[name='miglad_occupation']").val('');
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
         //var index = jQuery(this).find('input.mglrec').attr('name');
         //alert(index);
         var aData = oTable.cell('.selectedrow', 8).data();
         var index = getIndex( aData );

         if( tr.hasClass('shown') )
         {
         
          tr.removeClass('shown');
          var n = tr.next();
          n.remove();         
         }else{
          tr.addClass('shown');
          jQuery( format( ajaxData, index ) ).insertAfter(tr);
         }   
  } 
}

function add(){
      var fname, lname, amount, anonymous, campaign, date, address, email, country, state, provinces, zip, orgname, transactionType, employer, occupation;
       var newRow = {};

       fname = jQuery("input[name='miglad_firstname']").val();
       lname = jQuery("input[name='miglad_lastname']").val();
       var dirtyamount = jQuery("input[name='miglad_amount']").val();
       amount = dirtyamount.replace( jQuery('#miglaDecimalSep').val() , ".");

       if ( jQuery("input[name='miglad_anonymous']").is(":checked") ) { anonymous='yes';}else{ anonymous='no';}

       campaign = jQuery("select[name='miglad_campaign'] option:selected").text();
       date = jQuery("input[name='miglad_date']").val();
       address = jQuery("input[name='miglad_address']").val();
       email = jQuery("input[name='miglad_email']").val();
       country = jQuery("select[name='miglad_country'] option:selected").text();

provinces = '';
if( country == 'Canada'){
       provinces = jQuery("select[name='miglad_province'] option:selected").val();
}

state = '';
if( country == 'United States'){
       state = jQuery("select[name='miglad_state'] option:selected").val();
}

       zip = jQuery("input[name='miglad_zip']").val();
       orgname = jQuery("input[name='miglad_orgname']").val();
       transactionType = jQuery("#miglad_transactionType").val();
       employer = jQuery("input[name='miglad_employer']").val();
       occupation = jQuery("input[name='miglad_occupation']").val();

       var theData = { action:'miglaA_insert_offline_donation', mfirstname:fname, mlastname:lname, mamount:amount,
          manonymous:anonymous, mcampaign:campaign, mdate:date, maddress:address, memail:email, mcountry:country,
          mstate:state, mprovince:provinces, mzip:zip, morgname:orgname, mtransactionType:transactionType, memployer:employer, moccupation:occupation};

newRow['date'] = date; 
newRow['firstname'] = fname; 
newRow['lastname'] = lname; 
newRow['amount'] = amount; 
newRow['campaign'] = campaign; 
newRow['anonymous'] = anonymous; 
newRow['email'] = email; 
newRow['address'] = address; 
newRow['country'] = country; 
newRow['state'] = ''; 
newRow['zip'] = zip; 
newRow['orgname'] = orgname;
newRow['transactionType'] = transactionType;
newRow['employer'] = employer;
newRow['occupation'] = occupation;

if( country == 'Canada'){
  newRow['state'] = provinces; 
}else if( country == 'United States'){
  newRow['state'] = state; 
}

   jQuery.ajax({
        type : "post",
        url :  miglaAdminAjax.ajaxurl,  
        data :  theData,
        success: function(msg) {
          //alert(msg);
           newRow['id'] = msg;
           newRow['remove'] = "<input type='hidden' name='"+msg+"' class='removeRow' /><i class='fa fa-trash'></i>";
           newRow['detail'] = "<input class='mglrec' type=hidden name='" + ajaxData.length + "' >";

           oTable.row.add( newRow ).draw();
           addToJSON( newRow ); //alert(JSON.stringify(ajaxData, null, 4));
           saved( '#miglaAddOffline' );
		   allAmount = allAmount + Number(amount);
		   document.getElementById("miglaOnTotalAmount").innerHTML = before +" "+ (allAmount).formatMoney(2, thouSep , decSep ) + after; 
           init();

       }//success
       }); //ajax 
 
}

function addToJSON( d ){
  var tes = {};
  tes.id = d['id'];
  tes.remove = d['remove'];
  tes.detail = d['detail'];
  tes.date = d['date'];
  tes.firstname = d['firstname'];
  tes.lastname = d['lastname'];
  tes.amount = d['amount'];
  tes.anonymous = d['anonymous'];
  tes.campaign = d['campaign'];
  tes.address = d['address'];
  tes.email = d['email'];
  tes.country = d['country'];
  tes.state = d['state'];
  tes.zip = d['zip'];
  tes.orgname = d['orgname'];
  tes.transactionType = d['transactionType'];
  tes.employer = d['employer'];
  tes.occupation = d['occupation'];
  ajaxData.push(tes);
}

function format ( d, idx ) {
    var str = '<tr class="det"><td colspan=7><table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">';

        str = str + '<tr>'+
            '<td>Date : </td>'+
            '<td>'+ d[idx]['date']  + '</td></tr>' ; 

        str = str + '<tr>'+
            '<td>Name : </td>'+
            '<td>'+ d[idx]['firstname'] + ' ' + d[idx]['lastname'] + '</td></tr>' ; 

    if( d[idx]['orgname'] != '' ){
        str = str + '<tr>'+
            '<td>Organisation : </td>'+
            '<td>'+ d[idx]['orgname'] + '</td></tr>' ;
    }  

        str = str + '<tr>'+
            '<td>Amount : </td>'+
            '<td>'+ d[idx]['amount']  + '</td></tr>' ; 

    if( d[idx]['transactionType'] != '' ){
        str = str + '<tr>'+
            '<td>Payment Type : </td>'+
            '<td>'+ d[idx]['transactionType'] + '</td></tr>' ;
    }  


        str = str + '<tr>'+
            '<td>Campaign : </td>'+
            '<td>'+ d[idx]['campaign']  + '</td></tr>' ; 


    if( d[idx]['email'] != '' ){
        str = str + '<tr>'+
            '<td>Email : </td>'+
            '<td>'+ d[idx]['email'] + '</td></tr>' ;
    }    

    if( d[idx]['address'] != '' ){
        str = str + '<tr>'+
            '<td>Address : </td>'+
            '<td>'+ d[idx]['address'] + '</td></tr>' ;
     }

     if( d[idx]['country'] != '' ){
        str = str + '<tr>'+
            '<td>Country : </td>'+
            '<td>'+ d[idx]['country'] + '</td></tr>' ;
     }

    if( d[idx]['state'] != ''){
      if( d[idx]['country']=='Canada'  ){
        str = str + '<tr>'+ '<td>Province : </td>';
      }
      if( d[idx]['country']=='United States'  ){
        str = str + '<tr>'+ '<td>State : </td>';
      }
        str = str + '<td>'+ d[idx]['state'] + '</td></tr>' ;
    }
  
    if( d[idx]['zip'] != '' ){
        str = str + '<tr>'+
            '<td>Postal Code : </td>'+
            '<td>'+ d[idx]['zip'] + '</td></tr>' ;
     }

    if( d[idx]['anonymous'] != '' ){
        str = str + '<tr>'+
            '<td>Anonymous : </td>'+
            '<td>' + d[idx]['anonymous'] + '</td>'+
        '</tr>';
    }

    if( d[idx]['employer'] != '' ){
        str = str + '<tr>'+
            '<td>Employer : </td>'+
            '<td>'+ d[idx]['employer'] + '</td></tr>' ;
    }  

    if( d[idx]['occupation'] != '' ){
        str = str + '<tr>'+
            '<td>Occupation : </td>'+
            '<td>'+ d[idx]['occupation'] + '</td></tr>' ;
    }  


        str = str +  '</table><td></tr>';
                
        return str;
}

function findWithAttr(array, attr, value) {
    for(var i = 0; i < array.length; i += 1) {
        if(array[i][attr] === value) {
            var r = "";
            r = r + "<tr><td width=''>"+array[i]['date']+"</td><td width='' align='center'>";
            r = r + array[i]['firstname']+"</td><td width=''>";
            r = r + array[i]['lastname']+"</td><td width=''>"+array[i]['amount']+"</td></tr>";
            return r;
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

jQuery(document).ready(function() {

init();

   jQuery('.miglaNAD2').on('keypress', function (e){ 
     var str = jQuery(this).val(); 
     var separator = jQuery('#miglaDecimalSep').val();
     var key = String.fromCharCode(e.which);
     //alert(String.fromCharCode(e.which) + e.keycode + e.which + separator);

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
  });

if( jQuery('#placement').text() == 'before'){ before =jQuery('div#symbol').html(); }else{ after =jQuery('div#symbol').html(); }
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
   data :  { action:'miglaA_getOffDonation' },
     success: function(msg) {
      ajaxData = JSON.parse(msg);
           jQuery('#miglaReportTable tfoot th').each( function () {
               var title = jQuery('#miglaReportTable thead th').eq( (jQuery(this).index()+2) ).text();
               if ( title != 'Detail' && title != 'Date' ) {
                 jQuery(this).html( '<input type="text" placeholder="'+title+'" name="'+title+'" />' );
               }
             } ); //

      oTable = mdataTable( ajaxData );

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
             
    }
}); //ajax  

////////////RETRIEVE ALL RECORDS END HERE////////////////////////

   // Open Tab
       jQuery('.fa-caret-blah').click(function() {
       jQuery('#row-upper').toggle();
    });	 
    
    jQuery.ajax({
     type : "post",
     url :  miglaAdminAjax.ajaxurl, 
     data : {action: "miglaA_totalOffAll" },
	success: function(msg) {
          d = eval( msg ); 
		  allAmount = d[0];
          document.getElementById("miglaOnTotalAmount").innerHTML = before +" "+ (allAmount).formatMoney(showDec, thouSep , decSep ) + after;         
	}//success
     })  ; //ajax        

   ///////////////Country detect///////////////////////////
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

jQuery('#miglaRemove').click( function(){
jQuery('#confirm-delete').show();
});

mexport();

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

jQuery('#miglacancel').click( function(){

});

jQuery('.danger').click( function(){
 if( removeList.length > 0 )
 {
  jQuery.ajax({
     type : "post",
     url :  miglaAdminAjax.ajaxurl,  
     data :  { action:'miglaA_remove_donation', list:inQuery },
     success: function() {
	   allAmount = allAmount - calcAmount();
		document.getElementById("miglaOnTotalAmount").innerHTML = before +" "+ (allAmount).formatMoney(showDec, thouSep , decSep ) + after; 
	   
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
var l = "";        inQuery = "";
jQuery(this).find('.modal-body').empty();
if( removeList.length > 0 )
{
  inQuery = "( " + removeList[0]; 
  l = l + "<table>" + findWithAttr(ajaxData, "id", removeList[0]);
  for(var i = 1; i < removeList.length; i++){
    inQuery = inQuery + ", " + removeList[i];
    l = l + findWithAttr(ajaxData, "id", removeList[i]);
  }
  inQuery = inQuery + ")";
  l = l + "</table>";
   msg = msg + "<p>" + jQuery('#mg-warningconfirm1').text() + "</p>" + l + "<p>" + jQuery('#mg-warningconfirm2').text() + "</p>";
  jQuery('.btn-danger').show();
}else{
  msg = "<p>Nothing selected for deletion</p>";
  jQuery('.btn-danger').hide();
}
  jQuery(msg).prependTo( jQuery(this).find('.modal-body') );
           
})

});