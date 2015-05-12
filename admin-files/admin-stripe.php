<?php
class migla_stripe_setting_class {

	function __construct() {
		add_action( 'migla_donation_menu', array( $this, 'menu_item' ), 12 );
	}
	
	function menu_item() {
		add_submenu_page(
			'migla_donation_menu_page',
			__( 'Stripe Settings', 'migla-donation' ),
			__( 'Stripe Settings', 'migla-donation' ),
			'manage_options',
			'migla_stripe_setting_page',
			array( $this, 'menu_page' )
		);
	}
	
	function menu_page() {

         $current_user = wp_get_current_user();
	 if ( current_user_can( 'manage_options' ) ) 
         {
         }else{
		wp_die( __( 'You do not have sufficient permissions to access this page.', 'migla-donation' ) );
	 }		

		echo "<div class='wrap'><div class='container-fluid'>";   
                echo "<h2 class='migla'>". __("Stripe Settings","migla-donation")."</h2>";

		echo "<div class='row'>";


/******* Web Hook Information *****************************************************************************/
echo "<div class='col-sm-12'>";
	echo "<section class='panel'><header class='panel-heading'><div class='panel-actions'><a class='fa fa-caret-down' data-toggle='collapse' data-parent='.panel' href='#collapseOne' aria-expanded='true'></a></div><h2 class='panel-title'><i class='fa  fa-cc-stripe'></i>Stripe's Webhook</h2></header>";
		echo "<div id='collapseOne' class='panel-body collapse in'>";

echo "<div class='row'><div class='col-sm-3'><label for='mg_stripe_webhook_url' class='control-label text-right-sm text-center-xs'>". __("Webhook's URL","migla-donation"). "</label></div><div class='col-sm-6 col-xs-12'>";
echo "<input type='text' value='".plugins_url( 'migla-stripe-weebhook.php' , dirname(__FILE__) )."' id='mg_stripe_webhook_url' />";
echo "</div>";

   echo "<div class='col-sm-3'><a><button value='Preview Page' class='btn btn-info obutton' id='miglaStripeWebhook' onclick='window.open(\"https://dashboard.stripe.com/account/webhooks\")'><i class='fa fa-fw fa-search'></i>". __(" Go to Stripe","migla-donation"). "</button></a></div><span class='help-control col-sm-12 col-sm-pull-3  text-right-sm text-center-xs'>". __("Copy the URL above and add it into the webhook from inside Stripe. Please read Stripe's documentation for more detailed information.","migla-donation"). "</span>";

echo "</div>";
echo "</section>";
echo "</div>";


/*************  Stripe ******************************************************************************************************/

$testSK = get_option('migla_testSK'); $testPK = get_option('migla_testPK'); 
$liveSK = get_option('migla_liveSK'); $livePK = get_option('migla_livePK');
$stripeMode = get_option('migla_stripemode');
$showStripe = get_option('migla_show_stripe');


 
echo "<div class='col-sm-12'>";
		echo "<section class='panel'><header class='panel-heading'><div class='panel-actions'><a class='fa fa-caret-down' data-toggle='collapse' data-parent='.panel' href='#collapseOne' aria-expanded='true'></a></div><h2 class='panel-title'><i class='fa fa-cc-stripe'></i>Stripe Info</h2></header>";
		echo "<div id='collapseOne' class='panel-body collapse in'>";

/////////////////Show on Form////////////////////////////////////////////////////

		echo "<div class='row'><div class='col-sm-3'><label for='migla_show_stripe' class='control-label text-right-sm text-center-xs'>". __("Stripe","migla-donation"). "</label></div><div class='col-sm-6 col-xs-12'>";
if( $showStripe == 'yes' ){
  echo "<input type='checkbox' id='migla_show_stripe' name='' class='' checked><label for='migla_show_stripe'>". __("Show Stripe as Payment Option","migla-donation"). " </div></div>";
}else{
  echo "<input type='checkbox' id='migla_show_stripe' name='' class=''><label for='migla_show_stripe'>". __("Show Stripe as Payment Option","migla-donation"). "</label></div></div>";
}

/////////////////// ALIVE ////////////////////////////////////////////////

		echo "<div class='row'><div class='col-sm-3'><label for='migla_liveSK' class='control-label text-right-sm text-center-xs'>". __("Secret Key Live","migla-donation"). "</label></div><div class='col-sm-6 col-xs-12'>";
echo "<input type='text' id='migla_liveSK' value='".$liveSK."' class='form-control'></div></div>";

		echo "<div class='row'><div class='col-sm-3'><label for='migla_livePK' class='control-label text-right-sm text-center-xs'>". __("Public Key Live","migla-donation"). "</label></div><div class='col-sm-6 col-xs-12'>";
echo "<input type='text' id='migla_livePK' value='".$livePK."' class='form-control'></div></div>";


/////////////////// Testing ////////////////////////////////////////////////
	
		echo "<div class='row'><div class='col-sm-3'><label for='migla_testSK' class='control-label text-right-sm text-center-xs'>". __("Secret Key Testing","migla-donation"). "</label></div><div class='col-sm-6 col-xs-12'>";
echo "<input type='text' id='migla_testSK' value='".$testSK."' class='form-control'></div></div>";

		echo "<div class='row'><div class='col-sm-3'><label for='migla_testPK' class='control-label text-right-sm text-center-xs'>". __("Public Key Testing","migla-donation"). "</label></div><div class='col-sm-6 col-xs-12'>";
echo "<input type='text' id='migla_testPK' value='".$testPK."' class='form-control'></div></div>";



echo "<div class='row'><div class='col-sm-3'></div><div class='col-sm-9'>";
														
if( $stripeMode == false)
{
  add_option( 'migla_stripemode', 'test' );
}

if( $stripeMode == 'test' )
{
  echo "<div class='radio'>
														<label>
															<input type='radio' name='miglaStripe' value='test' checked >Test Stripe</label>
													</div>


<div class='radio'>
														<label>
															<input type='radio' name='miglaStripe' value='live' >Live Stripe
														</label>
													</div>


</div>";

}else{

  echo "<div class='radio'>
														<label>
															<input type='radio' name='miglaStripe' value='test' >Testing Stripe</label>
													</div>


<div class='radio'>
														<label>
															<input type='radio' name='miglaStripe' value='live' checked >Live Stripe
														</label>
													</div>


</div>";

}

		
		echo "<div class='col-sm-3 col-xs-12 text-left-sm text-center-xs'></div><div class='col-sm-6'><br><button id='miglaUpdateStripeSettings' class='btn btn-info pbutton' value='save'><i class='fa fa-fw fa-save'></i>". __(" save","migla-donation"). "</button></div>";

		echo "</div></section>";
		echo "</div></div>"; 




//////////////////// Upload Button Image ///////////////////////////////////


$btnchoice = get_option('miglaStripeButtonChoice');
$choice['StripeButton'] = ""; $choice['imageUpload'] = ""; $choice['cssButton'] = "";

$btnlang = get_option('migla_stripebutton');

$btnurl = get_option('migla_stripebuttonurl');

$btnstyle = get_option('migla_stripecssbtnstyle');
  if( $btnstyle == false ){ add_option('migla_stripecssbtnstyle', 'Default'); }

$btntext = get_option('migla_stripecssbtntext');
  if( $btntext == false ){ add_option('migla_stripecssbtntext', 'Donate Now'); }

$btnclass = get_option('migla_stripecssbtnclass');
  if( $btnclass == false ){ add_option('migla_stripecssbtnclass', ''); }

if( $btnchoice == false ){ 

  $btnchoice = 'stripeButton'; 
  add_option('miglaStripeButtonChoice', $btnchoice );
  $choice['stripeButton'] = "checked";

}else if( $btnchoice == '' ){

  $btnchoice = 'stripeButton'; 
  update_option('miglaStripeButtonChoice', $btnchoice );
  $choice['stripeButton'] = "checked";

}else if( $btnchoice == 'stripeButton' ){

   $choice['stripeButton'] = "checked";

}else if( $btnchoice == 'imageUpload' ){

   $choice['imageUpload'] = "checked";

}else{ 

   $choice['cssButton'] = "checked";

}

echo "<div class='row'>";
echo "<div class='col-xs-12'>";
echo "<section class='panel'>";  
echo "<header class='panel-heading'>";
echo "<div class='panel-actions'>";
echo "<a class='fa fa-caret-down' data-toggle='collapse' data-parent='.panel' href='#collapseEight' aria-expanded='true'></a>";
echo "</div>";  
echo "<h2 class='panel-title'><div class='dashicons dashicons-admin-appearance'></div>".__("Stripe Button","migla-donation")."</h2>";
echo "</header>";
echo "<div id='collapseEight' class='panel-body collapse in'>";

echo "<div class='form-horizontal'><div class='form-group touching radio'><div class='col-sm-1'></div>
        <div class='col-sm-2'><label><input type='radio' ".$choice['stripeButton']." value='stripeButton' name='miglaStripeButtonChoice'>".__("Choose the default Stripe Button","migla-donation")."</label></div></div><div class='form-group touching'><div class='col-sm-3  col-xs-12'><label for='stripeButtonText' class='control-label text-right-sm text-center-xs'>".__("Button Text","migla-donation")."</label></div><div class='col-sm-6 col-xs-12'> <input id='stripeButtonText' type='text' value='' required='' placeholder='Donate Now' title='' class='form-control ' name=''></div><div class='col-sm-3'><button value='save' class='btn btn-info pbutton' id='miglaSavestripeButtonPicker'><i class='fa fa-fw fa-save'></i>".__(" save","migla-donation")."</button></div></div></div>";

echo "<div class='form-horizontal'><div class='form-group touching radio'><div class='col-sm-1'></div><div class='col-sm-11'><label><input type='radio' ".$choice['imageUpload']." value='imageUpload' name='miglaStripeButtonChoice'>".__("Upload Your Own Button","migla-donation")."</label></div></div><div class='form-group touching'><div class='col-sm-3  col-xs-12'><label for='mg_upload_image' class='control-label text-right-sm text-center-xs'>".__("Upload:","migla-donation")."</label></div>";

echo "<div class='col-sm-6 col-xs-12'>";

echo "<input id='mg_upload_image' type='text' size='36' name='mg_upload_image' value='".$btnurl."' />";

echo "</div><div class='col-sm-3  col-xs-12'><button value='upload' class='btn btn-info obutton ' id='miglaUploadstripeBtn'><i class='fa fa-fw fa-upload'></i>".__(" upload","migla-donation")."</button>";

echo "<button value='save' class='btn btn-info pbutton' id='miglaSavestripeBtnUrl'><i class='fa fa-fw fa-save'></i>".__(" save","migla-donation")."</button></div>";
echo "</div></div>";               

echo "<div class='form-horizontal'><div class='form-group touching radio'><div class='col-sm-1'></div><div class='col-sm-11'><label><input type='radio' ".$choice['cssButton']." value='cssButton' name='miglaStripeButtonChoice'>".__("Choose a CSS Button","migla-donation")."</label></div></div><div class='form-group touching'><div class='col-sm-3  col-xs-12'><label class='control-label text-right-sm text-center-xs' for='mg_CSSButtonPicker'>".__("Button","migla-donation")."</label></div><div class='col-sm-6 col-xs-12'><select id='mg_CSSButtonPicker' class='form-control touch-top' name='miglaCSSButtonPicker'>";

if( $btnstyle == 'Default'){
 echo "<option selected='selected' value='Default'>".__("Your Default Form Button","migla-donation")."</option><option value='Grey'>".__("Grey Button","migla-donation")."</option></select></div><div class='col-sm-3'></div></div>";
}else{
 echo "<option value='Default'>".__("Your Default Form Button","migla-donation")."</option><option selected='selected' value='Grey'>".__("Grey Button","migla-donation")."</option></select></div><div class='col-sm-3'></div></div>";

}

echo "<div class='form-group touching'><div class='col-sm-3  col-xs-12'><label for='mg_CSSButtonText' class='control-label text-right-sm text-center-xs'>".__("Button Text","migla-donation")."</label></div><div class='col-sm-6 col-xs-12'> <input id='mg_CSSButtonText' type='text' value='".$btntext."' required='' placeholder='Donate Now' title='' class='form-control touch-middle' name=''></div><div class='col-sm-3'></div></div><div class='form-group touching'><div class='col-sm-3  col-xs-12'><label for='mg_CSSButtonClass' class='control-label text-right-sm text-center-xs'>".__("Add CSS class (theme button only)","migla-donation")."</label></div><div class='col-sm-6 col-xs-12'> <input id='mg_CSSButtonClass' type='text' value='".$btnclass."' required='' placeholder='enter your css class here' title='' class='form-control touch-bottom' name=''>     </div><div class='col-sm-3'><button value='save' class='btn btn-info pbutton' id='miglaCSSButtonPickerSave'><i class='fa fa-fw fa-save'></i>".__(" save","migla-donation")."</button></div></div>";

echo "</div>";             

echo "</section>";  
echo "</div></div> <!-- row col-xs-12-->";
//////////////////// END OF Upload Button Image ///////////////////////////////////




echo "<section class='panel'>
							<header class='panel-heading'>
								<div class='panel-actions'>
									<a class='fa fa-caret-down ' data-toggle='collapse' data-parent='.panel' href='#collapseThree' aria-expanded='true'></a>
									
								</div>

								<h2 class='panel-title'><i class='fa fa-fw fa-list'></i>". __("List of Stripe Plans","migla-donation")."</h2>
							</header>
							<div id='collapseThree' class='panel-body collapse in'>
								<div id='datatable-default_wrapper' class='dataTables_wrapper no-footer'><div class='table-responsive'>";
 							
							   
   echo "<table id='miglaStripePlanTable' class='display' cellspacing='0' width='100%'>";

   echo "<thead>";
   echo "<tr>";
   echo "<th class='detailsHeader' style='width:15px;'>Detail</th>";
   echo "<th class=''>". __("Created","migla-donation")."</th>";
   echo "<th class=''>". __("id","migla-donation")."</th>";
   echo "<th class=''>". __("Name","migla-donation")."</th>";
   echo "<th class=''>". __("Interval","migla-donation")."</th>";
   echo "<th class=''>". __("Amount","migla-donation")."</th>";
   echo "<th></th>";
   echo "</tr>"; 
   echo "</thead>";

   echo "<tfoot><tr>";
   echo "<th id='f0'>". __("Detail","migla-donation")."</th>";   
   echo "<th id='f1'>". __("Created","migla-donation")."</th>";   
   echo "<th id='f2'>". __("id","migla-donation")."</th>";
   echo "<th id='f3'>". __("Name","migla-donation")."</th>";
   echo "<th id='f4'>". __("Interval","migla-donation")."</th>";
   echo "<th id='f5'>". __("Amount","migla-donation")."</th>";
   echo "<th id='f6'></th>";
   
   echo "</tr></tfoot>";
   echo "</table>";

echo "<div class='row datatables-footer'><div class='col-sm-12 col-md-6'>
   <button class='btn mbutton' id='miglaSyncPlan' style=''>
   <i class='fa fa-fw fa-square-o '></i>". __(" Synchronize Plan ","migla-donation")."</button>
   </div>
   
   <div class='col-sm-12 col-md-6'>

</div></div>";

   echo "  </div>   ";

   echo "</div> ";  
   					
		

              echo "</div></div></div>";
		
	}

}

?>