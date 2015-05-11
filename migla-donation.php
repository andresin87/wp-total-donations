<?php
/*
 Plugin Name: Total Donations
 Plugin URI: http://calmar-webmedia.com/testing-area/wp-plugin-dev
 Description: A plugin for accepting donations.
 Version: 1.4.5
 Author: Binti Brindamour and Astried Silvanie
 Author URI: http://calmar-webmedia.com/
 License: Licensed
*/

require_once 'migla-donation-admin.php';
require_once 'migla-functions.php';
require_once 'migla-currency.php';
require_once 'migla-geography.php';
require_once 'migla-timezone.php';
require_once 'migla-locale.php';
require_once 'migla-icon-style.php';

require_once 'migla-donation-widget.php';
require_once 'migla-top-donor-widget.php';
require_once 'migla-bar-widget.php';

/** 1.CALL HOOK FILES that require on plugin main page ********************************************************/ 
 function migla_call_hooks(){
   global $wpdb;
   $require_files = array();
   $require_files = $wpdb->get_results( 
	 $wpdb->prepare( 
	  "SELECT * FROM {$wpdb->prefix}options WHERE option_name like %s" ,
	  'miglarequire%'
      ) 
    ); 
	
	if( count($require_files) > 0){
      foreach( (array)$require_files as $f ){
	    $url = $f->option_value;
		if( $url == '' ){ }else{ include( dirname(__FILE__). $url);}
      }
	}
 }

 migla_call_hooks();
 
 /*  This function call all hooks on front end form Shortcode */
  function migla_hook_action_1_array(){
   global $wpdb;
   $hookactions = array();
   $hookactions = $wpdb->get_results( 
	 $wpdb->prepare( 
	  "SELECT * FROM {$wpdb->prefix}options WHERE option_name like %s" ,
	  'miglaactions_1%'
      )
    ); 

	$out = array(); $i = 0;
    foreach( (array)$hookactions as $ha ){
	  if( $ha != '')
	  {
         $out[$i]['action_name'] = ($ha->option_name);
	     $varx = explode(';' , $ha->option_value);
         $out[$i]['action_function'] = $varx[0];
         $out[$i]['action_priority'] = $varx[1];
         $out[$i]['action_num_args'] = $varx[2];
		 $out[$i]['action_purpose'] = $varx[3];

	     $i++;
	   }
    }	
	
    return $out;
 }
/*****************************************************************************/ 


/******************************** LANGUAGES  ***************************************************/
function migla_donate_plugins_loaded() {
	load_plugin_textdomain( 'migla-donation', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'migla_donate_plugins_loaded' );

/*****************************************************************************/ 


function migla_admin_notice() {
  $payment_method = get_option('migla_payment');
  $business_email = get_option('migla_paypal_emails');

 $email1 = get_option( 'migla_replyTo' );
 $email2 = get_option( 'migla_replyToName' );
 $email3 = get_option( 'migla_notif_emails' );

  $msg = "";
  $ready = true;

  if( $business_email == '' ){
    $ready = false;  
    $msg .= "<p>". __("Please fill in your paypal account details in Total Donations. It is required to begin accepting donations. Go to ","migla-donation");
    $msg .= "<a class='' href='".get_admin_url()."admin.php?page=migla_donation_paypal_settings_page'>". __(" Paypal Settings","migla-donation"). "</a></p>";
  }

  if( $payment_method == 'sandbox' ){
    $ready = false;  
    $msg .= "<p>". __("Total Donations is currently in sandbox testing mode. To switch to production mode, please go to ","migla-donation");
    $msg .= "<a class='' href='".get_admin_url()."admin.php?page=migla_donation_paypal_settings_page'>". __(" Paypal Settings","migla-donation"). "</a>". __(" and change the payment method to Paypal","migla-donation"). "</p>";
  }

  if( $email1 == '' || $email2 == '' || $email3 == '' ){
    $ready = false;  
    $msg .= "<p>". __("Please fill notification email, your email name and your email address. Go to ","migla-donation");
    $msg .= "<a class='' href='".get_admin_url()."admin.php?page=migla_donation_settings_page'>". __(" Settings","migla-donation"). "</a></p>";
  }

  if( $ready == false ){
   echo "<div class='error'><h2>". __("Warning ","migla-donation")."</h2>";
   echo $msg;
   echo "</div>";
 }
}


function migla_enqueue_style( $style ){
  if( wp_script_is( $style, 'queue' ) ){}else{
    wp_enqueue_style( $style );
  }
}

function migla_enqueue_script( $script ){
  if( wp_script_is( $script, 'queue' ) ){}else{
    wp_enqueue_style( $script );
  }
}

/*************LOAD SCRIPTS AND STYLE*******************************************************************/
function migla_load_admin_scripts($hook) { 
	
	//menu     : toplevel_page . [slug name]
	//sub menu : [menu name on wp-admin] . _page_ . [slug name]
	$migla_is_in_the_hook = ( $hook == ("toplevel_page_migla_donation_menu_page") || 
          substr($hook, 0, 21) == 'total-donations_page_'
	 );

	if( $migla_is_in_the_hook ) 
	{


         /***************chek if it is cleaning day****************************/
         /*
         $cc = get_option('migla_daybeforeclean');
         if( strcmp( date('d'), "10" ) == 0 && $cc == 'no' ){
            //echo "<br>Cleaning....". date("Ymd");  
            purgeTransient();
            purgeTransient2();
            update_option('migla_daybeforeclean', 'done');
         }
         if( strcmp( date('d'), "10" ) != 0 ){
            update_option('migla_daybeforeclean', 'no');
         }
         */
         /***************chek if it is cleaning day****************************/
	  
        add_action( 'admin_notices', 'migla_admin_notice' );

        migla_enqueue_script('jquery');
        migla_enqueue_script('jquery-ui-core');

  	  wp_enqueue_script( 'miglageneric-js', plugins_url( 'totaldonations/js/migla_generic.js' , dirname(__FILE__)) );

          wp_enqueue_script( 'respond.min.js', plugins_url( 'totaldonations/js/respond.min.js' , dirname(__FILE__)) );
          	  
 	  wp_enqueue_script( 'miglabootstrap.min.js', plugins_url( 'totaldonations/js/bootstrap.min.js' , dirname(__FILE__)) );
	   

	if( $hook == ("toplevel_page_migla_donation_menu_page")) 
        {
           wp_enqueue_script( 'migla-jschart-js', plugins_url( 'totaldonations/js/Chart.js' , dirname(__FILE__)) );
           wp_enqueue_script( 'migla-main-js', plugins_url( 'totaldonations/js/migla_main.js' , dirname(__FILE__)) );
      
           wp_localize_script( 'migla-main-js', 'miglaAdminAjax',
		array( 'ajaxurl' => plugins_url( 'totaldonations/the-ajax-caller.php' , dirname(__FILE__)),
                'nonce' => wp_create_nonce( 'migla-donate-nonce' )
 	   ));
        }

	if( strcmp( $hook , 'total-donations_page_migla_donation_campaigns_page') == 0 ) 
        {

           wp_enqueue_script( 'migla-campaign-js', plugins_url( 'totaldonations/js/migla_campaign.js' , dirname(__FILE__)) );

 wp_localize_script( 'migla-campaign-js', 'miglaAdminAjax',
		array( 'ajaxurl' => plugins_url( 'totaldonations/the-ajax-caller.php' , dirname(__FILE__)),
                'nonce' => wp_create_nonce( 'migla-donate-nonce' )
 	   ));
        }

	if( $hook == ('total-donations_page_migla_donation_help') ) 
        {

           wp_enqueue_script( 'migla-help-js', plugins_url( 'totaldonations/js/migla_help.js' , dirname(__FILE__)) );

 wp_localize_script( 'migla-help-js', 'miglaAdminAjax',
		array( 'ajaxurl' => plugins_url( 'totaldonations/the-ajax-caller.php' , dirname(__FILE__)),
                'nonce' => wp_create_nonce( 'migla-donate-nonce' )
 	   ));
        }
	  
	if( $hook == ('total-donations_page_migla_donation_form_options_page') ) 
        {

           wp_enqueue_script('media-upload');
          wp_enqueue_script('thickbox');
          wp_enqueue_style('thickbox');

          wp_enqueue_script( 'migla-form-settings-js',  plugins_url( 'totaldonations/js/migla_form_settings.js' , dirname(__FILE__)) , 
		  array('jquery-ui-core','jquery-ui-sortable','jquery-ui-draggable','jquery-ui-droppable', 'jquery','media-upload','thickbox') );
	   
           wp_localize_script( 'migla-form-settings-js', 'miglaAdminAjax',
		array( 'ajaxurl' =>  plugins_url( 'totaldonations/the-ajax-caller.php' , dirname(__FILE__)),
                'nonce' => wp_create_nonce( 'migla-donate-nonce' )
 	   ));

        }

	if( $hook == ('total-donations_page_migla_donation_settings_page') ) 
        {

           wp_enqueue_script( 'migla-settings-js', plugins_url( 'totaldonations/js/migla_settings.js' , dirname(__FILE__)) );
	   wp_localize_script( 'migla-settings-js', 'miglaAdminAjax',
		array( 'ajaxurl' =>  plugins_url( 'totaldonations/the-ajax-caller.php' , dirname(__FILE__)),
'nonce' => wp_create_nonce( 'migla-donate-nonce' )
 	   ));
        }


	if( $hook == ('total-donations_page_migla_donation_paypal_settings_page') ) 
        {

           wp_enqueue_script( 'migla-settings-js', plugins_url( 'totaldonations/js/migla_paypal_settings.js' , dirname(__FILE__)) );
	   wp_localize_script( 'migla-settings-js', 'miglaAdminAjax',
		array( 'ajaxurl' =>  plugins_url( 'totaldonations/the-ajax-caller.php' , dirname(__FILE__)),
'nonce' => wp_create_nonce( 'migla-donate-nonce' )
 	   ));
        }

        
	if( $hook == ('total-donations_page_migla_offline_donations_page')  ) 
        {
  	  
         wp_enqueue_script( 'migla-offline-js', plugins_url( 'totaldonations/js/migla_offline.js' , dirname(__FILE__)) ,
             array('jquery-ui-core', 'jquery-ui-datepicker') );

           wp_enqueue_script( 'migla-offlineTables-js', plugins_url( 'totaldonations/js/jquery.dataTables.min.js' , dirname(__FILE__)) );
	
   wp_localize_script( 'migla-offline-js', 'miglaAdminAjax',
		array( 'ajaxurl' => plugins_url( 'totaldonations/the-ajax-caller.php' , dirname(__FILE__)),
                'nonce' => wp_create_nonce( 'migla-donate-nonce' )
 	   ));

        wp_register_style( 'migla-dataTables-css', plugins_url( 'totaldonations/css/jquery.dataTables.min.css' , dirname(__FILE__)) );
	  wp_enqueue_style( 'migla-dataTables-css' );
       wp_register_style( 'migla-dataTables2-css', plugins_url( 'totaldonations/css/extra.css' , dirname(__FILE__)) );
	  wp_enqueue_style( 'migla-dataTables2-css' ); 	
	  
        }        

	if( $hook == ('total-donations_page_migla_reports_page') ) 
        {
           wp_enqueue_script( 'migla-reports-js', plugins_url( 'totaldonations/js/migla_reports.js' , dirname(__FILE__)) ,
               array('jquery-ui-core', 'jquery-ui-datepicker')  );
           wp_enqueue_script( 'migla-dataTables-js', plugins_url( 'totaldonations/js/jquery.dataTables.min.js' , dirname(__FILE__)) );

 wp_localize_script( 'migla-reports-js', 'miglaAdminAjax',
		array( 'ajaxurl' => plugins_url( 'totaldonations/the-ajax-caller.php' , dirname(__FILE__)),
                'nonce' => wp_create_nonce( 'migla-donate-nonce' )
 	   ));
 	   
        wp_register_style( 'migla-dataTables-css', plugins_url( 'totaldonations/css/jquery.dataTables.min.css' , dirname(__FILE__)) );
	  wp_enqueue_style( 'migla-dataTables-css' ); 
       wp_register_style( 'migla-dataTables2-css', plugins_url( 'totaldonations/css/extra.css' , dirname(__FILE__)) );
	  wp_enqueue_style( 'migla-dataTables2-css' ); 	
 
        }


	if( $hook == ('total-donations_page_migla_donation_custom_theme') ) 
        {
          wp_enqueue_script( 'jminicolor.js', plugins_url( 'totaldonations/js/jquery.minicolors.js' , dirname(__FILE__)) );
	  wp_register_style( 'jminicolor_css', plugins_url( 'totaldonations/css/jquery.minicolors.css' , dirname(__FILE__)) );
	  wp_enqueue_style( 'jminicolor_css' );

          wp_enqueue_script( 'migla-color-themes-js', plugins_url( 'totaldonations/js/migla_color_themes.js' , dirname(__FILE__)) );

	   wp_localize_script( 'migla-color-themes-js', 'miglaAdminAjax',
		array( 'ajaxurl' => plugins_url( 'totaldonations/the-ajax-caller.php' , dirname(__FILE__)),
           'nonce' => wp_create_nonce( 'migla-donate-nonce' )
 	   ));
        }
        


          ////STYLE//////////////////////////////////////////////////////////////////////
          wp_register_style( 'miglabootstrap-css', plugins_url( 'totaldonations/css/bootstrap.min.css' , dirname(__FILE__)) );
	  wp_enqueue_style( 'miglabootstrap-css' );

	  wp_register_style( 'migla_admin_css', plugins_url( 'totaldonations/css/admin_migla.css' , dirname(__FILE__)) );
	  wp_enqueue_style( 'migla_admin_css' );
	  
          wp_register_style( 'miglafont-awesome-css', plugins_url( 'totaldonations/css/font-awesome/css/font-awesome.min.css' , dirname(__FILE__)) );
	  wp_enqueue_style( 'miglafont-awesome-css' );

	  }else{

	  return;
	}
	
}
add_action('admin_enqueue_scripts', 'migla_load_admin_scripts');

/************************ INITIALIAZE VARIABLES & ACTIVATED *********************************************/
function miglainit_option( $key, $value )
{
  $op = get_option($key);
  if( $op == false ){ 
   add_option( $key , $value );
  }
}


function migla_donation_active() {

/////////FORM////////////////////////
$fields = array (
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
        'title' => 'És en nom d[q]algú?',
        'child' => array(
                   '0' => array( 'type'=>'checkbox','id'=>'memorialgift', 'label'=>"És una aportació com a entitat??", 'status'=>'1', 'code' => 'miglad_' ),
                   '1' => array( 'type'=>'text','id'=>'honoreename', 'label'=>"Nom de la entitat", 'status'=>'1', 'code' => 'miglad_' ),
                   '2' => array( 'type'=>'text','id'=>'honoreeemail', 'label'=>"Correu", 'status'=>'1', 'code' => 'miglad_' ),
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
        'title' => 'És una aportació com a entitat?',
        'child' => array(
                   '0' => array( 'type'=>'text','id'=>'employer', 'label'=>'Nom del col·laborador', 'status'=>'1', 'code' => 'miglad_' ),
                   '1' => array( 'type'=>'text','id'=>'occupation', 'label'=>'Càrrec', 'status'=>'1', 'code' => 'miglad_' )
                 ),
        'parent_id' => 'NULL',
        'depth' => 3,
        'toggle' => '1'
    )        
);

miglainit_option( 'migla_daybeforeclean' , 'no' );

/////FORM
miglainit_option( 'migla_form_fields', $fields ) ;
miglainit_option('migla_undesignLabel', 'undesignated');
miglainit_option('migla_hideUndesignated', 'no');

//CAMPAIGN
miglainit_option( 'migla_campaign' , '' );

//THEME SETTINGS
 miglainit_option( 'migla_2ndbgcolor' , '#fafafa,1' ); 
 miglainit_option( 'migla_2ndbgcolorb' , '#eeeeee,1,1' ); 
 miglainit_option( 'migla_borderRadius' , '8,8,8,8' );

 $barinfo = "Hem aconseguit [total] dels [target] necessaris. Això comporta un [percentage] del total per portal a terme la campanya [campaign].";
 miglainit_option('migla_progbar_info', $barinfo); 
 miglainit_option( 'migla_bar_color' , '#428bca,1' );
 miglainit_option( 'migla_progressbar_background', '#bec7d3,1');
 miglainit_option( 'migla_wellboxshadow', '#969899,1, 1,1,1,1');	

 $arr = array( 'Stripes' => 'yes', 'Pulse' => 'yes', 'AnimatedStripes' => 'yes', 'Percentage' => 'yes' );
 miglainit_option( 'migla_bar_style_effect' , $arr);

////////// EMAILS ////////////////////////////////////////////////////////
$thankyou = "Dear [firstname] [lastname],[newline][newline]
Thank you for your donation of [amount] on the [date]. Your help is deeply appreciated and your generosity will make an immediate difference to our cause. We'd like to extend our heartfelt thanks for your contribution. We appreciate your generosity.";

  miglainit_option( 'migla_thankyoupage' , $thankyou);
  
  miglainit_option( 'migla_thankyouemail' , $thankyou);
  miglainit_option('migla_thankSbj', 'Thank you for your donation'); 
  miglainit_option('migla_thankBody',   $thankyou); 
  miglainit_option('migla_thankRepeat', 'This donation will be repeated each month.[newline]'); 
  miglainit_option('migla_thankAnon', 'Your name will not appear in public.[newline][newline]'); 
  miglainit_option('migla_thankSig', 'Sincerely, [newline]Our team'); 

//Honoree's letter
 miglainit_option('migla_honoreESbj', 'A donation was made in your name.'); 
 $honoreebody = "Dear [honoreename],[newline][newline]We wanted to let you know that a donation has been made in honor of you for $[amount] on [date] by [firstname] [lastname]. Thank you for your support.";

 miglainit_option('migla_honoreEBody', $honoreebody); 
 miglainit_option('migla_honoreECustomIntro', '[firstname] [lastname] has included a message for you below:[newline][newline]'); 
 miglainit_option('migla_honoreERepeat', 'This donation will be repeated each month.[newline]'); 
 miglainit_option('migla_honoreEAnon', 'Your name will not appear in public.[newline][newline]'); 
 miglainit_option('migla_honoreESig', 'Sincerely, [newline]Our team'); 

 miglainit_option( 'migla_replyTo' , '');
 miglainit_option( 'migla_replyToName' , '');
 miglainit_option( 'migla_notif_emails','');


////////////AMOUNTS//////////////////////////////
  $f['10'] = '10';
  $f['25'] = '25';
  $f['50'] = '50';
  $f['100'] = '100';
  miglainit_option( 'migla_amounts' , $f);

//////CURRENCY & COUNTRY///////////////
miglainit_option( 'migla_world_countries', (array)migla_get_world_countries() );
miglainit_option( 'migla_default_country', 'Canada');
miglainit_option( 'migla_US_states', (array)migla_get_US_states() );
miglainit_option( 'migla_Canada_provinces', (array)migla_get_Canada_provinces() );

miglainit_option( 'migla_currencies' , (array)migla_get_currency_array() ); //array of array
miglainit_option( 'migla_default_currency' , 'CAD');
miglainit_option( 'migla_thousandSep' , ',');
miglainit_option( 'migla_decimalSep' , '.');
miglainit_option( 'migla_curplacement' , 'before');
miglainit_option( 'migla_showDecimalSep' , 'yes');

///////////TimeZone////////////////////////
miglainit_option( 'migla_timezones', (array)migla_get_timezone() );
miglainit_option( 'migla_default_timezone', 'Server Time' );

/////////Paypal//////////////////////////////
miglainit_option( 'migla_paypal_emails' , '');
miglainit_option( 'migla_payment' , 'sandbox');
miglainit_option('migla_paypalitem', 'donation' );
miglainit_option('migla_paypalcmd', 'donation' );


////////////BUTTON//////////////////////////////////
miglainit_option('migla_paypalbutton', 'English');
miglainit_option('migla_paypalcssbtnstyle', 'Default');
miglainit_option('migla_paypalcssbtntext', 'Donate Now');
miglainit_option('migla_paypalcssbtnclass', '');
miglainit_option('migla_paypalbuttonurl', '');

miglainit_option( 'migla_form_url', '' );

miglainit_option('migla_show_recover', 'no') ;
miglainit_option('migla_use_nonce', 'no');
miglainit_option('migla_delete_settings', 'no');


}

register_activation_hook( __FILE__, 'migla_donation_active' );


function migla_donation_deactived(){


   if( get_option('migla_delete_settings') == 'yes' )
   {
      migla_delete_all_settings();
   }
}

register_deactivation_hook( __FILE__, 'migla_donation_deactived' );

//////////////////////////////////////////////////////////////////////////////////////////////////

function register_session(){
    if( !session_id() )
        session_start();
}

 
function migla_get_current_url()
{
   if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on'){
	$http = 'https';
    }else{
	$http = 'http';
    }

    $currentUrl = $http . '://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
    return $currentUrl;
} 


/***********************************************************************************/
/*             CLASSES FOR FORM SHORTCODES */
/***********************************************************************************/

class Migla_Shortcode {
	  static $add_script; static $nonce;

	 function Migla_Shortcode()
		{
			
		}

	static function init() {
	  
	  add_shortcode('totaldonations', array(__CLASS__, 'handle_shortcode'));

	  add_action('init', array(__CLASS__, 'register_script'));
	  add_action('wp_footer', array(__CLASS__, 'print_script'));

	}


	static function handle_shortcode($atts) {
	   self::$add_script = true;    
	   
	   $content = "";

	  update_option( 'migla_form_url', migla_current_page_url() );
	  
	  require_once 'migla-form.php';

	  $isThank = false;
	  	  
	  /*********** New One Feb 18 *********************************************************************/
	  
	  if ( isset( $_GET['thanks'] ) && $_GET['thanks'] == 'thanks' && isset( $_GET['id'] ) ) {
		$isThank = true;
	  } else if( isset( $_POST['thanks'] ) && $_POST['thanks'] != '' ) { 
		$isThank = true;
	  }
	  
	  if( isset( $_POST['thanks'] ) && $_POST['thanks'] == 'widget_bar' ){ $isThank = false; }

	  /********************************************************************************************/
 	  
	   /*********This a thank you page ****************************************/
	   if ( $isThank ) {
	   
			$str = get_option( 'migla_thankyoupage' );
			$trimquote = str_replace( '\"', '' ,$str );
			
			if( $_POST['thanks'] == 'testThanks' )  //Testing thank You Page
			{
		 
			   $placeholder = array( '[firstname]','[lastname]' ,'[amount]' ,'[date]', '[newline]' );
			   $replace = array( 'John','Doe' ,'100' ,'September 10th, 2015', '<br>' );
			   $page =  str_replace($placeholder, $replace, $trimquote);
			   
			}else{
			
			   $page = "";
			   $transientKey = "t_". esc_attr( $_GET['id'] );
			   $postData =  get_option( $transientKey );
			   
			   if(  $postData == false ){
			   
				   $field = explode(" ", esc_attr( $_GET['payment_date'] ) );
				   $day = str_replace( ",", "", $field[2] );
				   $month = "";
				   if( $field[1]=="Jan" ){ $month="01"; }
				   else if( $field[1]=="Feb" ){ $month="02"; }
				   else if( $field[1]=="March" ){ $month="03"; }
				   else if( $field[1]=="April" ){ $month="04"; }
				   else if( $field[1]=="May" ){ $month="05"; }
				   else if( $field[1]=="June" ){ $month="06"; }
				   else if( $field[1]=="July" ){ $month="07"; }
				   else if( $field[1]=="Aug" ){ $month="08"; }
				   else if( $field[1]=="Sept" ){ $month="09"; }
				   else if( $field[1]=="Oct" ){ $month="10"; }
				   else if( $field[1]=="Nov" ){ $month="11"; }
				   else if( $field[1]=="Dec" ){ $month="12"; }
				   $temp = $month."/".$day."/".$field[3];

				   $amount = esc_attr( $_GET['payment_gross'] );
				   if( $amount == '' ){ 
					 $amount = esc_attr( $_GET['mc_gross'] ); 
				   }

				   $placeholder = array( '[firstname]','[lastname]' ,'[amount]' ,'[date]' , '[newline]' );
				   
				   $replace = array( esc_attr( $_GET['first_name'] ), esc_attr( $_GET['last_name'] ) , $amount , date("F jS, Y", strtotime( $temp ) ) , '<br>'  );

				   $page =  str_replace($placeholder, $replace, $trimquote);
				   
				}else{
				
				   $placeholder = array( '[firstname]','[lastname]' ,'[amount]' ,'[date]' , '[newline]' );
				   
				   $replace = array( $postData['miglad_firstname'], $postData['miglad_lastname'] , $postData['miglad_amount'] , 
								  date("F jS, Y", strtotime($postData['miglad_date']) ) , '<br>'  );

				   $page =  str_replace($placeholder, $replace, $trimquote);
				}	

			}

			$content .= $page ;

	   }else{  /******** This is not thank you page then call migla_draw_form function *************/

		// Open the form
		$content .= "<div style='clear:both' class='bootstrap-wrapper'><div class='container-fluid' id='wrap-migla'>";
		$content .= "<div id='migla_donation_form' style='' >";

		// Save the session ID as a hidden input
		$session_id = 'migla' . date("Ymdhms"). "_" . rand() ;
			//$session_id = "migla858224695_20150103030114"; //testing repeating

		$content .= "<input type='hidden' name='migla_session_id' value='".$session_id."' />";
		$content .= migla_drawForm(  $session_id );
		$content .= "</div>";
		
		$content .= "</div>";	
		$content .= migla_hidden_form( $session_id );
		$content .= "</div>";

                $content .= migla_modal_box();
		$content .= "<p></p>";
	  }

	   return $content;
			
	}

	static function register_script() {
			  wp_register_style( 'migla-front-end', plugins_url( 'totaldonations/css/migla-frontend.css' , dirname(__FILE__)) );
	}

	static function print_script( ) {
			if ( ! self::$add_script )
				return;
				
				
		/******************************************** call all actions ************************************************/
		$array_of_action = migla_hook_action_1_array();
		if( empty($array_of_action) ){
		}else{
		  foreach( $array_of_action as $act )
		  {
			if( $act['action_purpose'] == 'add' ){
			 add_action( $act['action_name'] , $act['action_function'], $act['action_priority'], $act['action_num_args']);   
			 do_action( $act['action_name'], '', plugins_url( 'totaldonations/the-ajax-caller.php' , dirname(__FILE__)), '');
			}
		  }
		}
		/*******************************************************************************************************************/ 
				

		migla_enqueue_script( 'jquery' );
		wp_enqueue_script( 'respond.min.js', plugins_url( 'totaldonations/js/respond.min.js' , dirname(__FILE__)) );

           if( get_option('migla_use_nonce') == 'yes' ){

               self::$nonce = wp_create_nonce('migla_');

		wp_enqueue_script( 'migla-checkout-js', plugins_url( 'totaldonations/js/migla_checkOut_nonce.js', dirname(__FILE__)), array( 'jquery' ), false, true );

		   wp_localize_script( 'migla-checkout-js', 'miglaAdminAjax',
			array( 'ajaxurl' => plugins_url( 'totaldonations/the-ajax-caller.php' , dirname(__FILE__) ),
                          'notifyurl' => migla_get_notify_url(),
                          'successurl' => migla_get_succesful_url(),
                          'nonce' => self::$nonce    
		    ));	

		wp_enqueue_script( 'migla-donation-js', plugins_url( 'totaldonations/js/migla_form.js', dirname(__FILE__) ), array( 'jquery' ), false, true );

		   wp_localize_script( 'migla-donation-js', 'miglaAdminAjax',
			array( 'ajaxurl' => plugins_url( 'totaldonations/the-ajax-caller.php' , dirname(__FILE__) ),
                          'notifyurl' => migla_get_notify_url(),
                          'successurl' => migla_get_succesful_url(),
                          'nonce' => self::$nonce
		   ));		

           }else{

		wp_enqueue_script( 'migla-checkout-js', plugins_url( 'totaldonations/js/migla_checkOut.js', dirname(__FILE__)), array( 'jquery' ), false, true );

		   wp_localize_script( 'migla-checkout-js', 'miglaAdminAjax',
			array( 'ajaxurl' => plugins_url( 'totaldonations/the-ajax-caller.php' , dirname(__FILE__) ),
                          'notifyurl' => migla_get_notify_url(),
                          'successurl' => migla_get_succesful_url()   
		   ));	

		wp_enqueue_script( 'migla-donation-js', plugins_url( 'totaldonations/js/migla_form.js', dirname(__FILE__) ), array( 'jquery' ), false, true );

		   wp_localize_script( 'migla-donation-js', 'miglaAdminAjax',
			array( 'ajaxurl' => plugins_url( 'totaldonations/the-ajax-caller.php' , dirname(__FILE__) ),
                          'notifyurl' => migla_get_notify_url(),
                          'successurl' => migla_get_succesful_url()   
		   ));		

           }	  
		  wp_enqueue_style( 'migla-front-end' );

	}
} //End of Migla_Shortcode Class

Migla_Shortcode::init();


/*****    Migla_ProgressBar_Shortcode      ********/

class Migla_ProgressBar_Shortcode {
  static $progressbar_script;

 static function init() {
  add_shortcode('totaldonations-progress-bar', array(__CLASS__, 'handle_shortcode'));
  add_action('init', array(__CLASS__, 'register_script'));
  add_action('wp_footer', array(__CLASS__, 'print_script'));
 }

 static function handle_shortcode($atts){

   self::$progressbar_script = true;

   extract(shortcode_atts(array(
        'campaign' => '', 
        'button' => 'no',
        'button_text' => 'Donate Now',
        'text' => ''
    ), $atts )
   ); 

$args = shortcode_atts( 
    array(
        'campaign' => '', 
        'button' => 'no',
        'button_text' => 'Donate Now',
        'text' => ''
    ), 
    $atts
);

 $draw = "";

 if( $atts == null || count($atts) <= 0)
 {
    $draw = migla_shortcode_progressbar( "", "no" );
 }else
 {	
   if( $args['campaign'] == "" ){	
     $draw = migla_shortcode_progressbar( "", "no", "" );
   }else{
      $draw = migla_shortcode_progressbar( $args['campaign'], $args['button'] , $args['button_text'], $args['text'] );
   }
  }
 return $draw;
 
}//function

 static function register_script() {
  if( wp_script_is( 'mg_progress-bar', 'registered' ) ){
  }else{
    wp_register_style( 'mg_progress-bar', plugins_url( 'totaldonations/css/mg_progress-bar.css' , dirname(__FILE__)) );
  }
 }

 static function print_script() {
   if ( ! self::$progressbar_script )
	return;

  if( wp_script_is( 'mg_progress-bar', 'queue' ) ){
  }else{
    wp_enqueue_style( 'mg_progress-bar' );
  }


  }
}

Migla_ProgressBar_Shortcode::init();

/********    Text Shortcode   ******************************************************/

class Migla_TextAmountRaised_Shortcode {
  static $progressbar_script;

 static function init() {

  add_shortcode('totaldonations-text-fields', array(__CLASS__, 'handle_shortcode'));
  add_action('init', array(__CLASS__, 'register_script'));
  add_action('wp_footer', array(__CLASS__, 'print_script'));
 

 }

 static function handle_shortcode($atts){

   self::$progressbar_script = true;

   extract(shortcode_atts(array(
        'campaign' => '', 
        'button' => 'no',
        'button_text' => 'Donate Now',
        'text' => ''
    ), $atts )
   ); 

$args = shortcode_atts( 
    array(
        'campaign' => '', 
        'button' => 'no',
        'button_text' => 'Donate Now',
        'text' => ''
    ), 
    $atts
);

 $draw = "";

 if( $atts == null || count($atts) <= 0)
 {
    //$draw = migla_shortcode_progressbar( "", "no" );
 }else{	
   if( $args['campaign'] == "" ){	
     //$draw = migla_shortcode_progressbar( "", "no", "" );
   }else{
      $draw = migla_draw_textbarshortcode( $args['campaign'], $args['button'] , $args['button_text'], $args['text'] );
   }
  }

 return $draw;
 
}//function

 static function register_script() {
  if( wp_script_is( 'mg_progress-bar', 'registered' ) ){
  }else{
    wp_register_style( 'mg_progress-bar', plugins_url( 'totaldonations/css/mg_progress-bar.css' , dirname(__FILE__)) );
  }
 }

 static function print_script() {
   if ( ! self::$progressbar_script )
	return;

  if( wp_script_is( 'mg_progress-bar', 'queue' ) ){
  }else{
    wp_enqueue_style( 'mg_progress-bar' );
  }

  }
}

Migla_TextAmountRaised_Shortcode::init();


/*****  Top Donor Shortcode   ***********/
class Migla_TopDonors_Shortcode {
  static $progressbar_script;

 static function init() {
  add_shortcode('totaldonations-top-donors', array(__CLASS__, 'handle_shortcode'));
  add_action('init', array(__CLASS__, 'register_script'));
  add_action('wp_footer', array(__CLASS__, 'print_script'));
 }

 static function handle_shortcode($atts){

   self::$progressbar_script = true;

$args = shortcode_atts( 
    array(
        'title' => '',
        'num_rec' =>  '',
        'donation_type' =>  '',
        'use_link' => '',
        'btn_class' => '',
	'btn_style' =>  '',
        'btn_text' => ''
    ), 
    $atts
);

 $draw = "";

 if( $atts == null || count($atts) <= 0)
 {
    $draw = draw_topdonors( "Top Donors", 5, "online" , "", "", "", "" );
 }else
 {	
    $draw = draw_topdonors( $args['title'], $args['num_rec'], $args['donation_type'] , $args['use_link'], $args['btn_class'], $args['btn_style'], $args['btn_text'] );
 }
 return $draw;
 
}//function

 static function register_script() {
  if( wp_script_is( 'mg_progress-bar', 'registered' ) ){
  }else{
    wp_register_style( 'mg_progress-bar', plugins_url( 'totaldonations/css/mg_progress-bar.css' , dirname(__FILE__)) );
  }

  if( wp_script_is( 'mg_progress-bar-js', 'registered' ) ){
  }else{
    wp_register_script( 'mg_progress-bar-js', plugins_url( 'totaldonations/js/migla_link_button.js' , dirname(__FILE__)) );
  }

 }

 static function print_script() {
   if ( ! self::$progressbar_script )
	return;

  if( wp_script_is( 'mg_progress-bar', 'queue' ) ){
  }else{
    wp_enqueue_style( 'mg_progress-bar' );
  }

  if( wp_script_is( 'mg_progress-bar-js', 'queue' ) ){
  }else{
    wp_enqueue_script( 'mg_progress-bar-js' );
  }

  }
}

Migla_TopDonors_Shortcode::init();



/*****  Recent donor Shortcode   ***********/

class Migla_Recent_Donor_Shortcode {
  static $progressbar_script;

 static function init() {
  add_shortcode('totaldonations-recent-donors', array(__CLASS__, 'handle_shortcode'));
  add_action('init', array(__CLASS__, 'register_script'));
  add_action('wp_footer', array(__CLASS__, 'print_script'));
 }

 static function handle_shortcode($atts){

   self::$progressbar_script = true;

$args = shortcode_atts( 
    array(
        'title' => '',
        'num_rec' =>  '',
        'donation_type' =>  '',
        'use_link' => '',
        'btn_class' => '',
	'btn_style' =>  '',
        'btn_text' => '',
        'language' => ''
    ), 
    $atts
);

 $draw = "";

 if( $atts == null || count($atts) <= 0)
 {
    $draw = migla_draw_donor_recent( "Recent donors", 5, "online" , "", "", "", "", "" );
 }else
 {	
    $draw = migla_draw_donor_recent( $args['title'], $args['num_rec'], $args['donation_type'] , $args['use_link'], $args['btn_class'], $args['btn_style'], $args['btn_text'], $args['language'] );
 }
 return $draw;
 
}//function

 static function register_script() {
  if( wp_script_is( 'mg_progress-bar', 'registered' ) ){
  }else{
    wp_register_style( 'mg_progress-bar', plugins_url( 'totaldonations/css/mg_progress-bar.css' , dirname(__FILE__)) );
  }

  if( wp_script_is( 'mg_progress-bar-js', 'registered' ) ){
  }else{
    wp_register_script( 'mg_progress-bar-js', plugins_url( 'totaldonations/js/migla_link_button.js' , dirname(__FILE__)) );
  }


 }

 static function print_script() {
   if ( ! self::$progressbar_script )
	return;

  if( wp_script_is( 'mg_progress-bar', 'queue' ) ){
  }else{
    wp_enqueue_style( 'mg_progress-bar' );
  }

  if( wp_script_is( 'mg_progress-bar-js', 'queue' ) ){
  }else{
    wp_enqueue_script( 'mg_progress-bar-js' );
  }

  }
}

 Migla_Recent_Donor_Shortcode::init();

//////////////////////END OF FORM SHORTCODE/////////////////////////////////////////

  ?>