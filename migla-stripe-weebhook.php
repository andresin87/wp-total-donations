<?php
session_start();

include_once '../../../wp-blog-header.php';

include_once "./migla-functions.php";

require_once 'migla-call-stripe.php';

  Migla_Stripe::setApiKey( migla_getSK() );

  // retrieve the request's body and parse it as JSON
  $body = @file_get_contents('php://input');
  $event_json = json_decode($body);

  $customer_id = "";
  $customer_id = $event_json->data->object->customer;
  
  //Testing data to analyze
  //add_option( ('_miglaCharge'.time()), $event_json );
  //add_option( ('_miglaCustID'.time()), $customer_id );

 // This will send receipts on succesful invoices for subscription only
 if ( $event_json->type == 'charge.succeeded' )
 {
    //If this charge has a customer a.k.a Recurring Payment
    if( $customer_id != '' || $customer_id != null) 
    {
       //Get Customer ID
       $customer = MStripe_Customer::retrieve($customer_id);
       $description = $customer->description;

       //Testing data to analyze
       //add_option( ('_miglaCust'.time()), $customer );

	$php_time_zone = date_default_timezone_get();
        $t = ""; $d = ""; $default = "";
        $default = get_option('migla_default_timezone');
        if( $default == 'Server Time' ){
           $gmt_offset = -get_option( 'gmt_offset' );
  	   if ($gmt_offset > 0){ 
              $time_zone = 'Etc/GMT+' . $gmt_offset; 
           }else{		
              $time_zone = 'Etc/GMT' . $gmt_offset;    
           }
	   date_default_timezone_set( $time_zone );
	   $t = date('H:i:s');
	   $d = date('m')."/".date('d')."/".date('Y');
        }else{
 	   date_default_timezone_set( $default );
	   $t = date('H:i:s');
	   $d = date('m')."/".date('d')."/".date('Y');
        }
 	date_default_timezone_set( $php_time_zone );

      //Get it done
      $desc = explode(";", $description ) ;
      $session = $desc[1];
      $transient_key = "t_migla". $session;
      $session_id = 'migla'. $session;
    
      $old_id = migla_cek_repeating_id( $session_id );

      if( $old_id == -1 ) //This is not repeating but initial subscriber
      { 

         $postData = get_option( $transient_key ); //Get transient Data

         if( $postData != false )
         {
            //post for donation
            $new_donation = array(
	        'post_title' => 'migla_donation',
	        'post_content' => '',
	        'post_status' => 'publish',
	        'post_author' => 1,
	        'post_type' => 'migla_donation'
             );

             $new_id = wp_insert_post( $new_donation );
 
             $meta_data = array();
             $keys = array_keys( $postData );

             foreach( (array)$postData as $value)
             {
                  add_post_meta( $new_id , $keys[$i], $value );
                  $i++;
             }
       
             add_post_meta( $new_id, 'miglad_session_id', $session ); 

             add_post_meta( $new_id, 'miglad_paymentmethod', 'credit card' );
             add_post_meta( $new_id, 'miglad_timezone', $default );
             add_post_meta( $new_id, 'miglad_transactionType', 'Recurring (Stripe)' );
             add_post_meta( $new_id, 'miglad_subscription_type', 'inital' ); 
             add_post_meta( $new_id, 'miglad_subscription_id', $customer_id ); 

             update_post_meta( $new_id, 'miglad_time', $t ); 
             update_post_meta( $new_id, 'miglad_date', $d ); 

             $e = get_option('migla_replyTo');
             $en = get_option('migla_replyToName');
             $ne = get_option('migla_notif_emails');
		

            if( get_option( 'miglaactions_2_1' ) == 'yes' )
            {
                 sendThankYouEmailCustom( $postData, 2 ,  $e, $en );		         
	         sendNotifEmailCustom( $postData, 2, $e, $en, $ne);
            }else{
                 sendThankYouEmail( $postData, 2 ,  $e, $en );		         
	         sendNotifEmail( $postData, 2, $e, $en, $ne);
            }

           //Get Charge ID
           $charge_id = $event_json->data->object->id;
           $charge = MStripe_Charge::retrieve($charge_id);
           add_post_meta( $post_id, 'miglad_paymentdata', $charge );
           add_post_meta( $new_id, 'miglad_transactionId', $charge_id  );

       }else{  //No transient Data

       }

    }else{ //There is an old repeating id, recurring payment

          //post for donation
          $new_donation = array(
	      'post_title' => 'migla_donation',
	      'post_content' => '',
	      'post_status' => 'publish',
	      'post_author' => 1,
	      'post_type' => 'migla_donation'
          );

           $new_id = wp_insert_post( $new_donation );

           //This a repeating, old subscriber
           migla_create_from_old_donation( $old_id, $new_id);

           update_post_meta( $new_id, 'miglad_time', $t ); 
           update_post_meta( $new_id, 'miglad_date', $d ); 

           add_post_meta( $new_id, 'miglad_paymentmethod', 'credit card' );
           add_post_meta( $new_id, 'miglad_timezone', $default );
           add_post_meta( $new_id, 'miglad_transactionType', 'Recurring (Stripe)' );
           add_post_meta( $new_id, 'miglad_subscription_type', 'current' ); 
           add_post_meta( $new_id, 'miglad_subscription_id', $customer_id ); 

            $e = get_option('migla_replyTo');
            $en = get_option('migla_replyToName');
            $ne = get_option('migla_notif_emails');
		
             if( get_option( 'miglaactions_2_1' ) == 'yes' )
             {
                       sendThankYouEmailRepeatingCustom( $new_id, $e, $en ) ;
                       sendNotifEmailRepeatingCustom( $new_id, $e, $en, $ne) ;
             }else{
                       sendThankYouEmailRepeating( $new_id, $e, $en );
	               sendNotifEmailRepeating( $new_id, $e, $en, $ne);
             }

           //Get Charge ID
           $charge_id = $event_json->data->object->id;
           $charge = MStripe_Charge::retrieve($charge_id);
           add_post_meta( $post_id, 'miglad_paymentdata', $charge );
           add_post_meta( $new_id, 'miglad_transactionId', $charge_id  );

      }//End If Repeating

   }//endif has customer

 }else if ( $event_json->type == 'charge.dispute.created' )
 {
    //Ok get this charge id
    $charge_id = $event_json->data->object->charge;
    $charge = MStripe_Charge::retrieve($charge_id);   
    $description = $charge->description;
    $desc = explode(";", $description ) ;

    $session = $desc[2];
    $session_id = 'migla'. $session;

    $who_is = migla_cek_repeating_id( $session_id );
    add_post_meta( $who_is , 'miglad_charge_dispute', 'dispute' ); 
 
 }else if ( $event_json->type == 'charge.dispute.closed' )
 {
    //Ok get this charge id
    $charge_id = $event_json->data->object->charge;
    $charge = MStripe_Charge::retrieve($charge_id);   
    $description = $charge->description;
    $desc = explode(";", $description ) ;

    $session = $desc[2];
    $session_id = 'migla'. $session;

    $who_is = migla_cek_repeating_id( $session_id );
    update_post_meta( $who_is , 'miglad_charge_dispute', '' ); 
 
 }else{ // ELSE This will send receipts on succesful invoices

 }


?>