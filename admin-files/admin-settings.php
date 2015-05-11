<?php
class migla_settings_class {

	function __construct() {
		add_action( 'migla_donation_menu', array( $this, 'menu_item' ), 12 );
	}
	
	function menu_item() {
		add_submenu_page(
			'migla_donation_menu_page',
			__( 'Settings', 'migla-donation' ),
			__( 'Settings', 'migla-donation' ),
			'manage_options',
			'migla_donation_settings_page',
			array( $this, 'menu_page' )
		);
	}
	
	function menu_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.', 'migla-donation' ) );
		}		
		
		echo "<div class='wrap'><div class='container-fluid'>";   
                echo "<h2 class='migla'>". __("Settings","migla-donation")."</h2>";

	

                echo "<div class='row'>";

		echo "<div class='col-sm-12'>";
		echo "<section class='panel'><header class='panel-heading'><div class='panel-actions'><a class='fa fa-caret-down' data-toggle='collapse' data-parent='.panel' href='#collapseFour' aria-expanded='true'></a></div><h2 class='panel-title'><div class='dashicons dashicons-plus'></div>". __("Notifications","migla-donation"). "</h2></header>";
		echo "<div id='collapseFour' class='panel-body collapse in'><div class='row'>";
		
        $nEmails = get_option( 'migla_notif_emails' ) ;
		
		echo "<div class='col-sm-3'><label class='control-label text-right-sm text-center-xs'>" . esc_html__( 'emails to notify upon new donations', 'migla-donation' );
		echo "</label></div>";
		echo "<div class='col-sm-6 col-xs-12'><input class='form-control' id='miglaNotifEmails' type='text' value='".$nEmails."' />";
		echo "</div><div class='col-sm-3 col-xs-12 text-left-sm text-center-xs'><button id='miglaUpdateNotifEmails' class='btn btn-info pbutton miglaThankEmail' value='save'><i class='fa fa-fw fa-save'></i> save</button></div><span class='help-control col-sm-12 col-sm-pull-3  text-right-sm text-center-xs'>". __("Use commas to separate emails","migla-donation"). "</span>";

		echo "</div></div></section></div>";

		echo "<div class='col-sm-12'>";
		echo "<section class='panel'><header class='panel-heading'><div class='panel-actions'><a class='fa fa-caret-down ' data-toggle='collapse' data-parent='.panel' href='#collapseOne' aria-expanded='true'></a></div><h2 class='panel-title'><div class='dashicons dashicons-plus'></div>". __("Thank You Page","migla-donation"). "<span class='panel-subtitle'>". __("The page that appears after you donate","migla-donation"). "</span></h2></header>";
		echo "<div id='collapseOne' class='panel-body collapse in'>";
		echo "<div class='row'>";
		echo "<div class='col-sm-12'>";
	
/*******************   Tiny MCE    *******************************/
$content = get_option( 'migla_thankyoupage' );
echo "<div id='content' style='display:none'>".$content."</div>";

$settings =   array(
    'wpautop' => true, // use wpautop?
    'media_buttons' => true, // show insert/upload button(s)
    'textarea_name' => 'migla_editor', // set the textarea name to something different, square brackets [] can be used here
    'textarea_rows' => 30, // rows="..."
    'tinymce' => true
);
wp_editor(  stripslashes($content) , 'migla_editor', $settings  );

echo "</div>";

echo "<span>";
echo "<div class='col-sm-12 '><button id='miglaThankPage' class='btn btn-info pbutton' value='save'><i class='fa fa-fw fa-save'></i>". __(" save","migla-donation"). "</button> ";
$url = get_option( 'migla_form_url' );
echo "<div id='migla_urlshortcode' style='display:none'>".$url."</div>";
echo "<form id='miglaFormPreviewThank' style='display:inline;' action='".$url."' method='post' target='_blank' >";
echo "<input type='hidden' name='thanks' value='testThanks' />";
echo "</form>";
echo "<button id='miglaThankPagePrev' class='btn btn-info obutton' value='Preview Page'><i class='fa fa-fw fa-search'></i>". __(" Preview","migla-donation"). "</button>";

echo "</span>";

echo "&nbsp;&nbsp;Shortcodes allowed: <code>[firstname][lastname][amount][date]</code><br></div>";
echo "</div></section></div>";
		
		
		
		
		
		// new panel


		echo "<div class='col-sm-12'>";
		echo "<section class='panel'><header class='panel-heading'><div class='panel-actions'><a class='fa fa-caret-down ' data-toggle='collapse' data-parent='.panel' href='#collapseTwo' aria-expanded='true'></a></div><h2 class='panel-title'><i class='fa fa-envelope-o'></i>". __("Thank you Email","migla-donation"). "</h2></header>";
		echo "<div id='collapseTwo' class='panel-body collapse in' >";

echo "<div class='row'><div class='col-sm-3'><label class='control-label text-right-sm text-center-xs'>".esc_html__( 'Email Address: ', 'migla-donation' );
echo "</label></div><div class='col-sm-6 col-xs-12'>
		
		
		 <input type='text' id='miglaReplyToTxt' placeholder='".get_option('migla_replyTo')."' value='".get_option('migla_replyTo')."' class='form-control'></div><div class='col-sm-3 col-xs-12 text-left-sm text-center-xs'><button value='save' class='btn btn-info pbutton' id='miglaReplyTo'><i class='fa fa-fw fa-save'></i>". __(" save","migla-donation"). "</button></div><span class='help-control col-sm-12 col-sm-pull-3  text-right-sm text-center-xs'>". __("The name your email will appeear from when your donor receives an email","migla-donation"). "</span></div>";

echo "<div class='row'><div class='col-sm-3'><label class='control-label text-right-sm text-center-xs'>".esc_html__( 'Email Name : ', 'migla-donation' );
echo "</label></div><div class='col-sm-6 col-xs-12'>

<input type='text' id='miglaReplyToNameTxt' class='form-control' placeholder='".get_option('migla_replyToName')."' value='".get_option('migla_replyToName')."'class='form-control' /></div><div class='col-sm-3 col-xs-12 text-left-sm text-center-xs'><button id='miglaReplyToName' class='btn btn-info pbutton' value='save'><i class='fa fa-fw fa-save'></i>". __(" save","migla-donation"). "</button></div><span class='help-control col-sm-12 col-sm-pull-3  text-right-sm text-center-xs'>". __("This is the name your thank you emails will appear from","migla-donation"). "</span></div>";

// Form Grouping for Astried

echo "<div class='form-horizontal'> <div class='form-group touching'><div class='col-sm-3 col-xs-12'><label class=' control-label text-right-sm text-center-xs'>". __(" Email Subject:","migla-donation")."</label>
  </div><div class='col-sm-6 col-xs-12'><input type='text' name='migla_thankSbj' class='form-control touch-top' title='Please enter subject of email' placeholder='' required='' value='".get_option('migla_thankSbj')."'></div>
<div class='col-sm-3 hidden-xs'></div></div><div class='form-group touching '><div class='col-sm-3'><label class=' control-label text-right-sm text-center-xs'>". __(" Thank you Email Text Body: ","migla-donation"). "</label>
 </div><div class='col-sm-6 col-xs-12'>"; 

echo "<textarea  id='miglaThankBody' class='form-control touch-middle'  cols='50' rows='3' name='miglaThankEmailTxt'>";

$thankstr = get_option( 'migla_thankBody' );

echo $thankstr;

echo "</textarea></div><div class='col-sm-3'> </div> </div><div class='form-group touching '><div class='col-sm-3 col-xs-12'><label class='control-label text-right-sm text-center-xs'>". __("Repeating Donations:","migla-donation")."</label> </div><div class='col-sm-6 col-xs-12'><input required='' placeholder='' class='form-control touch-middle' title='' rows='5' name='migla_thankRepeat' style='overflow: hidden;' value='".get_option('migla_thankRepeat')."'></div>	
<div class='col-sm-3 hidden-xs'> </div></div>


<div class='form-group touching '>
									<div class='col-sm-3 col-xs-12'>		<label class='control-label text-right-sm text-center-xs'>". __("Anonymous Donations:","migla-donation"). "</label> </div>
											<div class='col-sm-6 col-xs-12'>
												<input required='' placeholder='' class='form-control touch-middle' title='' rows='5' name='migla_thankAnon' style='overflow: hidden;' value='".get_option('migla_thankAnon')."'>
												
												
								</div>				
												
												
											


<div class='col-sm-3 hidden-xs'> </div>

										</div>

  
  
  
  <div class='form-group'>
									<div class='col-sm-3 col-xs-12'>		<label class='control-label text-right-sm text-center-xs'>". __("Signature:","migla-donation"). "</label> </div>
											<div class='col-sm-6 col-xs-12'>
												<input required='' placeholder='' class='form-control touch-bottom' title='' rows='5' name='migla_thankSig' style='overflow: hidden;' value='".get_option('migla_thankSig')."'>

<div style='border:none' class='row'><br><label class='col-sm-6 help-control'>". __(" Use the following shortcodes in the email body:","migla-donation"). "</label>

<div class='col-sm-6'><code>[firstname]</code>". __(" Donor's First Name", "migla-donation"). "<br><code>[lastname]</code>". __(" Donor's Last Name","migla-donation"). "<br><code>[amount]</code>". __(" Donation Amount","migla-donation"). "<br><code>[date]</code>". __(" Donation date ","migla-donation")."<br><code>[newline] </code>". __(" Line Break ","migla-donation")."</div></div>
												
												
								</div>												


<div class='col-sm-3 col-xs-12 text-left-sm text-center-xs'><button id='miglaThankEmail' class='btn btn-info pbutton' value='save'><i class='fa fa-fw fa-save'></i>". __(" save","migla-donation")."</button>  </div>

										 </div>

</div>";


  echo "<div class='row'><div class='col-sm-3'><label class='control-label text-right-sm text-center-xs'>" . esc_html__( 'Email address for Test:', 'migla-donation' );
		echo "</label></div>";
		echo "<div class='col-sm-6 col-xs-12'><input class='form-control' id='miglaTestEmailAdd' type='text' value='' />";
		echo "</div><div class='col-sm-3 col-xs-12 text-left-sm text-center-xs'><button id='miglaTestEmail' class='btn btn-info obutton' value='Send Testing Email'><i class='fa fa-fw fa-envelope-o'></i>". __(" Preview Email","migla-donation"). "</button></div><span class='help-control col-sm-12 col-sm-pull-3  text-right-sm text-center-xs'>". __(" Use this to preivew what your donors will see when they donate.","migla-donation"). "</span> </div></div></div>";


// Honoreee


echo "<div class='col-sm-12'>";
		echo "<section class='panel'><header class='panel-heading'><div class='panel-actions'><a class='fa fa-caret-down' data-toggle='collapse' data-parent='.panel' href='#collapseThree' aria-expanded='true'></a></div><h2 class='panel-title'><div class='dashicons dashicons-plus'></div>". __("Honoree Email","migla-donation"). "</h2></header>";
		echo "<div id='collapseThree' class='panel-body collapse in'>";


echo "<div class='form-horizontal'> <div class='form-group touching'><div class='col-sm-3 col-xs-12'><label class=' control-label text-right-sm text-center-xs'>". __("Email Subject:","migla-donation"). "</label>
  </div><div class='col-sm-6 col-xs-12'><input type='text' name='migla_honoreESbj' class='form-control touch-top' title='Plase enter a name.' placeholder='' required='' value='".get_option('migla_honoreESbj')."'></div>
<div class='col-sm-3 hidden-xs'></div></div><div class='form-group touching '><div class='col-sm-3'><label class=' control-label text-right-sm text-center-xs'>". __("Thank you Email Text Body: ","migla-donation"). "</label>
 </div><div class='col-sm-6 col-xs-12'>"; 

echo "<textarea  id='migla_honoreEBody' class='form-control touch-middle'  cols='50' rows='3' name=''>";

$thankstr = get_option('migla_honoreEBody');

echo $thankstr;

echo "</textarea></div><div class='col-sm-3'> </div> </div><div class='form-group touching '><div class='col-sm-3 col-xs-12'><label class='control-label text-right-sm text-center-xs'>". __("Repeating Donations:","migla-donation"). "</label> </div><div class='col-sm-6 col-xs-12'><input required='' placeholder='' class='form-control touch-middle' title='' rows='5' name='migla_honoreERepeat' style='overflow: hidden;' value='".get_option('migla_honoreERepeat')."'></div>	
<div class='col-sm-3 hidden-xs'> </div></div>";

echo "<div class='form-group touching '><div class='col-sm-3 col-xs-12'><label class='control-label text-right-sm text-center-xs'>". __("Custom Message Intro:","migla-donation"). "</label> </div>
<div class='col-sm-6 col-xs-12'><input value='".get_option('migla_honoreECustomIntro')."' name='migla_honoreECustomIntro' rows='5' title='' class='form-control touch-middle' placeholder='' required=''>
</div> <div class='col-sm-3 hidden-xs'> </div></div>";

echo "<div class='form-group touching '>
									<div class='col-sm-3 col-xs-12'>		<label class='control-label text-right-sm text-center-xs'>". __("Anonymous Donations:","migla-donation"). "</label> </div>
											<div class='col-sm-6 col-xs-12'>
												<input required='' placeholder='' class='form-control touch-middle' title='' rows='5' name='migla_honoreEAnon' style='overflow: hidden;' value='".get_option('migla_honoreEAnon')."'>
												
												
								</div>				
												
												
											


<div class='col-sm-3 hidden-xs'> </div>

										</div>

  
  
  
  <div class='form-group'>
									<div class='col-sm-3 col-xs-12'>		<label class='control-label text-right-sm text-center-xs'>". __("Signature:","migla-donation"). "</label> </div>
											<div class='col-sm-6 col-xs-12'>
												<input required='' placeholder='' class='form-control touch-bottom' title='' rows='5' name='migla_honoreESig' style='overflow: hidden;' value='".get_option('migla_honoreESig')."'>

<div style='border:none' class='row'><br><label class='col-sm-6 help-control'>". __(" Use the following shortcodes in the email body:","migla-donation"). "</label>

<div class='col-sm-6'> <code>[honoreename]</code>". __(" Honoree's Name","migla-donation"). "<br> <code>[firstname]</code>". __(" Donor's First Name","migla-donation"). "<br><code>[lastname]</code>". __(" Donor's Last Name","migla-donation"). "<br> <code>[amount]</code>". __("Donation Amount","migla-donation"). "<br><code>[date]</code> Donation date <br><code>[newline] </code> ". __(" Line Break","migla-donation"). "</div></div>
												
												
								</div>			
<div class='col-sm-3 col-xs-12 text-left-sm text-center-xs'><button id='miglaHonoreEmail' class='btn btn-info pbutton' value='save'><i class='fa fa-fw fa-save'></i>". __("save","migla-donation"). "</button>  </div>

										</div> 




</div> 



<div class='row'><div class='col-sm-3'><label class='control-label text-right-sm text-center-xs'>" . esc_html__( 'Email address for Honoree Email Test:', 'migla-donation' );
		echo "</label></div>";
		echo "<div class='col-sm-6 col-xs-12'><input class='form-control' id='miglaTestHEmailAdd' type='text' value='' />";
		echo "</div><div class='col-sm-3 col-xs-12 text-left-sm text-center-xs'><button id='miglaTestHEmail' class='btn btn-info obutton' value='Send Testing Email'><i class='fa fa-fw fa-envelope-o'></i>". __(" Preview Email","migla-donation"). "</button></div><span class='help-control col-sm-12 col-sm-pull-3  text-right-sm text-center-xs'>". __(" Use this to preivew what the honoree will see when they receive a message.","migla-donation"). "</span> </div></div>   
</div>";




		///////////////////////////////////////////////////////////////
		// Timezones Section	

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
        $now = date("F jS, Y", strtotime($d))." ".$t;
 	date_default_timezone_set( $php_time_zone );
       ///---------------------------------GET CURRENT TIME SETTINGS

		echo "<div class='col-sm-12'>";	
		$timezones = get_option( 'migla_timezones' );
		
	   echo "<section class='panel'><header class='panel-heading'><div class='panel-actions'><a class='fa fa-caret-down' data-toggle='collapse' data-parent='.panel' href='#collapseSeven' aria-expanded='true'></a></div><h2 class='panel-title'><i class='fa fa-fw fa-clock-o'></i>". __(" Default Time Zone Section","migla-donation"). "</h2></header>";
	      echo "<div id='collapseSeven' class='panel-body collapse in'><div class='row'>";
	   echo "<div class='col-sm-3 col-xs-12'><label class='control-label text-right-sm text-center-xs'>". __("Set Time Zone","migla-donation");
        echo "</label></div>";
       echo "<div class='col-sm-6 col-xs-12'><select id='miglaDefaultTimezone' name='miglaDefaultTimezone'>"; 
           echo "<option value='Server Time' >". __("Server Time","migla-donation")."</option>"; 
	   foreach ( (array) $timezones as $key => $value ) 
	   { 
	      if ( $value == get_option( 'migla_default_timezone' ) )
		   { 
		     echo "<option value='".$value."' selected >".$key."</option>"; 
		  }else{  
		    echo "<option value='".$value."'>".$key."</option>"; 
		  }
	   }	   
	   echo "</select></div><div class='col-sm-3 col-xs-12 text-left-sm text-center-xs'><button value='save' class='btn btn-info pbutton' id='miglaSetTimezoneButton'><i class='fa fa-fw fa-save'></i>". __(" save","migla-donation"). "</button></div>

<span class='help-control col-sm-12 col-sm-pull-3  text-right-sm text-center-xs'>". __(" Set your timezone here","migla-donation"). "</span><span id='migla_current_time' class='time-control col-sm-12 col-sm-pull-3'><strong>".$now."</strong></span></div>"; 
		
		
		
		echo "<div></section>";	
		//echo "</div>";

		
		echo "</div>"; 
              echo "</div></div>"; // row id=wrap
		
	}

}

?>