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
	
		echo "<div class='row'><div class='col-sm-3'><label class='control-label text-right-sm text-center-xs'>". __("Business Email","migla-donation"). "</label></div><div class='col-sm-6 col-xs-12'>";
echo "<input type='text' id='miglaPaypalEmails' value='".$pEmail."' class='form-control'></div>";
  
  echo "<div class='col-sm-3 col-xs-12 text-left-sm text-center-xs'><button value='update' class='btn btn-info pbutton' id='miglaUpdatePaypalEmails'><i class='fa fa-save'></i>". __(" save","migla-donation"). "</button></div></div>";
	
	
echo "<div class='row'><div class='col-sm-3'><label class='control-label text-right-sm text-center-xs'>". __("Type","migla-donation"). "</label></div><div class='col-sm-9'>
														

<div class='radio'>
														<label>
															<input type='radio' name='miglaPaypal' value='sandbox' ".$payment['sandbox']." >". __("Sand Box PayPal","migla-donation"). "</label>
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

	
	echo "<div class='row'><div class='col-sm-3'><label class='control-label text-right-sm text-center-xs'>". __("Item name","migla-donation"). "</label></div><div class='col-sm-6 col-xs-12'>";
echo "<input type='text' id='miglaPaypalItem' value='".$pItem."' class='form-control'></div>";
  
  echo "<div class='col-sm-3 col-xs-12 text-left-sm text-center-xs'><button value='update' class='btn btn-info pbutton' id='miglaUpdatePaypalItem'><i class='fa fa-save'></i>". __(" save","migla-donation"). "</button></div><span class='help-control col-sm-12 col-sm-pull-3  text-right-sm text-center-xs'>". __("This is the name of the item that shows up in PayPal, You can change it to something else: donation, backing, support. etc","migla-donation"). "
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


echo "<div class='row'><div class='col-sm-3'><label class='control-label text-right-sm text-center-xs'>". __("Payment Type","migla-donation"). "</label></div><div class='col-sm-9'>														

<div class='radio'>
														<label>
															<input type='radio' name='miglaPaypalcmd' value='donation' ".$paymentcmd['donation']." >". __("Donation","migla-donation"). "</label>
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
		
		//echo "<br> <br></div>"; 
              echo "</div></div>"; // row id=wrap
		
	}

}

?>