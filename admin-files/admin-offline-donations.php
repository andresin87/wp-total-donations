<?php

class migla_offline_donations_class{

function __construct(){
  add_action( 'migla_donation_menu', array( $this, 'menu_item' ), 7 );
}

	
function menu_item() {
 add_submenu_page( 
   'migla_donation_menu_page',
   __( 'Offline Donations', 'migla-donation' ),
   __( 'Offline Donations', 'migla-donation' ),
   'manage_options',
   'migla_offline_donations_page',
   array( $this, 'menu_page' )
   );
}

  function getSymbol(){
    $i = '';
    $currencies = get_option( 'migla_currencies' );
    $def = get_option( 'migla_default_currency' );

	   foreach ( (array)$currencies as $key => $value ) 
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
   if ( ! current_user_can( 'manage_options' ) ) {
     wp_die( __( 'You do not have sufficient permissions to access this page.', 'migla-donation' ) );
   }		
 
 echo "<div class='wrap'><div class='container-fluid'>";
   
   echo "<h2 class='migla'>Offline donations</h2>";
   echo "<div class='row'>";
   echo "<div class='col-sm-12'>";
   echo "<input type='hidden' id='miglaDecimalSep' value='".get_option('migla_decimalSep')."' />";
   
   echo "<section class='panel'><header class='panel-heading'><div class='panel-actions'><a class='fa fa-caret-down ' data-toggle='collapse' data-parent='.panel' href='#collapseOne' aria-expanded='true'></a></div><h2 class='panel-title'><div class='dashicons dashicons-plus'></div>". __( "Add a donation", "migla-donation")."</h2></header>";
   echo "<div id='collapseOne' class='panel-body collapse in'>";
   echo "<div id='row-upper'>";
   //echo "<form onSubmit='ajaxAdd(); return false;'>";
   echo "<div class='form-horizontal'> <div class='form-group touching'><div class='col-sm-3 col-xs-12'>";
   echo "<label class='control-label  text-right-sm text-center-xs required'>". __( "First Name: ", "migla-donation")."</label></div>";
   echo "<div class='col-sm-6 col-xs-12'><input name='miglad_firstname' type='text' class='required' /></div><div class='col-sm-3 hidden-xs'></div></div>";

   echo "<div class='form-group  touching'><div class='col-sm-3 col-xs-12'><label class='control-label  text-right-sm text-center-xs required'>". __( "Last Name: ", "migla-donation")."</label></div>";
   echo "<div class='col-sm-6 col-xs-12'><input name='miglad_lastname' type='text' class='required' /></div><div class='col-sm-3 hidden-xs'></div></div>";

   echo "<div class='form-group  touching'><div class='col-sm-3 col-xs-12'><label  class='control-label  text-right-sm text-center-xs required'>". __( "Amount: ", "migla-donation")."</label></div>";
   echo "<div class='col-sm-6 col-xs-12'><input class='form-control miglaNAD2 required' type='text' name='miglad_amount' size='10' value='' placeholder='' /></div><div class='col-sm-3 hidden-xs'></div></div>";

  echo "<div class='form-group  touching'><div class='col-sm-3 col-xs-12'><label  class='control-label  text-right-sm text-center-xs required' for='miglad_date'>". __( "Date: ", "migla-donation")."</label></div>";
   echo "<div class='col-sm-6 col-xs-12'><span class='input-group input-group-control mb-md'><span class='input-group-addon btn-success dashicons dashicons-calendar '></span><input class='miglaOffdate form-control custom_date required' type='text' name='miglad_date' size='10' value='' placeholder='' id='miglad_date'  /></span></div><div class='col-sm-3 hidden-xs'></div></div>";

   echo "<div class='form-group  touching'><div class='col-sm-3 col-xs-12'><label class='control-label  text-right-sm text-center-xs' for='miglad_orgname'>". __( "Organization: ", "migla-donation")."</label></div>";
   echo "<div class='col-sm-6 col-xs-12'><input name='miglad_orgname' type='text'  size='10' value='' />";
   echo "</span></div><div class='col-sm-3 hidden-xs'></div></div>";

   echo "<div class='form-group  touching'><div class='col-sm-3 col-xs-12'><label class='control-label  text-right-sm text-center-xs'>Anonymous: </label></div>";
   echo "<div class='col-sm-6 col-xs-12'><span class='checkbox-inline'><label><input type='checkbox' value='' name='miglad_anonymous'><small>". __( "Check this if you want the name to be hidden from the public. It will still be shown in the reports", "migla-donation")."</small></label></span></div><div class='col-sm-3 hidden-xs'></div></div>";

   echo "<div class='form-group  touching'><div class='col-sm-3 col-xs-12'><label id='mcampaign'  class='control-label  text-right-sm text-center-xs ' >". __( "Campaign : ", "migla-donation")."</label></div><div class='col-sm-6 col-xs-12'>";

  $label = get_option('migla_undesignLabel');
  if( $label == false ){ add_option('migla_undesignLabel', 'undesignated'); }
  if( $label == '' ){ $label = 'undesignated'; }

$fund_array = (array)get_option( 'migla_campaign' );
       echo "<select name='miglad_campaign' id='miglad_campaign'>"; 
	   echo "<option value='".$label."' checked>".$label."</option>";
           $b = "";
if( empty($fund_array[0]) ){}else{
	   foreach ( (array) $fund_array as $key => $value ) 
	   { 
	     if( strcmp($fund_array[$key]['show'],"1")==0  ){
                  $c_name = $fund_array[$key]['name'] ;
                  $c_name = str_replace( "[q]", "'", $c_name );
		    echo "<option value='".$fund_array[$key]['name']."' >".$c_name."</option>";
	     }
	   }	
}	   
	    echo "</select></div><div class='col-sm-3 hidden-xs'></div></div>"; 
	    
   echo "<div class='form-group  touching'><div class='col-sm-3 col-xs-12'><label class='control-label  text-right-sm text-center-xs'>". __( "Address: ", "migla-donation")."</label></div>";
   echo "<div class='col-sm-6 col-xs-12'><input name='miglad_address' type='textarea' /></div><div class='col-sm-3 hidden-xs'></div></div>";

   echo "<div class='form-group  touching'><div class='col-sm-3 col-xs-12'><label class='control-label  text-right-sm text-center-xs '>". __( "Email: ", "migla-donation")."</label></div>";
   echo "<div class='col-sm-6 col-xs-12'><input placeholder='' name='miglad_email' type='text' class='form-control'></div><div class='col-sm-3 hidden-xs'></div></div>";

   echo "<div class='form-group  touching'><div class='col-sm-3 col-xs-12'><label class='control-label  text-right-sm text-center-xs '>". __( "Country: ", "migla-donation")."</label></div><div class='col-sm-6 col-xs-12'>";
   $countries = get_option( 'migla_world_countries' );
   echo "<select id='country' name='miglad_country'> "; 
	   
   foreach ( $countries as $key => $value ) { 
	      if ( $value == get_option( 'migla_default_country' ) )
		  { 
		     echo "<option value='".$value."' selected >".$value."</option>"; 
		  }else{  
		    echo "<option value='".$value."'>".$value."</option>"; 
		  }
	   }	   
   echo "</select></div><div class='col-sm-3 hidden-xs'></div></div>"; 		
 

   echo "<div id='state' style='display:none'><div class='form-group  touching'><div class='col-sm-3 col-xs-12'> <label class='control-label  text-right-sm text-center-xs' >". __( "States ", "migla-donation")."</label></div>";
	   $states = get_option( 'migla_US_states' );
   echo "<div class='col-sm-6 col-xs-12'><select id='' name='miglad_state'>"; 
   echo "<option value=''>".__("Please pick one", "migla-donation")."</option>";
	   foreach ( $states as $key => $value ) 
	   { 
	      echo "<option value='".$value."'>".$value."</option>"; 
	   }	   
   echo "</select></div><div class='col-sm-3 hidden-xs'></div></div>"; 		
  echo "</div>";	   
	   
   echo "<div id='province' style='display:none'><div class='form-group  touching'><div class='col-sm-3 col-xs-12'><label class='control-label  text-right-sm text-center-xs'>". __( "Provinces", "migla-donation")."</label></div>";
	   $states = get_option( 'migla_Canada_provinces' );
   echo "<div class='col-sm-6 col-xs-12'><select id='' name='miglad_province'>"; 
   echo "<option value=''>".__("Please pick one", "migla-donation")."</option>";
	   foreach ( $states as $key => $value ) 
	   { 
	      echo "<option value='".$value."'>".$value."</option>"; 
	   }	   
   echo "</select></div><div class='col-sm-3 hidden-xs'></div></div>"; 		
   echo "</div>";	 

  // echo "<span>";
   echo "<div class='form-group  touching'><div class='col-sm-3 col-xs-12'><label class='control-label  text-right-sm text-center-xs' for='miglaOffzip'>". __( "Postal Code: ", "migla-donation")."</label></div>";
   echo "<div class='col-sm-6 col-xs-12'><input name='miglad_zip' type='text'  size='10' value='' />";
   echo "</span></div><div class='col-sm-3 hidden-xs'></div></div>";


   echo "<div class='form-group  touching'><div class='col-sm-3 col-xs-12'><label class='control-label  text-right-sm text-center-xs' for='miglad_employer'>". __( "Employer: ", "migla-donation")."</label></div>";
   echo "<div class='col-sm-6 col-xs-12'><input name='miglad_employer' type='text'  size='10' value='' />";
   echo "</span></div><div class='col-sm-3 hidden-xs'></div></div>";


   echo "<div class='form-group  touching'><div class='col-sm-3 col-xs-12'><label class='control-label  text-right-sm text-center-xs' for='miglad_occupation'>". __( "Occupation: ", "migla-donation")."</label></div>";
   echo "<div class='col-sm-6 col-xs-12'><input name='miglad_occupation' type='text'  size='10' value='' />";
   echo "</span></div><div class='col-sm-3 hidden-xs'></div></div>";


   echo "<div class='form-group  touching'><div class='col-sm-3 col-xs-12'><label class='control-label  text-right-sm text-center-xs' for='miglad_transactionType'>". __( "Type of payment : ", "migla-donation")."</label></div>";
   echo "<div class='col-sm-6 col-xs-12'><select id='miglad_transactionType' name='miglad_transactionType' >";
   echo "<option value=''>".__("Please pick one", "migla-donation")."</option>";
   echo "<option value='cash'>".__("Cash", "migla-donation")."</option>";
   echo "<option value='cheque'>".__("Cheque", "migla-donation")."</option>";
   echo "<option value='credit card'>".__("Credit Card", "migla-donation")."</option>";
   echo "</select>";
   echo "</span></div><div class='col-sm-3 hidden-xs'></div></div>";
   

    echo "<div class='form-group  touching'><div class='col-sm-12 center-button'><button value='save' class='btn btn-info pbutton'  id='miglaAddOffline'><i class='fa fa-fw fa-save'></i>". __( " save ", "migla-donation")."</button></div></div>";
   
   echo "</div>";
   //echo "</form>";
  

   echo "</div>";
  

   echo "</div>"; //col-container
   echo "</div>"; //col-sm-12
   echo "</div></section><br>";

echo "<section class='panel'>
							<header class='panel-heading'>
								<div class='panel-actions'>
									<a class='fa fa-caret-down ' data-toggle='collapse' data-parent='.panel' href='#collapseTwo' aria-expanded='true'></a>
									
								</div>
						
								<h1 class='panel-title'>". __( "Reports ", "migla-donation")."</h2>
							</header>
							<div id='collapseTwo' class='panel-body collapse in'>
								<div id='datatable-default_wrapper' class='dataTables_wrapper no-footer'><div class='table-responsive'>";
								
								
							   
   echo "<table id='miglaReportTable' class='display' cellspacing='0' width='100%'>";

   echo "<thead>";
   echo "<tr>";
   echo "<th class=''>Delete</th>";
   echo "<th class='detailsHeader' style='width:15px;'>Detail</th>";
   echo "<th class=''>". __("Date","migla-donation")."</th>";
   echo "<th class=''>". __("FirstName","migla-donation")."</th>";
   echo "<th class=''>". __("LastName","migla-donation")."</th>";
   echo "<th class=''>". __("Campaign","migla-donation")."</th>";
   echo "<th class=''>". __("Amount","migla-donation")."</th>";
   echo "<th class=''>". __("Country","migla-donation")."</th>";
   echo "<th></th>";
   echo "</tr>"; 
   echo "</thead>";

   echo "<tfoot><tr>";

   echo "<th id='f0' colspan='3'>";
   echo "<div data-plugin-datepicker='' class='input-daterange input-group migla-date-range-picker'>
   <span class='input-group-addon migla-date-range-icon'>
															<i class='fa fa-calendar'></i>
														</span>
														<input type='text' name='start' class='form-control miglaOffdate' placeholder='mm/dd/yyyy' id='sdate'>
														<span class='input-group-addon migla-to-date'>to</span>
														<input type='text' name='end' class='form-control miglaOffdate' placeholder='mm/dd/yyyy' id='edate'></div>";

   //echo "<th id='f2'>". __("Date","migla-donation")."</th>";   
   echo "<th id='f3'>". __("FirstName","migla-donation")."</th>";
   echo "<th id='f4'>". __("LastName","migla-donation")."</th>";
   echo "<th id='f5'>". __("Campaign","migla-donation")."</th>";
   echo "<th id='f6'>". __("Amount","migla-donation")."</th>";
   echo "<th id='f7'>". __("Country","migla-donation")."</th>";
   echo "<th id='f8'></th>";
   
   echo "</tr></tfoot>";
   echo "</table>";

echo "<div class='row datatables-footer'><div class='col-sm-12 col-md-6'>

   
   <button  class='btn rbutton'  id='miglaRemove' data-toggle='modal' data-target='#confirm-delete'>
   <i class='fa fa-fw fa-times'></i>". __( " remove ", "migla-donation")."</button>

<button class='btn mbutton' id='miglaUnselect' data-target='#unselect-all'>
   <i class='fa fa-fw fa-square-o '></i>". __( " Unselect All ", "migla-donation")."</button>

</div>
   
   <div class='col-sm-12 col-md-6'>

</div></div>";

   echo "  </div>   ";

   echo "</div> ";  
   
  $icon = $this->getSymbol();  
  $thousandSep = get_option('migla_thousandSep');
  $decimalSep = get_option('migla_decimalSep');
  $placement = get_option('migla_curplacement');
$showDecimal = get_option( 'migla_showDecimalSep');
        echo "<div style='display:none' id='thousandSep'>".$thousandSep."</div>";
        echo "<div style='display:none' id='decimalSep'>".$decimalSep."</div>";
        echo "<div style='display:none' id='placement'>".$placement."</div>";   
   	 echo "<div style='display:none' id='showDecimal'>".$showDecimal."</div>";							
 echo "<div id='symbol' style='display:none'>".$icon."</div>";

echo "</div></section> <div class='row'> <div class='col-sm-12 col-md-6'> <div class='tabs'>
								<ul class='nav nav-tabs nav-justified'>
									<li class='active'>
										<a class='text-center' data-toggle='tab' href='#thisreport' aria-expanded=''><i class='fa 

fa-star'></i>". __( " This Report", "migla-donation")."</a>
									</li>
									<li class=''>
										<a class='text-center' data-toggle='tab' href='#all' aria-expanded=''><i class='fa 

fa-star'></i>". __( " All Donations", "migla-donation")."</a>
									</li>
								</ul>
								<div class='tab-content'>
	<div class='tab-pane  active' id='thisreport'>									
  <div class='widget-summary'>
												<div class='widget-summary-col-icon'>
													<div class='summary-icon bg-primary'>";
	echo $icon;
        echo "</div>
												</div>
												<div class='widget-summary-col'>
													<div class='summary'>
														<h4 class='title'>". __( " Grand Total:", "migla-donation")."</h4>
														<div class='info'>
															<strong class='amount' id='miglaOnTotalAmount2'>".$icon."</strong>
															<span class='text-primary'></span>";
$export_url = plugins_url( '/export_offline.php', __FILE__ );

echo "<input id='exportTable' class='mbutton' type='submit' value='Export table to CSV file'>";
   echo "<form id='miglaExportTable' method='POST' action='" . esc_url( $export_url ) . "' >";
   echo "<input name='miglaFilters' type='hidden' >";
   echo "</form>";  
echo "</div>

<div class='widget-footer-2'>";
											
 
													echo "</div>												</div>
													
												</div>

											</div>
											
												
										
  </div>


<div id='all' class='tab-pane'>									
  <div class='widget-summary'>
												<div class='widget-summary-col-icon'>
													<div class='summary-icon bg-color-teal'>
														<i class='fa fa-check'></i>
													</div>
												</div>
												<div class='widget-summary-col'>
													<div class='summary'>
														<h4 class='title'>". __( " All Offline Donations:", "migla-donation")."</h4>
														<div class='info'>
															<strong class='amount' id='miglaOnTotalAmount'>".$icon."</strong>
															<span class='text-primary'></span>";


   echo "<input id='miglaExportAll' class='mbutton' type='submit' value='Export all to CSV file'>";

echo "</div>
													</div>



													
												</div>
											</div>
											
											
											</div></div></div></div> </div></div>


";	

echo "<div id='mg-warningconfirm1' style='display:none'>".__("You will delete these records:","migla-donation")."</div>";
echo "<div id='mg-warningconfirm2' style='display:none'>".__("Do you want to proceed?","migla-donation")."</div>";
echo "<div id='mg-warningconfirm3' style='display:none'>".__("A donation you wish to delete is reoccurring. Deleting this record will <strong>NOT</strong> stop those donations. Reoccurring donations must be stopped by PayPal","migla-donation")."</div>";									

 echo " <div class='modal fade' id='confirm-delete' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true' data-backdrop='true'>
        <div class='modal-dialog'>
            <div class='modal-content'>
            
                <div class='modal-header'>


                    <button type='button' class='close' data-dismiss='modal' aria-hidden='true' data-target='#confirm-delete'><i class='fa fa-times'></i></button>
                    <h4 class='modal-title' id='myModalLabel'>". __( " Confirm Delete", "migla-donation")."</h4>
                </div>
            
<div class='modal-wrap clearfix'>

           <div class='modal-alert'>
														<i class='fa fa-times-circle'></i>
													</div>  

   <div class='modal-body'>


                    <p>". __( " Are you sure you want to delete? This cannot be undone", "migla-donation")."</p>
                </div>

</div> 
                
                <div class='modal-footer'>
                    <button type='button' class='btn btn-default mbutton' data-dismiss='modal' id='miglacancel'>". __( "Cancel", "migla-donation")."</button>
                    <button type='button' class='btn btn-danger danger rbutton' >Delete</button>
                   
                </div>
            </div>
        </div>
    </div></div>"; 

}

}


?>