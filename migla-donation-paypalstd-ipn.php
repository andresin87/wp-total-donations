<?php
session_start();

include "../../../wp-config.php";

include_once "./migla-functions.php";



/************* CALLING HOOK ***************************/
$isHooked = get_option( 'miglaactions_2_1' );
if( $isHooked == 'yes' ){
   $url = get_option( 'migla_ipnrequire' );
   if( $url == '' ){ }else{ include( dirname(__FILE__). $url);}
}
/*******************************************************/


class migla_IPN_Handler {

    var $chat_back_url  = "tls://www.paypal.com";
	var $host_header    = "Host: www.paypal.com\r\n";
	var $session_id     = '';

	public function __construct() {

		// Set up for production or test
		if ( "sandbox" == get_option( 'migla_payment' ) ) {
			$this->chat_back_url = "tls://www.sandbox.paypal.com";
			$this->host_header   = "Host: www.sandbox.paypal.com\r\n";
		}

		$this->session_id = isset( $_POST[ 'custom' ] ) ? $_POST[ 'custom' ] : '';

		if ( ! empty( $this->session_id ) ) {
			$response = $this->migla_to_paypal();

			if ( "VERIFIED" == $response ) {
				$this->handle_verified_ipn();
			} else if ( "INVALID" == $response ) {
				$this->handle_invalid_ipn();
			} else {
				$this->handle_unrecognized_ipn( $response );
			}
		} else {
		}
	}

	function migla_to_paypal() {
		$req = 'cmd=_notify-validate';
		$get_magic_quotes_exists = function_exists( 'get_magic_quotes_gpc' );

		foreach ($_POST as $key => $value) {
			if( $get_magic_quotes_exists && get_magic_quotes_gpc() == 1 ) {
				$value = urlencode( stripslashes( $value ) );
			} else {
				$value = urlencode( $value );
			}
			$req .= "&$key=$value";
		}

		$header = "POST /cgi-bin/webscr HTTP/1.0\r\n";
		$header .= $this->host_header;
		$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$header .= "Content-Length: " . strlen( $req ) . "\r\n\r\n";

		$response = '';

		$fp = fsockopen( $this->chat_back_url, 443, $errno, $errstr, 30 );
		if ( $fp ) {
			fputs( $fp, $header . $req );

			$done = false;
			do {
				if ( feof( $fp ) ) {
					$done = true;
				} else {
					$response = fgets( $fp, 1024 );
					$done = in_array( $response, array( "VERIFIED", "INVALID" ) );
				}
			} while ( ! $done );
		} else {
		}
		fclose ($fp);

		return $response;
	}

	function handle_verified_ipn() {
	   $payment_status = $_POST['payment_status'];
       
	   if ( "Completed" == $payment_status || "completed" == $payment_status ) {
	    $id = $_POST[ "custom" ];

            $post_id = migla_create_post();

            $transientKey = "t_". $_POST[ 'custom' ];

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

	          $postData = get_option( $transientKey );

                   if( $postData == false )
		    {
                         $new_id = $post_id;

                         //1 Cek if this donation session id exist
                         $old_ids =  migla_cek_repeating_id( $_POST[ 'custom' ] );

                         //IF the session is not repeating then save from paypal
                         if( $old_ids == -1 ) 
                         {
                           // migla_donation_from_paypal($post_id, $post);
                     	   add_post_meta( $post_id, 'miglad_session_id', $_POST['custom'] );
                           add_post_meta( $post_id, 'miglad_firstname', $_POST['first_name'] );
                           add_post_meta( $post_id, 'miglad_lastname', $_POST['last_name'] );

                           $amountfrompaypal = $_POST['payment_gross'] ;
                           if( $amountfrompaypal == '' ){ 
                              $amountfrompaypal = $_POST['mc_gross']; 
                           }
                           add_post_meta( $post_id, 'miglad_amount', $amountfrompaypal );

                           add_post_meta( $post_id, 'miglad_phone', $_POST['contact_phone'] );
                           add_post_meta( $post_id, 'miglad_country', $_POST['address_country'] );
                           add_post_meta( $post_id, 'miglad_address', $_POST['address_street'] );
                           add_post_meta( $post_id, 'miglad_email', $_POST['payer_email'] );
                           add_post_meta( $post_id, 'miglad_city', $_POST['address_city'] ); 
                           add_post_meta( $post_id, 'miglad_state', $_POST['address_state'] ); 

                            //Additional data
                             add_post_meta( $new_id, "miglad_time" , $t );
                             add_post_meta( $new_id, "miglad_date" , $d );

                       sendThankYouEmail( $_POST, 1 , $e, $en );
	                   sendNotifEmail( $_POST, 1, $e, $en, $ne);

                         }else{ //This is probably repeating donation

			             //  if( migla_cek_id_exist( $new_id )==0 )
			             //  {
                              migla_create_from_old_donation( $old_ids, $new_id);

                              //Additional data
                              add_post_meta( $new_id, "miglad_time" , $t );
                              add_post_meta( $new_id, "miglad_date" , $d );
                              
                                if( get_option( 'miglaactions_2_1' ) == 'yes' )
                              {
                                   sendThankYouEmailRepeatingCustom( $new_id, $e, $en ) ;
                                   sendNotifEmailRepeatingCustom( $new_id, $e, $en, $ne) ;
                               }else{
                                   sendThankYouEmailRepeating( $new_id, $e, $en );
	                           sendNotifEmailRepeating( $new_id, $e, $en, $ne);
                               }


                           // }
							
                        }//if repeating

		  }else{ //This definitely new donation and has transient data
                         $i = 0; 

                         $keys = array_keys( $postData );
                         foreach( (array)$postData as $value)
                         {
                              add_post_meta( $post_id, $keys[$i], $value );
                            $i++;
                          }

                    if( get_option( 'miglaactions_2_1' ) == 'yes' )
                    {
                        sendThankYouEmailCustom( $postData, 2 ,  $e, $en );		         
	                sendNotifEmailCustom( $postData, 2, $e, $en, $ne);
                    }else{
                        sendThankYouEmail( $postData, 2 ,  $e, $en );		         
	                sendNotifEmail( $postData, 2, $e, $en, $ne);
                    }

                    update_post_meta( $post_id, 'miglad_time', $t ); 
                    update_post_meta( $post_id, 'miglad_date', $d ); 

                    $tdata =  $transientKey. "hletter";

                    $content =  get_option( $tdata );

                    migla_hletter( $e, $en , $postData['miglad_honoreeemail'], $content, $postData['miglad_repeating']
                                , $postData['miglad_anonymous'], $postData['miglad_firstname'], $postData['miglad_lastname'], 
                                 $postData['miglad_amount'], $postData['miglad_honoreename'] , $d );

		  }//IF get_transient( $transientKey )

                  //Save data from paypal
                   add_post_meta( $post_id, 'miglad_paymentmethod', $_POST['payment_type'] );
                   add_post_meta( $post_id, 'miglad_paymentdata', $_POST );
                   add_post_meta( $post_id, 'miglad_transactionId', $_POST['txn_id'] );
                   add_post_meta( $post_id, 'miglad_transactionType', $_POST['txn_type'] );
                   add_post_meta( $post_id, 'miglad_timezone', $default );
				   
	  }else{ //IF Status is not completed
				   
	  } // If $payment_status
	}//function

	function handle_invalid_ipn() {
	}

	function handle_unrecognized_ipn( $paypal_response ) {
	}


}

$migla_paypal_responder = new migla_IPN_Handler();

echo "content-type: text/plain\n\n";