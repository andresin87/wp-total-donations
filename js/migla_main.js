//jQuery = jQuery.noConflict();
var decimalSep; var thousandSep; var decimalNum; var showDec;
var off = []; var on = [];
var offArray = []; var onArray = [];

function calMonth( month ){
var m;
 switch ( Number(month) ) {
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
return m;
}

function convertDate( theDate ){
  var str; var m; var d; var y;
  var field = [ '01', '01', '2015' ];
  if( theDate != '' ){
    field = theDate.split('/');
  }
  m = calMonth( field[0] );

 var dd = field[1];
 dd     = dd.slice(-1); 
 var something;
 if( dd == "1" ){ 
    something = "st"; 
 }else if( dd == "2" ){ 
    something = "nd"; 
 }else{ 
   something = "th"; 
 } 

 str = m + " " + String(Number(field[1])) +  something +", " + field[2];

return str;
}

/**** Added April 2th ****/
function clean_undefined( x ){
  if( (typeof x === 'undefined') )
  {
    return '';
  }else{
    return x;
  }
}

/*** Updated April 2th *****/
function recentItem( tdate ,time,name,amount,address, city, state, province, country,repeat, anon)
{
  clean_undefined( name ); clean_undefined( amount ); clean_undefined( address ); clean_undefined( city ); 
  clean_undefined( state ); clean_undefined( province ); clean_undefined( country );

  var timedif = 0;
  var cdate = convertDate( tdate );

  var province_state = state;
  if( state == ''){ province_state = province; }

  str = "";
  str = str + "<div class='timeline-item'><div class='row'><div class='col-xs-3 date'>";
  str = str + "<span class=''>" + jQuery("div#symbol").html() + "</span>";
  str = str + cdate;
  str = str + "<br> <small class='text-navy'>"+ time +"</small> </div>";
  str = str + "<div class='col-xs-7 content'><p class='m-b-xs'>";
  str = str + "<strong>"+ amount +"</strong>";
  str = str + "<span class='donorname'>" + name + "</span></p>";

  var address1 = '' ; var address2 = '' ; var address3 = ''; 

  if( address == '' || typeof address == 'undefined' ){

  }else{
    address1 = address;
  }

  if(city == '' ){
     address2 = province_state ;
  }else if(city != '' && province_state != '' ){
     address2 = city + ", " + province_state ;
  }else{
     address2 = city  ;
  }

  address3 = country;

  str = str + "<p>";

  var line1 ; var line2; var line3; 

  if( address1 != ''  ){ 
        line1 = address1; 
  }

  if( line1 != ' ' ){ 
        line2 = "<br>" + address2; 
  }else{
        line2 = address2; 
  }

  if( line2 != '' ){ 
        line3 = "<br>" + address3; 
  }else{
        line3 = address3; 
  }

  str = str + line1 + line2 + line3;

  if( line1 != '' || line2 != '' || line3 != '' ){
     str = str + "<br>";
  }

  str = str + "Anonynmous : ";
  str = str + " <strong>" + anon + "</strong>";
  str = str + "<br>Repeating : ";
  str = str + " <strong>" + repeat + "</strong>";
  str = str + "</p>";

  str = str + "</div></div></div>";

  return str;
}

function getcampaigns(n, c, p, s, t, a, type)
{
var stat = "open"; var statclass = 'label-success';
if( s == '0' || s == '-1'){ stat = "closed"; statclass = 'label-warning'; }

var lbl = c.replace("[q]", "'");
//if( a == null ){ a = '0'; }
str = "";
str = str + "<tr><td>" + n + "</td><td>"+lbl+"</td>";
str = str + "<td><span class='label " + statclass + "'>" + stat + "</span></td>";
if( Number(t) != 0 ){
  str = str + "<td><div class='progress progress-sm progress-half-rounded m-none mt-xs light mg_percentage'>";
  str = str + "<div style='width: "+p+"%;' aria-valuemax='100' aria-valuemin='0' aria-valuenow='60' role='progressbar'";
  str = str + "class='progress-bar progress-bar-primary'>"+p+"%</div></div></td>";    			
}else{
 if( jQuery('#placement').html() == 'before' ){
   str = str + "<td><div class='undeclared-campaign'> Raised " + jQuery('#symbol').html() + a + "</div></td>";
 }else{
   str = str + "<td><div class='undeclared-campaign'> Raised "  + a + jQuery('#symbol').html() + "</div></td>";
 }
} 
//str = str + "<td>(" + a +" of "+ t + ")</td>";

str = str + "</tr>";

  return str;
}

function drawChart(on, off, flag)
{
var major = new Array(); 
var amount = new Array(); 
var amount2 = new Array(); 
var d;
var lastMonth;
var l1 = on.length - 1 ; var l2 = off.length - 1;
var startMonthOn = on[0].month ; var endMonthOn = on[l1].month ; var startMonthOff = off[0].month ; var endMonthOff = off[l2].month;
var startYearOn = on[0].year ; var endYearOn = on[l1].year ; var startYearOff = off[0].year ; var endYearOff = off[l2].year;



if( startMonthOn.length < 2 ){ startMonthOn = "0" + startMonthOn; }
if( endMonthOn.length < 2 ){ endMonthOn = "0" + endMonthOn; }

if( startMonthOff.length < 2 ){ startMonthOff = "0" + startMonthOff; }
if( endMonthOff.length < 2 ){ endMonthOff = "0" + endMonthOff; }

//alert( startYearOn + startMonthOn + "--" + endYearOn + endMonthOn );
//alert( startYearOff + startMonthOff + "----" + endYearOff + endMonthOff);

var onlineStart = Number(startYearOn + startMonthOn );
var offlineStart = Number(startYearOff + startMonthOff );

var onlineEnd = Number(endYearOn + endMonthOn);
var offlineEnd = Number(endYearOff + endMonthOff);

if( l1 < 0 && l2 < 0){
}else{  
   var start;var end; 
   if( onlineStart < offlineStart ){ 
     start = startYearOn + startMonthOn ;
   }else{
     start = startYearOff + startMonthOff;   
   }
   
   if( onlineEnd < offlineEnd ){ 
     end = endYearOff + endMonthOff;   
   }else{
     end = endYearOn + endMonthOn;
   }
   
   //alert( (on[0].year+on[0].month) + "--" + (off[0].year+off[0].month) + ",,," + (on[l1].year+on[l1].month) + ",,," + (off[l2].year+off[l2].month) );
   //alert( start + " " + end );

   var pos = start.toString();    
   var yy; var mm;
	   if( Number(end.slice(4)) < 12 )
	   {
	     yy = end.slice(0,4); 
	     mm =(Number(end.slice(4)) + 1);
	   }else{
             
	     yy = Number(end.slice(0,4)) + 1; 
	     mm = '01';	   
	   }
           if( Number(mm) < 10  ){ mm = "0" + mm; }
	   var pos2 =  yy + mm;   
   
   var idx = 0; var y; var m; var p1 = 0; var p2 = 0;
  
   //alert( pos + " " + pos2 );

   var index1 = 0; var index2 = 0 ; amount[idx] = 0; amount2[idx] = 0;
   var temp1 = {}; var temp2 = {} ; 

   while( pos != pos2 )
   {
      //get current month & year
       major[idx] = calMonth( pos.slice(4) ) + " " + pos.slice(0,4) ;

      temp1[ pos ] = 0;
      temp2[ pos ] = 0;

     amount[idx] = 0;
     amount2[idx] = 0;

     idx = idx + 1;

	if( Number(pos.slice(4)) < 12 )
	   {
	     y= pos.slice(0,4); 
	     m =(Number(pos.slice(4)) + 1);
	   }else{
	     y= Number(pos.slice(0,4)) + 1; 
	     m = '1';	   
	  }
           if( Number(m) < 10  ){ m = "0" + m; }
	   pos =  y + m;


   } //while 
   
       major[idx] = calMonth( pos2.slice(4) ) + " " + pos2.slice(0,4);    
       amount[idx] = 0;
     amount2[idx] = 0;
 
      temp1[ pos2 ] = 0;
      temp2[ pos2 ] = 0;
} 

    for( var index1 = 0 ;  index1 <= l1 ; index1++)
    {
       var mmm = on[index1].month;
       if( mmm.length < 2  ){ mmm = "0" + mmm; }
        var str = on[index1].year + mmm;
       temp1[ str ] = temp1[ str ] + Number( on[index1].amount ) ;
    } 

    var j = 0;
    for( key in temp1)
    {
       amount[j] = temp1[key]; j++;
    }

    for( var index2 = 0 ;  index2 <= l2 ; index2++)
    {
       var mmm = off[index2].month;
       if( mmm.length < 2  ){ mmm = "0" + mmm; }
        var str = off[index2].year + mmm;
       temp2[ str ] = temp2[ str ] + Number( off[index2].amount ) ;
    } 

    j = 0;
    for( key in temp2)
    {
       amount2[j] = temp2[key]; j++;
    }

	var lineChartData  = {
    labels:major,
    datasets: [
        {
            label: "Online",
            fillColor: "rgba(66,139,202,0.1)",
            strokeColor: "rgba(66,139,202,1)",
            pointColor: "rgba(66,139,202,1)",
            pointStrokeColor: "#fff",
            pointHighlightFill: "#fff",
            pointHighlightStroke: "rgba(220,220,220,1)",
            data: amount
        },
        {
            label: "Offline",
            fillColor: "rgba(151,187,205,0.1)",
            strokeColor: "rgba(43,170,177,1)",
            pointColor: "rgba(43,170,177,1)",
            pointStrokeColor: "#fff",
            pointHighlightFill: "#fff",
            pointHighlightStroke: "rgba(151,187,205,1)",
            data: amount2
        }
    ]
};
	
	return lineChartData;

}


function campaignPrototype(campaign, percent, status) {
  this.campaign = campaign;
  this.percent = percent;
  this.status = status;
}

jQuery(document).ready(
function() {	

Number.prototype.formatMoney = function(decPlaces, thouSeparator, decSeparator) {
    var n = this,
        decPlaces = isNaN(decPlaces = Math.abs(decPlaces)) ? 2 : decPlaces,
        decSeparator = decSeparator == undefined ? "." : decSeparator,
        thouSeparator = thouSeparator == undefined ? "," : thouSeparator,
        sign = n < 0 ? "-" : "",
        i = parseInt(n = Math.abs(+n || 0).toFixed(decPlaces)) + "",
        j = (j = i.length) > 3 ? j % 3 : 0;
    return sign + (j ? i.substr(0, j) + thouSeparator : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thouSeparator) + (decPlaces ? decSeparator + Math.abs(n - i).toFixed(decPlaces).slice(2) : "");
};

var ajaxData ; var ajaxData2;
var d;

    jQuery.ajax({
     type : "post",
     url :  miglaAdminAjax.ajaxurl, 
     data : {action: "miglaA_campaignprogress" },
	success: function(msg) {
         var t = ""; var n = 0; var p = 0;
       var campaigns = JSON.parse(msg);
   // alert( JSON.stringify(campaigns) );

    decimalSep = jQuery('#thousandSep').text();
    thousandSep = jQuery('#decimalSep').text();
    showDec = 0;
    if( jQuery('#showDecimal').text() == 'yes' ){ showDec = 2; }

      if( campaigns.length > 0){
      for (var i = 0; i < campaigns.length; i++) {
          var item = campaigns[i];
          var money1 = item.amount;
            t = t +  getcampaigns( (i+1), item.campaign, Number(item.percent), item.status, item.target, 
                 money1 , item.type );
        }  
      }else{
        t = "you have no designated campaign yet";
      }

     jQuery(t).appendTo( jQuery('.table tbody') );

    }//success
  }) 
	

    jQuery.ajax({
     type : "post",
     url :  miglaAdminAjax.ajaxurl, 
     data : {action: "miglaA_getGraphData" },
	success: function(msg) { 
       var d = JSON.parse(msg);
	   on = d[0]; off = d[1];

	   //alert( JSON.stringify(on) );

if( on.length == 1 && off.length == 1 && on[0].amount == 0 && off[0].amount == 0 ){
  document.getElementById("sectionB").remove();
  jQuery('<p>No data to display</p>').insertAfter('#migla-donation-title');
}else{	   
      var linechart = drawChart( on, off, "month");
           var ctx = document.getElementById("sectionB").getContext("2d");
	   window.myLine = new Chart(ctx).Line(linechart, {
	      responsive: true
   	    });
	  document.getElementById("legendDiv").innerHTML = window.myLine.generateLegend(); 
 
       jQuery('ul.line-legend').find('li').each(function(){
           jQuery(this).remove('span');
           var label = jQuery(this).text(); var change = "";
           jQuery(this).empty();
         if( label == 'Online'){
           change = change + "<div class='swatch' style='background-color:rgba(66,139,202,1);'></div><span class='swatchLabel'>";
           change = change + "Online</span>";
         }else{
           change = change + "<div class='swatch' style='background-color:rgba(43, 170, 177, 1);'></div><span class='swatchLabel'>";
           change = change + "Offline</span>";
         }
          jQuery(change).appendTo( jQuery(this) ); 
       });

     jQuery('#legendDiv').insertAfter('#migla-donation-title');
  }	   
	   }//ajax success bracket
     })  ; //ajax  	
	 

document.getElementById("monthOnAmount").innerHTML = "online";
document.getElementById("onAmount").innerHTML = "online";
decimalSep = jQuery('#thousandSep').text();
thousandSep = jQuery('#decimalSep').text();
showDec = 0;
if( jQuery('#showDecimal').text() == 'yes' ){ showDec = 2; }
var before = ''; var after = '';

if( jQuery('#placement').text() == 'before' ){ before = jQuery("div#symbol").html(); }else{ after = jQuery("div#symbol").html(); }
    jQuery.ajax({
     type : "post",
     url :  miglaAdminAjax.ajaxurl, 
     data : {action: "miglaA_totalAll" },
	success: function(msg) {
          d = JSON.parse( msg );
          //alert(d);
          document.getElementById("amount").innerHTML = "<span class=''>" + before + "</span>" + (d[2]).formatMoney(showDec,decimalSep,thousandSep) + after;   
          document.getElementById("onAmount").innerHTML = "<span class=''>" + before + "</span>" + (d[0]).formatMoney(showDec,decimalSep,thousandSep)+ after + " online";           
	}//success
     })  ; //ajax  	

    jQuery.ajax({
     type : "post",
     url :  miglaAdminAjax.ajaxurl, 
     data : {action: "miglaA_totalThisMonth" },
	success: function(msg) {
          d = JSON.parse( msg );
          //alert (d);
          document.getElementById("monthAmount").innerHTML = "<span class=''>" + before + "</span>" + (d[2]).formatMoney(showDec,decimalSep,thousandSep) + after;   
          document.getElementById("monthOnAmount").innerHTML = "<span class=''>" + before + "</span>" + (d[0]).formatMoney(showDec,decimalSep,thousandSep)+ after + " online";           
	}//success
     }) 

var list;
jQuery.ajax({
        type : "post",
        url :  miglaAdminAjax.ajaxurl, 
        data : {action: "miglaA_recentDonations"},
        success: function(msg) { 
      
        list = JSON.parse(msg);
        var t = "";  
        //alert( JSON.stringify(list) );
        
    if( list.length > 0){ 
       list.sort(function (a, b) {
         return (new Date(b.date + ' ' + b.time) - new Date(a.date + ' ' + a.time) );
       });

	for (var i = 0; i < list.length; i++) {
          var item = list[i];
          formatAmount = Number(item.amount);	  
         
          t = t +  recentItem( 
                  item.date, 
                  item.time, 
                  item.name, 
                  formatAmount.formatMoney(showDec,decimalSep,thousandSep), 
                  item.address, item.city, item.state, item.province, item.country, 
                  item.repeating, item.anonymous);          
        }  
          jQuery(t).appendTo( jQuery(".ibox-content") );
      }else{
          t = t + "<div class='timeline-item'><div class='row'>";
          t = t + "No donation has been made";
          t = t + "</div></div>";
          jQuery(t).appendTo( jQuery(".ibox-content") );
     }
	 
   }//ajax success

})  ; //ajax


});


