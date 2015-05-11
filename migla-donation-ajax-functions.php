<?php

/***********************************************/
/*   AJAX CALLERS  */
/***********************************************/
function miglaA_purgeCache(){
 global $wpdb; $msg = ""; $count = 0;
 
 $option_id = array(); 
 $option_id = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}options WHERE option_name LIKE 't_migla%'" );
 
 foreach( $option_id as $id )
 {
    $now = intval( Date("Ymd") );
	$option_name = $id->option_name;
    $date = substr(  $option_name , 7, 8);
	$date = intval( $date );
      
	if( ($now - $date) > 0  )
	{
	   delete_option( $option_name );
           //$msg .= $now ." ". $date. " ".($now - $date). " " . $option_name. "<br>";
           $count++;
	}

 }
 $msg .= $count . " caches erased";
 echo $msg;
 die();
}

function get_option_id( $op ){
  global $wpdb; $res =array();
  $sql = "SELECT option_id from {$wpdb->prefix}options WHERE option_name='".$op."'";
  $res = $wpdb->get_row($sql);
  return $res->option_id;
}

/**************         Main Page            ***********************/
/*******************************************************************/
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

function miglaA_totalOffAll()
{

 $toff = 0;

 global $wpdb;
 $data = array();
 $data = $wpdb->get_results( 
	$wpdb->prepare( 
		"SELECT {$wpdb->prefix}posts.ID FROM {$wpdb->prefix}posts
	  WHERE post_type = %s",
	  'migla_odonation'
        )
 );
 foreach( $data as $id )
 {
     $toff =  $toff + get_post_meta( intval( $id->ID ) , 'miglad_amount', true);
 }


$out = array();
$out[0] = $toff;

  echo json_encode ( $out );
  die();
}

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
	 "
       );
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

function miglaA_recentDonations() {
 global $wpdb;
 $data = array();
 $data = $wpdb->get_results( 
	$wpdb->prepare( 
         "SELECT {$wpdb->prefix}posts.ID FROM {$wpdb->prefix}posts 
	  WHERE {$wpdb->prefix}posts.post_type = %s 
          ORDER BY post_date DESC
          LIMIT 0,5
          " 
          , 'migla_donation'
        )
 );

 $out = array(); $key = ""; $row = 0; $id = 0; $state = "";
 foreach( $data as $id )
 {
    
    $addr = "";
    $out[$row]['time'] = get_post_meta( intval( $id->ID ) , 'miglad_time', true);
    $out[$row]['date'] = get_post_meta( intval( $id->ID ) , 'miglad_date', true);
    $out[$row]['name'] = get_post_meta( intval( $id->ID ) , 'miglad_firstname', true)." ".get_post_meta( intval( $id->ID ) , 'miglad_lastname', true);
    $out[$row]['amount'] = get_post_meta( intval( $id->ID ) , 'miglad_amount', true);

    $addr .= get_post_meta( intval( $id->ID ) , 'miglad_address', true);

    $addr .= "<br>". get_post_meta( intval( $id->ID ) , 'miglad_city', true);

    $s = get_post_meta( intval( $id->ID ) , 'miglad_state', true);
    $p = get_post_meta( intval( $id->ID ) , 'miglad_province', true);
    if( $s=='' || $s==false){
      $addr .= " ".get_post_meta( intval( $id->ID ) , 'miglad_province', true);
    }else if ($p=='' || $p==false){
      $addr .= " ".get_post_meta( intval( $id->ID ) , 'miglad_state', true);
    }

    $addr .= "<br>".get_post_meta( intval( $id->ID ) , 'miglad_country', true);
    $addr .= " ".get_post_meta( intval( $id->ID ) , 'miglad_postalcode', true);

    $out[$row]['address'] = $addr;     

$out[$row]['repeating'] = get_post_meta( intval( $id->ID ) , 'miglad_repeating', true);   
$out[$row]['anonymous'] = get_post_meta( intval( $id->ID ) , 'miglad_anonymous', true);   

    $row = $row + 1;
}
 
 echo json_encode($out);  
 die();
}


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
function miglaA_reset_theme() {
 //THEME SETTINGS
 update_option( 'migla_2ndbgcolor' , '#FAFAFA,1' ); 

 update_option( 'migla_bglevelcolor', '#eeeeee' ); 
 update_option( 'migla_borderlevelcolor', '#C1C1C1' ); 
 update_option( 'migla_borderlevel', '1' ); 

 update_option( 'migla_2ndbgcolorb' , '#DDDDDD,1,1' ); 
 update_option( 'migla_borderRadius' , '8,8,8,8' );

 $barinfo = "We have collected [total] of our [target] target. It is [percentage] of our goal for the [campaign] campaign";
 update_option('migla_progbar_info', $barinfo); 
 update_option( 'migla_bar_color' , '#428bca,1' );
 update_option( 'migla_progressbar_background', '#bec7d3,1');
 update_option( 'migla_wellboxshadow', '#969899,1, 1,1,1,1');	

 $arr = array( 'Stripes' => 'yes', 'Pulse' => 'yes', 'AnimatedStripes' => 'yes', 'Percentage' => 'yes' );
 update_option( 'migla_bar_style_effect' , $arr);
}

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
/**********************************************************************/
/***********************************************/
/*   GET OPTION Values from certain key
/***********************************************/
function migla_getme(){
  $r =  get_option($_POST['key']);
  echo $r;
  die();
}

function miglaA_update_me() {
   $key = $_POST['key'];
   $value = $_POST['value'];

  update_option( $key , $value);
   
   die();
}

function miglaA_update_barinfo() {
   $key = $_POST['key'];
   $value = $_POST['value'];

  update_option( $key , $value);
   
   die();
}

function miglaA_update_arr() {
   $key = $_POST['key'];
   $value = serialize( $_POST['value'] );

   $op = get_option( $key );

if( $op == false ){ add_option( $key , $value); }else{ update_option( $key , $value); }   
   
   die();
}

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


/**********************************************************************/
/********** GIVING LEVELS ***********************/
/**********************************************************************/

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
function miglaA_get_option() {
  echo json_encode( get_option( $_POST['option'] ) );
  die();
}

function miglaA_get_currencies() {
  $op =  get_option( 'migla_currencies' );
  echo json_encode( $op );
  die();
}

function miglaA_updateUndesignated(){
  update_option( 'migla_undesignLabel' , $_POST['new'] );
  updateACampaign($_POST['old'], $_POST['new']);
  die();
}

function miglaA_update_formOptions() {
  $formOp = array();
  $formOp = $_POST['options'];
  $state = array();
  $state = $_POST['state'];
  
  $idx = 0;
  foreach( (Array) $formOp as $f){
    update_option( $f , $state[$idx] );
	$idx++;
  }
  
  echo $state;
  die();
}


function miglaA_update_form() {
	global $wpdb;
	if( $_POST['values'] != '' ){
		$d = serialize($_POST['values']);
	}

	/*
	 $sql = "UPDATE {$wpdb->prefix}options SET option_value = '".$d."' WHERE {$wpdb->prefix}options.option_name ='migla_form_fields'";
	 $wpdb->query($sql);
	*/

	update_option('migla_form_fields', $_POST['values']);
	
  die();
}

function miglaA_reset_form() {
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


update_option('migla_form_fields', $fields);

  die();
}

/***********************************************/
/*             CAMPAIGN   FINISH Nov 21st */
/***********************************************/
function updateACampaign($old, $new){
	 global $wpdb;
	 $sql = "UPDATE {$wpdb->prefix}postmeta SET meta_value = '".$new."' WHERE meta_value ='".$old."'";
	 $wpdb->query($sql);
}


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


function miglaA_update_campaign() {
   $s = $_POST['stat'];

	if( $s == 'n')
	{
	   $newname = $_POST['name'];
	   $oldname = $_POST['oldname'];
	   $keyData = substr($_POST['key'],1);

	   $result = get_option('migla_campaign');
	   //sort($result);

		foreach( $result as $keyData => $value )
		{
		  if( strcmp($result[$keyData]['name'],$oldname)==0 ){
			 $result[$keyData]['name'] = $newname;
			 $result[$keyData]['target'] =  $result[$keyData]['target'];
			 $result[$keyData]['show'] = $result[$keyData]['show'];
			 break;
		  }
		}
	}else{
	
	   $target = $_POST['target'];
	   $keyData = $_POST['key'];

	   $result = get_option('migla_campaign');

		 $result[$keyData]['name'] = $result[$keyData]['name'];
		 $result[$keyData]['target'] =  $target;
		 $result[$keyData]['show'] = $result[$keyData]['show'];

	}
	
   update_option( 'migla_campaign' , $result );   

   die(); 
}


function miglaA_delete_campaign() {
   $keyData2 = $_POST['keyCampaign'];

   $result = get_option('migla_campaign');
   sort($result);
   $idx = 0;

   foreach( $result as $k => $v )
   {
	$ok = str_replace( "\\", "", $result[$k]['name']);
	$ok = str_replace( "'", "", $ok);
        if( $ok == $keyData2){
	 unset( $result[$k] );
        }
        $idx++;
   }

   update_option( 'migla_campaign' , $result );
   die(); 
}

/***********************************************/
/*           OFFLINE  FINISH BEFORE Nov 21st */
/***********************************************/
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

function miglaA_getOffDonation()
{
   $out = migla_get_offline();
   echo json_encode($out);
   die();
}

function miglaA_remove_donation() {
  migla_remove_donation( $_POST['list'] ) ; 
   die();
}

/***********************************************/
/*      Checkout  march */
/***********************************************/
function miglaA_checkout()
{
	 // Repack the Default Field Post
        $arr = $_POST['donorinfo'];
        $map = array();

        $map['miglad_anonymous'] = 'no'; 
        $map['miglad_repeating'] = 'no'; 

        foreach( (array)$arr as $d)
        {
          $map[ $d[0] ] = $d[1];
        }

        $transientKey =  "t_". $map['miglad_session_id'];

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
        add_option( $transientKey, $map);

    if(  $map['miglad_honoreeletter'] != '' )
    {
        $hletter = "t_". $transientKey. "hletter";
        add_option($hletter , $map['miglad_honoreeletter'] );
    }

    echo "";
    die();  
       
}


function miglaA_checkout_nonce()
{
  $msg ='';

 if ( wp_verify_nonce( $_POST['nonce'], 'migla_' ) )
 {
	 // Repack the Default Field Post
        $arr = $_POST['donorinfo'];
        $map = array();
        
        $map['miglad_anonymous'] = 'no'; 
        $map['miglad_repeating'] = 'no'; 

        foreach( (array)$arr as $d)
        {
          $map[ $d[0] ] = $d[1];
        }

        $transientKey =  "t_". $map['miglad_session_id'];

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
        $hletter = "t_". $transientKey. "hletter" ;
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
/*    Progress BAR draw on Form Nov 21st still continue */
/***********************************************/
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

/*********************************************************************/
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
function migla_getRemovedFields(){
 global $wpdb;
 $data = array(); $output = array(); $idx = 0;
 $data = $wpdb->get_results( "SELECT DISTINCT meta_key FROM {$wpdb->prefix}postmeta WHERE meta_key LIKE 'migla%' AND meta_value != ''" );
 foreach( $data as $id )
 {   $output[$idx] = $id->meta_key; $idx++; }

 $idx = 0;
 $formfield = (array)get_option('migla_form_fields');
 foreach( $formfield as $field )
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

function miglaA_report() 
{
  $IDs = array();
  $IDs = migla_get_ids_all() ; $fieldType = array(); $campaigns = array();
  $formfield = (array)get_option('migla_form_fields');

  $output = array();  $out = array(); $orphan = array(); $recurring = array();

  if( count($IDs) > 0)
  {   
   $row = 0;
  $removed = (array)migla_getRemovedFields();
   foreach( $IDs as $id )
   { 

    //execute through the form
    foreach( (array)$formfield as $field ){
     if( count($field['child']) > 0  ){
       foreach ( (array) $field['child'] as $c )
       {  
         if( $c['status'] != '0' ){
           $output[$row][ ($c['code'].$c['id']) ] = get_post_meta( intval( $id->ID ) , ($c['code'].$c['id']), true);
           $fieldType[$row][ ($c['code'].$c['id']) ] = $c['type'];
         }
       }
     }
    } 

    //Lets get Payment Data
    $paymentdata = (array)get_post_meta( intval( $id->ID ) , 'miglad_paymentdata', true);

      //Checking missing session id
      $sessionid = get_post_meta( intval( $id->ID ) ,'miglad_session_id' , true);
      if( $sessionid == false ){
         add_post_meta( intval( $id->ID ) ,'miglad_session_id' , $paymentdata['custom'] );
      }
      if( $sessionid == '' ){
         update_post_meta( intval( $id->ID ) ,'miglad_session_id' , $paymentdata['custom'] );
      }

      $amount = get_post_meta( intval( $id->ID ) ,'miglad_amount' , true);
      if( $amount == false ){
         add_post_meta( intval( $id->ID ) ,'miglad_amount' , $paymentdata['mc_gross'] );
      }
      if( $amount == '' ){
         update_post_meta( intval( $id->ID ) ,'miglad_amount' , $paymentdata['mc_gross']  );
      }


    //Paypal Data
    $output[$row][ 'paypaldata' ] = $paymentdata;
    $fieldType[$row][ 'paypaldata' ] = 'text';
    
    $c = get_post_meta( intval( $id->ID ) , 'miglad_campaign', true);
    $output[$row][ 'miglad_campaign' ] = str_replace( "[q]", "'" ,$c);
    $fieldType[$row][ 'miglad_campaign' ] = 'select';

      $output[$row]['miglad_state'] = get_post_meta( intval( $id->ID ) , 'miglad_state', true); $fieldType[$row][ 'miglad_state' ] = 'text';
      $output[$row]['miglad_province'] = get_post_meta( intval( $id->ID ) , 'miglad_province', true); $fieldType[$row][ 'miglad_province' ] = 'text';

      $output[$row]['miglad_honoreestate'] = get_post_meta( intval( $id->ID ) , 'miglad_honoreestate', true); $fieldType[$row]['miglad_honoreestate'] = 'text';
      $output[$row]['miglad_honoreeprovince'] = get_post_meta( intval( $id->ID ) , 'miglad_honoreeprovince', true); $fieldType[$row]['miglad_honoreeprovince'] = 'text';

      $output[$row][ 'miglad_date' ] = get_post_meta( intval( $id->ID ) , 'miglad_date', true); $fieldType[$row]['miglad_date'] = 'text';
      $output[$row][ 'miglad_time' ] = get_post_meta( intval( $id->ID ) , 'miglad_time', true); $fieldType[$row]['miglad_time'] = 'text';
      $output[$row][ 'miglad_timezone' ] = get_post_meta( intval( $id->ID ) , 'miglad_timezone', true); $fieldType[$row]['miglad_timezone'] = 'text';

      $output[$row]['id'] = $id->ID  ; $fieldType[$row]['id'] = 'text';
      $output[$row]['remove'] = "<input type='hidden' name='".$id->ID."' class='removeRow' /><i class='fa fa-trash'></i>"; $fieldType[$row]['remove'] = 'text';
      $output[$row]['detail'] = "<input class='mglrec' type=hidden name='".$row."' >"; $fieldType[$row]['detail'] = 'text';

   //////PAYMENT DATA
 $output[$row]['miglad_session_id'] = get_post_meta( intval( $id->ID ) ,'miglad_session_id' , true);  $fieldType[$row]['miglad_session_id'] = 'text';
 $output[$row]['miglad_paymentmethod'] = get_post_meta( intval( $id->ID ) ,'miglad_paymentmethod' , true); $fieldType[$row]['miglad_paymentmethod'] = 'text';
 $output[$row]['miglad_transactionType'] = get_post_meta( intval( $id->ID ) ,'miglad_transactionType' , true); $fieldType[$row]['miglad_transactionType'] = 'text';
 $output[$row]['miglad_transactionId'] = get_post_meta( intval( $id->ID ) ,'miglad_transactionId' , true); $fieldType[$row]['miglad_transactionId'] = 'text';

  //add recurring table
  $subscr_id = "";
  $subscr_id = $paymentdata['subscr_id']; 

  $output[$row]['miglad_subscr_id'] = $subscr_id ; $fieldType[$row]['miglad_subscr_id'] = 'text';

  $length = sizeof($recurring[ $subscr_id ]); 
  $new_input = array('date' => $output[$row][ 'miglad_date' ], 'time' => $output[$row][ 'miglad_time' ]  );
  $recurring[ $subscr_id ][ $length ] = $new_input;

  //removed fields
   foreach( (array)$removed as $r){
     if( $r != null ||  $r != '' )
     { 
       $orphan[$row][$r] =  get_post_meta( intval( $id->ID ) , $r, true); 
       $fieldType[$row][$r] = 'text';
     }else{  }
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
  die();
}

function miglaA_get_data_for_edit_form(){
  $out = array();
  $out[0] = get_option('migla_world_countries');
  $out[1] = get_option('migla_US_states');
  $out[2] = get_option('migla_Canada_provinces');

  echo json_encode($out);
  die();
}
///////////////////////////////////////////////////////////////////
function miglaA_get_number_and_total() {
  $out = array();
  $out = migla_number_and_total( $_POST['campaign'] );
  
  echo json_encode($out);
 
  die();
}

/**********************************************************************/
/************************* TEST AJAX ***********************/

function miglaA_test_email(){
  $test = test_email( $_POST['email'], $_POST['emailname'], $_POST['testemail']);
  if( $test ){ echo "Email has been sent to ".$_POST['testemail']; } else { echo "Sending email failed"; }
  die();
}

function miglaA_test_hEmail(){
  $test = migla_test_hletter( $_POST['email'], $_POST['emailname'], $_POST['testemail']);
  if( $test ){ echo "Email has been sent to ".$_POST['testemail']; } else { echo "Sending email failed"; }
  die();
}

/**********************************************************************/
/*        UPDATING & RESTORING TASKS                                  */   
/*********************************************************************/
function miglaA_change_donation()
{
  $post_id    = $_POST['post_id'];
  $arrayData = (array)$_POST['arrayData'];
  
  $keys = array_keys( $arrayData ); $i = 0;
  foreach( (array)$arrayData as $value)
  {
     if( $value[1] == '' || empty($value[1]) ){
     }else{
       update_post_meta( $post_id , $value[0], $value[1] );
     }
     $i++;
  }

   echo "done";
   die();
}

//restore from transient
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