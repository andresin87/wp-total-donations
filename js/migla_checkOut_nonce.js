var mdata = [];
var sessionid; var amount; var cleanAmount; var repeating; var anonymous;
var token_;
var warning = ["", "", ""];
var recurring_time=''; var recurring_period ='';
var state_code = {} ;
var province_code = {} ;
var plan_info = [];
var honoreecountry; var honoreestate; var honoreeprovince;

function getCreditCardType(accountNumber)
{

  //start without knowing the credit card type
  var result = "unknowncard";

  //first check for MasterCard
  if (/^5[1-5]/.test(accountNumber))
  {
    result = "mastercard";
  }
  //then check for Visa
  else if (/^4/.test(accountNumber))
  {
    result = "visa";
  }
  //then check for AmEx
  else if (/^3[47]/.test(accountNumber))
  {
    result = "amex";
  }
  //then check for Discover
  else if (/^6(?:011|5[0-9]{2}|22[21])[0-9]{3,}/.test(accountNumber))
  {
    result = "discover";
  }
  //then check for JCB 
  else if (/^(?:2131|1800|35[0-9]{3})[0-9]{3,}|3569/.test(accountNumber))
  {
    result = "JCB";
  }
  //then check for Maestro only for Euro
  else if (/^(5018|5020|5038|5612|5893|6304|6759|6761|6762|6763|0604|6390|6705|6777|6766)/.test(accountNumber))
  {
    result = "maestro";
  }
  else if( /(\d{1,4})(\d{1,6})?(\d{1,4})?/.test(accountNumber) ){
    result = "dinnersclub";
  }

  return result;
}

function sendtoPaypal(){
   jQuery( '#migla-hidden-form' ).submit();
} 

function stripeResponseHandler(status, response) {

   if (response.error) {
   
     // re-enable the submit button
     //jQuery('#miglastripecheckout').removeAttr("disabled");

     // show the errors on the form
     jQuery(".payment-errors").html(response.error.message);

     alert( response.error.message );
     jQuery('#miglastripecheckout').show(); 
     jQuery('#mg_wait_stripe').hide();

   } else {

     var form$ = jQuery("#mg-stripe-payment-form");

     // token contains id, last4, and card type
     var token = response['id'];

     var repeatingField = jQuery("#migla_donation_form").find("input[name='miglad_repeating']");
     if( repeatingField.length > 0 )
     {
        if( repeatingField.attr("type") == "checkbox" ){
          if( jQuery("#migla_donation_form").find("input[name='miglad_repeating']").is(":checked") ){
             var info = jQuery('#infomiglad_repeating').val();
             plan_info = info.split(";");
          }else{
             plan_info[0] = 'No'; plan_info[1] = '0'; plan_info[2] = '0';
          }
        }else{
             var p_info = "#info" + jQuery("input[name='miglad_repeating']:checked").attr('id');
             var info   = jQuery( p_info ).val();
             plan_info  = info.split(";");   
        }
     }else{
          plan_info[0] = 'No'; plan_info[1]=''; plan_info[2]='';
     }
             
     //alert( JSON.stringify(plan_info) );

     if( plan_info[0] != 'No'  )
     { 
        //alert('repeating stripe');

        var qty = cleanAmount * 100;
        var plan_name = plan_info[0];
        
        jQuery.ajax({
          type : "post",
          url :  miglaAdminAjax.ajaxurl,  
          data :  { action:"miglaA_createSubscription" , 
                      stripeToken:token,session:sessionid,plan:plan_name,quantity:qty
                  },
          success: function(stripemsg1) {
             if( stripemsg1 == "1" ){
                 var url = miglaAdminAjax.successurl + "?" + "thanks=thanks&id=" + sessionid;
                 window.location.replace(url);
             }else{
                 alert( stripemsg1 );
                 jQuery('#miglastripecheckout').show(); 
                 jQuery('#mg_wait_stripe').hide();
             }
          }
        }); //ajax 

     }else{

        //alert('one time stripe');

        var url = miglaAdminAjax.successurl + "?" + "thanks=thanks&id=" + sessionid;
        window.location.replace(url);

        jQuery.ajax({
          type : "post",
          url :  miglaAdminAjax.ajaxurl,  
          data :  { action:"miglaA_stripeCharge" , 
                  stripeToken:token, amount:(cleanAmount*100), session:sessionid
                  },
          success: function(stripemsg) {
             if( stripemsg == "1" ){
                 var url = miglaAdminAjax.successurl + "?" + "thanks=thanks&id=" + sessionid;
                 window.location.replace(url);
             }else{
                 alert( stripemsg1 );
                 jQuery('#miglastripecheckout').show(); 
                 jQuery('#mg_wait_stripe').hide();
             }
          }
        }); //ajax 

     } //IF THEN ELSE

   }

} 

function sendtoStripe(){
 jQuery( "#mg-stripe-payment-form" ).submit();
}

///////////////////////////////////////////////////////

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
  var _dirty = new String(dirty);
  var clean ;
  
  clean = _dirty.replace(/</gi,"");
  clean = clean.replace(/>/gi,"");
  clean = clean.replace(/!/gi,"");
  clean = clean.replace(/&amp/gi,"");
  clean = clean.replace(/&/gi,"");
  clean = clean.replace(/#/gi,"");  
  clean = clean.replace(/"/gi,"");
  clean = clean.replace(/'/gi,"");
  return clean;
}

function isEmailAddress(str) {
   var pattern =/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
   return pattern.test(str);  // returns a boolean 
}

function isValid(){
  var isVal = true;
  warning = ["", "", ""];
  var email_id = jQuery('#miglad_email');
  var email = email_id.next("input[type='text']").val();
 
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

function get_state_code( search_code ){
  var r = "";
  for( key in state_code ){
    if( state_code[key] == search_code ){ 
       r = key; 
       break;
    }
  }
  return r;
}

function get_province_code( search_code ){
  var r = "";
  for( key in province_code ){
    if( province_code[key] == search_code ){ 
       r = key; 
       break;
    }
  }
  return r;
}

jQuery(document).ready( function() {

//alert("Load OK");

      jQuery.ajax({
        type : "post",
        url :  miglaAdminAjax.ajaxurl,  
        data :  { action:"migla_getme_array" , key:"migla_US_states" },
        success: function( mst ) {
            var jdata = JSON.parse( mst );
            for( key in jdata ){
                state_code[key] = jdata[key];
            }        
       }, 
       async: false
    }); //ajax 

      jQuery.ajax({
        type : "post",
        url :  miglaAdminAjax.ajaxurl,  
        data :  { action:"migla_getme_array" , key:"migla_Canada_provinces" },
        success: function(msp) {
            province_code = JSON.parse(msp);
            //alert( JSON.stringify(msp) );
        }, 
       async: false
    }); //ajax 
 

  jQuery("#mg-stripe-payment-form").submit(function(event) {

	//jQuery('#miglastripecheckout').attr("disabled", "disabled");

        Stripe.setPublishableKey( miglaAdminAjax.stripe_PK );

       var countryin = getMapValue( 'miglad_country' ); var statein = '';
       if( countryin == 'Canada' ){
          statein = getMapValue( 'miglad_province' );
       }
       if( countryin == 'United States' ){
          statein = getMapValue( 'miglad_state' );
       }

       var name_on_card = jQuery('#mg_stripe_card_name').val();
       if( name_on_card == '' )
       { 
           name_on_card =  getMapValue( 'miglad_firstname' ) + " " + getMapValue( 'miglad_lastname' );
       }
       var card_number = cleanIt( jQuery('.card-number').val() );
       card_number = card_number.trim();

       Stripe.createToken({
           name            : name_on_card,
	   number          : card_number,
	   cvc             : jQuery('.card-cvc').val(),
	   exp_month       : jQuery('.card-expiry-month').val(),
	   exp_year        : jQuery('.card-expiry-year').val(),
           address_line1   : getMapValue( 'miglad_address' ),
           address_city    : getMapValue( 'miglad_city' ),
           address_country : countryin,
           address_zip     : getMapValue( 'miglad_postalcode' ),
           address_state   : statein
	}, 
          stripeResponseHandler
        );
	
	 return false; 
    });

  sessionid = jQuery("input[name=migla_session_id]").val();
  repeating = 'no'; anonymous='no';

  jQuery('.miglacheckout').click(function(){

    if( isValid() )
    {
   
      //alert( jQuery(this).attr('id') );

      if( jQuery(this).attr('id') === "miglapaypalcheckout" )
      {
           jQuery('#miglapaypalcheckout').remove();
           jQuery('#mg_wait_paypal').show();
      }else{
           jQuery('#miglastripecheckout').hide(); 
           jQuery('#mg_wait_stripe').show();
      }

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

         var whoami = jQuery(this).find('.idfield').attr('id');  var val = "";

          if(  whoami == 'miglad_amount' || whoami == 'miglad_camount' || whoami == 'miglad_campaign' ){ 
             //if this amount or campaign select just skip it 

          }else{  

           //certain input type
           var type = jQuery(this).find("input").attr('type'); 

           if( jQuery(this).find('select').length >= 1 ){
               type = 'select';
           }
          
          if( jQuery(this).find("textarea").length >= 1)
          {
              val = cleanIt(jQuery(this).find("textarea").val());

          }else{

            if( type == 'text'){ //text

              val = cleanIt(jQuery(this).find("input").val());  
           
            }else if( type == 'radio' ) { //radio

              val = jQuery(this).find("input[type=radio]:checked").val();

            }else if( type == 'checkbox' ) { //checkbox

                if( jQuery(this).find('input').length > 1 )
                {
                    jQuery(this).find('input').each(function(){
                        if( jQuery(this).is(':checked') )
                        {
                            val = val + jQuery(this).val() + ", ";
                        }
                    });
                }else{
                  if( jQuery(this).find('input').is(':checked') ){
                     val = 'yes';
                  }else{
                     val = 'no';
                  }
                }

            }else if( type == 'select' ) { //select
                var name = jQuery(this).find('select').attr('name');
                val = jQuery(this).find("select[name='" + name + "'] option:selected").val();
            }
         }
 
          //////////push it//////////////////////////////
          var e = [ whoami , val ];    

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

        var whoami = jQuery(this).find('.idfield').attr('id');   var val = "";


       if(  whoami == 'miglad_amount' || whoami == 'miglad_camount' || whoami == 'miglad_campaign'  ){ 
           //if this amount or campaign select just skip it 
        }else{  
          
           //certain input type
           var type = jQuery(this).find("input").attr('type'); 

           if( jQuery(this).find('select').length >= 1 ){
               type = 'select';
           }
          
          if( jQuery(this).find("textarea").length >= 1)
          {
              val = cleanIt(jQuery(this).find("textarea").val());

          }else{

            if( type == 'text'){ //text

              val = cleanIt(jQuery(this).find("input").val());  
           
            }else if( type == 'radio' ) { //radio

              val = jQuery(this).find("input[type=radio]:checked").val();

            }else if( type == 'checkbox' ) { //checkbox
                if( jQuery(this).find('input').length > 1 )
                {
                    jQuery(this).find('input').each(function(){
                        if( jQuery(this).is(':checked') )
                        {
                            val = val + jQuery(this).val() + ", ";
                        }
                    });
                }else{
                  if( jQuery(this).find('input').is(':checked') ){
                     val = 'yes';
                  }else{
                     val = 'no';
                  }
                }
            }else if( type == 'select' ) { //select
                var name = jQuery(this).find('select').attr('name');
                val = jQuery(this).find("select[name='" + name + "'] option:selected").val();
            }
         }
          
          ////////// PUSH IT ////////////////////////
          var e = [ whoami , val ];  
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
      }
      else{
         mdata[idx3][1] = ""; 
         mdata[idx4][1] = ""; 
      }   
   }


  // alert( "Testing only now : " + JSON.stringify(mdata) );
   /*
     if( jQuery("#migla_donation_form").find("input[name='miglad_repeating']").attr("type") == "checkbox" ){

         if( jQuery("#migla_donation_form").find("input[name='miglad_repeating']").is(":checked") ){
            var info = jQuery('#infomiglad_repeating').val();
            plan_info = info.split(";");
         }else{
            plan_info[0] = 'No'; plan_info[1] = '0'; plan_info[2] = '0';
         }
     }else{
         var info = jQuery("input[name='miglad_repeating']:checked").val();
         plan_info = info.split(";");   
     }
             
    alert( JSON.stringify(plan_info) );
  */

   //GET the repeating
        var isRepeat = "";
        var repeatingField = jQuery("#migla_donation_form").find("input[name='miglad_repeating']");
        if( repeatingField.length > 0 )
        {
            if( repeatingField.attr("type") == "checkbox" )
            {
               if( jQuery("#migla_donation_form").find("input[name='miglad_repeating']").is(":checked") ){
                   var info = jQuery('#infomiglad_repeating').val();
                   plan_info = info.split(";");
                }else{
                   plan_info[0] = 'No'; plan_info[1] = '0'; plan_info[2] = '0';
                }
            }else{
                var p_info = "#info" + jQuery("input[name='miglad_repeating']:checked").attr('id');
                var info   = jQuery( p_info ).val();
                plan_info  = info.split(";");   
            }
        }else{
              plan_info[0] = 'No'; plan_info[1]=''; plan_info[2]=''; isRepeat = "no";
        }     

        if( plan_info[0] == 'No' ){
             isRepeat = "no";
        }else{
             recurring_time = plan_info[1];
             recurring_period = plan_info[2];
 
             var idx5 = getMapIndex('miglad_repeating');
             mdata[idx5][1] = plan_info[3];  

        } 

   if( jQuery(this).attr('id') == 'miglapaypalcheckout' ){

      /////HIDDEN FORM////////////////////
	var hiddenForm = jQuery('#migla-hidden-form');
        
	hiddenForm.find('input[name="first_name"]').val(  getMapValue( 'miglad_firstname' ) );
	hiddenForm.find('input[name="last_name"]').val(  getMapValue( 'miglad_lastname' ) );
	hiddenForm.find('input[name="address1"]').val(  getMapValue( 'miglad_address' ) );
        hiddenForm.find('input[name="city"]').val(  getMapValue( 'miglad_city' ) );
        hiddenForm.find('input[name="zip"]').val(  getMapValue( 'miglad_postalcode' ) );
        hiddenForm.find('input[name="country"]').val( c );
       
        if( c == 'Canada' ){ 
           hiddenForm.find('input[name="state"]').val( get_province_code( getMapValue( 'miglad_province' ) ) );
        }else if( c == 'United States' ){
           hiddenForm.find('input[name="state"]').val(  get_state_code( getMapValue( 'miglad_state' ) )  );
        }

 	hiddenForm.find('input[name="email"]').val( getMapValue( 'miglad_email' ));
	hiddenForm.find('input[name="custom"]').val(sessionid);
	hiddenForm.find('input[name="amount"]').val(cleanAmount);

   
        var paypalName = getMapValue( 'miglad_firstname' ) + " " + getMapValue( 'miglad_lastname' );
        hiddenForm.find('input[name="os0"]').val(paypalName);

        var occupation = getMapValue( 'miglad_employer' ) + "," + getMapValue( 'miglad_occupation' );
        hiddenForm.find('input[name="os1"]').val(occupation);

        var campaign_send = jQuery('select[name=campaign] option:selected').text();
        hiddenForm.find('input[name="os2"]').val(campaign_send);

	if ( isRepeat == 'no') {
		hiddenForm.find( 'input[name="src"]' ).remove();
		hiddenForm.find( 'input[name="p3"]' ).remove();
		hiddenForm.find( 'input[name="t3"]' ).remove();
		hiddenForm.find( 'input[name="a3"]' ).remove();
 	} else {
		hiddenForm.find( 'input[name="cmd"]' ).val( '_xclick-subscriptions' );
 		hiddenForm.find( 'input[name="p3"]' ).val( '1' );
                
  		hiddenForm.find( 'input[name="p3"]' ).val( recurring_time );

                switch( recurring_period )
                {  
 		      case 'day' : hiddenForm.find( 'input[name="t3"]' ).val( 'D' ); break;
 		      case 'week' : hiddenForm.find( 'input[name="t3"]' ).val( 'W' ); break;
 		      case 'month' : hiddenForm.find( 'input[name="t3"]' ).val( 'M' ); break;
 		      case 'year' : hiddenForm.find( 'input[name="t3"]' ).val( 'Y' ); break;
                }
              
		hiddenForm.find( 'input[name="a3"]' ).val( cleanAmount );
		hiddenForm.find( 'input[name="amount"]' ).remove();
	}

       var successUrl = new String(miglaAdminAjax.successurl);
       if ( successUrl.search( "\\?" ) < 0 )
       {
	   successUrl = successUrl + "?";
       }else{
	   successUrl = successUrl + "&";
       }

        successUrl = successUrl + "thanks=thanks";
	successUrl = successUrl + "&id=";
	successUrl = successUrl + sessionid;

        jQuery.ajax({
          type : "post",
          url :  miglaAdminAjax.ajaxurl,  
          data :  { action:"miglaA_checkout_nonce" , 
                  donorinfo:mdata, 
                  session:sessionid,
                  nonce:miglaAdminAjax.nonce
                  },
          success: function(msg_checkout1) {
            if( msg_checkout1 == '0' )
            {
 
                hiddenForm .append("<input type='hidden' name='return' value='"+ successUrl +"' >");
                hiddenForm .append("<input type='hidden' name='notify_url' value='"+ miglaAdminAjax.notifyurl +"' >");

                sendtoPaypal();
            }else{
                alert('unrecognized caller');
            }
          },
          async:false
        }); //ajax 

   }else{

    jQuery.ajax({
        type : "post",
        url :  miglaAdminAjax.ajaxurl,  
        data :  { action:"miglaA_checkout_nonce" , 
                  donorinfo:mdata, 
                  session:sessionid,
                   nonce:miglaAdminAjax.nonce
                  },
        success: function(msg_checkout2) {
            if( msg_checkout2 == '0' )
            {
              sendtoStripe(); 
            }else{
              alert('unrecognized caller');
            }   
        },
        async:false
    }); //ajax 

   }

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

}); //Donate Button Clicked


  jQuery('#mg_stripe_card_number').keyup(function(e){
     var cc_number = jQuery(this).val();
     cc_number = cc_number.trim();
     var card_type = getCreditCardType( cc_number ) ; 
     var p         = jQuery(this).closest('div');   
     var icon_span = p.find('span.mg_creditcardicons'); 
     icon_span.removeClass();
     icon_span.addClass( 'mg_creditcardicons' );
     icon_span.addClass( ('mg_stripe-' + card_type.toLowerCase() ) );
  });



});