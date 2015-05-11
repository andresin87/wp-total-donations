<?php

class migla_campaign_menu_class {

	function __construct(  ) {
		add_action( 'migla_donation_menu', array( $this, 'menu_item' ), 7 );
	}
	
	function menu_item() {
		add_submenu_page(
			'migla_donation_menu_page',
			__( 'Campaigns', 'migla-donation' ),
			__( 'Campaigns', 'migla-donation' ),
			'manage_options',
			'migla_donation_campaigns_page',
			array( $this, 'menu_page' )
		);
	}

  function getSymbol(){
    $i = '';
    $currencies = get_option( 'migla_currencies' );
    $def = get_option( 'migla_default_currency' );

	   foreach ( $currencies as $key => $value ) 
	   { 
	      if ( strcmp($def,$currencies[$key]['code'] ) == 0 )
              { 
                 if( $currencies[$key]['faicon']!='' ) { 
                     $i = "<i class='fa fa-fw ".$currencies[$key]['faicon']."'></i>";
                     //$icon = $currencies[$key]['faicon']; 
                 }else{ $i = $currencies[$key]['symbol']; }
              }
	   }

    return $i;
   }
	
	function menu_page() {
		// Validate user
		if ( ! current_user_can( 'manage_options' ) ) {
	      wp_die( __( 'You do not have sufficient permissions to access this page.', 'migla-donation' ) );
	    }

		// Display the designated funds	
		echo "<div class='wrap'><div class='container-fluid'>";
               
                echo "<h2 class='migla'>". __('Campaign', 'migla-donation'). "</h2>";
		echo "<div class='row'>";
		echo "<div class='col-sm-12'>";

		echo "<section class='panel'><header class='panel-heading'><div class='panel-actions'><a class='fa fa-caret-down ' data-toggle='collapse' data-parent='.panel' href='#collapseOne' aria-expanded='true'></a></div><h2 class='panel-title'><div class='dashicons dashicons-plus'></div>";
		__("Add New Campaigns","migla-donation");
		echo "</h2></header>";
		echo "<div class='panel-body collapse in' id='collapseOne'>";
		//echo "<input type='hidden' name='migla_donation_fund_nonce' id='migla_donation_fund_nonce' value='" . esc_attr( $fund_nonce ) . "' />";	
		
		echo "<div class='row'><div class='col-sm-3'><label class='miglaCampaignNameLabel  control-label  text-right-sm text-center-xs'>". __('Campaign Name', 'migla-donation');
		echo "</label></div><div class='col-sm-6 col-xs-12'><span class='input-group input-group-control'><span class='input-group-addon '><i class='fa fa-medkit  fa-fw'></i>";
                echo "</span><input type='text' id='mName' placeholder='Name' class='form-control' /></span></div><div class='col-sm-3 hidden-xs'></div>";
                echo "<div class='col-sm-12 col-xs-12'><div class='help-control-center'>". __('Enter the name of the Campaign (e.g. Bulid a School)','migla-donation') ."</div></div></div>";
		
		echo "<div class='row'><div class='col-sm-3'><label class='miglaCampaignTargetLabel control-label  text-right-sm text-center-xs'>". __('Donation Target','migla-donation') ;
		echo "</label></div><div class='col-sm-6 col-xs-12'><span class='input-group input-group-control'><span class='input-group-addon'>";
     echo $this->getSymbol();
echo "</i></span><input type='text' class='form-control miglaNAD' placeholder='0' id='mAmount'></span></div><div class='col-sm-3 hidden-xs'></div><div class='col-sm-12 col-xs-12'><div  class='help-control-center'>". __("No currency symbol or decimal place. Leave blank if you don't want the progress bar.","migla-donation") . "</div></div></div>";
		
		echo "<p><button id='miglaAddCampaign' class='btn btn-info pbutton miglaAddCampaign' value='save'><i class='fa fa-fw fa-save'></i>". __(" save","migla-donation")."</button></p>";
		echo "<div></section><br></div>";

echo "<div class='col-sm-12'>";

		echo "<section class='panel'><header class='panel-heading'><div class='panel-actions'><a class='fa fa-caret-down' data-toggle='collapse' data-parent='.panel' href='#collapseTwo' aria-expanded='true'></a></div>";	

		echo "<h2 class='panel-title'><div class='dashicons dashicons-list-view'></div>". __("List of Available Campaigns","migla-donation") ."</h2></header>";
		echo "<div id='collapseTwo' class='panel-body collapse in'>";	


echo "<ul class='rows'>";
$data = get_option( 'migla_campaign' ) ;

$idk = 0;

if( empty($data) )
{
  echo __('You do not have any campaigns yet','migla-donation');
}else{

 foreach( (array)$data as $d => $value){

  echo "<li class='ui-state-default formfield clearfix'>";
 
  $n = $data[$idk]['name'];
  $t = $data[$idk]['target'];
  $s = $data[$idk]['show'];

echo "<input type='hidden' name='oldlabel' value='".$n."' />";
echo "<input type='hidden' name='label' value='".$n."' />";
echo "<input type='hidden' name='target' value='".$t."' />";
echo "<input type='hidden' name='show'  value='".$s."' />";

  echo"<div class='col-sm-1 hidden-xs'><label class='control-label'>". __('Campaign','migla-donation'). "</label></div>";
  echo "<div class='col-sm-3 col-xs-12'>"; 
  echo "<input type='text' class='labelChange' name='' placeholder='' value='".$n."' /></div>";

  echo "<div class='col-sm-1 hidden-xs'><label class='control-label'>". __('Target','migla-donation'). "</label></div>";
  echo "<div class='col-sm-2 col-xs-12'>";
  echo "<input type='text' class='targetChange miglaNAD' name='' placeholder='' value='" . $t . "' /></div>";


  $s = ""; $h = ""; $da = ""; $cl ="";
  if( strcmp($s,'1') == 0 ){ $s = "checked"; }else if( strcmp($s,'0') == 0 ){ $h = "checked"; 
  }else{ $da = "checked";$cl="pink-highlight" ;}

  echo "<div class='control-radio-sortable col-sm-5 col-xs-12'>";
  
  echo "<span><label><input type='radio' name='r".$idk."'  value='1' ".$s." class='' >". __(" Show","migla-donation") ."</label></span>";
  echo "<span><label><input type='radio' name='r".$idk."'  value='-1' ".$da." class=''>". __(" Deactivate","migla-donation") ."</label></span>";

  echo "<span><button class='removeField' data-toggle='modal' data-target='#confirm-delete'><i class='fa fa-fw fa-trash'></i></button></span>";
  echo "</div>";  

  $idk++;

  echo "</li>";
 }
}

echo "</ul>";

echo "<div class='row'><div class='col-sm-6'><button value='save' class='btn btn-info pbutton' id='miglaSaveCampaign'><i class='fa fa-fw fa-save'></i>". __(' update list of campaigns','migla-donation'). "</button></div></div>";

echo "</div></section>";
		
		echo "</div></div> <!--  -->";		

		

 echo " <div class='modal fade' id='confirm-delete' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true' data-backdrop='true'>
        <div class='modal-dialog'>
            <div class='modal-content'>
            
                <div class='modal-header'>


                    <button type='button' class='close' data-dismiss='modal' aria-hidden='true' data-target='#confirm-delete'><i class='fa fa-times'></i></button>
                    <h4 class='modal-title' id='myModalLabel'>". __("Confirm Delete","migla-donation"). "</h4>
                </div>
<div class='modal-wrap clearfix'>
           <div class='modal-alert'>
														<i class='fa fa-times-circle'></i>
													</div>  
   <div class='modal-body'>
                    <p>". __("Are you sure you want to delete this campaign? Deleting this campaign will not delete it from the reports","migla-donation") . "</p>
                </div>
</div> 
                
                <div class='modal-footer'>
                    <button type='button' class='btn btn-default mbutton' data-dismiss='modal'>". __("Cancel","migla-donation") ."</button>
                    <button type='button' id='mRemove' class='btn btn-danger danger rbutton' >". __("Delete","migla-donation") ."</button>
                   
                </div>
            </div>
        </div>
    </div></div>"; 

		echo "</div><!-- container-fluild  -->";

	}
	
}

?>