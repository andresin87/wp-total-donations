//jQuery = jQuery.noConflict();

var mdata = [];
var sessionid; var amount; var cleanAmount; var repeating; var anonymous;
var warning = ["", "", ""];

function sendtoPaypal(){
 jQuery( '#migla-hidden-form' ).submit();
} 

function getMapValue( v ){
 str = "";
 for (i = 0; i < mdata.length; i++) {
    if( v == mdata[i][0] )
    {
      return mdata[i][1];
    }
 }  
 return str;
}

function getMapIndex( v ){
 str = 0;
 for (i = 0; i < mdata.length; i++) {
    if( v == mdata[i][0] )
    {
      return i;
    }
 }  
 return str;
}

function cleanIt( dirty ){
  var clean ;
  clean = dirty.replace(/</gi,"");
  clean = clean.replace(/>/gi,"");
  clean = clean.replace(/!/gi,"");
  clean = clean.replace(/&amp/gi,"");
  clean = clean.replace(/&/gi,"");
  clean = clean.replace(/#/gi,"");  
  return clean;
}

function isEmailAddress(str) {
   var pattern =/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
   return pattern.test(str);  // returns a boolean 
}

function isValid(){
  var isVal = true;
  warning = ["", "", ""];
  var email = jQuery("#email").val();
 
 if( email.search('@') < 0  ){
    isVal = false; warning[1] = jQuery('#mg_warning2').text();
  }else{
    if( !isEmailAddress(email) ){ 
     isVal = false; warning[1] = jQuery('#mg_warning2').text();
    }
  }

  jQuery('#migla_donation_form').find('.migla-panel').each(function(){ 
     var toggle = jQuery(this).find('.mtoggle');
     if( (toggle.length < 1) || ( (toggle.length > 0) && toggle.is(':checked') ) )
     {
        jQuery(this).find('.required').each(function(){
        if(  jQuery(this).attr('type') == 'checkbox' ){
        }else{
           if(  jQuery(this).val() == '' ) 
           {
             jQuery(this).addClass('pink-highlight'); isVal = false;
             warning[0] = jQuery('#mg_warning1').text();
           }else{
             jQuery(this).removeClass('pink-highlight');
           }
        }
        });

     }
  });//perpanel

   amount = jQuery("input[name=miglaAmount]:checked").val();
   if( amount == 'custom' && jQuery("#miglaCustomAmount").val() == '') { 
      isVal = false;
      warning[2] = jQuery('#mg_warning3').text();
      jQuery('#miglaCustomAmount').addClass('pink-highlight');
   }else{
      jQuery('#miglaCustomAmount').removeClass('pink-highlight');
   }

  if( amount == '' || amount == '0' )
  {
      isVal = false;
      warning[2] = "Please fill the valid amount";
  }

  var campaign = jQuery('select[name=campaign] option:selected').text();

  return isVal;
}

jQuery(document).ready( function() {

//alert("Load OK");

 sessionid = jQuery("input[name=migla_session_id]").val();
 repeating = 'no'; anonymous='no';


 jQuery('#miglacheckout').click(function(){

 
  if( isValid() )
  {

   jQuery('#miglacheckout').hide();
   jQuery('#mg_wait').show();

  //////////// NEW CODES//////////////////////
   mdata.length = 0;
   var item = [];

   //RETRIEVE ALL DEFAULT MANDATORY FOR DONOR

   amount = jQuery("input[name=miglaAmount]:checked").val();

   if( amount == 'custom') { amount = cleanIt(jQuery("#miglaCustomAmount").val()); } 

   cleanAmount = amount.replace( jQuery('#miglaThousandSep').val() ,"");
   cleanAmount = amount.replace( jQuery('#miglaDecimalSep').val() ,".");

   var campaign = jQuery('select[name=campaign] option:selected').val();

   item = [ 'miglad_session_id_', sessionid ]; mdata.push( item );
   item = [ 'miglad_session_id', sessionid ]; mdata.push( item );
   item = [ 'miglad_amount', cleanAmount ]; mdata.push( item );

   item = [ 'miglad_campaign', campaign ]; mdata.push( item );


   //READ LOOP FOR EACH FIELD
   jQuery('#migla_donation_form').find('.migla-panel').each(function(){ //READ PERPANEL
     var toggle = jQuery(this).find('.mtoggle');
     if( (toggle.length > 0)  )  //IF HAS TOGGLE
     {
       //////////////TOGGLE IS CHECKED/////////////////////////////////////////////////////
       if( toggle.is(':checked') )
       {
       //loop per form group
       jQuery(this).find('.form-group').each(function(){

        var whoami = jQuery(this).find('.idfield').attr('id');

        if(  whoami == 'miglad_amount' || whoami == 'miglad_camount' || whoami == 'miglad_campaign' ){ 
           //if this amount or select just skip it 
        }else{  
          //certain input type
          var type = jQuery(this).find("input").attr('type'); 
          var val = "";
          
         if( jQuery(this).find("textarea").length < 1)
         {

          if( type == 'text'){ //text

            val = cleanIt(jQuery(this).find("input").val());  
           
          }else if( type == 'radio' ) { //radio

             val = jQuery(this).find("input[type=radio]:checked").val();

          }else if( type == 'checkbox' ) { //checkbox

             if( jQuery(this).find("input").is(':checked') ){
               val = 'yes';
             }else{
               val = 'no';
             }

          }else{ //if not input type then it must be select

            val = jQuery(this).find('select option:selected').text();

          }
         }else{
           val = cleanIt(jQuery(this).find("textarea").val());
         }
 
          //////////push it//////////////////////////////
          var e = [ jQuery(this).find('.idfield').attr('id') , val ];    

          mdata.push(e);
          ////////////////////////////////////////////////
        }
       }); //foreach form loop

       }else{

        //loop per form group
        jQuery(this).find('.form-group').each(function(){
          var e = [ jQuery(this).find('.idfield').attr('id') , "" ];
          mdata.push(e);
        });
       }
      //////////////END OF TOGGLE IS CHECKED/////////////////////////////////////////////////////

     }else{ //does not have toggle 

       ////////////////////loop per form group
       jQuery(this).find('.form-group').each(function(){

        var whoami = jQuery(this).find('.idfield').attr('id');

        if(  whoami == 'miglad_amount' || whoami == 'miglad_camount' || whoami == 'miglad_campaign' ){ 
           //if this amount or campaign select just skip it 
        }else{  
          
          //certain input type
          var type = jQuery(this).find("input").attr('type'); 
          var val = "";
         
         if( jQuery(this).find("textarea").length < 1)
         {
          if( type == 'text'){ //text
            val = cleanIt(jQuery(this).find("input").val());             
          }else if( type == 'radio' ) { //radio
             val = jQuery(this).find("input[type=radio]:checked").val();
          }else if( type == 'checkbox' ) { //checkbox
             if( jQuery(this).find("input").is(':checked') ){
               val = 'yes';
             }else{
               val = 'no';
             }
          }else{ //if not input type then it must be select
            val = jQuery(this).find('select option:selected').text();
          }
         }else{
           val = cleanIt(jQuery(this).find("textarea").val());
         }
          
          ////////// PUSH IT ////////////////////////
          var e = [ jQuery(this).find('.idfield').attr('id') , val ];  
          mdata.push(e);
          ////////////////////////////////////////////////
        }
       }); //foreach form loop

     } 
   }) //READ EACH FIELD

   var idx1 = getMapIndex('miglad_state');
   var idx2 = getMapIndex('miglad_province');
   var c = getMapValue( 'miglad_country' );
   if( c == 'Canada' )
   {
      mdata[idx1][1] = "";  
   }else if( c == 'United States' ){
      mdata[idx2][1] = "";
   }else{
     mdata[idx1][1] = ""; 
     mdata[idx2][1] = ""; 
   }
   
   var m = getMapValue( 'miglad_memorialgift' );
   var hc = getMapValue( 'miglad_honoreecountry' );
   var idx3 = getMapIndex('miglad_honoreestate');
   var idx4 = getMapIndex('miglad_honoreeprovince');
 if( m == 'yes')
 {
     mdata[idx3][1] = ""; 
     mdata[idx4][1] = "";   
     var idx5 = getMapIndex('miglad_honoreecountry'); 
     mdata[idx5][1] = "";  
 }else{
   if( hc == 'Canada' )
   {
      mdata[idx3][1] = "";  
   }else if( hc == 'United States' ){
      mdata[idx4][1] = "";
   }else{
     mdata[idx3][1] = ""; 
     mdata[idx4][1] = ""; 
   }   
 }

   /////HIDDEN FORM////////////////////

	var hiddenForm = jQuery('#migla-hidden-form');
        
	hiddenForm.find('input[name="first_name"]').val(  getMapValue( 'miglad_firstname' ) );
	hiddenForm.find('input[name="last_name"]').val(  getMapValue( 'miglad_lastname' ) );
	hiddenForm.find('input[name="address1"]').val(  getMapValue( 'miglad_address' ) );
        hiddenForm.find('input[name="city"]').val(  getMapValue( 'miglad_city' ) );
        hiddenForm.find('input[name="zip"]').val(  getMapValue( 'miglad_postalcode' ) );

	hiddenForm.find('input[name="email"]').val( getMapValue( 'miglad_email' ));
	hiddenForm.find('input[name="custom"]').val(sessionid);
	hiddenForm.find('input[name="amount"]').val(cleanAmount);

   
        var paypalName = getMapValue( 'miglad_firstname' ) + " " + getMapValue( 'miglad_lastname' );
        hiddenForm.find('input[name="os0"]').val(paypalName);

        var occupation = getMapValue( 'miglad_employer' ) + "," + getMapValue( 'miglad_occupation' );
        hiddenForm.find('input[name="os1"]').val(occupation);

        var campaign_send = jQuery('select[name=campaign] option:selected').text();
        hiddenForm.find('input[name="os2"]').val(campaign_send);

        var isRepeat = getMapValue( 'miglad_repeating' ) ;
	if ( isRepeat == 'no' ) {
		hiddenForm.find( 'input[name="src"]' ).remove();
		hiddenForm.find( 'input[name="p3"]' ).remove();
		hiddenForm.find( 'input[name="t3"]' ).remove();
		hiddenForm.find( 'input[name="a3"]' ).remove();
	} else if ( isRepeat == 'yes' ) {
		hiddenForm.find( 'input[name="cmd"]' ).val( '_xclick-subscriptions' );
		hiddenForm.find( 'input[name="p3"]' ).val( '1' );
		hiddenForm.find( 'input[name="t3"]' ).val( 'M' );
		hiddenForm.find( 'input[name="a3"]' ).val( cleanAmount );
		hiddenForm.find( 'input[name="amount"]' ).remove();
	}else{
		hiddenForm.find( 'input[name="src"]' ).remove();
		hiddenForm.find( 'input[name="p3"]' ).remove();
		hiddenForm.find( 'input[name="t3"]' ).remove();
		hiddenForm.find( 'input[name="a3"]' ).remove();
        }

   //alert(msg);
 //alert( "Testing only now : " + JSON.stringify(mdata) );

   jQuery.ajax({
        type : "post",
        url :  miglaAdminAjax.ajaxurl,  
        data :  { action:"miglaA_checkout" , 
                  donorinfo:mdata, 
                  session:sessionid
                  },
        success: function() {

            sendtoPaypal();
        }
   }); //ajax 


 }else{
   var warn = warning[0];
   if( warning[1] != "" && warn != ""){
       warn = warn + "\n" + warning[1];
   }

   if( warning[1] != "" && warn == ""){
       warn = warn +  warning[1];
   }

   if( warning[2] != "" && warn != ""){
       warn = warn + "\n" + warning[2];
   }

   if( warning[2] != "" && warn == ""){
       warn = warn +  warning[2];
   }

   alert(warn);

 }

}); //Paypal clicked

});