	////////////////////////////////////////////////////
	////////// migla_donation.js ///////////////////////

//jQuery = jQuery.noConflict();

jQuery(document).ready(
function() {

function month(m){
  var month;
  switch(m){
  case "01" : month = "January"; break;
  case "02" : month = "February"; break;
  case "03" : month = "March"; break;
  case "04" : month = "April"; break;
  case "05" : month = "May"; break;
  case "06" : month = "June"; break;
  case "07" : month = "July"; break;
  case "08" : month = "August"; break;
  case "09" : month = "September"; break;
  case "10" : month = "October"; break;
  case "11" : month = "November"; break;
  case "12" : month = "December"; break;
  }
  return month;
}

   jQuery('.campaigntab').click(function() {

       var div = "#" + jQuery(this).closest('div').attr('id');
       var c = jQuery(this).val();
       var container1 = jQuery(div).find('#miglacontainer');
       var container2 = jQuery(div).find('.donation-container');
       var result ;
       container1.toggle();

       if( jQuery(div).find(".thelist").length < 1 && jQuery("#miglaStartDate").val()=="" )
       {
          jQuery.ajax({
		type : "post",
		url :  miglaAdminAjax.ajaxurl, 
		data : {action: "miglaA_get_donations_", campaign:c, start:"", end:"" },
		success: function(msg) { 
		 alert(msg);
                 container2.empty(); 
                 result = eval(msg);
                 
                 jQuery("#miglaStartDate").val(result[0]);
                 jQuery("#miglaEndDate").val(result[1]);

                 jQuery(result[4]).appendTo(container2);
                 jQuery("#mglYear").text(result[2]);
                 jQuery("#mglMonth").text(month(result[3]));

                 //jQuery(result[2]).insertBefore(".mNavigation");
		}
	  })  ; //ajax
      }
    });

   jQuery('.mprev').click(function() {
   
   
   });

   jQuery('.mnext').click(function() {
   });

//Loading total and number of donations
jQuery(".tab_campaign").each(function() {
   var c = jQuery(this).find('.campaigntab').val();
   var count =  jQuery(this).find('#mgl_count');
   var total =  jQuery(this).find('#mgl_total');
   var result ;
   jQuery.ajax({
	type : "post",
	url :  miglaAdminAjax.ajaxurl, 
	data : {action: "miglaA_get_number_and_total", campaign:c },
	success: function(msg) { 
         result = eval(msg);
         //count.text( "Number of donations : " + result[0] );	
         count.text(  result[0] + " donations with a grand total of " +  result[1] );	
         //total.text( "Total amount : " + result[1] );		
	}
  })  ; //ajax  
 
});


});