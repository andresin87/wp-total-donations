<?php


/************************************************************************
* 	Functions that need
*	Author : Astried & Binti
*************************************************************************/

/**** added April 2th ********/
function migla_delete_post_meta1( $meta_key ) {
  global $wpdb;
 
  $wpdb->query( 
	$wpdb->prepare( 
         "DELETE FROM {$wpdb->prefix}postmeta WHERE meta_key like %s" 
          , $meta_key
        )
     ); 
}


function migla_delete_post_meta2( $meta_id ) {
  global $wpdb;
 
  $wpdb->query( 
	$wpdb->prepare( 
         "DELETE FROM {$wpdb->prefix}postmeta WHERE meta_id = %d" 
          , $meta_id
        )
     ); 
}

/***********      Stripe's Function    *********************************/
function migla_getSK(){
   $SK = get_option('migla_liveSK');

   if( get_option('migla_stripemode') == 'test' ){
      $SK = get_option('migla_testSK');
   }

   return $SK;
}

function migla_getPK(){
   $PK = get_option('migla_livePK');

   if( get_option('migla_stripemode') == 'test' ){
      $PK = get_option('migla_testPK');
   }

   return $PK;
}

function migla_get_stripeplan_id() {
    global $wpdb;
    $pid = $wpdb->get_var( $wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_type = %s ORDER BY ID ASC" , 'migla_stripe_plan') );
    if( $pid != '' )
    {
        return $pid;
    }else{
 
      $new_donation = array(
	'post_title' => 'migla_donation',
	'post_content' => '',
	'post_status' => 'publish',
	'post_author' => 1,
	'post_type' => 'migla_stripe_plan'
       );

       $new_id = wp_insert_post( $new_donation );

       return $new_id;
   }
}

function migla_get_select_values_postid() 
{
    global $wpdb;
    $pid = $wpdb->get_var( $wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_type = %s ORDER BY ID ASC" , 'migla_custom_values') );
    if( $pid != '' )
    {
        return $pid;
    }else{
 
      $new_donation = array(
	'post_title' => 'migla_donation',
	'post_content' => '',
	'post_status' => 'publish',
	'post_author' => 1,
	'post_type' => 'migla_custom_values'
       );

       $new_id = wp_insert_post( $new_donation );

       return $new_id;
   }
}

/************************************************************************/


function migla_delete_all_settings(){
  global $wpdb;
 
  $wpdb->query( 
	$wpdb->prepare( 
         "DELETE FROM {$wpdb->prefix}options WHERE option_name like %s" 
          , 'migla%'
        )
     ); 

  $wpdb->query( 
	$wpdb->prepare( 
         "DELETE FROM {$wpdb->prefix}options WHERE option_name like %s" 
          , 't_migla%'
        )
     ); 

    $pid = $wpdb->get_var( $wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_type = %s ORDER BY ID ASC" , 'migla_custom_values') );
    if( $pid != '' )
    {
       $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}postmeta WHERE post_id = %d" , $pid  ));
       $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}posts WHERE ID = %d" , $pid  ));
    }else{

    }

}


function migla_get_succesful_url(){
	$successUrl = migla_get_current_url();

	if (strpos($successUrl, "?") === false)
	{
		$successUrl .= "?";
	}
	else
	{
		$successUrl .= "&";
	}

	$successUrl .= "thanks=thanks";
	$successUrl .= "&id=";

   return $successUrl;
}


function migla_get_notify_url(){
    $notifyUrl = plugins_url('totaldonations/migla-donation-paypalstd-ipn.php', dirname(__FILE__) );

    return $notifyUrl;
}

/***************************************************************************************/
/*     THANK YOU EMAIL, NOTIFICATION EMAIL AND LETTER FOR HONOREE Nov 23rd  2014       */
/***************************************************************************************/
function mg_mail_from( $email )
{
    $pEmail = get_option('migla_ReplyTo'); 
    return $pEmail;
}

function mg_mail_from_name( $name )
{
    return get_option('migla_ReplyToName');
}


function sendThankYouEmail( $postdata, $code , $e, $en )
{
  $subject = "";
  $subject .= get_option( 'migla_thankSbj' );
  $string = "";
  $string .= get_option( 'migla_thankBody' ) ."<br>" ;
  $placeholder = array( '[firstname]','[lastname]' ,'[amount]' ,'[date]', '[newline]' );
  $amount = "";  

    //IF it has Paypal Data only
      if( $code == 1 ){ 
         $amount .= $postdata["mc_gross"];
          $donor_email = $postdata["payer_email"];
         $replace = array( $postdata["first_name"] , $postdata["last_name"] , $amount
                      , date("F jS, Y", strtotime($postdata['payment_date'])), '<br>' );

      }else{
          $donor_email = $postdata['miglad_email']; $amount .= $postdata['miglad_amount'];
          $replace = array( $postdata['miglad_firstname'], 
                     $postdata['miglad_lastname'] ,
                       $amount,
                       date("F jS, Y", strtotime($postdata['miglad_date'])) , '<br>' );
         if( $postdata['miglad_repeating']=='yes' ){   $string .= "<br>".get_option( 'migla_thankRepeat' ) ; }
         if( $postdata['miglad_anonymous']=='yes' ){   $string .= "<br>".get_option( 'migla_thankAnon' )  ; }        
      }

  $string .= "<br>".get_option( 'migla_thankSig' ) ."<br>" ;

  $content =  str_replace($placeholder, $replace, $string);
  $content2 = $content;
  $content =  str_replace("\\","",$content2);
  $content = "<html><body>" .$content ; 
  $content .= "</body><html>";

 $arr = explode("@", $e);

$temp = 'From: webmaster <somebody>';
$fromto = str_replace('webmaster', $en , $temp );  
$temp = $fromto;
$fromto = str_replace('somebody', $arr[0] , $temp );  

$headers[] = $fromto ;

 add_filter( 'wp_mail_content_type', 'mg_set_html_content_type' );
 add_filter( 'wp_mail_from', 'mg_mail_from' );
 add_filter( 'wp_mail_from_name', 'mg_mail_from_name' );

  $status = wp_mail( $donor_email, $subject, $content, $headers);
 
 remove_filter( 'wp_mail_content_type', 'mg_set_html_content_type' );
 remove_filter( 'wp_mail_from', 'mg_mail_from' );
 remove_filter( 'wp_mail_from_name', 'mg_mail_from_name' );

}

function sendThankYouEmailRepeating( $id, $e, $en )
{
  $subject = "";
  $subject .= get_option( 'migla_thankSbj' );
  $string = "";
  $string .= get_option( 'migla_thankBody' ) ."<br>" ;
  $string .= "<br> This is a repeating donation. <br><br>";
$amount =  get_post_meta( $id , 'miglad_amount', true);  

  $placeholder = array( '[firstname]','[lastname]' ,'[amount]' ,'[date]', '[newline]' );

  $donor_email =  get_post_meta( $id , 'miglad_email', true);
  $replace = array( get_post_meta( $id , 'miglad_firstname', true), 
                    get_post_meta( $id , 'miglad_lastname', true) ,
                    $amount,
                    date("F jS, Y", strtotime( get_post_meta( $id ,'miglad_date', true) )) , '<br>' );

  if( get_post_meta( $id, 'miglad_anonymous', true ) =='yes' ){   $string .= "<br>".get_option( 'migla_thankAnon' )  ; }        

  $string .= "<br>".get_option( 'migla_thankSig' ) ."<br>" ;

  $content =  str_replace($placeholder, $replace, $string);
  $content2 = $content;
  $content =  str_replace("\\","",$content2);
  $content = "<html><body>" .$content ; 
  $content .= "</body><html>";

 $arr = explode("@", $e);

$temp = 'From: webmaster <somebody>';
$fromto = str_replace('webmaster', $en , $temp );  
$temp = $fromto;
$fromto = str_replace('somebody', $arr[0] , $temp );  

    $headers[] = $fromto ;

 add_filter( 'wp_mail_content_type', 'mg_set_html_content_type' );
 add_filter( 'wp_mail_from', 'mg_mail_from' );
 add_filter( 'wp_mail_from_name', 'mg_mail_from_name' );

 $status = wp_mail( $donor_email, $subject, $content, $headers);

 remove_filter( 'wp_mail_content_type', 'mg_set_html_content_type' );
 remove_filter( 'wp_mail_from', 'mg_mail_from' );
 remove_filter( 'wp_mail_from_name', 'mg_mail_from_name' );

}

function sendNotifEmail( $postdata, $code, $e, $en, $ne)
{
  $subject = "You have just received a donation";

  $body = "<html><body>";
  $body .= "You have just received a donation. Here is the information about that donation: <br><br>" ;

 $body .= "<table>";

 //From Paypal Data
 if( $code == 1 ){
   $body .= "<tr><td>Name </td><td>: ". $postdata["first_name"] ." ". $postdata["last_name"] . "</td></tr>";
   $body .= "<tr><td>Amount </td><td>: ". $postdata["mc_gross"] . "</td></tr>";
   $body .= "<tr><td>Address </td><td>: ".$postdata["address_street"]. " ".$postdata["address_country"] . "</td></tr>";
   $body .= "<tr><td>Phone </td><td>: " . $postdata["contact_phone"] . "</td></tr>";
   $body .= "<tr><td>Email </td><td>: " .$postdata["payer_email"] . "</td></tr>";
 }else{
   $keys = array_keys( $postdata ); $i = 0;
 
   foreach( (array)$postdata as $value)
   {
     if( !empty($value) )
     {  
        if ( $keys[$i]=='miglad_session_id' || $keys[$i]=='miglad_session_id_' )
        {
        }
        else if (  $keys[$i] == 'miglad_date')
        {
           $body .= "<tr><td>".substr( $keys[$i], 7) ." </td><td>: ".date("F jS, Y", strtotime($value))."</td></tr>";
        }else{
          $body .= "<tr><td>".substr( $keys[$i], 7) ." </td><td>: ". $value ."</td></tr>";  
        }
     }
      $i++;
   }
 }

 $body .= "</table>";

  $body .= "<br><br>";
  $url = get_admin_url();
  $url .= "admin.php?page=migla_reports_page";
  $body .= "<a href=".$url.">Click here for more detailed information about this donation</a>";
  $body .= "<br>";

  $body .= "</body></html>";

//SEND
$arr = explode("@", $e);

$temp = 'From: webmaster <somebody>';
$fromto = str_replace('webmaster', $en , $temp );  
$temp = $fromto;
$fromto = str_replace('somebody', $arr[0] , $temp );  
$headers[] = $fromto ;

// Loop on addresses
	$notifyEmailAr = explode(',', $ne);

 add_filter( 'wp_mail_content_type', 'mg_set_html_content_type' );
 add_filter( 'wp_mail_from', 'mg_mail_from' );
 add_filter( 'wp_mail_from_name', 'mg_mail_from_name' );

foreach ( (array)$notifyEmailAr as $notifyEmail)
{
	$notifyEmail = trim($notifyEmail);
	if (!empty($notifyEmail))
	{
 			$mail_sent = wp_mail( $notifyEmail, $subject, $body, $headers );
			 if ( ! $mail_sent ) { }else{ }
	}
}

 remove_filter( 'wp_mail_content_type', 'mg_set_html_content_type' );
 remove_filter( 'wp_mail_from', 'mg_mail_from' );
 remove_filter( 'wp_mail_from_name', 'mg_mail_from_name' );

} 

function sendNotifEmailRepeating( $id, $e, $en, $ne)
{
  $subject = "You have just received a donation";

  $body = "<html><body>";
  $body .= "You have just received a repeating donation. Here is the information about that donation: <br><br>" ;

$campaign = get_post_meta( $id, 'miglad_campaign', true );
$amount = get_post_meta( $id, 'miglad_amount', true );
$firstname =  get_post_meta( $id, 'miglad_firstname', true ) ;
$lastname = get_post_meta( $id, 'miglad_lastname', true );
$address = get_post_meta( $id, 'miglad_address', true ) ;
$country = get_post_meta( $id, 'miglad_country', true );
$state = get_post_meta( $id, 'miglad_state', true );
$province = get_post_meta( $id, 'miglad_province', true );
$postalcode = get_post_meta( $id, 'miglad_postalcode', true );


 $body .= "<table>"; 
 if( $campaign != '' ){ $body .= "<tr><td>Campaign </td><td>: " . $campaign . "</td></tr>"; }
 if( $amount != '' ){ $body .= "<tr><td>Amount </td><td>: " . $amount . "</td></tr>"; }
 if( $firstname != '' ){ $body .= "<tr><td>First Name </td><td>: " . $firstname . "</td></tr>"; }
 if( $lastname != '' ){ $body .= "<tr><td>Last Name </td><td>: " . $lastname . "</td></tr>"; }
 if( $address != '' ){ $body .= "<tr><td>Address </td><td>: " . $address . "</td></tr>"; }
 if( $country != '' ){ $body .= "<tr><td>Country </td><td>: " . $country . "</td></tr>"; }
 if( $state != '' ){ 
    $body .= "<tr><td>State </td><td>: " . $state . "</td></tr>"; 
 }else if( $province != '' ){ 
    $body .= "<tr><td>Province </td><td>: " . $province . "</td></tr>"; 
 }
 if( $postalcode != '' ){ $body .= "<tr><td>Postal Code </td><td>: " . $postalcode . "</td></tr>"; }
 $body .= "</table>";

  $body .= "<br><br>";
  $url = get_admin_url();
  $url .= "admin.php?page=migla_reports_page";
  $body .= "<a href=".$url.">Click for detail information of this donation</a>";
  $body .= "<br>";

  $body .= "</body></html>";

//SEND
$arr = explode("@", $e);

$temp = 'From: webmaster <somebody>';
$fromto = str_replace('webmaster', $en , $temp );  
$temp = $fromto;
$fromto = str_replace('somebody', $arr[0] , $temp );  
//$fromto = str_replace('somebody', $e , $temp ); 
$headers[] = $fromto ;

// Loop on addresses
	$notifyEmailAr = explode(',', $ne);

 add_filter( 'wp_mail_content_type', 'mg_set_html_content_type' );
 add_filter( 'wp_mail_from', 'mg_mail_from' );
 add_filter( 'wp_mail_from_name', 'mg_mail_from_name' );

foreach ( (array)$notifyEmailAr as $notifyEmail)
{
	$notifyEmail = trim($notifyEmail);
	if (!empty($notifyEmail))
	{
 			$mail_sent = wp_mail( $notifyEmail, $subject, $body, $headers );
			 if ( !$mail_sent ) { }else{ }
	}
}

 remove_filter( 'wp_mail_content_type', 'mg_set_html_content_type' );
 remove_filter( 'wp_mail_from', 'mg_mail_from' );
 remove_filter( 'wp_mail_from_name', 'mg_mail_from_name' );

} 

function migla_hletter( $e, $en , $to, $content, $repeat, $anon, $firstname, $lastname,$amount, $honoreename, $date)
{
  $subject = get_option('migla_honoreESbj');
  
  $string .= get_option( 'migla_honoreEBody' ) ."<br>" ;

  if( $content != '' ){
   $string .= get_option('migla_honoreECustomIntro');
   $string .= "<br>\"" . $content . "\"<br><br>";
  }

  if( $repeat == 'yes' ){
   $string .= "<br>".get_option( 'migla_honoreERepeat' ) ."<br>"  ;
  }
  
  if( $anon == 'yes' ){
   $string .= get_option( 'migla_honoreEAnon' ) ."<br>" ;
  }
  
  $string .= "<br>". get_option( 'migla_honoreESig' ) ."<br>" ;  
	
  $placeholder = array( '[honoreename]' ,'[amount]' ,'[date]', '[newline]', '[firstname]','[lastname]' );
  $replace     = array(  $honoreename    ,$amount ,    date("F jS, Y", strtotime( $date ) ),    '<br>',     $firstname,   $lastname );

  $content =  str_replace($placeholder, $replace, $string);
  $content2 = $content;
  $content =  str_replace("\\","",$content2);
  $content = "<html><body>" .$content ; 
  $content .= "</body><html>";
  
 $arr = explode("@", $e);

$temp = 'From: webmaster <somebody>';
$fromto = str_replace('webmaster', $en , $temp );  

$temp = $fromto;
$fromto = str_replace('somebody', $arr[0] , $temp );  

    $headers[] =  $fromto ;

 add_filter( 'wp_mail_content_type', 'mg_set_html_content_type' );
 add_filter( 'wp_mail_from', 'mg_mail_from' );
 add_filter( 'wp_mail_from_name', 'mg_mail_from_name' );

 $status = wp_mail( $to , $subject, $content, $headers);

 remove_filter( 'wp_mail_content_type', 'mg_set_html_content_type' );
 remove_filter( 'wp_mail_from', 'mg_mail_from' );
 remove_filter( 'wp_mail_from_name', 'mg_mail_from_name' );

return $status;
}



function migla_restore_from_old_donation( $old_id, $new_id){
   global $wpdb;

   //get data from old id
   $sql = "SELECT distinct meta_key,meta_value FROM {$wpdb->prefix}postmeta WHERE post_id = '".$old_id."' ";
   $post = array(); $result = array(); $i = 0;
   $post = $wpdb->get_results($sql);
   
   //insert into new id
   foreach ( $post as $id ){
    $key = (string)$id->meta_key;
    if( $key == "miglad_paymentdata" || $key == "miglad_paymentmethod" || 
         $key == "miglad_time" || $key == "miglad_date" || 
         $key == "miglad_transactionType" || $key == "miglad_transactionId"
     ){
       // add_post_meta( $new_id, $key , $post[$key] );     
     }else{
        update_post_meta( $new_id, (string)$id->meta_key , (string)$id->meta_value );
     }
   }//foreach  

}




/****************************************************************************************/
/************* TESTING EMAILS                      *******************************************/
function test_email( $e, $en , $to)
{
 $arr = explode("@", $e);

  $subject = get_option( 'migla_thankSbj' );

  $string = get_option( 'migla_thankBody' ) ."<br>" ;
  $string .= "<br>".get_option( 'migla_thankRepeat' ) ."<br>"  ;
  $string .= get_option( 'migla_thankAnon' ) ."<br>" ;
  $string .= get_option( 'migla_thankSig' ) ."<br>" ;
  
  $placeholder = array( '[firstname]','[lastname]' ,'[amount]' ,'[date]', '[newline]' );  
  $replace = array( 'John','Doe' ,'100' ,'September 10th, 2015', '<br>');
  
  $content =  str_replace($placeholder, $replace, $string);
  $content2 = $content;
  $content =  str_replace("\\","",$content2);
  $content = "<html><body>" .$content ; 
  $content .= "</body><html>";

$temp = 'From: webmaster <somebody>';
$fromto = str_replace('webmaster', $en , $temp );  

$temp = $fromto;
$fromto = str_replace('somebody', $arr[0] , $temp );  

    $headers[] =  ""; //$fromto ;

 add_filter( 'wp_mail_content_type', 'mg_set_html_content_type' );
 add_filter( 'wp_mail_from', 'mg_mail_from' );
 add_filter( 'wp_mail_from_name', 'mg_mail_from_name' );

 $status = wp_mail( $to , $subject, $content, $headers);

 remove_filter( 'wp_mail_content_type', 'mg_set_html_content_type' );
 remove_filter( 'wp_mail_from', 'mg_mail_from' );
 remove_filter( 'wp_mail_from_name', 'mg_mail_from_name' );

return $status;

}

function migla_test_hletter( $e, $en , $to)
{

  $subject = get_option('migla_honoreESbj');
  
  $string .= get_option( 'migla_honoreEBody' ) ."<br>" ;
  $string .= get_option('migla_honoreECustomIntro');
  $string .= "<br>\"Happy Birthday\"<br>";
  
  $string .= "<br>".get_option( 'migla_honoreERepeat' ) ."<br>"  ;
  
  $string .= get_option( 'migla_honoreEAnon' ) ."<br>" ;
  
  $string .= get_option( 'migla_honoreESig' ) ."<br>" ;  

  $placeholder = array( '[honoreename]', '[firstname]','[lastname]' ,'[amount]' ,'[date]', '[newline]' );
  $replace = array( 'Jane', 'John','Doe' ,'100' ,'September 10th, 2015' , '<br>');

  $content =  str_replace($placeholder, $replace, $string);
  $content2 = $content;
  $content =  str_replace("\\","",$content2);
  $content = "<html><body>" .$content ; 
  $content .= "</body><html>";

 $arr = explode("@", $e);

$temp = 'From: webmaster <somebody>';
$fromto = str_replace('webmaster', $en , $temp );  

$temp = $fromto;
$fromto = str_replace('somebody', $arr[0] , $temp );  
//$fromto = str_replace('somebody', $e , $temp );  

    $headers[] =  $fromto ; //'From: webmaster <somebody>'; //$fromto ; 


 add_filter( 'wp_mail_content_type', 'mg_set_html_content_type' );
 add_filter( 'wp_mail_from', 'mg_mail_from' );
 add_filter( 'wp_mail_from_name', 'mg_mail_from_name' );

 $status = wp_mail( $to , $subject, $content, $headers);

 remove_filter( 'wp_mail_content_type', 'mg_set_html_content_type' );
 remove_filter( 'wp_mail_from', 'mg_mail_from' );
 remove_filter( 'wp_mail_from_name', 'mg_mail_from_name' );

return $status;

}

 
function mg_set_html_content_type() {
 return 'text/html';
}

/************************** THE REPEATING HANDLER ******************************/
/*********************************************************************************************/
function testing_repeat(){
  $old_ids = array(); 
  $new_id = 504;
  $post = array();
  $post[0] = "paymentdata";
  $post[1] = "paymentmethod";
  $post[2] = "transaction_type";
  $post[3] = "id";
  $post[4] = "session";
  $post[5] = (string)date( 'H:i:s', current_time( 'timestamp', 0 ) );
  $post[6] = (string)date( 'm/d/Y');

  //1
  $old_ids =  migla_cek_repeating_id( "migla161715530_20141221021252" );
  if(  migla_cek_id_exist( $new_id )==1  || empty($old_ids[0]) )
  {
  }else{
     migla_create_from_old_donation( $old_ids[0], $new_id);

        //Payment data

   add_post_meta( $new_id, "miglad_paymentdata" , $post[0] );
   add_post_meta( $new_id, "miglad_paymentmethod" , $post[1] );
   add_post_meta( $new_id, "miglad_transactionType" , $post[2] );
   add_post_meta( $new_id, "miglad_transactionId" , $post[3] );
   add_post_meta( $new_id, "miglad_session_id" , $post[4] );
   add_post_meta( $new_id, "miglad_time" , $post[5] );
   add_post_meta( $new_id, "miglad_date" , $post[6] );
  } 
}

function migla_cek_repeating_id( $meta_value ) {
    global $wpdb;
    $pid = $wpdb->get_var( $wpdb->prepare(
           "SELECT post_id FROM $wpdb->postmeta WHERE meta_value = %s AND meta_key = 'miglad_session_id' ORDER BY post_id ASC"
            ,$meta_value  ));
    if( $pid != '' )
        return $pid;
    else 
        return -1;
}

function migla_cek_id_exist( $id ){
   $isExist = 1;
   $sql = "SELECT distinct post_id FROM {$wpdb->prefix}postmeta WHERE post_id = ".$id." ";
   global $wpdb;
   $post = array(); $id = array(); $i = 0;
   $post = $wpdb->get_results($sql);   
   foreach ( $post as $p ){
     $id[$i] = intval( $p->post_id ); $i++;
   }  
   if( empty( $id[0] )  ){ $isExist = 0; } 
   return $isExist;
}

function migla_create_from_old_donation( $old_id, $new_id)
{
   global $wpdb;

   //get data from old id
   $sql = "SELECT distinct meta_key,meta_value FROM {$wpdb->prefix}postmeta WHERE post_id = '".$old_id."' ";
   $post = array(); $result = array(); $i = 0;
   $post = $wpdb->get_results($sql);
   
   //insert into new id
   foreach ( $post as $id ){
    $key = (string)$id->meta_key;
    if( $key == "miglad_paymentdata" || $key == "miglad_paymentmethod" || 
         $key == "miglad_time" || $key == "miglad_date" || 
         $key == "miglad_transactionType" || $key == "miglad_transactionId"
     ){
       // add_post_meta( $new_id, $key , $post[$key] );     
     }else{
        add_post_meta( $new_id, (string)$id->meta_key , (string)$id->meta_value );
     }
   }//foreach  

}

/**********************************************************************************************************************************/

/***********************************************/
/*            GET OPTION ID  FINISH Nov 21st */
/***********************************************/
function migla_get_option_id( $op ){
  global $wpdb; $res =array();
  $sql = "SELECT option_id from {$wpdb->prefix}options WHERE option_name='".$op."'";
  $res = $wpdb->get_row($sql);
  return $res->option_id;
}

function migla_reset_form() {
global $wpdb;

$fields =  array (
    '0' => array (
        'title' => 'Donation Information',
        'child' =>  array(
                   '0' => array( 'type'=>'radio','id'=>'amount', 'label'=>'How much would you like to donate?', 'status'=>'3', 'code' => 'miglad_'),
                   '1' => array( 'type'=>'select','id'=>'campaign', 'label'=>'Would you like to donate this to a specific campaign?', 'status'=>'3', 'code' => 'miglad_'),
                   '2' => array( 'type'=>'checkbox','id'=>'repeating', 'label'=>'Repeat Monthly?', 'status'=>'1', 'code' => 'miglad_')
                 ),
        'parent_id' => 'NULL',
        'depth' => 2,
        'toggle' => '-1'
    ),
    '1' => array (
        'title' => 'Donor Information',
        'child' => array(
                   '0' => array( 'type'=>'text','id'=>'firstname', 'label'=>'First Name', 'status'=>'3', 'code' => 'miglad_' ),
                   '1' => array( 'type'=>'text','id'=>'lastname', 'label'=>'Last Name', 'status'=>'3', 'code' => 'miglad_' ),
                   '2' => array( 'type'=>'text','id'=>'address', 'label'=>'Address', 'status'=>'1' , 'code' => 'miglad_' ),
                   '3' => array( 'type'=>'select','id'=>'country', 'label'=>'Country', 'status'=>'1' , 'code' => 'miglad_' ),
                   '4' => array( 'type'=>'text','id'=>'city', 'label'=>'City', 'status'=>'1' , 'code' => 'miglad_' ),
                   '5' => array( 'type'=>'text','id'=>'postalcode', 'label'=>'Postal Code', 'status'=>'1' , 'code' => 'miglad_' ),
                   '6' => array( 'type'=>'checkbox','id'=>'anonymous', 'label'=>'Anonymous?', 'status'=>'1' , 'code' => 'miglad_' ),
                   '7' => array( 'type'=>'text','id'=>'email', 'label'=>'Email', 'status'=>'3' , 'code' => 'miglad_' )
                 ),
        'parent_id' => 'NULL',
        'depth' => 8,
        'toggle' => '-1'
    ),
    '2' => array (
        'title' => 'Is this donation a Gift?',
        'child' => array(
                   '0' => array( 'type'=>'checkbox','id'=>'memorialgift', 'label'=>"Is this a Memorial Gift?", 'status'=>'1', 'code' => 'miglad_' ),
                   '1' => array( 'type'=>'text','id'=>'honoreename', 'label'=>"Honoree[q]s Name", 'status'=>'1', 'code' => 'miglad_' ),
                   '2' => array( 'type'=>'text','id'=>'honoreeemail', 'label'=>"Honoree[q]s Email", 'status'=>'1', 'code' => 'miglad_' ),
                   '3' => array( 'type'=>'textarea','id'=>'honoreeletter', 'label'=>"Write a custom note to the Honoree here", 'status'=>'1', 'code' => 'miglad_' ),
                   '4' => array( 'type'=>'text','id'=>'honoreeaddress', 'label'=>"Honoree[q]s Address", 'status'=>'1', 'code' => 'miglad_' ),
                   '5' => array( 'type'=>'text','id'=>'honoreecountry', 'label'=>"Honoree[q]s Country", 'status'=>'1', 'code' => 'miglad_' ),
                   '6' => array( 'type'=>'text','id'=>'honoreecity', 'label'=>'Honoree[q]s City', 'status'=>'1' , 'code' => 'miglad_' ),
                   '7' => array( 'type'=>'text','id'=>'honoreepostalcode', 'label'=>'Honoree[q]s Postal Code', 'status'=>'1' , 'code' => 'miglad_' ),				   
                 ),
        'parent_id' => 'NULL',
        'depth' => 5,
        'toggle' => '1'

    ),
    '3' => array (
        'title' => 'Is this a matching gift?',
        'child' => array(
                   '0' => array( 'type'=>'text','id'=>'employer', 'label'=>'Employer[q]s Name', 'status'=>'1', 'code' => 'miglad_' ),
                   '1' => array( 'type'=>'text','id'=>'occupation', 'label'=>'Occupation', 'status'=>'1', 'code' => 'miglad_' )
                 ),
        'parent_id' => 'NULL',
        'depth' => 3,
        'toggle' => '1'
    )        
);

if ( migla_get_option_id( 'migla_form_fields' ) > 0){
  $sql = "UPDATE {$wpdb->prefix}options SET option_value = '".serialize($fields)."' WHERE option_name ='migla_form_fields'";
}else{
  $sql = "INSERT INTO {$wpdb->prefix}options(option_name, option_value) values('migla_form_fields', '".serialize($fields)."')";

}
$wpdb->query($sql);
}


/************************************************************************
* 	PURGE TRANSIENT Dec 04 2014
*************************************************************************/
function purgeTransient(){
 global $wpdb;

 $sql = "DELETE FROM {$wpdb->prefix}options
        where option_name like '%transient_migla%'
        AND SUBSTRING(option_name, 12) IN
       (
         SELECT SUBSTRING(option_name,20) from {$wpdb->prefix}options
         WHERE option_name LIKE '%transient_timeout_migla%'
         AND DATEDIFF( DATE_FORMAT( FROM_UNIXTIME( option_value ) , '%Y-%m-%d' ) , Now() ) < -10
        )";

 $wpdb->query( $sql );
}

function purgeTransient2(){
 global $wpdb;
 $sql = " DELETE from {$wpdb->prefix}options
          WHERE option_name LIKE '%transient_timeout_migla%'
          AND DATEDIFF( DATE_FORMAT( FROM_UNIXTIME( option_value ) , '%Y-%m-%d' ) , Now() ) < -10";
 $wpdb->query( $sql );
}



/************************************************************************
* 	PROGRESS BAR   
*************************************************************************/
function migla_get_campaign_target( $cname ){
  $t = 0;
    
  $data = (array)get_option('migla_campaign');
  if( $cname == '' )
  {
    if( empty($data[0]) ){
    }else{ 
       foreach( (array)$data as $d ){
          $t = $t + (float)$d['target'];
       }
    }
  }else{
    if( empty($data[0]) ){
    }else{ 
       foreach( (array)$data as $d ){
          if( strcmp($cname, $d['name']) == 0 ){ 
             $t = $d['target']; break; 
          }
       }
    }
  }

  return $t;
}

function migla_get_total( $cname, $posttype )
{

  global $wpdb; $res =array();

 if( $cname == '' )
 {
     $sql = "select sum(meta_value) as total from {$wpdb->prefix}posts inner join {$wpdb->prefix}postmeta
             on {$wpdb->prefix}posts.ID = {$wpdb->prefix}postmeta.post_id
             where post_type like 'migla%donation' AND meta_key = 'miglad_amount'";
 }else{

     $sql = "select sum(meta_value) as total from {$wpdb->prefix}posts inner join {$wpdb->prefix}postmeta
             on {$wpdb->prefix}posts.ID = {$wpdb->prefix}postmeta.post_id
             where (post_type = 'migla_donation' OR post_type = 'migla_odonation') AND post_id in (
             select post_id from {$wpdb->prefix}postmeta where meta_value = '".$cname."' and meta_key = 'miglad_campaign'
              ) and meta_key = 'miglad_amount'";
  }

  if( $posttype != '' ){
    $sql = $sql . " and post_type = '".$posttype."'";
  }
  $res = $wpdb->get_results($sql , ARRAY_A);
  return  $res[0]['total'];
}


function getCurrencySymbol2()
{
   $code = (string)get_option(  'migla_default_currency'  );
   $arr = get_option( 'migla_currencies' ); 
   $icon ='';
   foreach ( $arr as $key => $value ) {
     if(  strcmp( $code, $arr[$key]['code'] ) == 0  ){
       $icon = $arr[$key]['symbol']; 
       break;
     }
   }
   return $icon;
}

function miglahex2RGB($hex) 
{
        preg_match("/^#{0,1}([0-9a-f]{1,6})$/i",$hex,$match);
        if(!isset($match[1]))
        {
            return false;
        }

        if(strlen($match[1]) == 6)
        {
            list($r, $g, $b) = array($hex[0].$hex[1],$hex[2].$hex[3],$hex[4].$hex[5]);
        }
        elseif(strlen($match[1]) == 3)
        {
            list($r, $g, $b) = array($hex[0].$hex[0],$hex[1].$hex[1],$hex[2].$hex[2]);
        }
        else if(strlen($match[1]) == 2)
        {
            list($r, $g, $b) = array($hex[0].$hex[1],$hex[0].$hex[1],$hex[0].$hex[1]);
        }
        else if(strlen($match[1]) == 1)
        {
            list($r, $g, $b) = array($hex.$hex,$hex.$hex,$hex.$hex);
        }
        else
        {
            return false;
        }

        $color = array();
        $color['r'] = hexdec($r);
        $color['g'] = hexdec($g);
        $color['b'] = hexdec($b);

        return $color;
}

////////////////////// Progress Bar and Text Shortcodes //////////////////////////////////

function migla_draw_progress_bar( $percent )
{
   $effect = (array)get_option( 'migla_bar_style_effect' );
		// Five Row Progress Bar
                $effectClasses = "";
                if( strcmp( $effect['Stripes'] , "yes") == 0){
                  $effectClasses = $effectClasses . " striped";
                }
                if( strcmp( $effect['Pulse'] , "yes") == 0){
                  $effectClasses = $effectClasses . " mg_pulse";
                }
                if( strcmp( $effect['AnimatedStripes'] ,"yes") == 0){
                  $effectClasses = $effectClasses . " active animated-striped";
                }
                if( strcmp( $effect['Percentage'], "yes") == 0 ){
                  $effectClasses = $effectClasses . " mg_percentage";
                }

        $borderRadius = explode(",", get_option( 'migla_borderRadius' )); //4spinner
        $bar_color = explode(",", get_option( 'migla_bar_color' ));  //rgba
        $progressbar_bg = explode(",", get_option( 'migla_progressbar_background' )); //rgba
        $boxshadow_color = explode(",", get_option( 'migla_wellboxshadow' )); //rgba 4spinner 

        $style1 = "";
        $style1 .= "box-shadow:".$boxshadow_color[2]."px ".$boxshadow_color[3]."px ".$boxshadow_color[4]."px ".$boxshadow_color[5]."px " ;
        $style1 .= $boxshadow_color[0]." inset !important;";
        $style1 .= "background-color:".$progressbar_bg[0].";";

        $style1 .= "-webkit-border-top-left-radius:".$borderRadius[0]."px; -webkit-border-top-right-radius: ".$borderRadius[1]."px;";
        $style1 .= "-webkit-border-bottom-left-radius: ".$borderRadius[2]."px; -webkit-border-bottom-right-radius:".$borderRadius[3]."px;";

        $style1 .= "-moz-border-radius-topleft:".$borderRadius[0]."px; -moz-border-radius-topright: ".$borderRadius[1]."px;";
        $style1 .= "-moz-border-radius-bottomleft: ".$borderRadius[2]."px;-moz-border-radius-bottomright:".$borderRadius[3]."px;";

        $style1 .= "border-top-left-radius:".$borderRadius[0]."px; border-top-right-radius: ".$borderRadius[1]."px;";
        $style1 .= "border-bottom-left-radius:  ".$borderRadius[2]."px;border-bottom-right-radius:".$borderRadius[3]."px;";
	
        $stylebar = "background-color:".$bar_color[0].";";

	$output = "";

        $output .= "<div id='me' class='progress ".$effectClasses."' style='".$style1."'> ";
        $output .= "<div class='progress-bar bar' role='progressbar' aria-valuenow='20' aria-valuemin='0' aria-valuemax='100'";
        $output .= "style='width:".$percent."%;".$stylebar."'>";
        $output .= $percent . "%";
        $output .= "</div>";
        $output .= "</div>";

        return $output;
}


function migla_text_progressbar(  $cname, $posttype , $linkbtn, $btntext, $text )
{
  	$total_amount = 0; 
        $target = 0;
	
        $total_amount = migla_get_total( $cname, $posttype );
        $target = migla_get_campaign_target( $cname );

    if(  $target != 0 ){
	if( $total_amount == 0 )
	{
          $percent = 0;	
	}else if( $target != 0 ) {
          $percent = number_format(  ( $total_amount / $target) * 100 , 2);		
	}

        $op = get_option('migla_progbar_info'); 

        $symbol = getCurrencySymbol2();
        $x = array();
        $x[0] = get_option('migla_thousandSep');
        $x[1] = get_option('migla_decimalSep');
		$before = ''; $after = '';

		if( strtolower(get_option('migla_curplacement')) == 'before' ){
		  $before = $symbol;
		}else{
		  $after = $symbol;		
		}
		
        $showSep = get_option('migla_showDecimalSep');
        $decSep = 0;
        if( strcmp($showSep , "yes") == 0 ){ $decSep = 2; }

        $total_amount = $before. number_format( $total_amount , $decSep, $x[1], $x[0]). $after;
        $target = $before. number_format( $target , $decSep, $x[1], $x[0]) .$after;
        $percentStr = $percent . "%";

        //codes [target] [total] [percentage] [campaign]
        $cname2 = str_replace("[q]", "'", $cname);

        $placeholder = array( '[total]','[target]' ,'[campaign]', '[percentage]' );
        $replace = array( $total_amount , $target , $cname2, $percentStr  );
        $content =  str_replace($placeholder, $replace, $op);
        $output = "";
        $output .= "<div class='bootstrap-wrapper'>";
        if($text == 'yes' || $text == '' )
        {
          $output .= "<div class='progress-bar-text'><p class='progress-bar-text'>";
          $output .= $content;
          $output .= "</p></div>";
        }
	$output .= migla_draw_progress_bar( $percent );
        $output .= "</div>";


}else{
  $output = "";
}

       if( $linkbtn == "yes"){
         $c = str_replace( "'", "", $cname ); //Clean
         $c = str_replace( " ", "", $c ); //Clean
         $form_url = 'migla_url_' . $c;
         $url = get_option( $form_url );

         if( $url == '' || $url == false){
	    $url = get_option('migla_form_url');
	 }

         $output .= "<form action='".$url."' method='post'>";
         $output .= "<input type='hidden' name='campaign' value='".$cname."' />";
         $output .= "<input type='hidden' name='thanks' value='widget_bar' />";
         $output .= "<button class='migla_donate_now mg-btn-grey'>".$btntext."</button>";
         $output .= "</form>";
       }

        return $output;
}


/**************** Widget Progress Bar *********************************/

function migla_widget_progressbar(  $cname )
{
  	$total_amount = 0; 
        $target = 0;
	
        $total_amount = migla_get_total( $cname, "" );
        $target = migla_get_campaign_target( $cname );

if(  $target != 0 ){
	if( $total_amount == 0 )
	{
          $percent = 0;	
	}else if( $target != 0 ) {
          $percent = number_format(  ( $total_amount / $target) * 100 , 2);		
	}

        $op = get_option('migla_progbar_info'); 

        $symbol = getCurrencySymbol2();
        $x = array();
        $x[0] = get_option('migla_thousandSep');
        $x[1] = get_option('migla_decimalSep');
		$before = ''; $after = '';

		if( strtolower(get_option('migla_curplacement')) == 'before' ){
		  $before = $symbol;
		}else{
		  $after = $symbol;		
		}
		
        $showSep = get_option('migla_showDecimalSep');
        $decSep = 0;
        if( strcmp($showSep , "yes") == 0 ){ $decSep = 2; }

        $total_amount = $before. number_format( $total_amount , $decSep, $x[1], $x[0]). $after;
        $target = $before. number_format( $target , $decSep, $x[1], $x[0]) .$after;
        $percentStr = $percent . "%";

        //codes [target] [total] [percentage] [campaign]
        $cname2 = str_replace("[q]", "'", $cname);

        $placeholder = array( '[total]','[target]' ,'[campaign]', '[percentage]' );
        $replace = array( $total_amount , $target , $cname2, $percentStr  );
        $content =  str_replace($placeholder, $replace, $op);
        $output = "";
        $output .= "<div class='bootstrap-wrapper'>";
        $output .= "<div class='progress-sidebar'><p class='progress-sidebar'>";
        $output .= $content;
        $output .= "</p></div>";
 	$output .= migla_draw_progress_bar( $percent );
        $output .= "</div>";
}else{
  $output = "";
}
        return $output;
}

/************************** Migla progress circle widget *************************************/
function migla_get_percentage(  $cname ){
  	$total_amount = 0; 
        $target = 0; $percent = 0;
	
        $total_amount = migla_get_total( $cname, "" );
        $target = migla_get_campaign_target( $cname );

        if(  $target != 0 ){
	  if( $total_amount == 0 )
	  {
	  }else if( $target != 0 ) {
             $percent = number_format(  ( $total_amount / $target) * 100 , 2);		
	  }
        }else{
        }
   return $percent;
}


function migla_draw_all_progress_bar( $c ){

  $output = "";
  if( $c == '' )
  {
    $campaignArr = (array)get_option('migla_campaign');
    if( empty($campaignArr[0]) ){
    }else{
      foreach( $campaignArr as $key => $value )
      {
        $output = migla_text_progressbar( $campaignArr[$key]['name'], "", "", "no", "no");
      }
    }

  }else{
        $output = migla_text_progressbar(  $c, "","", "no", "no");
  }
  echo $output;
}


/***************  Shortcode Progress Bar    ******************************/
function migla_shortcode_progressbar( $c, $btn , $btntext, $text ){

  $output = "";
  if( $c == '' )
  {
    $campaignArr = (array)get_option('migla_campaign');
    if( empty($campaignArr[0]) ){
    }else{
      foreach( $campaignArr as $key => $value )
      {
        $output = migla_text_progressbar( $campaignArr[$key]['name'], "", $btn, $btntext, $text );
      }
    }

  }else{
        $output = migla_text_progressbar(  $c, "", $btn , $btntext, $text );
  }
  return $output;
}




/************************************************************************
   Text Raised Amount Shortcodes
*************************************************************************/

function migla_draw_textbarshortcode(  $cname, $button, $buttontext, $text )
{
  	$total_amount = 0; 
        $target = 0;
	
        $total_amount = migla_get_total( $cname, $posttype );
        $target = migla_get_campaign_target( $cname );

     //if(  $target != 0 ){
       if( $total_amount == 0 ){
          $percent = 0;	
       }else if( $target != 0 ) {
          $percent = number_format(  ( $total_amount / $target) * 100 , 2);		
       }
     
        $op = get_option('migla_progbar_info'); 

        $symbol = getCurrencySymbol2();
        $x = array();
        $x[0] = get_option('migla_thousandSep');
        $x[1] = get_option('migla_decimalSep');
		$before = ''; $after = '';

         if( strtolower(get_option('migla_curplacement')) == 'before' ){
		   $before = $symbol;
         }else{
		   $after = $symbol;		
         }
		
        $showSep = get_option('migla_showDecimalSep');
        $decSep = 0;
        if( strcmp($showSep , "yes") == 0 ){ $decSep = 2; }

        $total_amount = $before. number_format( $total_amount , $decSep, $x[1], $x[0]). $after;
        $target = $before. number_format( $target , $decSep, $x[1], $x[0]) .$after;
        $percentStr = $percent . "%";

        //codes [target] [total] [percentage] [campaign]
        $cname2 = str_replace("[q]", "'", $cname);
        
  
        $placeholder = array( '#campaign#', '#total#','#target#' , '#percentage#' );
        $replace = array(  $cname2, $total_amount , $target , $percentStr  );
        $content =  str_replace($placeholder, $replace, $text);

        $start = $content;
        $pos1 = strpos($start , "#textlink:"); $afterform = "";

         $c = str_replace( "'", "", $cname ); //Clean
         $c = str_replace( " ", "", $c ); //Clean
         $form_url = 'migla_url_' . $c;
         $url = get_option( $form_url );
 
         if( $url == '' || $url == false){
	    $url = get_option('migla_form_url');
	 }

        if( $pos1 >= 0)
        {
          $start = substr($start, ( $pos1 + 1) );     
          $pos2 = strpos( $start , "#");

          $id = rand(); $id = "mgtextlink" . $id;
          $thecode = substr( $start , 0, $pos2 );
          $textlink = substr( $thecode , 9 );
          $thecode =  "#".$thecode."#";
          $temp = $content;

          $temp2 .= "<a style='display:inline;padding:0px;margin:0px !important' href='javascript:{}' onclick='document.getElementById(\"".$id."\").submit(); return false;'>". $textlink."</a>";

          $afterform .= "<form id='".$id."' action='".$url."' method='post' style='display:none inline;padding:0px;margin:0px !important' class='form-inline' role='form'>";
          $afterform .= "<input type='hidden' name='campaign' value='".$cname."' style='display:inline;padding:0px;margin:0px !important' />";
          $afterform .= "<input type='hidden' name='thanks' value='widget_bar' />";
          $afterform .= "</form>";

          $content =  str_replace( $thecode, $temp2, $temp );
        }

        $output = "";
        $output .= "<div style='display:inline;' class='wrapper'>";
        $output .= $content;
        $output .= "</div>";
        $output .= $afterform;


       if( $button == "yes"){

         $output .= "<form action='".$url."' method='post'>";
         $output .= "<input type='hidden' name='campaign' value='".$cname."' />";
         $output .= "<button class='migla_donate_now mg-btn-grey'>".$buttontext."</button>";
         $output .= "</form>";
       }

     return $output;
}


/************************************************************************
  TARGET, TOTAL Campaign
*************************************************************************/

function  migla_get_target( $campaign ){
	$campaignArray = get_option( 'migla_campaign' );
	$t = 0;
	foreach( (Array) $campaignArray as $key => $value){
	  if ( $campaignArray[$key]['name'] == $campaign )
	  {
	    $t = $campaignArray[$key]['target']; 
	    break;
	  }
	}	
	return $t;
}

function migla_get_total_amount($campaign) 
{
 global $wpdb;
 $postIDs = array();
 $postIDs = $wpdb->get_results(" SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_value = '".$campaign."'");

$t = 0;
foreach ( $postIDs as $id ){
 $t = $t + get_post_meta( intval( $id->post_id ) , 'miglad_amount', true);
}

return $t;

}

function migla_number_and_total($campaign){
 global $wpdb;
 $postIDs = array();
 $postIDs = $wpdb->get_results( 
	$wpdb->prepare( 
		"
SELECT post_id
FROM {$wpdb->prefix}postmeta
WHERE meta_value = %s
AND meta_key = %s
		",
	        $campaign,'migla_campaign' 
        )
 ); 


 $t = 0;
   foreach ( $postIDs as $id ){
   $t = $t + get_post_meta( intval( $id->post_id ) , 'migla_amount', true);
 }

 $arrOut = array();
 $arrOut[0] = count( $postIDs ); //number of records
 $arrOut[1] = $t; //total amount

  return $arrOut;
} //migla_number_and_total



/************************************************************************
  Create POST ONLINE AND OFFLINE
*************************************************************************/

function migla_create_post() 
{

$new_donation = array(
	'post_title' => 'migla_donation',
	'post_content' => '',
	'post_status' => 'publish',
	'post_author' => 1,
	'post_type' => 'migla_donation'
);

$new_id = wp_insert_post( $new_donation );

return $new_id;

}

function migla_create_offpost() 
{

$new_donation = array(
	'post_title' => 'migla_offlinedonation',
	'post_content' => '',
	'post_status' => 'publish',
	'post_author' => 1,
	'post_type' => 'migla_odonation'
);

$new_id = wp_insert_post( $new_donation );

return $new_id;

}

/************************************************************************
  GET POST ID
*************************************************************************/
function migla_get_ids_campaign( $campaign ) 
{
 
 global $wpdb;
 $postIDs = array();
 $postIDs = $wpdb->get_results( 
	$wpdb->prepare( 
		"SELECT pm.post_id,pm.meta_value FROM {$wpdb->prefix}postmeta pm
        where pm.post_id in ( 
	SELECT {$wpdb->prefix}posts.ID FROM {$wpdb->prefix}posts
	INNER JOIN {$wpdb->prefix}postmeta ON {$wpdb->prefix}posts.ID = {$wpdb->prefix}postmeta.post_id
	WHERE post_type = %s
	AND meta_key = %s
	AND meta_value = %s
        )
        AND pm.meta_key = %s
        ORDER BY STR_TO_DATE( pm.meta_value, %s) DESC" ,
	         'migla_donation','migla_campaign', $campaign, 'migla_date','%m/%d/%Y'
        )
 ); 

  return $postIDs;
}

function migla_get_ids_all() 
{
 
 global $wpdb;
 $postIDs = array();
 $postIDs =  $wpdb->get_results( 
	$wpdb->prepare( 
	"SELECT ID, meta_value, meta_key FROM {$wpdb->prefix}posts INNER JOIN {$wpdb->prefix}postmeta ON {$wpdb->prefix}posts.ID = {$wpdb->prefix}postmeta.post_id
	 WHERE post_type = %s and meta_key = %s
         ORDER BY STR_TO_DATE( meta_value, %s) DESC
        " ,
	   'migla_donation','miglad_date','%m/%d/%Y'
        )
 );

  return $postIDs;
}

function migla_get_oflineids_all() 
{
 
 global $wpdb;
 $postIDs = array();
 $postIDs =  $wpdb->get_results( 
	$wpdb->prepare( 
	"SELECT ID, meta_value, meta_key FROM {$wpdb->prefix}posts INNER JOIN {$wpdb->prefix}postmeta ON {$wpdb->prefix}posts.ID = {$wpdb->prefix}postmeta.post_id
	 WHERE post_type = %s and meta_key = %s
         ORDER BY STR_TO_DATE( meta_value, %s) DESC
        " ,
	   'migla_odonation','miglad_date','%m/%d/%Y'
        )
 ); 

  return $postIDs;
}

/************************************************************/
/*           DATA RETRIEVING FOR REPORT  FINISH Nov 23st */
/**********************************************************/
function migla_get_id_range( $start, $end ) 
{
 global $wpdb;
 $postIDs = array();

 $postIDs = $wpdb->get_results( 
	$wpdb->prepare( 
	"SELECT ID, meta_value, meta_key FROM {$wpdb->prefix}posts INNER JOIN {$wpdb->prefix}postmeta ON {$wpdb->prefix}posts.ID = {$wpdb->prefix}postmeta.post_id
	 WHERE post_type = %s and meta_key = %s
         ORDER BY STR_TO_DATE( meta_value, %s) DESC
        " ,
	   'migla_donation','miglad_date','%m/%d/%Y'
        )
 ); 

  return $postIDs;
}



/***********************************************************************************************************/
/**							OFFLINE REPORT																***/
/***********************************************************************************************************/

function migla_get_ofids_all() 
{
 
 global $wpdb;
 $postIDs = array();
 $postIDs = $wpdb->get_results( 
	$wpdb->prepare( 
	"SELECT ID FROM {$wpdb->prefix}posts INNER JOIN {$wpdb->prefix}postmeta ON {$wpdb->prefix}posts.ID = {$wpdb->prefix}postmeta.post_id
	 WHERE post_type = %s and meta_key = %s
         ORDER BY STR_TO_DATE( meta_value, %s) DESC
        " ,
	   'migla_odonation','miglad_date','%m/%d/%Y'
        ), ARRAY_N 
 ); 

  return $postIDs;
}


function migla_remove_donation($str) 
{
	 global $wpdb;
	 $wpdb->query( "DELETE FROM {$wpdb->prefix}posts where ID in" . $str);
	 $wpdb->query( "DELETE FROM {$wpdb->prefix}postmeta where post_id in ". $str );       
}

function migla_get_offline(){
 global $wpdb;
 $data = array();
 $data = $wpdb->get_results( 
	$wpdb->prepare( 
	"SELECT ID FROM {$wpdb->prefix}posts INNER JOIN {$wpdb->prefix}postmeta ON {$wpdb->prefix}posts.ID = {$wpdb->prefix}postmeta.post_id
	 WHERE post_type = %s and meta_key = %s
         ORDER BY STR_TO_DATE( meta_value, %s) DESC
        " ,
	   'migla_odonation','miglad_date','%m/%d/%Y'
        )
 );

 $out = array(); $key = ""; $row = 0; $id = 0; $state = "";
 foreach( $data as $id )
 {
   
    $c = str_replace("\\","",get_post_meta( intval( $id->ID ) , 'miglad_campaign', true));

    $out[$row]['id'] =  $id->ID ;
    $out[$row]['remove'] = "<input type='hidden' name='".$id->ID."' class='removeRow' /><i class='fa fa-trash'></i>";
    $out[$row]['detail'] = "<input class='mglrec' type=hidden name='".$row."' >";

    $out[$row]['date'] = get_post_meta( intval( $id->ID ) , 'miglad_date', true);
    $out[$row]['firstname'] = get_post_meta( intval( $id->ID ) , 'miglad_firstname', true);
    $out[$row]['lastname'] = get_post_meta( intval( $id->ID ) , 'miglad_lastname', true);
    $out[$row]['amount'] = get_post_meta( intval( $id->ID ) , 'miglad_amount', true);;
    $out[$row]['anonymous'] = get_post_meta( intval( $id->ID ) , 'miglad_anonymous', true);
    $out[$row]['campaign'] = $c;
    $out[$row]['email'] = get_post_meta( intval( $id->ID ) , 'miglad_email', true);
    $out[$row]['address'] = get_post_meta( intval( $id->ID ) , 'miglad_address', true);
    $out[$row]['country'] = get_post_meta( intval( $id->ID ) , 'miglad_country', true);
    $out[$row]['zip'] = get_post_meta( intval( $id->ID ) , 'miglad_zip', true);
    $out[$row]['orgname'] = get_post_meta( intval( $id->ID ) , 'miglad_orgname', true);
    $out[$row]['transactionType'] = get_post_meta( intval( $id->ID ) , 'miglad_transactionType', true);
    $out[$row]['employer'] = get_post_meta( intval( $id->ID ) , 'miglad_employer', true);
    $out[$row]['occupation'] = get_post_meta( intval( $id->ID ) , 'miglad_occupation', true);

    if( get_post_meta( intval( $id->ID ) , 'miglad_state', true) != '' ){
      $out[$row]['state'] = get_post_meta( intval( $id->ID ) , 'miglad_state', true);
    }else if( get_post_meta( intval( $id->ID ) , 'miglad_province', true) != ''){
      $out[$row]['state'] = get_post_meta( intval( $id->ID ) , 'miglad_province', true);
    }else{
      $out[$row]['state'] = '';
    }
        
    $row = $row + 1;
}

 return $out;
}

/*******************************************************************************************/
/*************** CURRENT PAGE URL ********************************/
/*******************************************************************************************/
function migla_current_page_url() {
	$pageURL = 'http';
	if( isset($_SERVER["HTTPS"]) ) {
		if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
	}
	$pageURL .= "://";
	if ($_SERVER["SERVER_PORT"] != "80") {
		$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	} else {
		$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	}
	return $pageURL;
}

/*******************************************************************************************/
/*************** DONATION WIDGETS ********************************/
/*******************************************************************************************/
function miglaCurrencySymbol()
{
   $code = (string)get_option(  'migla_default_currency'  );
   $arr = get_option( 'migla_currencies' ); 
   $icon ='';
   foreach ( $arr as $key => $value ) {
     if(  strcmp( $code, $arr[$key]['code'] ) == 0  ){
       $icon = $arr[$key]['symbol']; 
       break;
     }
   }
   return $icon;
}

function migla_donor_recent($type, $num){
 global $wpdb;
 $data = array();

 if( $type == "" ){
  $data = $wpdb->get_results( 
	$wpdb->prepare( 
         "SELECT {$wpdb->prefix}posts.ID, {$wpdb->prefix}posts.post_type FROM {$wpdb->prefix}posts INNER JOIN {$wpdb->prefix}postmeta
          on {$wpdb->prefix}posts.ID = {$wpdb->prefix}postmeta.post_id
	  WHERE {$wpdb->prefix}posts.post_type like %s and meta_key=%s
          ORDER BY STR_TO_DATE( meta_value, %s ) DESC
          " 
          , 'migla%donation', 'miglad_date', '%m/%d/%Y'
        )
     );
 }else{
  $data = $wpdb->get_results( 
	$wpdb->prepare( 
         "SELECT {$wpdb->prefix}posts.ID, {$wpdb->prefix}posts.post_type FROM {$wpdb->prefix}posts INNER JOIN {$wpdb->prefix}postmeta
          on {$wpdb->prefix}posts.ID = {$wpdb->prefix}postmeta.post_id
	  WHERE {$wpdb->prefix}posts.post_type like %s and meta_key=%s
          ORDER BY STR_TO_DATE( meta_value, %s ) DESC
          " 
          , $type, 'miglad_date', '%m/%d/%Y'
        )
     );
 }

 $row = 0;
 $list = array(); 
 foreach( $data as $id )
 {
   $anon = get_post_meta( intval( $id->ID ) , 'miglad_anonymous', true);

   if( strtolower($anon) != 'yes' ){
    $list[$row]['firstname'] = get_post_meta( intval( $id->ID ) , 'miglad_firstname', true);
    $list[$row]['lastname'] = get_post_meta( intval( $id->ID ) , 'miglad_lastname', true);
    $list[$row]['amount'] = get_post_meta( intval( $id->ID ) , 'miglad_amount', true);
    $list[$row]['date'] = get_post_meta( intval( $id->ID ) , 'miglad_date', true); 
    $list[$row]['time'] = get_post_meta( intval( $id->ID ) , 'miglad_time', true); 
    $list[$row]['type'] = $id->post_type;
    $row++;
   }
 }

   usort($list, 'mgcompareTime');

 return $list;  
}

function mgcompareTime($a, $b)
{
   $first = strtotime( $a['date']." ".$a['time'] );
   $second = strtotime( $b['date']." ".$b['time'] );

     return  $second - $first;

}

function migla_donorwall_top( $type, $num ){
 global $wpdb;
 $data = array();

 if( $type == "" ){
  $data = $wpdb->get_results( 
	$wpdb->prepare( 
         "SELECT DISTINCT {$wpdb->prefix}posts.ID FROM {$wpdb->prefix}posts INNER JOIN {$wpdb->prefix}postmeta
          on {$wpdb->prefix}posts.ID = {$wpdb->prefix}postmeta.post_id
	  WHERE {$wpdb->prefix}posts.post_type like %s 
          ORDER BY ID
          " 
          , 'migla%donation'
        )
     );
 }else{
  $data = $wpdb->get_results( 
	$wpdb->prepare( 
         "SELECT DISTINCT {$wpdb->prefix}posts.ID FROM {$wpdb->prefix}posts INNER JOIN {$wpdb->prefix}postmeta
          on {$wpdb->prefix}posts.ID = {$wpdb->prefix}postmeta.post_id
	  WHERE {$wpdb->prefix}posts.post_type = %s 
          " 
          , $type
        )
     );
 }


 $row = -1; 
 $list = array(); $name = array(); $n = array();
 foreach( $data as $id )
 {
   $f = get_post_meta( intval( $id->ID ) , 'miglad_firstname', true);
   $l = get_post_meta( intval( $id->ID ) , 'miglad_lastname', true);
   $theirname = $f . $l ;
   $theirname = strtolower( $theirname );
   $theirname = str_replace(" ", "", $theirname);

   //cek new one
   if(  in_array( $theirname , $n, true )  )
   {

     $anon = get_post_meta( intval( $id->ID ) , 'miglad_anonymous', true);
     if( strtolower($anon) != 'yes' ){
        $index = array_search( $theirname , $n, true );
        $name[ $index ]['total'] = $name[ $index ]['total'] + floatval( get_post_meta( intval( $id->ID ) , 'miglad_amount', true) );
  
     }


   }else{

     $anon = get_post_meta( intval( $id->ID ) , 'miglad_anonymous', true);
     if( strtolower($anon) != 'yes'  ){
		   $row++;
		   $n[$row] =  $theirname;

		   $name[$row]['total'] =  floatval ( get_post_meta( intval( $id->ID ) , 'miglad_amount', true) );
                   $name[$row]['name'] = $theirname;
		   $name[$row]['firstname'] = $f;
		   $name[$row]['lastname'] = $l;
      }

   } //look for them

 } //foreach

 usort($name, 'mgcompareOrder');

 return $name; 
}

function mgcompareOrder($a, $b)
{
  return $b['total'] - $a['total'];
}



/********************* Top Donors & Recent Donations Shortcodes **********************************************/

function mg_draw_topdonors( $title, $num_rec, $donation_type, $use_link, $btn_class, $btn_style, $btn_text, $urlLink )
{
    $out = "";

    $out .= "<h3 class='top_donors_title'>";
    $out .= $title. "<br>";
    $out .= "</h3>";
    
     $posttype = 'migla_donation';
     if( $donation_type == 'offline' ){ $posttype = 'migla_odonation'; }
     if( $donation_type == 'both' ){ $posttype = ''; }

      $symbol = miglaCurrencySymbol();
      $b = ""; $a = "";
      $showdec = get_option('migla_showDecimalSep'); $dec = 0;
      if( $showdec == 'yes' ){ $dec = 2; }
      if( get_option('migla_curplacement') == 'before' ){ $b = $symbol; }else{ $a = $symbol; }
      $thousep = get_option('migla_thousandSep'); $decsep = get_option('migla_decimalSep');
      $data = array();

    if( $use_link == 'yes' ){ $BtnExisted = 'mg_widgetButton'; }else{ $BtnExisted = ''; }
      
  
      $data = migla_donorwall_top($posttype, $numberOfRecords);

      $i = 0;

      $out .= "<ol class='mg_top_donors ".$BtnExisted."'>";
      foreach( (array)$data as $datum ){

          $out .= "<li>" ;
          $out .= "<span class='mg_top_donors_name'>". $datum['firstname'] ."&nbsp;". $datum['lastname'] . " </span>"; 
          $out .= "<span class='mg_top_donors_amount'>".$b.number_format( $datum['total'], $dec , $decsep, $thousep ) .$a. " </span>";
          $out .= "</li>"; 
          $i++;
          if( $i == $num_rec ){ break; }

      }

     $out .= "</ol>";
     $out .= "<br>";
      
     $class2 = "";
     if( $btn_style == 'grey_button' ){  $class2 = ' mg-btn-grey';	  }	  
	
      if( $use_link=='yes' ){
        if( $urlLink == '' || $urlLink == false ){ $urlLink = get_option('migla_form_url');  }

        $out .= "<form action='".esc_url( $urlLink)."' method='post'>";
          if( $btn_text == '' ){ $btn_text = 'Donate'; }
        $out .= "<input type='hidden' name='thanks' value='widget_bar' />";
        $out .= "<button class='migla_donate_now ".$btn_class . $class2."'>".$btn_text."</button>";
        $out .= "</form>";
      }

   return $out;   
}


function migla_draw_donor_recent( $title, $num_rec, $donation_type, $use_link, $btn_class, $btn_style, $btn_text , $language, $url_link){
    $out = ""; 
  
    $out .= "<h3 class='mg-recent-donors-title'>";
    $out .= $title. "<br>";
    $out .= "</h3>";
    
     $posttype = 'migla_donation';
     if( $donation_type == 'offline' ){ $posttype = 'migla_odonation'; }
     if( $donation_type == 'both' ){ $posttype = ''; }

      $symbol = miglaCurrencySymbol();
      $b = ""; $a = "";
      $showdec = get_option('migla_showDecimalSep'); $dec = 0;
      if( $showdec == 'yes' ){ $dec = 2; }
      if( get_option('migla_curplacement') == 'before' ){ $b = $symbol; }else{ $a = $symbol; }
      $thousep = get_option('migla_thousandSep'); $decsep = get_option('migla_decimalSep');
      $data = array();

    if( $use_link == 'yes' ){ $BtnExisted = 'mg_widgetButton'; }else{ $BtnExisted = ''; }
      
  
      $data = migla_donor_recent($posttype, $numberOfRecords);
      $row = 1; 
      
      $out .= "<div class='bootstrap-wrapper mg_latest_donations_widget ".$BtnExisted."'><div class='mg_donations_wrap'> ";
      
      $df = array('%B %d %Y', '%b %d %Y', '%B %d, %Y', '%b %d, %Y' , '%d %B %Y', '%d %b %Y' ,'%Y-%m-%d', '%m/%d/%Y');
      $date_format = $df[0];

      $my_locale = get_locale();
      if( $language == "" ){
        $language = $my_locale;
      }
      setlocale(LC_TIME, $language );

      foreach( $data as $datum ){
        if( $row > $num_rec ){ break; }
  
         $out .= "<section class='mg_recent_donors_Panel'>";

         if($hide_date == 'on'){
               $out .= "<div class='mg_recent_donors_date pull-right'></div> ";
         }else{
              $out .= "<div class='mg_recent_donors_date pull-right'>". strftime( $date_format , date(strtotime($datum['date'])) )."</div> ";
         }

         $out .= "<div class='mg_recent_donors_amount'>".$b.number_format( $datum['amount'], $dec , $decsep, $thousep ) .$a. "</div>";

         $out .= "<div class='mg_recent_donors_name'>". $datum['firstname']. "&nbsp;" .$datum['lastname']  . "</p>";

         $out .=  "</section>";
         $row++;
      }

      $out .= "</div></div>";

      setlocale(LC_TIME, $my_locale );

     $class2 = "";
     if( $btn_style == 'grey_btn' ){  $class2 = ' mg-btn-grey';	  }	  
	
      if( $use_link == 'yes' ){
        if( $url_link == '' || $url_link == false ){   $url_link = get_option( 'migla_form_url' ); }
        $out .= "<form action='".esc_url($url_link)."' method='post'>";
          if( $btn_text == '' ){ $btn_text = 'Donate'; }
        $out .= "<input type='hidden' name='thanks' value='widget_bar' />";
        $out .= "<button class='migla_donate_now ".$btnclass . $class2."'>".$btn_text."</button>";

        $out .= "</form>";
      }		

   return $out;
}



?>