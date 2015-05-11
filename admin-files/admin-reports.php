<?php

class migla_reports_class{

function __construct(){
  add_action( 'migla_donation_menu', array( $this, 'menu_item' ), 7 );
}

    
function menu_item() {
 add_submenu_page( 
   'migla_donation_menu_page',
   __( 'Online Donations', 'migla-donation' ),
   __( 'Online Donations', 'migla-donation' ),
   'manage_options',
   'migla_reports_page',
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
   
    echo "<h2 class='migla'>". __( "Online Donations","migla-donation")."</h2>";

echo "<section class='panel'>
							<header class='panel-heading'>
								<div class='panel-actions'>
									<a class='fa fa-caret-down ' data-toggle='collapse' data-parent='.panel' href='#collapseTwo' aria-expanded='true'></a>
									
								</div>
						
								<h1 class='panel-title'>". __("Reports","migla-donation")."</h2>
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
   echo "<th class=''>". __("Transaction","migla-donation")."</th>";
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
   echo "<th id='f8'>". __("Transaction","migla-donation")."</th>";
   echo "<th id='f9'></th>";
   
   echo "</tr></tfoot>";
   echo "</table>";

echo "<div class='row datatables-footer'><div class='col-sm-12 col-md-6'>

   
   <button  class='btn rbutton'  id='miglaRemove' data-toggle='modal' data-target='#confirm-delete'>
   <i class='fa fa-fw fa-times'></i>". __("REMOVE ","migla-donation")."</button>

<button class='btn mbutton' id='miglaUnselect' data-target='#unselect-all'>
   <i class='fa fa-fw fa-square-o '></i>". __(" Unselect All ","migla-donation")."</button>

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

fa-star'></i>". __(" This Report ","migla-donation")."</a>
									</li>
									<li class=''>
										<a class='text-center' data-toggle='tab' href='#all' aria-expanded=''><i class='fa 

fa-star'></i>". __(" All Donations","migla-donation")."</a>
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
														<h4 class='title'>". __(" Grand Total:","migla-donation")."</h4>
														<div class='info'>
															<strong class='amount' id='miglaOnTotalAmount2'>".$icon."</strong>
															<span class='text-primary'></span>";
$export_url = plugins_url( '/export.php', __FILE__ );

echo "<input id='exportTable' class='mbutton' type='submit' value='". __(" Export table to CSV file","migla-donation")."'>";
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
														<h4 class='title'>". __(" All Online Donations:","migla-donation")."</h4>
														<div class='info'>
															<strong class='amount' id='miglaOnTotalAmount'>".$icon."</strong>
															<span class='text-primary'></span>";


   echo "<input id='miglaExportAll' class='mbutton' type='submit' value='". __("Export all to CSV file","migla-donation")."'>";

echo "</div>
													</div>



													
												</div>
											</div>
											
											
											</div></div></div></div> </div></div>


";	

echo "</div>";


 echo " <div class='modal fade' id='confirm-delete' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true' data-backdrop='true'>
        <div class='modal-dialog'>
            <div class='modal-content'>
            
                <div class='modal-header'>


                    <button type='button' class='close' data-dismiss='modal' aria-hidden='true' data-target='#confirm-delete'><i class='fa fa-times'></i></button>
                    <h4 class='modal-title' id='myModalLabel'>". __("Confirm Delete","migla-donation")."</h4>
                </div>
            
<div class='modal-wrap clearfix'>

           <div class='modal-alert'>
														<i class='fa fa-times-circle'></i>
													</div>  

   <div class='modal-body'>


                    <p>". __("Are you sure you want to delete? This cannot be undone","migla-donation")."</p>
                </div>

</div> 
                
                <div class='modal-footer'>
                    <button type='button' class='btn btn-default mbutton' data-dismiss='modal'>". __("Cancel","migla-donation")."</button>
                    <button type='button' class='btn btn-danger danger rbutton' >". __("Delete","migla-donation")."</button>
                   
                </div>
            </div>
        </div>
    </div>"; 

echo "<div id='mg-warningconfirm1' style='display:none'>".__("You will delete these records:","migla-donation")."</div>";
echo "<div id='mg-warningconfirm2' style='display:none'>".__("Do you want to proceed?","migla-donation")."</div>";
echo "<div id='mg-warningconfirm3' style='display:none'>".__("A donation you wish to delete is reoccurring. Deleting this record will <strong>NOT</strong> stop those donations. Reoccurring donations must be stopped by PayPal","migla-donation")."</div>";

 echo "<div class='modal fade' id='mg-edit-record' tabindex='-1' role='dialog' aria-labelledby='mgModalEditLabel' aria-hidden='true'>
  <div class='modal-dialog'>
    <div class='modal-content'>
      <div class='modal-header'>
        <button type='button' id='mg-edit-record-close' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
        <input type='hidden' name='recordID' id='recordID' val='' disabled>
        <h4 class='modal-title' id='mgModalEditLabel'>". __("Edit Record Form","migla-donation")."</h4>
      </div>
      <div class='modal-body'>
      </div>
      <div class='modal-footer'>";

if( get_option('migla_show_recover') == 'yes' ){

echo       "<button type='button' id='mg_restore_record1' class='btn btn-primary obutton alignleft'><i class='fa fa-refresh'></i>
". __(" Recover from transient","migla-donation")."</button>
       <button type='button' id='mg_restore_record2' class='btn btn-primary obutton alignleft'><i class='fa fa-refresh'></i>
". __(" Recover from paypal","migla-donation")."</button>
       <button type='button' id='mg_restore_record3' class='btn btn-primary obutton alignleft'><i class='fa fa-refresh'></i>
". __(" Recover initial data","migla-donation")."</button>";
echo "<br><br><br>";
}

echo "<button type='button' class='btn btn-default mbutton' data-dismiss='modal'>". __("Cancel","migla-donation")."</button>";
echo "        <button type='button' id='mg_update_record' class='btn btn-primary pbutton '><i class='fa fa-fw fa-save'></i>". __(" Save changes","migla-donation")."</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->"; 


}

}


?>