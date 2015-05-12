<?php
/***********************************************/
/*   AJAX CALLERS  */

/************* April 2th ***********************/
  add_action("wp_ajax_miglaA_update_postmeta", "miglaA_update_postmeta");
  add_action("wp_ajax_nopriv_miglaA_update_postmeta", "miglaA_update_postmeta");

function miglaA_update_postmeta() 
{
   $id = $_POST['id'];
   $key = $_POST['key'];
   $value = $_POST['value'];

 if( empty($value) || $value == '' ){
     global $wpdb;
     $wpdb->query( 
	$wpdb->prepare( "DELETE FROM {$wpdb->prefix}postmeta WHERE meta_key = %s and post_id = %d" , $key, $id )
     );
 }else{
   update_post_meta( $id, $key , '');
   update_post_meta( $id, $key , $value);
 }  
   die();
}

  add_action("wp_ajax_miglaA_delete_postmeta", "miglaA_delete_postmeta");
  add_action("wp_ajax_nopriv_miglaA_delete_postmeta", "miglaA_delete_postmeta");

function miglaA_delete_postmeta() 
{
   $id = $_POST['id'];
   $key = $_POST['key'];

   global $wpdb;
   $wpdb->query( 
	$wpdb->prepare( "DELETE FROM {$wpdb->prefix}postmeta WHERE meta_key = %s and post_id = %d" , $key, $id )
   );

   die();
}

  add_action("wp_ajax_miglaA_get_postmeta", "miglaA_get_postmeta");
  add_action("wp_ajax_nopriv_miglaA_get_postmeta", "miglaA_get_postmeta");

function miglaA_get_postmeta() {

   $id = $_POST['id'];
   $key = $_POST['key'];

   $out = '-1'; 

   $data = get_post_meta( $id, $key );
   if( !empty($data) ){
      $out = $data[0];
   }
   echo $out ;
   die();
}

  add_action("wp_ajax_miglaA_update_recurring_plans", "miglaA_update_recurring_plans");
  add_action("wp_ajax_nopriv_miglaA_update_recurring_plans", "miglaA_update_recurring_plans");

function miglaA_update_recurring_plans(){
 
        $isUpgrade = false;
        if( $_POST['old_interval_count'] != $_POST['new_interval_count'] ) {
            $isUpgrade = true;
        } 
        if(  $_POST['old_interval'] != $_POST['new_interval']  ){
            $isUpgrade = true;
        } 
 
   $success = "1"; $error1 = ""; $error2 = ""; $error3 = ""; $error4 = ""; $error5 = ""; $error6 = "";

   try{

        require_once 'migla-call-stripe.php';

        if( $_POST['old_payment_method'] == 'paypal' && ( strpos( $_POST['new_payment_method'] , 'stripe') !== false ) )
        {
		   //add the new plan to stripe and database
			   if( !isset($_POST['new_interval_count']) || empty($_POST['new_interval_count']) ){ 
					 $_count = 1; 
			   }else{ 
					 $_count = $_POST['new_interval_count'] ;
			   }

				 //Retrieve
				  Migla_Stripe::setApiKey( migla_getSK() );

			   $plan = MStripe_Plan::create(
				  array(
				   "amount"         => 1,
				   "interval"       => $_POST['new_interval'],
				   "interval_count" => $_count,
				   "name"           => $_POST['new_name'],
				   "currency"       => get_option('migla_default_currency'),
				   "id"             => $_POST['new_id']
				  )
			   );

			   $plan_array = $plan->__toArray(true); 
			   $post_id = migla_get_stripeplan_id();
			   add_post_meta( $post_id, 'stripeplan_'.$_POST['new_id'], $plan_array );

            $success = "1";

       }else if( ( strpos( $_POST['old_payment_method'], 'stripe') !== false) && ( strpos( $_POST['new_payment_method'] , 'stripe') !== false ) ){
           
           if( $isUpgrade ){

					//Oh this is a change on Interval values in Stripe Plan, well we need to delete and create new
					$post_id = migla_get_stripeplan_id();

				   Migla_Stripe::setApiKey( migla_getSK() );

				   //delete Plan in Stripe and Database
				   $plan = MStripe_Plan::retrieve( $_POST['old_id'] );
				   $plan->delete();
	 
				   global $wpdb; 
				   $delete_key = 'stripeplan_'.$_POST['old_id'];
				   $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}postmeta WHERE post_id = %d AND META_KEY = %s" , $post_id, $delete_key ) );

				   //Recreate a new Plan              
				   //Ok new beginning, This is very hard and all you do just click a button
				   Migla_Stripe::setApiKey( migla_getSK() );

				   if( !isset($_POST['new_interval_count']) || empty($_POST['new_interval_count']) ){ 
					   $_count = 1; 
				   }else{ 
					   $_count = $_POST['new_interval_count'] ;
				   }

				   $plan = MStripe_Plan::create(
					  array(
						"amount" => 1,
						"interval" => $_POST['new_interval'],
						"interval_count" => $_count,
						"name" => $_POST['new_name'],
						"currency" => get_option('migla_default_currency'),
						"id" => $_POST['old_id']
					  )
					);

				   $plan_array = $plan->__toArray(true); 
				   add_post_meta( $post_id, 'stripeplan_'.$_POST['new_id'], $plan_array );
                   $success = "1";

           }else{ //Ok thanks, This is only change the name

			//update Plan in Stripe and Database
			//Well if the intervals doesn't change then
			Migla_Stripe::setApiKey( migla_getSK() );
			$plan = MStripe_Plan::retrieve( $_POST['old_id'] );
			$plan->name = $_POST['new_name'];
			$plan->save();

                        //Renew postmeta
                        $post_id = migla_get_stripeplan_id();
                        $new_plan_array = $plan->__toArray(true) ;
                        update_post_meta( $post_id, ("stripeplan_".$_POST['old_id']), $new_plan_array );

                    $success = "1";
           }

       }else if( ( strpos( $_POST['old_payment_method'], 'stripe') !== false ) && $_POST['new_payment_method'] == 'paypal' ){

                //Oh this is a downgrade
               Migla_Stripe::setApiKey( migla_getSK() );

               //delete Plan in Stripe and Database
               $plan = MStripe_Plan::retrieve( $_POST['old_id'] );
               $plan->delete();
  
               //Remove it from db
               $post_id = migla_get_stripeplan_id();
               $delete_key = 'stripeplan_'.$_POST['old_id'];
               delete_post_meta( $post_id, ("stripeplan_".$_POST['old_id']) );

              $success = "1";

       }else if( ($_POST['old_payment_method'] == 'paypal') && ($_POST['new_payment_method'] == 'paypal') ){  
              $success = "1";
       }

   } catch( MStripe_CardError $e ) {
            $error1 = $e->getMessage();  $success = "-1";
   } catch ( MStripe_InvalidRequestError $e ) {
                // Invalid parameters were supplied to Stripe's API
           $error2 = $e->getMessage(); $success = "-1";
   } catch ( MStripe_AuthenticationError $e ) {
                 // Authentication with Stripe's API failed
           $error3 = $e->getMessage(); $success = "-1";
   } catch ( MStripe_ApiConnectionError $e ) {
                  // Network communication with Stripe failed
           $error4 = $e->getMessage(); $success = "-1";
   } catch ( MStripe_Error $e ) {
           $error5 = $e->getMessage(); $success = "-1";
   } catch ( Exception $e ) {
                 // Something else happened, completely unrelated to Stripe
          $error6 = $e->getMessage(); $success = "-1";
   }

   $message = "";
   if( $success == "1" ){
       update_option( 'migla_recurring_plans' , $_POST['list'] ); $message = $success;
   }else{
        $message .= $error1. " " . $error2. " " . $error3. " " . $error4. " " .$error5. " " .$error6 ;
   }

   echo $message;
   die();
}

/***********************************************/
/*      Checkout  April2th    */
/***********************************************/
  add_action("wp_ajax_miglaA_checkout", "miglaA_checkout");
  add_action("wp_ajax_nopriv_miglaA_checkout", "miglaA_checkout");

function miglaA_checkout()
{
	 // Repack the Default Field Post
        $arr = $_POST['donorinfo'] ;
        $map = array();

        $map['miglad_anonymous'] = 'no'; 
        $map['miglad_repeating'] = 'no'; 

        foreach( (array)$arr as $d)
        {
           $map[ esc_attr( $d[0] ) ] = esc_attr( $d[1] );
        }

        $transientKey =  "t_". esc_attr( $map['miglad_session_id'] );

     
       ///GET CURRENT TIME SETTINGS----------------------------------
	  $php_time_zone = date_default_timezone_get();
        $t = ""; $d = "";
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
       ///---------------------------------GET CURRENT TIME SETTINGS
   
        $map['miglad_date'] = $d; 
        $map['miglad_time'] = $t; 

   
	// Put the results in a transient. Expire after 12 hours.
	//set_transient( $transientKey, $map, 12 * HOUR_IN_SECONDS ); //this is for default data

        add_option( $transientKey, $map );

    if(  $map['miglad_honoreeletter'] != '' )
    {
        $hletter =  $transientKey. "hletter";
        add_option($hletter , $map['miglad_honoreeletter'] );
    }

    echo "";
    die();  
       
}

  add_action("wp_ajax_miglaA_checkout_nonce", "miglaA_checkout_nonce");
  add_action("wp_ajax_nopriv_miglaA_checkout_nonce", "miglaA_checkout_nonce");

function miglaA_checkout_nonce()
{
  $msg ='';

   if ( wp_verify_nonce( $_POST['nonce'], 'migla_' ) )
   {
	 // Repack the Default Field Post
        $arr = $_POST['donorinfo'] ;
        $map = array();
        
        $map['miglad_anonymous'] = 'no'; 
        $map['miglad_repeating'] = 'no'; 

        foreach( (array)$arr as $d)
        {
          $map[ esc_attr( $d[0] ) ] = esc_attr( $d[1] );
        }

        $transientKey =  "t_". esc_attr( $map['miglad_session_id'] );

       ///GET CURRENT TIME SETTINGS----------------------------------
	  $php_time_zone = date_default_timezone_get();
        $t = ""; $d = "";
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
       ///---------------------------------GET CURRENT TIME SETTINGS
   
        $map['miglad_date'] = $d; 
        $map['miglad_time'] = $t; 
   
	// Put the results in a transient. Expire after 12 hours.
        add_option( $transientKey, $map );

      if(  $map['miglad_honoreeletter'] != '' )
      {
        $hletter = $transientKey. "hletter" ;
        add_option($hletter , $map['miglad_honoreeletter'] );
      }

      $msg = '0';
   }else{
      $msg = '-1';
   }

    echo $msg;
    die();  
       
}


/***********************************************/
/*   STRIPE    APRIL 2th */
/***********************************************/
  add_action("wp_ajax_miglaA_syncPlan", "miglaA_syncPlan");
  add_action("wp_ajax_nopriv_miglaA_syncPlan", "miglaA_syncPlan");

function miglaA_syncPlan(){

  require_once 'migla-call-stripe.php'; 

  $post_id = migla_get_stripeplan_id();
   
  global $wpdb;
  $data = array(); 
  $data =  $wpdb->get_results( 
	$wpdb->prepare( 
  	   "SELECT * FROM {$wpdb->prefix}postmeta WHERE post_id = %d" , $post_id
        )
  );

  $row1 = 0; $row2 = 0; $metaid_on_server = array(); $list_on_server = array();
  foreach( $data as $d ){

     $val = unserialize( $d->meta_value );
     $keys = array_keys($val);

       $metaid_on_server[$row1]  = $d->meta_id;

     foreach($keys as $key){
        if( $key == 'id' ){
           if( in_array( $val[$key] , $list_on_server, true) ){
             migla_delete_post_meta2( $d->meta_id  );
           }else{
             $list_on_server[$row2] = $val[$key];
             $row2++;
           }
        }
     }

    $row1++;
  }


 //Retrieve
 Migla_Stripe::setApiKey( migla_getSK() );

 $plans = MStripe_Plan::all(); 

 $plans_arr = $plans->__toArray(true);
 $plan_data = (array)$plans_arr['data'];


  //Let's make comparison and add plan that doesn't exist on server
   $list_on_stripe = array(); $row = 0;
   $keys = array_keys($plan_data);
   foreach( $keys as $key ){
     $id_from_stripe = $plan_data[$key]['id']; 
     $list_on_stripe[$row] = $id_from_stripe; $row++;
     if( in_array( $id_from_stripe , $list_on_server, TRUE) ){
     }else{
        add_post_meta( $post_id, 'stripeplan_'.$id_from_stripe, $plan_data[$key] ); //if is not add to database
     }
   }

  //Reverse . Let's make comparison and delete plan that doesn't exist on stripe
  $keys = array_keys( $list_on_server );
   foreach( $keys as $key ){
     if( in_array( $list_on_server[$key] ,  $list_on_stripe , TRUE) ){
     }else{
        $metakey = "stripeplan_" . $list_on_server[$key] . "%";
        migla_delete_post_meta1( $metakey );
     }
   }

  echo miglaA_stripe_getPlan();
  die();

}

  add_action("wp_ajax_miglaA_stripe_addPlan", "miglaA_stripe_addPlan");
  add_action("wp_ajax_nopriv_miglaA_stripe_addPlan", "miglaA_stripe_addPlan");

function miglaA_stripe_addPlan(){

   require_once 'migla-call-stripe.php'; 
   Migla_Stripe::setApiKey( migla_getSK() );

   if( !isset($_POST['interval_count']) || empty($_POST['interval_count']) ){ 
      $_count = 1; 
   }else{ 
      $_count = $_POST['interval_count'] ;
   }

  $plan = MStripe_Plan::create(
    array(
      "amount" => $_POST['amount'],
      "interval" => $_POST['interval'],
      "interval_count" => $_count,
      "name" => $_POST['name'],
      "currency" => get_option('migla_default_currency'),
      "id" => $_POST['id']
    )
 );

  $plan_array = $plan->__toArray(true);
   
   $post_id = migla_get_stripeplan_id();

   add_post_meta( $post_id, 'stripeplan_'.$_POST['id'], $plan_array );

   echo $arr;
   die();
}

  add_action("wp_ajax_miglaA_stripe_addBasicPlan", "miglaA_stripe_addBasicPlan");
  add_action("wp_ajax_nopriv_miglaA_stripe_addBasicPlan", "miglaA_stripe_addBasicPlan");

function miglaA_stripe_addBasicPlan(){

   $success = "-1"; $error1 = ""; $error2 = ""; $error3 = ""; $error4 = ""; $error5 = ""; $error6 = "";

   try{

      require_once 'migla-call-stripe.php'; 
      Migla_Stripe::setApiKey( migla_getSK() );
 
      if( !isset($_POST['interval_count']) || empty($_POST['interval_count']) ){ 
          $_count = 1; 
      }else{ 
          $_count = (int)$_POST['interval_count'] ;
      }

      $plan = MStripe_Plan::create(
          array(
              "amount" => 1,
              "interval" => $_POST['interval'],
              "interval_count" => $_count,
              "name" => $_POST['name'],
              "currency" => get_option('migla_default_currency'),
              "id" => $_POST['id']
          ));

       $plan_array = $plan->__toArray(true);
       $success = "1";

   } catch( MStripe_CardError $e ) {
            $error1 = $e->getMessage();  $success = "-1";
   } catch ( MStripe_InvalidRequestError $e ) {
                // Invalid parameters were supplied to Stripe's API
           $error2 = $e->getMessage(); $success = "-1";
   } catch ( MStripe_AuthenticationError $e ) {
                 // Authentication with Stripe's API failed
           $error3 = $e->getMessage(); $success = "-1";
   } catch ( MStripe_ApiConnectionError $e ) {
                  // Network communication with Stripe failed
           $error4 = $e->getMessage(); $success = "-1";
   } catch ( MStripe_Error $e ) {
           $error5 = $e->getMessage(); $success = "-1";
   } catch ( Exception $e ) {
                 // Something else happened, completely unrelated to Stripe
          $error6 = $e->getMessage(); $success = "-1";
   }

   $message = "";
   if( $success == "1" ){
        $post_id = migla_get_stripeplan_id();
        add_post_meta( $post_id, 'stripeplan_'.$_POST['id'], $plan_array ); $message = $success;
   }else{
        $message .= $error1. " " . $error2. " " . $error3. " " . $error4. " " .$error5. " " .$error6 ;
   }

   echo $message;
   die();
}

  add_action("wp_ajax_miglaA_stripe_deletePlan", "miglaA_stripe_deletePlan");
  add_action("wp_ajax_nopriv_miglaA_stripe_deletePlan", "miglaA_stripe_deletePlan");

function miglaA_stripe_deletePlan(){

   require_once 'migla-call-stripe.php'; 
   $success = "-1"; $error1 = ""; $error2 = ""; $error3 = ""; $error4 = ""; $error5 = ""; $error6 = "";

 try{
      Migla_Stripe::setApiKey( migla_getSK() );
 
     $plan = MStripe_Plan::retrieve( $_POST['id'] );
     $plan->delete();
   
     $post_id = migla_get_stripeplan_id();
     $meta_key = 'stripeplan_'.$_POST['id'];

     global $wpdb;
     $data =  $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}postmeta WHERE post_id = %d and meta_key = %s" ,
	       $post_id,  $meta_key 
            )
     );
     $success = "1";

   } catch( MStripe_CardError $e ) {
            $error1 = $e->getMessage();  $success = "-1";
   } catch ( MStripe_InvalidRequestError $e ) {
                // Invalid parameters were supplied to Stripe's API
           $error2 = $e->getMessage(); $success = "-1";
   } catch ( MStripe_AuthenticationError $e ) {
                 // Authentication with Stripe's API failed
           $error3 = $e->getMessage(); $success = "-1";
   } catch ( MStripe_ApiConnectionError $e ) {
                  // Network communication with Stripe failed
           $error4 = $e->getMessage(); $success = "-1";
   } catch ( MStripe_Error $e ) {
           $error5 = $e->getMessage(); $success = "-1";
   } catch ( Exception $e ) {
                 // Something else happened, completely unrelated to Stripe
          $error6 = $e->getMessage(); $success = "-1";
   }

   $message = "";
   if( $success == "1" ){
       $message = $success;
   }else{
       $message .= $error1. " " . $error2. " " . $error3. " " . $error4. " " .$error5. " " .$error6 ;
   }

   echo $message;
   die();
}

  add_action("wp_ajax_miglaA_stripe_getPlan", "miglaA_stripe_getPlan");
  add_action("wp_ajax_nopriv_miglaA_stripe_getPlan", "miglaA_stripe_getPlan");

function miglaA_stripe_getPlan(){

   $out = array(); $result = array(); $row = 0;

   $post_id = migla_get_stripeplan_id();
   
     global $wpdb;
     $data = array();
     $data =  $wpdb->get_results( 
	$wpdb->prepare( 
	"SELECT * FROM {$wpdb->prefix}postmeta WHERE post_id = %d" ,
	   $post_id
        )
     );

     $result = array(); $row = 0;  $out = array();

   if( count($data) > 0 ){
     
      foreach( $data as $d )
      {
         $out[$row]['id'] = $d->meta_id;
         $out[$row]['detail'] = "<input class='mglrec' type=hidden name='".$row."' >"; 
 
         $x = unserialize( $d->meta_value );
         $keys = array_keys($x);

         foreach($keys as $key){
           if( $key == 'created' ){  
              $out[$row][$key] = date( "Y-m-d" , $x[$key] );
           }else if( $key == 'id' ){  
              $out[$row]['planid'] =  $x[$key];
           }else{
              $out[$row][$key] = $x[$key];
           }
         }

         $row++;
      }//foreach
  }

  $result[0] = $out;
 
  echo json_encode( $result );
  die();

}

/********************************************************************************************/
            /** CHARGE **/
  add_action("wp_ajax_miglaA_stripeCharge", "miglaA_stripeCharge");
  add_action("wp_ajax_nopriv_miglaA_stripeCharge", "miglaA_stripeCharge");

function miglaA_stripeCharge()
{

    require_once 'migla-call-stripe.php'; 
    Migla_Stripe::setApiKey( migla_getSK() );
    $message = '';
    $success = "-1"; $error1 = ""; $error2 = ""; $error3 = ""; $error4 = ""; $error5 = ""; $error6 = "";

    try {
       if (!isset($_POST['stripeToken']))
         throw new Exception("The Stripe Token was not generated correctly");
       
      $charge = MStripe_Charge::create(array("amount" => $_POST['amount'],
                        "currency" => get_option('migla_default_currency'),
                        "card" => $_POST['stripeToken'] 
                 ));

     $array = $charge->__toArray(true);  

      if( $charge['status'] == 'paid' ){

         $new_donation = array(
	   'post_title' => 'migla_donation',
	   'post_content' => '',
	   'post_status' => 'publish',
	   'post_author' => 1,
	   'post_type' => 'migla_donation'
          );

           $new_id = wp_insert_post( $new_donation );

           $transient_key = "t_". $_POST['session'];
           $postData = get_option( $transient_key );
           $desc = "Name: ". $postData['miglad_firstname'] . " " . $postData['miglad_lastname'] . "; Email: " . $postData['miglad_email'] .";" ;
           $desc .= substr( $postData['miglad_session_id'], 5 ) ;

                        $keys = array_keys( $postData );
                         foreach( (array)$postData as $value)
                         {
                              add_post_meta( $new_id , $keys[$i], $value );
                            $i++;
                          }        

            $e = get_option('migla_replyTo');
            $en = get_option('migla_replyToName');
            $ne = get_option('migla_notif_emails');
			
           ///GET CURRENT TIME SETTINGS----------------------------------
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
          ///---------------------------------GET CURRENT TIME SETTINGS

               /*****  Add transaction data *******/
                   add_post_meta( $new_id, 'miglad_paymentmethod', 'credit card' );
                   add_post_meta( $new_id, 'miglad_paymentdata', $array );
                   add_post_meta( $new_id, 'miglad_transactionId', $charge['id'] );
                   add_post_meta( $new_id, 'miglad_transactionType', 'One time (Stripe)' );
                   add_post_meta( $new_id, 'miglad_timezone', $default );
                   update_post_meta( $new_id, 'miglad_session_id', $_POST['session'] ); 

                    if( get_option( 'miglaactions_2_1' ) == 'yes' )
                    {
                        sendThankYouEmailCustom( $postData, 2 ,  $e, $en );		         
	                sendNotifEmailCustom( $postData, 2, $e, $en, $ne);
                    }else{
                        sendThankYouEmail( $postData, 2 ,  $e, $en );		         
	                sendNotifEmail( $postData, 2, $e, $en, $ne);
                    }


                    $tdata =  $transientKey. "hletter";

                    $content =  get_option( $tdata );

                    migla_hletter( $e, $en , $postData['miglad_honoreeemail'], $content, $postData['miglad_repeating']
                                , $postData['miglad_anonymous'], $postData['miglad_firstname'], $postData['miglad_lastname'], 
                                 $postData['miglad_amount'], $postData['miglad_honoreename'] , $d );
       
           //Update Charge Description
           $ch = MStripe_Charge::retrieve( $charge['id'] );
           $ch->description =  $desc;
           $ch->save();

         $success = "1";

      }else{  //If status is not paid

      }

   } catch( MStripe_CardError $e ) {
            $error1 = $e->getMessage();  $success = "-1";
   } catch ( MStripe_InvalidRequestError $e ) {
                // Invalid parameters were supplied to Stripe's API
           $error2 = $e->getMessage(); $success = "-1";
   } catch ( MStripe_AuthenticationError $e ) {
                 // Authentication with Stripe's API failed
           $error3 = $e->getMessage(); $success = "-1";
   } catch ( MStripe_ApiConnectionError $e ) {
                  // Network communication with Stripe failed
           $error4 = $e->getMessage(); $success = "-1";
   } catch ( MStripe_Error $e ) {
           $error5 = $e->getMessage(); $success = "-1";
   } catch ( Exception $e ) {
                 // Something else happened, completely unrelated to Stripe
          $error6 = $e->getMessage(); $success = "-1";
   }

   $message = "";
   if( $success == "1" ){
       $message = $success;
   }else{
       $message .= $error1. " " . $error2. " " . $error3. " " . $error4. " " .$error5. " " .$error6 ;
   }

   echo $message; 

    die();
}

  add_action("wp_ajax_miglaA_createSubscription", "miglaA_createSubscription");
  add_action("wp_ajax_nopriv_miglaA_createSubscription", "miglaA_createSubscription");

function miglaA_createSubscription(){

  $success = "-1"; $error1 = ""; $error2 = ""; $error3 = ""; $error4 = ""; $error5 = ""; $error6 = "";

  try{

     require_once 'migla-call-stripe.php'; 
     Migla_Stripe::setApiKey( migla_getSK() );

     $transient_key = "t_".$_POST['session'];
     $postData = get_option( $transient_key  );
     $desc = "Name: ". $postData['miglad_firstname'] . " " . $postData['miglad_lastname'] . ";" . substr( $postData['miglad_session_id'] ,5 );

     $customer = MStripe_Customer::create(array(
        "source" => $_POST['stripeToken'], 
        "email" => $postData['miglad_email'],
        "description" => $desc
     ));

     $cu = MStripe_Customer::retrieve( $customer['id'] ); 
     $subscr = $cu->subscriptions->create(array(
                         "plan" => $_POST['plan'],
                         "quantity" => $_POST['quantity']
                      ));
     $success = "1";
  

   } catch( MStripe_CardError $e ) {
            $error1 = $e->getMessage();  $success = "-1";
   } catch ( MStripe_InvalidRequestError $e ) {
                // Invalid parameters were supplied to Stripe's API
           $error2 = $e->getMessage(); $success = "-1";
   } catch ( MStripe_AuthenticationError $e ) {
                 // Authentication with Stripe's API failed
           $error3 = $e->getMessage(); $success = "-1";
   } catch ( MStripe_ApiConnectionError $e ) {
                  // Network communication with Stripe failed
           $error4 = $e->getMessage(); $success = "-1";
   } catch ( MStripe_Error $e ) {
           $error5 = $e->getMessage(); $success = "-1";
   } catch ( Exception $e ) {
                 // Something else happened, completely unrelated to Stripe
          $error6 = $e->getMessage(); $success = "-1";
   }

   $message = "";
   if( $success == "1" ){
        $message = $success;
   }else{
        $message .= $error1. " " . $error2. " " . $error3. " " . $error4. " " .$error5. " " .$error6 ;
   }
  
  echo $message;
  die();
}

/**************************************************************************/

add_action("wp_ajax_miglaA_purgeCache", "miglaA_purgeCache");
add_action("wp_ajax_nopriv_miglaA_purgeCache", "miglaA_purgeCache");

function miglaA_purgeCache(){
 global $wpdb; $msg = ""; $count = 0;
 
 $option_id = array(); 
 $option_id = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}options WHERE option_name LIKE 't_migla%'" );
 
 foreach( $option_id as $id )
 {
        $now = time();
	$option_name = $id->option_name;
        $date = substr(  $option_name , 7, 8);      
	if( ( $now - time($date) ) > 0  )
	{
	   delete_option( $option_name );
           //$msg .= $now ." ". $date. " ".($now - $date). " " . $option_name. "<br>";
           $count++;
	}

 }
 $msg .= $count . " cache(s) erased";
 echo $msg;
 die();
}

add_action("wp_ajax_get_option_id", "get_option_id");
add_action("wp_ajax_nopriv_get_option_id", "get_option_id");

function get_option_id( $op ){
  global $wpdb; $res =array();
  $sql = "SELECT option_id from {$wpdb->prefix}options WHERE option_name='".$op."'";
  $res = $wpdb->get_row($sql);
  return $res->option_id;
}

/**************         Main Page            ***********************/
/*******************************************************************/
  add_action("wp_ajax_miglaA_totalAll", "miglaA_totalAll");
  add_action("wp_ajax_nopriv_miglaA_totalAll", "miglaA_totalAll");

function miglaA_totalAll()
{
 global $wpdb;
 $data = array();
 $data = $wpdb->get_results( 
	$wpdb->prepare( 
		"SELECT {$wpdb->prefix}posts.ID FROM {$wpdb->prefix}posts
	  WHERE post_type = %s",
	  'migla_donation'
        )
 );
 $ton = 0;
 foreach( $data as $id )
 {
    $ton = $ton + get_post_meta( intval( $id->ID ) , 'miglad_amount', true);
 }

 global $wpdb;
 $data = array();
 $data = $wpdb->get_results( 
	$wpdb->prepare( 
		"SELECT {$wpdb->prefix}posts.ID FROM {$wpdb->prefix}posts
	  WHERE post_type = %s",
	  'migla_odonation'
        )
 );
 $toff = 0;
 foreach( $data as $id )
 {
    $toff = $toff + get_post_meta( intval( $id->ID ) , 'miglad_amount', true);
 }

$out = array();
$out[0] = $ton; $out[1] = $toff; $out[2] = $ton + $toff;

  echo json_encode ( $out );
  die();
}

  add_action("wp_ajax_miglaA_totalOffAll", "miglaA_totalOffAll");
  add_action("wp_ajax_nopriv_miglaA_totalOffAll", "miglaA_totalOffAll");

function miglaA_totalOffAll()
{
  $toff = 0;

  global $wpdb;
  $data = array();
  $data = $wpdb->get_results( $wpdb->prepare( 
		"SELECT {$wpdb->prefix}posts.ID FROM {$wpdb->prefix}posts
	  WHERE post_type = %s",
	  'migla_odonation'
        ));
  foreach( $data as $id )
  {
     $toff =  $toff + get_post_meta( intval( $id->ID ) , 'miglad_amount', true);
  }

  $out = array();
  $out[0] = $toff;
  echo json_encode ( $out );
  die();
}

  add_action("wp_ajax_miglaA_totalThisMonth", "miglaA_totalThisMonth");
  add_action("wp_ajax_nopriv_miglaA_totalThisMonth", "miglaA_totalThisMonth");

function miglaA_totalThisMonth()
{
  global $wpdb;
  $data = array();
  $data = $wpdb->get_results( "
        SELECT {$wpdb->prefix}posts.ID, post_date
        FROM {$wpdb->prefix}posts
        WHERE post_type = 'migla_donation'
        AND year( post_date ) = year( current_date( ) )
         AND month( post_date ) = month( current_date( ) )
	 ");
  $ton = 0;
  foreach( $data as $id )
  {
     $ton = $ton + get_post_meta( intval( $id->ID ) , 'miglad_amount', true);
  } 

  global $wpdb;
  $data = array();
  $data = $wpdb->get_results( "
        SELECT {$wpdb->prefix}posts.ID, post_date
        FROM {$wpdb->prefix}posts
        WHERE post_type = 'migla_odonation'
        AND year( post_date ) = year( current_date( ) )
         AND month( post_date ) = month( current_date( ) )
	 "
       );

  $toff = 0;
  foreach( $data as $id )
  {
     $toff =  $toff + get_post_meta( intval( $id->ID ) , 'miglad_amount', true);
  }
  $out = array();
  $out[0] = $ton; $out[1] = $toff; $out[2] = $ton + $toff;

  echo json_encode ( $out );
  die();
}


/** Updated April 4th **/
  add_action("wp_ajax_miglaA_recentDonations", "miglaA_recentDonations");
  add_action("wp_ajax_nopriv_miglaA_recentDonations", "miglaA_recentDonations");

function miglaA_recentDonations() {
   global $wpdb;
   $data = array();
   $data = $wpdb->get_results( 
	$wpdb->prepare( 
         "SELECT DISTINCT ID from {$wpdb->prefix}posts inner join {$wpdb->prefix}postmeta
          on ID = post_id
          WHERE post_type = %s
          ORDER BY post_date DESC
          LIMIT 0,5"
          , 'migla_donation'
        )
   );

   $out = array(); $key = ""; $row = 0; $id = 0; $state = "";
   foreach( $data as $id )
   {
    
      $out[$row]['time'] = get_post_meta( intval( $id->ID ) , 'miglad_time', true);
      $out[$row]['date'] = get_post_meta( intval( $id->ID ) , 'miglad_date', true);
      $out[$row]['name'] = get_post_meta( intval( $id->ID ) , 'miglad_firstname', true)." ".get_post_meta( intval( $id->ID ) , 'miglad_lastname', true);
      $out[$row]['amount'] = get_post_meta( intval( $id->ID ) , 'miglad_amount', true);

      $out[$row]['address'] = get_post_meta( intval( $id->ID ) , 'miglad_address', true);
      $out[$row]['city'] = get_post_meta( intval( $id->ID ) , 'miglad_city', true);

      $out[$row]['state'] = get_post_meta( intval( $id->ID ) , 'miglad_state', true);
      $out[$row]['province'] = get_post_meta( intval( $id->ID ) , 'miglad_province', true);

      $out[$row]['country'] = get_post_meta( intval( $id->ID ) , 'miglad_country', true);
      $out[$row]['postalcode'] = get_post_meta( intval( $id->ID ) , 'miglad_postalcode', true);

      $out[$row]['repeating'] = get_post_meta( intval( $id->ID ) , 'miglad_repeating', true);   
      $out[$row]['anonymous'] = get_post_meta( intval( $id->ID ) , 'miglad_anonymous', true);   

      $row = $row + 1;
  }
 
 echo json_encode($out);  
 die();
}

  add_action("wp_ajax_miglaA_campaignprogress", "miglaA_campaignprogress");
  add_action("wp_ajax_nopriv_miglaA_campaignprogress", "miglaA_campaignprogress");

function miglaA_campaignprogress(){
   $out = array(); //[index][campaign][percent]

   $campaignArray = (array)get_option( 'migla_campaign' );
   $row = 0;
   $dec = 2;
   $showDecimal = get_option('migla_showDecimalSep');
   if( $showDecimal == 'no' ){ $dec = 0; }

   if( $campaignArray[0] != '')
   {
    foreach( (Array) $campaignArray as $key => $value)
    { 
     $cname = $campaignArray[$key]['name'];
     $ccname = str_replace( "[q]", "'", $cname );

     $out[$row]['type'] = 'designated';
     $out[$row]['campaign'] = $ccname; //remember ' is replaced by [q] 

     $target = migla_get_campaign_target( $cname );
     $amount = migla_get_total( $cname , "" );
     $out[$row]['target'] = number_format( $target , 2);
     $out[$row]['amount'] = number_format( $amount, $dec);

     if( $target != 0 ){
      $out[$row]['percent']  =  number_format( ($amount / $target) * 100, 2);
     }else{
      $out[$row]['percent'] = 0;
     }    
     $out[$row]['status'] = $campaignArray[$key]['show'];

     $row = $row + 1;
    }	
   }

   echo json_encode($out); 
   die(); 
}

//////// GRAPHIC //////////////////
  add_action("wp_ajax_migla_donations_6months", "migla_donations_6months");
  add_action("wp_ajax_nopriv_migla_donations_6months", "migla_donations_6months");

function migla_donations_6months() {
  $out = array();
 global $wpdb;
 $arr = array();
 $arr = $wpdb->get_results( 
	$wpdb->prepare( 
	"SELECT ID FROM {$wpdb->prefix}posts INNER JOIN {$wpdb->prefix}postmeta ON {$wpdb->prefix}posts.ID = {$wpdb->prefix}postmeta.post_id
         WHERE post_type = %s and meta_key = %s
         AND
         ( DATEDIFF( DATE_FORMAT(STR_TO_DATE(meta_value, %s), %s), Now() ) BETWEEN -180 AND 0)
         ORDER BY post_date ASC
        "  ,
	   'migla_donation','miglad_date', '%m/%d/%Y', '%Y-%m-%d', '%m/%d/%Y'
        )
 ); 

   $row = 0;
   if( empty($arr) ){
     $out[0]['amount'] = 0;
     $out[0]['date'] = date("m/d/Y");
     $out[0]['month'] = date("m");
     $out[0]['day'] = date("d");
     $out[0]['year'] = date("Y");   
   }else{
    foreach( $arr as $id )
    { 
     $out[$row]['amount'] = get_post_meta( intval( $id->ID ) , 'miglad_amount', true);
     $thedate = get_post_meta( intval( $id->ID ) , 'miglad_date', true);
     $out[$row]['date'] = $thedate;
     $dateField = explode( "/", $thedate);
     $out[$row]['month'] = $dateField[0]; //substr($thedate, 0,2);
     $out[$row]['day'] = $dateField[1];//substr($thedate, 6);
     $out[$row]['year'] = $dateField[2];//substr($thedate, 3,2);
     $row = $row + 1;
    }
   }  
  return $out;
}

  add_action("wp_ajax_migla_Ofdonations_6months", "migla_Ofdonations_6months");
  add_action("wp_ajax_nopriv_migla_Ofdonations_6months", "migla_Ofdonations_6months");

function migla_Ofdonations_6months() {
  $out = array();
 global $wpdb;
 $arr = array();
 $arr = $wpdb->get_results( 
	$wpdb->prepare( 
	"SELECT ID FROM {$wpdb->prefix}posts INNER JOIN {$wpdb->prefix}postmeta ON {$wpdb->prefix}posts.ID = {$wpdb->prefix}postmeta.post_id
         WHERE post_type = %s and meta_key = %s
         AND
         ( DATEDIFF( DATE_FORMAT(STR_TO_DATE(meta_value, %s), %s), Now() ) BETWEEN -180 AND 0)
         ORDER BY STR_TO_DATE( meta_value, %s) ASC
        " ,
	   'migla_odonation','miglad_date', '%m/%d/%Y', '%Y-%m-%d', '%m/%d/%Y'
        )
 ); 

   $row = 0;
   if( empty($arr) ){
     $out[0]['amount'] = 0;
     $out[0]['date'] = date("m/d/Y");
     $out[0]['month'] = date("m");
     $out[0]['day'] = date("d");
     $out[0]['year'] = date("Y");   
   }else{   
   foreach( $arr as $id )
   { 

    $out[$row]['amount'] = get_post_meta( intval( $id->ID ) , 'miglad_amount', true);;
    $thedate = get_post_meta( intval( $id->ID ) , 'miglad_date', true);
    $out[$row]['date'] = $thedate;
    $dateField = explode( "/", $thedate);
    $out[$row]['month'] = $dateField[0]; //substr($thedate, 0,2);
    $out[$row]['day'] = $dateField[1];//substr($thedate, 6);
    $out[$row]['year'] = $dateField[2];//substr($thedate, 3,2);
	
    $row = $row + 1;
   }
   }  
 return $out;
}

  add_action("wp_ajax_miglaA_getGraphData", "miglaA_getGraphData");
  add_action("wp_ajax_nopriv_miglaA_getGraphData", "miglaA_getGraphData");

function miglaA_getGraphData(){
  $output = array();
  $output[0] =  (array)migla_donations_6months();
  $output[1] =  (array)migla_Ofdonations_6months();
  echo json_encode( $output );
  die();
}

/**********************************************************************/
/********** THEME COLOR SETTINGS ***********************/
/**********************************************************************/
  add_action("wp_ajax_miglaA_reset_theme", "miglaA_reset_theme");
  add_action("wp_ajax_nopriv_miglaA_reset_theme", "miglaA_reset_theme");

function miglaA_reset_theme() {
   //THEME SETTINGS
   update_option( 'migla_2ndbgcolor' , '#FAFAFA,1' ); 

   update_option( 'migla_bglevelcolor', '#eeeeee' ); 
   update_option( 'migla_borderlevelcolor', '#C1C1C1' ); 
   update_option( 'migla_borderlevel', '1' ); 

   update_option( 'migla_2ndbgcolorb' , '#DDDDDD,1,1' ); 
   update_option( 'migla_borderRadius' , '8,8,8,8' );

   $barinfo = "Hem aconseguit [total] dels [target] necessaris. Això comporta un [percentage] del total per portal a terme la campanya [campaign].";
   update_option('migla_progbar_info', $barinfo); 
   update_option( 'migla_bar_color' , '#428bca,1' );
   update_option( 'migla_progressbar_background', '#bec7d3,1');
   update_option( 'migla_wellboxshadow', '#969899,1, 1,1,1,1');	

   $arr = array( 'Stripes' => 'yes', 'Pulse' => 'yes', 'AnimatedStripes' => 'yes', 'Percentage' => 'yes' );
   update_option( 'migla_bar_style_effect' , $arr);
}

  add_action("wp_ajax_miglaA_form_bgcolor", "miglaA_form_bgcolor");
  add_action("wp_ajax_nopriv_miglaA_form_bgcolor", "miglaA_form_bgcolor");

function miglaA_form_bgcolor() {
   $code = $_POST['color_code'];
   $op = get_option( 'migla_bgcolor' );
   if( get_option( 'migla_bgcolor' ) == ''){
      add_option( 'migla_bgcolor' , $code);
   }else{                     
      update_option( 'migla_bgcolor' , $code);   
   }
   die();
}

/**********************************************************************/
/********** GENERIC UPDATE OF OPTIONS ***********************/

  add_action("wp_ajax_migla_getme", "migla_getme");
  add_action("wp_ajax_nopriv_migla_getme", "migla_getme");

function migla_getme(){
  $r =  get_option($_POST['key']);
  echo $r;
  die();
}

  add_action("wp_ajax_migla_getme_array", "migla_getme_array");
  add_action("wp_ajax_nopriv_migla_getme_array", "migla_getme_array");

function migla_getme_array(){
  $r =  (array)get_option($_POST['key']);

  echo json_encode( $r );
  die();
}

  add_action("wp_ajax_nopriv_miglaA_update_me", "miglaA_update_me");
  add_action("wp_ajax_miglaA_update_me", "miglaA_update_me");

function miglaA_update_me() {
   $key = $_POST['key'];
   $value = $_POST['value'];

   update_option( $key , $value);
   
   die();
}

  add_action("wp_ajax_miglaA_update_barinfo", "miglaA_update_barinfo");
  add_action("wp_ajax_nopriv_miglaA_update_barinfo", "miglaA_update_barinfo");

function miglaA_update_barinfo() {
   $key = $_POST['key'];
   $value = $_POST['value'];

  update_option( $key , $value);
   
   die();
}

  add_action("wp_ajax_miglaA_update_arr", "miglaA_update_arr");
  add_action("wp_ajax_nopriv_miglaA_update_arr", "miglaA_update_arr");

function miglaA_update_arr() {
   $key = $_POST['key'];
   $value = serialize( $_POST['value'] );

   $op = get_option( $key );
   if( $op == false ){ add_option( $key , $value); }else{ update_option( $key , $value); }   
   
   die();
}

  add_action("wp_ajax_miglaA_update_us", "miglaA_update_us");
  add_action("wp_ajax_nopriv_miglaA_update_us", "miglaA_update_us");

function miglaA_update_us() {
  $arr = array();

  $arr = array(
    'Stripes' => $_POST['Stripes'],
    'Pulse' => $_POST['Pulse'],
    'AnimatedStripes' => $_POST['AnimatedStripes'],
    'Percentage' => $_POST['Percentage']    
  );

   update_option( 'migla_bar_style_effect' , $arr);
   echo( $_POST['Stripes'] );
   die();
}

/********** GIVING LEVELS ***********************/

  add_action("wp_ajax_miglaA_remove_options", "miglaA_remove_options");
  add_action("wp_ajax_nopriv_miglaA_remove_options", "miglaA_remove_options");

function miglaA_remove_options() {

   $key =  $_POST['key'];
   $option = $_POST['option_name'];
   $op = get_option( $option );

   unset( $op[$key] ); 
    
   update_option( $option ,  $op ); 

   $newData = get_option( $option );
   sort($newData); 
 
   echo json_encode($newData); 
   
   die();
}

  add_action("wp_ajax_miglaA_add_options", "miglaA_add_options");
  add_action("wp_ajax_nopriv_miglaA_add_options", "miglaA_add_options");

function miglaA_add_options() {  

   $key = $_POST['key'];
   $value = $_POST['value'];   
   $option = $_POST['option_name'];
   
   $op = get_option( $option );
                       
      $op[$key] = $value;
      update_option( $option , $op);   
   
      
   $newData = get_option( $option );
   sort($newData); 
   
   echo json_encode($newData);   
   
   die();
}

/***********************************************/
/*            FORM OPTIONS  FINISH Nov 21st */
/***********************************************/
  add_action("wp_ajax_miglaA_get_option", "miglaA_get_option");
  add_action("wp_ajax_nopriv_miglaA_get_option", "miglaA_get_option");

function miglaA_get_option() {
  echo json_encode( get_option( $_POST['option'] ) );
  die();
}

  add_action("wp_ajax_miglaA_get_currencies", "miglaA_get_currencies");
  add_action("wp_ajax_nopriv_miglaA_get_currencies", "miglaA_get_currencies");

function miglaA_get_currencies() {
  $op =  get_option( 'migla_currencies' );
  echo json_encode( $op );
  die();
}

  add_action("wp_ajax_miglaA_updateUndesignated", "miglaA_updateUndesignated");
  add_action("wp_ajax_nopriv_miglaA_updateUndesignated", "miglaA_updateUndesignated");

function miglaA_updateUndesignated(){
  update_option( 'migla_undesignLabel' , $_POST['new'] );
  updateACampaign($_POST['old'], $_POST['new']);
  die();
}

function mg_updateARecord($old, $new){
	 global $wpdb;
	 $sql = "UPDATE {$wpdb->prefix}postmeta SET meta_key = 'miglac_".$new."' WHERE meta_key ='miglac_".$old."'";
	 $wpdb->query($sql);
}

  add_action("wp_ajax_miglaA_update_form", "miglaA_update_form");
  add_action("wp_ajax_nopriv_miglaA_update_form", "miglaA_update_form");

function miglaA_update_form() {
   if( $_POST['values'] != '' ){
	 $d = serialize($_POST['values']);
    }
    update_option('migla_form_fields', $_POST['values']);
    
	if( isset($_POST['changes']) ){
		$data = (array)$_POST['changes'];
		if( count($data) > 0 && $data[0] != '')
		{
			  foreach( (array)$data as $d ){
				   mg_updateARecord($d[0], $d[1]);
			  }
		}
	}   
  
    die();
}

  add_action("wp_ajax_miglaA_reset_form", "miglaA_reset_form");
  add_action("wp_ajax_nopriv_miglaA_reset_form", "miglaA_reset_form");

function miglaA_reset_form() {
global $wpdb;

/////////FORM////////////////////////
$fields = array (
    '0' => array (
        'title' => 'Donation Information',
        'child' =>  array(
                   '0' => array( 'type'=>'radio','id'=>'amount', 'label'=>'How much would you like to donate?', 'status'=>'3', 'code' => 'miglad_', 
                       'uid' => ("f".date("Ymdhis"). "_" . rand()) ),
                   '1' => array( 'type'=>'select','id'=>'campaign', 'label'=>'Would you like to donate this to a specific campaign?', 'status'=>'3', 'code' => 'miglad_', 
                       'uid' => ("f".date("Ymdhis"). "_" . rand()) ),
                   '2' => array( 'type'=>'radio','id'=>'repeating', 'label'=>'Is this a recurring donation?', 'status'=>'1', 'code' => 'miglad_', 
                       'uid' => ("f".date("Ymdhis"). "_" . rand()) )
                 ),
        'parent_id' => 'NULL',
        'depth' => 2,
        'toggle' => '-1'
    ),
    '1' => array (
        'title' => 'Donor Information',
        'child' => array(
                   '0' => array( 'type'=>'text','id'=>'firstname', 'label'=>'First Name', 'status'=>'3', 'code' => 'miglad_', 'uid' => ("f".date("Ymdhis"). "_" . rand()) ),
                   '1' => array( 'type'=>'text','id'=>'lastname', 'label'=>'Last Name', 'status'=>'3', 'code' => 'miglad_', 'uid' => ("f".date("Ymdhis"). "_" . rand()) ),
                   '2' => array( 'type'=>'text','id'=>'address', 'label'=>'Address', 'status'=>'1' , 'code' => 'miglad_', 'uid' => ("f".date("Ymdhis"). "_" . rand()) ),
                   '3' => array( 'type'=>'select','id'=>'country', 'label'=>'Country', 'status'=>'1' , 'code' => 'miglad_', 'uid' => ("f".date("Ymdhis"). "_" . rand()) ),
                   '4' => array( 'type'=>'text','id'=>'city', 'label'=>'City', 'status'=>'1' , 'code' => 'miglad_', 'uid' => ("f".date("Ymdhis"). "_" . rand()) ),
                   '5' => array( 'type'=>'text','id'=>'postalcode', 'label'=>'Postal Code', 'status'=>'1' , 'code' => 'miglad_', 'uid' => ("f".date("Ymdhis"). "_" . rand()) ),
                   '6' => array( 'type'=>'checkbox','id'=>'anonymous', 'label'=>'Anonymous?', 'status'=>'1' , 'code' => 'miglad_', 'uid' => ("f".date("Ymdhis"). "_" . rand()) ),
                   '7' => array( 'type'=>'text','id'=>'email', 'label'=>'Email', 'status'=>'3' , 'code' => 'miglad_' , 'uid' => ("f".date("Ymdhis"). "_" . rand()) )
                 ),
        'parent_id' => 'NULL',
        'depth' => 8,
        'toggle' => '-1'
    ),
    '2' => array (
        'title' => 'Is this in honor of someone?',
        'child' => array(
                   '0' => array( 'type'=>'checkbox','id'=>'memorialgift', 'label'=>"Is this a Memorial Gift?", 'status'=>'1', 'code' => 'miglad_', 
                        'uid' => ("f".date("Ymdhis"). "_" . rand()) ),
                   '1' => array( 'type'=>'text','id'=>'honoreename', 'label'=>"Honoree[q]s Name", 'status'=>'1', 'code' => 'miglad_', 
                        'uid' => ("f".date("Ymdhis"). "_" . rand()) ),
                   '2' => array( 'type'=>'text','id'=>'honoreeemail', 'label'=>"Honoree[q]s Email", 'status'=>'1', 'code' => 'miglad_', 
                        'uid' => ("f".date("Ymdhis"). "_" . rand()) ),
                   '3' => array( 'type'=>'textarea','id'=>'honoreeletter', 'label'=>"Write a custom note to the Honoree here", 'status'=>'1', 'code' => 'miglad_', 
                        'uid' => ("f".date("Ymdhis"). "_" . rand()) ),
                   '4' => array( 'type'=>'text','id'=>'honoreeaddress', 'label'=>"Honoree[q]s Address", 'status'=>'1', 'code' => 'miglad_', 
                        'uid' => ("f".date("Ymdhis"). "_" . rand()) ),
                   '5' => array( 'type'=>'text','id'=>'honoreecountry', 'label'=>"Honoree[q]s Country", 'status'=>'1', 'code' => 'miglad_', 
                        'uid' => ("f".date("Ymdhis"). "_" . rand()) ),
                   '6' => array( 'type'=>'text','id'=>'honoreecity', 'label'=>'Honoree[q]s City', 'status'=>'1' , 'code' => 'miglad_', 
                         'uid' => ("f".date("Ymdhis"). "_" . rand()) ),
                   '7' => array( 'type'=>'text','id'=>'honoreepostalcode', 'label'=>'Honoree[q]s Postal Code', 'status'=>'1' , 'code' => 'miglad_', 
                         'uid' => ("f".date("Ymdhis"). "_" . rand()) )		   
                 ),
        'parent_id' => 'NULL',
        'depth' => 5,
        'toggle' => '1'

    ),
    '3' => array (
        'title' => 'Is this a matching gift?',
        'child' => array(
                   '0' => array( 'type'=>'text','id'=>'employer', 'label'=>'Employer[q]s Name', 'status'=>'1', 'code' => 'miglad_', 
                       'uid' => ("f".date("Ymdhis"). "_" . rand()) ),
                   '1' => array( 'type'=>'text','id'=>'occupation', 'label'=>'Occupation', 'status'=>'1', 'code' => 'miglad_', 
                       'uid' => ("f".date("Ymdhis"). "_" . rand()) )
                 ),
        'parent_id' => 'NULL',
        'depth' => 3,
        'toggle' => '1'
    )        
 );

  update_option('migla_form_fields', $fields);

   global $wpdb;
    $pid = $wpdb->get_var( $wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_type = %s ORDER BY ID ASC" , 'migla_custom_values') );
    if( $pid != '' )
    {
       $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}postmeta WHERE post_id = %d" , $pid  ));
       $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}posts WHERE ID = %d" , $pid  ));
    }else{

    }


  die();
}

/***********************************************/
/*             CAMPAIGN   FINISH Nov 21st */

function updateACampaign($old, $new){
	 global $wpdb;
	 $sql = "UPDATE {$wpdb->prefix}postmeta SET meta_value = '".$new."' WHERE meta_value ='".$old."'";
	 $wpdb->query($sql);
}

  add_action("wp_ajax_miglaA_save_campaign", "miglaA_save_campaign");
  add_action("wp_ajax_nopriv_miglaA_save_campaign", "miglaA_save_campaign");

function miglaA_save_campaign() {
	global $wpdb;

	$d = '';

	if( isset($_POST['values']) && $_POST['values'] != '' ){
		$d = serialize($_POST['values']);
	}

	if( isset($_POST['update']) ){
		$up = (array)$_POST['update'];
		if( count($up) > 0 && $up[0] != '')
		{
			  foreach( $up as $u ){
				   $change = array();
				   $change = explode( "-**-", $u);
				   updateACampaign($change[0], $change[1]);
			  }
		}
	}

  update_option('migla_campaign', $_POST['values']);
  die();
}



/***********************************************/
/*           OFFLINE  FINISH BEFORE Nov 21st */
/***********************************************/
  add_action("wp_ajax_miglaA_insert_offline_donation", "miglaA_insert_offline_donation");
  add_action("wp_ajax_nopriv_miglaA_insert_offline_donation", "miglaA_insert_offline_donation");

function miglaA_insert_offline_donation()
{	
	// Repack the POST
 
     $post_id = migla_create_offpost();
  
         add_post_meta( $post_id, 'miglad_amount' , $_POST['mamount'] );
         add_post_meta( $post_id, 'miglad_campaign' , $_POST['mcampaign'] );
         add_post_meta( $post_id, 'miglad_firstname' ,$_POST['mfirstname'] );
         add_post_meta( $post_id, 'miglad_lastname' , $_POST['mlastname'] );
         add_post_meta( $post_id, 'miglad_email' , $_POST['memail'] );
         add_post_meta( $post_id, 'miglad_address' , $_POST['maddress'] );
         add_post_meta( $post_id, 'miglad_state' , $_POST['mstate'] );
         add_post_meta( $post_id, 'miglad_province' , $_POST['mprovince'] );
         add_post_meta( $post_id, 'miglad_country' , $_POST['mcountry'] );
         add_post_meta( $post_id, 'miglad_anonymous' , $_POST['manonymous'] );
         add_post_meta( $post_id, 'miglad_date' , $_POST['mdate'] );
         add_post_meta( $post_id, 'miglad_zip' , $_POST['mzip'] );
         add_post_meta( $post_id, 'miglad_orgname' , $_POST['morgname'] );
         add_post_meta( $post_id, 'miglad_transactionType' , $_POST['mtransactionType'] );
         add_post_meta( $post_id, 'miglad_employer' , $_POST['memployer'] );
         add_post_meta( $post_id, 'miglad_occupation' , $_POST['moccupation'] );

        echo $post_id;

	die();
}

  add_action("wp_ajax_miglaA_getOffDonation", "miglaA_getOffDonation");
  add_action("wp_ajax_nopriv_miglaA_getOffDonation", "miglaA_getOffDonation");

function miglaA_getOffDonation()
{
   $out = migla_get_offline();
   echo json_encode($out);
   die();
}

  add_action("wp_ajax_miglaA_remove_donation", "miglaA_remove_donation");
  add_action("wp_ajax_nopriv_miglaA_remove_donation", "miglaA_remove_donation");

function miglaA_remove_donation() {
  migla_remove_donation( $_POST['list'] ) ; 
   die();
}


/***********************************************/
/*    Progress BAR draw on Form Nov 21st still continue */
/***********************************************/
  add_action("wp_ajax_miglaA_draw_progress_bar", "miglaA_draw_progress_bar");
  add_action("wp_ajax_nopriv_miglaA_draw_progress_bar", "miglaA_draw_progress_bar");

function miglaA_draw_progress_bar() {

 /* migla_text_progressbar(  $cname, $posttype , $linkbtn, $btntext, $text ) */
  $out = "";
  if( $_POST['cname'] == "undesignated" ){
  }else{
   $out .= migla_text_progressbar( $_POST['cname'], $_POST['posttype'], "no", "no", "yes"  );
  }

  echo $out;
  die();
}

  add_action("wp_ajax_miglaA_currentTime", "miglaA_currentTime");
  add_action("wp_ajax_nopriv_miglaA_currentTime", "miglaA_currentTime");

function miglaA_currentTime()
{
       ///GET CURRENT TIME SETTINGS----------------------------------
	$php_time_zone = date_default_timezone_get();
        $t = ""; $d = "";
        $default = $_POST['timezone'];
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
		
        $now =  date("F jS, Y", strtotime($d))." ".$t;
		date_default_timezone_set( $php_time_zone );
       ///---------------------------------GET CURRENT TIME SETTINGS
 
    echo $now;
    die();
}

/****************************************************************/
/*           DATA RETRIEVING FOR REPORT  FINISH Nov 23st        */
/***************************************************************/
  add_action("wp_ajax_migla_getRemovedFields", "migla_getRemovedFields");
  add_action("wp_ajax_nopriv_migla_getRemovedFields", "migla_getRemovedFields");

function migla_getRemovedFields(){
  global $wpdb;
  $data = array(); $output = array(); $idx = 0;
  $data = $wpdb->get_results( "SELECT DISTINCT meta_key FROM {$wpdb->prefix}postmeta WHERE meta_key like 'miglac_%' AND meta_value != ''" );

  foreach( $data as $id )
  {   
      $output[$idx] = $id->meta_key; 
      $idx++; 
  }

  $idx = 0;
  $formfield = (array)get_option('migla_form_fields');
  foreach( (array)$formfield as $field )
  {
     if( count($field['child']) > 0  ){
       foreach ( (array) $field['child'] as $c )
       { 
         $codeid = $c['code'] .$c['id'];
         $key = array_search( $codeid , $output, true);  
         if( $key != false ){ unset( $output[$key] ); };
       }
     }
  }
  $key = array_search( 'miglad_timezone' , $output, true);  unset( $output[$key] );
  $key = array_search( 'miglad_firstname' , $output, true);  unset( $output[$key] );
  $key = array_search( 'miglad_paymentdata' , $output, true);  unset( $output[$key] );
  $key = array_search( 'miglad_time' , $output, true);  unset( $output[$key] );
  $key = array_search( 'miglad_date' , $output, true);  unset( $output[$key] );
  $key = array_search( 'miglad_province' , $output, true);   unset( $output[$key] ); 
  $key = array_search( 'miglad_state' , $output, true);   unset( $output[$key] );  
  $key = array_search( 'miglad_honoreeprovince' , $output, true);   unset( $output[$key] ); 
  $key = array_search( 'miglad_honoreestate' , $output, true);   unset( $output[$key] );  
  $key = array_search( 'miglad_session_id_' , $output, true);   unset( $output[$key] );   
  $key = array_search( 'miglad_session_id' , $output, true);   unset( $output[$key] );  
  $key = array_search( 'miglad_paymentmethod' , $output, true);   unset( $output[$key] );  
  $key = array_search( 'miglad_transactionType' , $output, true);   unset( $output[$key] ); 
  $key = array_search( 'miglad_transactionId' , $output, true);   unset( $output[$key] ); 

  return $output;
}

  add_action("wp_ajax_miglaA_report", "miglaA_report");
  add_action("wp_ajax_nopriv_miglaA_report", "miglaA_report");

function miglaA_report() 
{

  $IDs = array();
  $IDs = migla_get_ids_all() ; 
  $fieldType = array(); 
  $campaigns = array();
  $formfield = (array)get_option('migla_form_fields');

  $output = array();  $out = array(); $recurring = array();
  global $wpdb;

  if( count($IDs) > 0)
  {   
   $row = 0;

   $removed = (array)migla_getRemovedFields();      $orphan = array();

   foreach( (array)$IDs as $id )
   { 

      //Execute through form
      foreach( (array)$formfield as $field ){
        if( count($field['child']) > 0  )
        {
          $children = (array)$field['child']; $j = 0;
          foreach ( $children as $child )
          {  
             if( $children[$j]['status'] != '0' ){
                $meta = get_post_meta( intval( $id->ID ) , ($children[$j]['code'].$children[$j]['id']), true);
                $child_id = $children[$j]['id'];
                /*
                if( $children[$j]['code'] == 'miglac_' ){
                      $child_id = $children[$j]['label'];
                }
                */
                if( $meta != null )
                {
                   $output[$row][ ($children[$j]['code'].$child_id) ] = $meta;
                   $output[$row][ ('uid:'.$children[$j]['code'].$child_id) ] = $children[$j]['uid'];
                   $fieldType[$row][ ($children[$j]['code'].$child_id) ] = $children[$j]['type'];
                }else{
                   $output[$row][ ($children[$j]['code'].$child_id) ] = '';
                   $output[$row][ ('uid:'.$children[$j]['code'].$child_id) ] = $children[$j]['uid'];
                   $fieldType[$row][ ($children[$j]['code'].$child_id) ] = $children[$j]['type'];                
                }
             }
               $j++;
          }
        }
      } 

    //Lets get Payment Data
    $paymentdata = (array)get_post_meta( intval( $id->ID ) , 'miglad_paymentdata', true);
 
      //Checking missing session id
      $sessionid = get_post_meta( intval( $id->ID ) ,'miglad_session_id' , true);
      $amount = get_post_meta( intval( $id->ID ) ,'miglad_amount' , true); 

      //Paypal Data
      //$output[$row][ 'paypaldata' ] = $paymentdata;
      //$fieldType[$row][ 'paypaldata' ] = 'text';
    
      $c = get_post_meta( intval( $id->ID ) , 'miglad_campaign', true);
      $output[$row][ 'miglad_campaign' ] = str_replace( "[q]", "'" ,$c);
      $fieldType[$row][ 'miglad_campaign' ] = 'select';

      $output[$row]['miglad_state'] = get_post_meta( intval( $id->ID ) , 'miglad_state', true); 
      $fieldType[$row][ 'miglad_state' ] = 'text';
      $output[$row]['miglad_province'] = get_post_meta( intval( $id->ID ) , 'miglad_province', true); 
      $fieldType[$row][ 'miglad_province' ] = 'text';

      $output[$row]['miglad_honoreestate'] = get_post_meta( intval( $id->ID ) , 'miglad_honoreestate', true); $fieldType[$row]['miglad_honoreestate'] = 'text';
      $output[$row]['miglad_honoreeprovince'] = get_post_meta( intval( $id->ID ) , 'miglad_honoreeprovince', true); $fieldType[$row]['miglad_honoreeprovince'] = 'text';

      $output[$row][ 'miglad_date' ] = get_post_meta( intval( $id->ID ) , 'miglad_date', true); $fieldType[$row]['miglad_date'] = 'text';
      $output[$row][ 'miglad_time' ] = get_post_meta( intval( $id->ID ) , 'miglad_time', true); $fieldType[$row]['miglad_time'] = 'text';
      $output[$row][ 'miglad_timezone' ] = get_post_meta( intval( $id->ID ) , 'miglad_timezone', true); $fieldType[$row]['miglad_timezone'] = 'text';

      $output[$row]['id'] = $id->ID  ; $fieldType[$row]['id'] = 'text';
      $output[$row]['remove'] = "<input type='hidden' name='".$id->ID."' class='removeRow' /><i class='fa fa-trash'></i>"; $fieldType[$row]['remove'] = 'text';
      $output[$row]['detail'] = "<input class='mglrec' type=hidden name='".$row."' >"; $fieldType[$row]['detail'] = 'text';

   //////PAYMENT DATA
   $output[$row]['miglad_charge_dispute'] = get_post_meta( intval( $id->ID ) , 'miglad_charge_dispute', true); $fieldType[$row]['miglad_charge_dispute'] = 'text';
   $output[$row]['miglad_session_id'] = $sessionid;  $fieldType[$row]['miglad_session_id'] = 'text';
   $output[$row]['miglad_paymentmethod'] = get_post_meta( intval( $id->ID ) ,'miglad_paymentmethod' , true); $fieldType[$row]['miglad_paymentmethod'] = 'text';
   $output[$row]['miglad_transactionType'] = get_post_meta( intval( $id->ID ) ,'miglad_transactionType' , true); $fieldType[$row]['miglad_transactionType'] = 'text';
   $output[$row]['miglad_transactionId'] = get_post_meta( intval( $id->ID ) ,'miglad_transactionId' , true); $fieldType[$row]['miglad_transactionId'] = 'text';
   $output[$row]['miglad_subscription_id'] = get_post_meta( intval( $id->ID ) ,'miglad_subscription_id' , true); $fieldType[$row]['miglad_transactionId'] = 'text';

   //add recurring table
   if( $output[$row]['miglad_transactionType'] == 'subscr_payment' || $output[$row]['miglad_transactionType'] == 'Recurring (Paypal)' )
   {
       $subscr_id = "";
       $paymentdata = (array)get_post_meta( intval( $id->ID ) , 'miglad_paymentdata', true);
       $subscr_id = $paymentdata['subscr_id'];

       $output[$row]['miglad_subscription_id'] = $subscr_id ; 
       $fieldType[$row]['miglad_subscription_id'] = 'text';

       $length = sizeof( $recurring[ $subscr_id ] ); 
       $new_input = array(
                       'date' => $output[$row][ 'miglad_date' ], 
                       'time' => $output[$row][ 'miglad_time' ]  
                     );
       $recurring[ $subscr_id ][ $length ] = $new_input;

    }else if( $output[$row]['miglad_transactionType'] == 'Recurring (Stripe)'  )
    {
       $subscr_id = "";
       $subscr_id = $output[$row]['miglad_subscription_id'];
   
       $output[$row]['miglad_subscription_id'] = $subscr_id ; 
       $fieldType[$row]['miglad_subscription_id'] = 'text';

       $length = sizeof( $recurring[ $subscr_id ] ); 
       $new_input = array(
                       'date' => $output[$row][ 'miglad_date' ], 
                       'time' => $output[$row][ 'miglad_time' ]  
                     );
       $recurring[ $subscr_id ][ $length ] = $new_input;       
    }

     //removed fields
     foreach( (array)$removed as $r)
     {
      $orphan[$row][$r]    = ""; 
     }

     foreach( (array)$removed as $r)
     {
      $orphan[$row][$r] = get_post_meta( intval( $id->ID ) , $r , true ); 
      $fieldType[$row][$r] = 'text';
     }

      $row = $row + 1;
  
    }
  }


  $out[0] = $output;
  $out[1] = $formfield;
  $out[2] = $orphan;
  $out[3] = $fieldType;
  $campaigns = get_option('migla_campaign'); $out[4] = $campaigns;
  $out[5] = get_option('migla_undesignLabel');
  $out[6] = $recurring;

  echo json_encode($out);
  wp_cache_flush();

  die();
}

function migla_get_all_custom_values_for_report(){  
  global $wpdb;
  $data = array(); //$post_id = migla_get_select_values_postid();
  $data =  $wpdb->get_results( $wpdb->prepare( "SELECT DISTINCT * FROM {$wpdb->prefix}postmeta WHERE meta_key like %s" , 'mgval_%' ));
  $result = array();
  foreach( (array)$data as $d ){
      $result[ $d->meta_key ] = (string)$d->meta_value;
  }
  return $result;
}

  add_action("wp_ajax_miglaA_get_data_for_edit_form", "miglaA_get_data_for_edit_form");
  add_action("wp_ajax_nopriv_miglaA_get_data_for_edit_form", "miglaA_get_data_for_edit_form");

function miglaA_get_data_for_edit_form(){
  $out = array();
  $out[0] = get_option('migla_world_countries');
  $out[1] = get_option('migla_US_states');
  $out[2] = get_option('migla_Canada_provinces');
  $out[3] = migla_get_all_custom_values_for_report();

  echo json_encode($out);
  die();
}

  add_action("wp_ajax_miglaA_get_number_and_total", "miglaA_get_number_and_total");
  add_action("wp_ajax_nopriv_miglaA_get_number_and_total", "miglaA_get_number_and_total");

function miglaA_get_number_and_total() {
  $out = array();
  $out = migla_number_and_total( $_POST['campaign'] );
  
  echo json_encode($out);
 
  die();
}

/**********************************************************************/
/************************* TEST AJAX ***********************/
  add_action("wp_ajax_miglaA_test_email", "miglaA_test_email");
  add_action("wp_ajax_nopriv_miglaA_test_email", "miglaA_test_email");

function miglaA_test_email(){
  $test = test_email( $_POST['email'], $_POST['emailname'], $_POST['testemail']);
  if( $test ){ echo "Email has been sent to ".$_POST['testemail']; } else { echo "Sending email failed"; }
  die();
}

  add_action("wp_ajax_miglaA_test_hEmail", "miglaA_test_hEmail");
  add_action("wp_ajax_nopriv_miglaA_test_hEmail", "miglaA_test_hEmail");

function miglaA_test_hEmail(){
  $test = migla_test_hletter( $_POST['email'], $_POST['emailname'], $_POST['testemail']);
  if( $test ){ echo "Email has been sent to ".$_POST['testemail']; } else { echo "Sending email failed"; }
  die();
}

/**********************************************************************/
/*        UPDATING & RESTORING TASKS                                  */   
/*********************************************************************/
  add_action("wp_ajax_miglaA_change_donation", "miglaA_change_donation");
  add_action("wp_ajax_nopriv_miglaA_change_donation", "miglaA_change_donation");

function miglaA_change_donation()
{
  $post_id    = $_POST['post_id'];
  $arrayData = (array)$_POST['arrayData'];
  
  $keys = array_keys( $arrayData ); $i = 0;
  foreach( (array)$arrayData as $value)
  {
       update_post_meta( $post_id , $value[0], $value[1] );
     $i++;
  }

   echo "done";
   die();
}

  add_action("wp_ajax_miglaA_restore_donation1", "miglaA_restore_donation1");
  add_action("wp_ajax_nopriv_miglaA_restore_donation1", "miglaA_restore_donation1");

function miglaA_restore_donation1(){
   $session_id = $_POST['session_id'];
   $post_id    = $_POST['post_id'];
   $transientdata = get_transient( $session_id );
   $msg = ""; $i = 0;

      $keys = array_keys( $transientdata); $i = 0;
      foreach( (array)$transientdata as $value)
      {
         if( $keys[$i]=='miglad_session_id' || $keys[$i]=='miglad_paymentmethod' ||  $keys[$i]=='miglad_transactionType' || $keys[$i]=='miglad_date'
          || $keys[$i]=='miglad_time' || $keys[$i]=='miglad_paymentdata' ){
         }else{
           update_post_meta( $post_id, $keys[$i], $value );
         }
         $i++;
      }
      $msg = "Recover from cache data";  

   echo $msg;
die();
}

  //recover from paypal
  add_action("wp_ajax_miglaA_restore_donation2", "miglaA_restore_donation2");
  add_action("wp_ajax_nopriv_miglaA_restore_donation2", "miglaA_restore_donation2");

function miglaA_restore_donation2(){
   $session_id = $_POST['session_id'];
   $post_id    = $_POST['post_id'];

   $msg = ""; $i = 0;


      $paypaldata = (array)get_post_meta( $post_id, 'miglad_paymentdata', true);


                     	   update_post_meta( $post_id, 'miglad_session_id', $paypaldata['custom'] );
                           update_post_meta( $post_id, 'miglad_firstname', $paypaldata['first_name'] );
                           update_post_meta( $post_id, 'miglad_lastname', $paypaldata['last_name'] );

                           $amountfrompaypal = $paypaldata['payment_gross'] ;
                           if( $amountfrompaypal == '' ){ 
                              $amountfrompaypal = $paypaldata['mc_gross']; 
                           }
                           update_post_meta( $post_id, 'miglad_amount', $amountfrompaypal );

                           update_post_meta( $post_id, 'miglad_phone', $paypaldata['contact_phone'] );
                           update_post_meta( $post_id, 'miglad_country', $paypaldata['address_country'] );
                           update_post_meta( $post_id, 'miglad_address', $paypaldata['address_street'] );
                           update_post_meta( $post_id, 'miglad_email', $paypaldata['payer_email'] );
                           update_post_meta( $post_id, 'miglad_city', $paypaldata['address_city'] ); 
                           update_post_meta( $post_id, 'miglad_state', $paypaldata['address_state'] ); 

                   update_post_meta( $post_id, 'miglad_paymentmethod', $paypaldata['payment_type'] );
                   update_post_meta( $post_id, 'miglad_transactionId', $paypaldata['txn_id'] );
                   update_post_meta( $post_id, 'miglad_transactionType', $paypaldata['txn_type'] );      

      $msg = "Recover from paypal data";
  echo $msg;
die();

}

  //recover from initial
  add_action("wp_ajax_miglaA_restore_donation3", "miglaA_restore_donation3");
  add_action("wp_ajax_nopriv_miglaA_restore_donation3", "miglaA_restore_donation3");

function miglaA_restore_donation3(){
   $session_id = $_POST['session_id'];
   $post_id    = $_POST['post_id'];
   $msg = ""; $i = 0;

  $old_id = migla_cek_repeating_id( $session_id  );

  migla_restore_from_old_donation( $old_id, $post_id ); 
  
  $msg = "This donation is reoccuring. Recover by initial donation";

  echo $msg;
die();
}

  add_action("wp_ajax_miglaA_restore_donation", "miglaA_restore_donation");
  add_action("wp_ajax_nopriv_miglaA_restore_donation", "miglaA_restore_donation");

function miglaA_restore_donation()
{
   $session_id = $_POST['session_id'];
   $post_id    = $_POST['post_id'];
   $transientdata = get_transient( $session_id );
   $msg = ""; $i = 0;

   $paypal = (array)get_post_meta( $post_id, 'miglad_paymentdata', true);

   if( $transientdata == false ){  
    $old_id = migla_cek_repeating_id( $session_id  );

    if( $paypal['subscr_id'] != '' && $old_id != -1){

       migla_restore_from_old_donation( $old_id, $post_id ); $msg = "This donation is reoccuring. Recover by initial donation";

    }else{

      $paypaldata = (array)get_post_meta( $post_id, 'miglad_paymentdata', true);


                     	   update_post_meta( $post_id, 'miglad_session_id', $paypaldata['custom'] );
                           update_post_meta( $post_id, 'miglad_firstname', $paypaldata['first_name'] );
                           update_post_meta( $post_id, 'miglad_lastname', $paypaldata['last_name'] );

                           $amountfrompaypal = $paypaldata['payment_gross'] ;
                           if( $amountfrompaypal == '' ){ 
                              $amountfrompaypal = $paypaldata['mc_gross']; 
                           }
                           update_post_meta( $post_id, 'miglad_amount', $amountfrompaypal );

                           update_post_meta( $post_id, 'miglad_phone', $paypaldata['contact_phone'] );
                           update_post_meta( $post_id, 'miglad_country', $paypaldata['address_country'] );
                           update_post_meta( $post_id, 'miglad_address', $paypaldata['address_street'] );
                           update_post_meta( $post_id, 'miglad_email', $paypaldata['payer_email'] );
                           update_post_meta( $post_id, 'miglad_city', $paypaldata['address_city'] ); 
                           update_post_meta( $post_id, 'miglad_state', $paypaldata['address_state'] ); 

                   update_post_meta( $post_id, 'miglad_paymentmethod', $paypaldata['payment_type'] );
                   update_post_meta( $post_id, 'miglad_transactionId', $paypaldata['txn_id'] );
                   update_post_meta( $post_id, 'miglad_transactionType', $paypaldata['txn_type'] );      

      $msg = "Recover from paypal data";
      
    }
  }else{
      $keys = array_keys( $transientdata); $i = 0;
      foreach( (array)$transientdata as $value)
      {
         if( $keys[$i]=='miglad_session_id' || $keys[$i]=='miglad_paymentmethod' ||  $keys[$i]=='miglad_transactionType' || $keys[$i]=='miglad_date'
          || $keys[$i]=='miglad_time' || $keys[$i]=='miglad_paymentdata' ){
         }else{
           update_post_meta( $post_id, $keys[$i], $value );
         }
         $i++;
      }
      $msg = "Recover from cache data";  
   }

   echo $msg;
   die();
}

?>