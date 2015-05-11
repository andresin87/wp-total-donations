<?php

class migla_top_level_class {
    public $migla_page = 'migla_donation_menu_page';
	function __construct() {
		add_action( 'admin_menu', array( $this, 'menu_item' ) );
	}
	
	function menu_item() {
          add_menu_page(
			__( 'Total Donations', 'migla-donation' ), //page title
			__( 'Total Donations', 'migla-donation' ), //menu title
			'manage_options', //capability
			'migla_donation_menu_page', //slug
			array( $this, 'menu_page' ), //function
                        plugins_url( 'totaldonations/images/icons/icon-admin-migla16.png' ) 
                      
	  );
	  do_action( 'migla_donation_menu' );
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
                 }else{ $i = $currencies[$key]['symbol']; }
              }
	   }

    return $i;
   }
	
	function menu_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'You do not have sufficient permissions to access this page.' );
		}
      
  $reporturl = get_admin_url()."admin.php?page=migla_reports_page";
  $settingpage = get_admin_url()."admin.php?page=migla_donation_settings_page";
  $symbol = $this->getSymbol();
  $thousandSep = get_option('migla_thousandSep');
  $decimalSep = get_option('migla_decimalSep');
  $placement = get_option('migla_curplacement');
  $showDecimal = get_option( 'migla_showDecimalSep');
 
      echo "<div class='wrap'><div class='container-fluid'>";
        echo "<h2 class='migla'>".__("Total Donations Dashboard","migla-donation"). "</h2>";
        echo "<div id='symbol' style='display:none'>".$symbol."</div>";
        echo "<div id='currency' style='display:none'>". get_option( 'migla_default_currency' ) ."</div>";
        echo "<input type='hidden' id='datenow' value='.date( 'm/d/Y').' />";
        echo "<input type='hidden' id='timenow' value='.date( 'H:i:s', current_time( 'timestamp', 0 ).' />";
        echo "<div style='display:none' id='thousandSep'>".$thousandSep."</div>";
        echo "<div style='display:none' id='decimalSep'>".$decimalSep."</div>";
        echo "<div style='display:none' id='placement'>".$placement."</div>";
         echo "<div style='display:none' id='showDecimal'>".$showDecimal."</div>";

		echo "<div class='row'>";
		
		 echo "<div class='col-lg-6 col-md-12'>
							<section class='panel panel-featured-left panel-featured-primary'>
										<div class='panel-body'>
											<div class='widget-summary'>
											<h2 class='panel-title'>".__("Latest Online Donations","migla-donation"). "</h2>
											<div class=''>
                        <div class='ibox-title'>
                            <h5>".__("Timeline","migla-donation")."</h5>
                            
                            
                        </div>

                        <div class='ibox-content '>

                        </div>


<div class='alignright'>  <a href='".$reporturl."'><button type='submit' id='miglaLatestButton' class='obutton btn' >".__(" Read More","migla-donation"). "</button></a> </div>

                    </div>
											
											</div>
										</div>
									</section></div>";
		
		
		
		
		
		
		echo "<div class='col-md-6 col-lg-6 col-xl-3'>
									<section class='panel panel-featured-left panel-featured-primary'>
										<div class='panel-body'>
											<div class='widget-summary'>
												<div class='widget-summary-col-icon'>
													<div class='summary-icon bg-primary'>";
		echo $symbol;											
		echo "</div>
												</div>
												<div class='widget-summary-col'>
													<div class='summary'>
														<h4 class='title'>".__("Total Donations:","migla-donation"). "</h4>
														<div class='info'>
															<strong class='amount' id='amount'>".$symbol."0</strong>
															<span class='badge alert-success' id='onAmount'>".$symbol."0 online</span>
														</div>
													</div>
													<div class='widget-footer'>
														<a class='text-muted text-uppercase' href='".$reporturl."'"; 
echo ">".__(" view reports","migla-donation"). "</a>
													</div>
												</div>
											</div>
										</div>
									</section>
								</div>";
								
								echo "<div class='col-md-6 col-lg-6 col-xl-3'>
									<section class='panel panel-featured-left panel-featured-primary'>
										<div class='panel-body'>
											<div class='widget-summary'>
												<div class='widget-summary-col-icon'>
													<div class='summary-icon bg-color-teal' style='font-size:3rem'>
														<i class='fa fa-calendar'></i>
													</div>
												</div>
												<div class='widget-summary-col'>
													<div class='summary'>
														<h4 class='title'>".__("This Month:","migla-donation"). "</h4>
														<div class='info'>
															<strong class='amount' id='monthAmount'>".$symbol."0</strong>
															<span class='badge alert-success' id='monthOnAmount'>".$symbol."0 online</span>
														</div>
													</div>
													<div class='widget-footer'>
														<a class='text-muted text-uppercase' href='".$reporturl."'"; 
echo ">".__(" view reports","migla-donation"). "</a>
													</div>
												</div>
											</div>
										</div>
									</section>
								</div>";
      
     echo "<div class='col-lg-6 col-md-12'>
							<section class='panel'>
								<header class='panel-heading panel-heading-transparent'>
									<div class='panel-actions'>
										<a class='fa fa-caret-down ' data-toggle='collapse' data-parent='.panel' href='#collapseOne' aria-expanded='true'></a>
										
									</div>

									<h2 class='panel-title'>".__("Campaign Progress","migla-donation"). "</h2> 
								</header>
								<div id='collapseOne' class='panel-body collapse in'>
									<div class='table-responsive'>
										<table class='table table-striped mb-none'>
											<thead>
												<tr>
													<th>#</th>
													<th>".__("Project","migla-donation")."</th>
													<th>".__("Status","migla-donation")."</th>
													<th>".__("Progress","migla-donation")."</th>
												</tr>
											</thead>
											<tbody>";
									
echo " </tbody>
										</table>
									</div>
								</div>
							</section>
						</div>";

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
        $now = $default ."<br>". date("F jS, Y", strtotime($d))."<br>".$t;
 	date_default_timezone_set( $php_time_zone );
       ///---------------------------------GET CURRENT TIME SETTINGS

echo "		

<div class='col-md-6 col-lg-6 col-xl-12'>
									<section class='panel panel-featured-left panel-featured-primary'>
										<div class='panel-body'>

<div class='widget-summary'>
											
											
                      <h3>".$now."</h3>

<div class='widget-footer'>
														<a href='".$settingpage."' class='text-muted text-uppercase'>".__("Change TimeZone","migla-donation")."</a>
													</div></div>









</div>
									</section>
								</div>";






						
echo "<div class='col-lg-6 col-md-12'>
									<section class='panel panel-featured-left panel-featured-primary'>
										<div class='panel-body'>

<h2 class='panel-title' id='migla-donation-title'>".__("Donation Graph","migla-donation")."</h2>


<!-- add later
<div class='panel-actions'>
<strong>
														<select id='salesSelector' class='form-control' style='display: none;'>
															<option selected='' value='Time Period'>".__("Time Period","migla-donation")."</option>
															<option value='One Month'>".__("One Month","migla-donation")."</option>
															<option value='Six Months'>".__("Six Months","migla-donation")."</option>
														</select><div class='btn-group'><button data-toggle='dropdown' class='multiselect dropdown-toggle btn btn-default' type='button' title='Porto Admin'>".__("Time Period ","migla-donation")."<b class='caret'></b></button><ul class='multiselect-container dropdown-menu pull-right'><li class='active'><a href=''><label class='radio'><input type='radio' name='multiselect' value='One Month'>".__("One Month","migla-donation")."</label></a></li><li><a href='javascript:void(0);'><label class='radio'><input type='radio' name='multiselect' value='Six Months'>".__("Six Months","migla-donation")."</label></a></li><li><a href='javascript:void(0);'><label class='radio'><input type='radio' name='multiselect' value='One Year'>".__("One Year","migla-donation")."</label></a></li></ul></div>
  </strong></div> -->



<br>
											 <div>
  <canvas id='sectionB' height='450' width='600'></canvas>
  <div id='legendDiv' class='legend'></div>
   </div>


										</div>
									</section>
								</div>


<!--
<div class='col-md-6 col-lg-6 col-xl-12'>
									<section class='panel panel-featured-left panel-featured-primary'>
										<div class='panel-body'>

<div class='widget-summary'>
											
											
                      <h3>".$now."</h3>

<div class='widget-footer'>
														<a href='".$settingpage."' class='text-muted text-uppercase'>".__("Change TimeZone","migla-donation")."</a>
													</div></div>









</div>
									</section>
								</div>
-->



</div></div>";						
	}
 
	       
}

?>