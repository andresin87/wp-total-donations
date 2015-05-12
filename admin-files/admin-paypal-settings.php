<?php
class migla_paypal_settings_class {

	function __construct() {
		add_action( 'migla_donation_menu', array( $this, 'menu_item' ), 12 );
	}
	
	function menu_item() {
		add_submenu_page(
			'migla_donation_menu_page',
			__( 'Paypal Settings', 'migla-donation' ),
			__( 'Paypal Settings', 'migla-donation' ),
			'manage_options',
			'migla_donation_paypal_settings_page',
			array( $this, 'menu_page' )
		);
	}
	
	function menu_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.', 'migla-donation' ) );
		}		
		
		echo "<div class='wrap'><div class='container-fluid'>";   
                echo "<h2 class='migla'>". __("Paypal Settings","migla-donation")."</h2>";

		echo "<div class='row'>";

$payment['sandbox'] = '';  $payment['paypal'] = '';
		$paymentMethod = get_option( 'migla_payment' ) ;
		$payment[ $paymentMethod ] = 'checked';		
        $pEmail = get_option( 'migla_paypal_emails' ) ;
        $pEmailName = get_option( 'migla_paypal_emailsname' ) ;


echo "<div class='col-sm-12'>";
		echo "<section class='panel'><header class='panel-heading'><div class='panel-actions'><a class='fa fa-caret-down' data-toggle='collapse' data-parent='.panel' href='#collapseFive' aria-expanded='true'></a></div><h2 class='panel-title'><i class='fa fa-paypal'></i>PayPal Account Settings</h2></header>";
		echo "<div id='collapseFive' class='panel-body collapse in'>";


/********* SHOW FORM ************************************************************/
 $showPaypal = get_option('migla_show_paypal');
 if( $showPaypal == false )
 { 
    add_option('migla_show_paypal', 'yes') ; $showPaypal = get_option('migla_show_paypal'); 
 }
		echo "<div class='row'><div class='col-sm-3'><label class='control-label text-right-sm text-center-xs'>". __("Paypal","migla-donation"). "</label></div><div class='col-sm-6 col-xs-12'>";
if( $showPaypal == 'yes' ){
  echo "<input type='checkbox' id='migla_show_paypal' name='' class='' checked><label for='migla_show_paypal'>". __("Show Paypal as Payment Option","migla-donation"). " </div></div>";
}else{
  echo "<input type='checkbox' id='migla_show_paypal' name='' class=''><label for='migla_show_paypal'>". __("Show Paypal as Payment Option","migla-donation"). "</label></div></div>";
}
/********* SHOW FORM ************************************************************/
	
		echo "<div class='row'><div class='col-sm-3'><label for='miglaPaypalEmails' class='control-label text-right-sm text-center-xs'>". __("Business Email:","migla-donation"). "</label></div><div class='col-sm-6 col-xs-12'>";
echo "<input type='text' id='miglaPaypalEmails' value='".$pEmail."' class='form-control'></div>";
 
/* 
  echo "<div class='col-sm-3 col-xs-12 text-left-sm text-center-xs'><button value='update' class='btn btn-info pbutton' id='miglaUpdatePaypalEmails'><i class='fa fa-save'></i>". __(" save","migla-donation"). "</button></div>
*/

echo "<span class='help-control col-sm-12 col-sm-pull-3  text-right-sm text-center-xs'>The PayPal address you use for accepting donations. </span>
</div>";
	
	
echo "<div class='row'><div class='col-sm-3'><label for='mg_sandbox' class='control-label text-right-sm text-center-xs'>". __("Type:","migla-donation"). "</label></div><div class='col-sm-9'>
														

<div class='radio'>
														<label>
															<input type='radio' id='mg_sandbox' name='miglaPaypal' value='sandbox' ".$payment['sandbox']." >". __("Sand Box PayPal","migla-donation"). "</label>
													</div>


<div class='radio'>
														<label>
															<input type='radio' name='miglaPaypal' value='paypal' ".$payment['paypal']." >". __("Paypal","migla-donation"). "
														</label>
													</div>




</div>";



		
		echo "<div class='col-sm-3 col-xs-12 text-left-sm text-center-xs'></div><div class='col-sm-6'><br><button id='miglaUpdatePaypalSettings' class='btn btn-info pbutton' value='save'><i class='fa fa-fw fa-save'></i>". __(" save","migla-donation"). "</button></div>";

		echo "</div>";
///binti
echo "</div></section></div>";


echo "<div class='col-sm-12'>";
		echo "<section class='panel'><header class='panel-heading'><div class='panel-actions'><a class='fa fa-caret-down' data-toggle='collapse' data-parent='.panel' href='#collapseSix' aria-expanded='true'></a></div><h2 class='panel-title'><i class='fa fa-paypal'></i>PayPal Settings</h2></header>";
		echo "<div id='collapseSix' class='panel-body collapse in'>";



///////////////////////////////////////////////////////////////////////////////////////////////////////////
$pItem = get_option('migla_paypalitem' );

 if( $pItem == false ){
      add_option( 'migla_paymentitem', 'donation') ;
      $pItem = 'donation';	
 }

 if( $pItem == '' ){
    update_option( 'migla_paymentitem', 'donation') ;
    $pItem = 'donation';	
 }

	
	echo "<div class='row'><div class='col-sm-3'><label for='miglaPaypalItem' class='control-label text-right-sm text-center-xs'>". __("Item name:","migla-donation"). "</label></div><div class='col-sm-6 col-xs-12'>";
echo "<input type='text' id='miglaPaypalItem' value='".$pItem."' class='form-control'></div>";

/*  
  echo "<div class='col-sm-3 col-xs-12 text-left-sm text-center-xs'><button value='update' class='btn btn-info pbutton' id='miglaUpdatePaypalItem'><i class='fa fa-save'></i>". __(" save","migla-donation"). "</button></div>";
*/

echo "<span class='help-control col-sm-12 col-sm-pull-3  text-right-sm text-center-xs'>". __("This is the name of the item that shows up in PayPal, You can change it to something else: donation, backing, support. etc","migla-donation"). "
 </span></div>";
	
$paymentcmd['donation'] = '';  $paymentcmd['payment'] = '';
      $paymentCmd = get_option( 'migla_paymentcmd' ) ;

 if( $paymentCmd == false ){
    add_option( 'migla_paymentcmd', 'donation') ;
      $paymentcmd[ 'donation'] = 'checked';	
 }else{
      $paymentcmd[ $paymentCmd ] = 'checked';	
 }

 if( $paymentCmd == '' ){
    update_option( 'migla_paymentcmd', 'donation') ;
      $paymentcmd[ 'donation' ] = 'checked';	
 }else{
      $paymentcmd[ $paymentCmd ] = 'checked';	
 }


// Payment type ///////////////////////////////////////////////////////////////////////////////////////////


echo "<div class='row'><div class='col-sm-3'><label for='miglaPaypalcmd' class='control-label text-right-sm text-center-xs'>". __("Payment Type","migla-donation"). "</label></div><div class='col-sm-9'>														

<div class='radio'>
														<label>
															<input type='radio' id='miglaPaypalcmd' name='miglaPaypalcmd' value='donation' ".$paymentcmd['donation']." >". __("Donation","migla-donation"). "</label>
													</div>


<div class='radio'>
														<label>
															<input type='radio' name='miglaPaypalcmd' value='payment' ".$paymentcmd['payment']." >". __("Payment","migla-donation"). "
														</label>
													</div>


</div>";

		
		echo "<div class='col-sm-3 col-xs-12 text-left-sm text-center-xs'></div><div class='col-sm-6'><br><button id='miglaUpdatePaypalCmdSettings' class='btn btn-info pbutton' value='save'><i class='fa fa-fw fa-save'></i>". __(" save","migla-donation"). "</button></div>";

        echo "</div>";

		echo "</div></section></div>";
		



//////////////////// Upload Button Image ///////////////////////////////////


$btnchoice = get_option('miglaPayPalButtonChoice');
$choice['paypalButton'] = ""; $choice['imageUpload'] = ""; $choice['cssButton'] = "";

$btnlang = get_option('migla_paypalbutton');
if( $btnlang == false ){ add_option('migla_paypalbutton', 'English'); }
if( $btnlang == '' ){ update_option('migla_paypalbutton', 'English'); }

$btnurl = get_option('migla_paypalbuttonurl');

$btnstyle = get_option('migla_paypalcssbtnstyle');
  if( $btnstyle == false ){ add_option('migla_paypalcssbtnstyle', 'Default'); }
$btntext = get_option('migla_paypalcssbtntext');
  if( $btntext == false ){ add_option('migla_paypalcssbtntext', 'Donate Now'); }
$btnclass = get_option('migla_paypalcssbtnclass');
  if( $btnclass == false ){ add_option('migla_paypalcssbtnclass', ''); }

if( $btnchoice == false ){ 

  $btnchoice = 'paypalButton'; 
  add_option('miglaPayPalButtonChoice', $btnchoice );
  update_option('migla_paypalbutton', 'English');
  $choice['paypalButton'] = "checked";

}else if( $btnchoice == '' ){

  $btnchoice = 'paypalButton'; 
  update_option('miglaPayPalButtonChoice', $btnchoice );
  update_option('migla_paypalbutton', 'English'); 
  $choice['paypalButton'] = "checked";

}else if( $btnchoice == 'paypalButton' ){

   $choice['paypalButton'] = "checked";

}else if( $btnchoice == 'imageUpload' ){

   $choice['imageUpload'] = "checked";

}else{ 

   $choice['cssButton'] = "checked";

}


echo "<div class='col-xs-12'>";
echo "<section class='panel'>";  
echo "<header class='panel-heading'>";
echo "<div class='panel-actions'>";
echo "<a class='fa fa-caret-down' data-toggle='collapse' data-parent='.panel' href='#collapseEight' aria-expanded='true'></a>";
echo "</div>";  
echo "<h2 class='panel-title'><div class='dashicons dashicons-admin-appearance'></div>".__("Button","migla-donation")."</h2>";
echo "</header>";
echo "<div id='collapseEight' class='panel-body collapse in'>";

echo "<div class='form-horizontal'><div class='form-group touching radio'><div class='col-sm-1'></div>
        <div class='col-sm-11'><label><input type='radio' ".$choice['paypalButton']." value='paypalButton' name='miglaPayPalButtonChoice'>".__("Use Paypal Button","migla-donation")."</label></div></div><div class='form-group touching'><div class='col-sm-3  col-xs-12'><label class='control-label text-right-sm text-center-xs'>".__("Language","migla-donation")."</label></div><div class='col-sm-6 col-xs-12'><select id='miglaPayPalButtonPicker' name='miglaPayPalButtonPicker'>";

$checkit = "";
if( $btnlang == 'english'){ $checkit = "selected='selected'"; }
 echo "<option ".$checkit." value='english'>".__("English","migla-donation")."</option>";

$checkit = "";
if( $btnlang == 'simplified_chinese'){ $checkit = "selected='selected'"; }
 echo "<option ".$checkit." value='simplified_chinese'>".__("Chinese (Simplified)","migla-donation")."</option>";

$checkit = "";
if( $btnlang == 'traditional_chinese'){ $checkit = "selected='selected'"; }
 echo "<option ".$checkit." value='traditional_chinese'>".__("Chinese (Traditional)","migla-donation")."</option>";

$checkit = "";
if( $btnlang == 'dutch'){ $checkit = "selected='selected'"; }
 echo "<option ".$checkit." value='dutch'>".__("Dutch","migla-donation")."</option>";

$checkit = "";
if( $btnlang == 'french'){ $checkit = "selected='selected'"; }
 echo "<option ".$checkit." value='french'>".__("French","migla-donation")."</option>";

$checkit = "";
if( $btnlang == 'hebrew'){ $checkit = "selected='selected'"; }
 echo "<option ".$checkit." value='hebrew'>".__("Hebrew","migla-donation")."</option>";

$checkit = "";
if( $btnlang == 'norwegian'){ $checkit = "selected='selected'"; }
 echo "<option ".$checkit." value='norwegian'>".__("Norwegian","migla-donation")."</option>";

$checkit = "";
if( $btnlang == 'polish'){ $checkit = "selected='selected'"; }
 echo "<option ".$checkit." value='polish'>".__("Polish","migla-donation")."</option>";

$checkit = "";
if( $btnlang == 'russian'){ $checkit = "selected='selected'"; }
 echo "<option ".$checkit." value='russian'>".__("Russian","migla-donation")."</option>";

$checkit = "";
if( $btnlang == 'spanish'){ $checkit = "selected='selected'"; }
 echo "<option ".$checkit." value='spanish'>".__("Spanish","migla-donation")."</option>";

$checkit = "";
if( $btnlang == 'swedish'){ $checkit = "selected='selected'"; }
 echo "<option ".$checkit." value='swedish'>".__("Swedish","migla-donation")."</option>";

$checkit = "";
if( $btnlang == 'turkey'){ $checkit = "selected='selected'"; }
 echo "<option ".$checkit." value='turkey'>".__("Turkish","migla-donation")."</option>";

echo "</select></div><div class='col-sm-3'><button value='save' class='btn btn-info pbutton' id='miglaSavePayPalButtonPicker'><i class='fa fa-fw fa-save'></i>".__(" save","migla-donation")."</button></div></div></div>";


echo "<div class='form-horizontal'><div class='form-group touching radio'><div class='col-sm-1'></div>
        <div class='col-sm-11'><label><input type='radio' ".$choice['imageUpload']." value='imageUpload' name='miglaPayPalButtonChoice'>".__("Upload Your Own Button","migla-donation")."</label></div></div>
<br><div class='form-group touching'>
<div class='col-sm-3  col-xs-12'><label class='control-label text-right-sm text-center-xs'>".__("Upload:","migla-donation")."</label></div>";

echo "<div class='col-sm-6 col-xs-12'>";

 echo "<input id='mg_upload_image' type='text' size='36' name='mg_upload_image' value='".$btnurl."' />";

echo "</div><div class='col-sm-3  col-xs-12'><button value='upload' class='btn btn-info obutton ' id='miglaUploadPaypalBtn'><i class='fa fa-fw fa-upload'></i>".__(" upload","migla-donation")."</button>";
echo "<button value='save' class='btn btn-info pbutton' id='miglaSavePaypalBtnUrl'><i class='fa fa-fw fa-save'></i>".__(" save","migla-donation")."</button></div>";
echo "</div></div>";               

echo "<div class='form-horizontal'><div class='form-group touching radio'><div class='col-sm-1'></div>
        <div class='col-sm-11'><label><input type='radio' ".$choice['cssButton']." value='cssButton' name='miglaPayPalButtonChoice'>".__("Choose a CSS Button","migla-donation")."</label></div></div><br>

<div class='form-group touching'><div class='col-sm-3  col-xs-12'><label class='control-label text-right-sm text-center-xs'>".__("Button","migla-donation")."</label></div><div class='col-sm-6 col-xs-12'><select id='mg_CSSButtonPicker' class='form-control touch-top' name='miglaCSSButtonPicker'>";

if( $btnstyle == 'Default'){
 echo "<option selected='selected' value='Default'>".__("Your Default Form Button","migla-donation")."</option><option value='Grey'>".__("Grey Button","migla-donation")."</option></select></div><div class='col-sm-3'></div></div>";
}else{
 echo "<option value='Default'>".__("Your Default Form Button","migla-donation")."</option><option selected='selected' value='Grey'>".__("Grey Button","migla-donation")."</option></select></div><div class='col-sm-3'></div></div>";

}

echo "<div class='form-group touching'><div class='col-sm-3  col-xs-12'><label class='control-label text-right-sm text-center-xs'>".__("Button Text","migla-donation")."</label></div><div class='col-sm-6 col-xs-12'> <input id='mg_CSSButtonText' type='text' value='".$btntext."' required='' placeholder='Donate Now' title='' class='form-control touch-middle' name=''></div><div class='col-sm-3'></div></div><div class='form-group touching'><div class='col-sm-3  col-xs-12'><label class='control-label text-right-sm text-center-xs'>".__("Add CSS class (theme button only)","migla-donation")."</label></div><div class='col-sm-6 col-xs-12'> <input id='mg_CSSButtonClass' type='text' value='".$btnclass."' required='' placeholder='enter your css here' title='' class='form-control touch-bottom' name=''>     </div><div class='col-sm-3'><button value='save' class='btn btn-info pbutton' id='miglaCSSButtonPickerSave'><i class='fa fa-fw fa-save'></i>".__(" save","migla-donation")."</button></div></div></div>";

echo "</div>";             

echo "</section>";  
echo "</div> <!-- row col-xs-12-->";
//////////////////// END OF Upload Button Image ///////////////////////////////////




		//echo "<br> <br></div>"; 
              echo "</div></div>"; // row id=wrap
		
	}

}

?>