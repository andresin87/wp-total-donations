<?php
class migla_help_class {

	function __construct() {
		add_action( 'migla_donation_menu', array( $this, 'menu_item' ), 19 );
	}
	
	function menu_item() {
		add_submenu_page(
			'migla_donation_menu_page',
			__( 'Help', 'migla-donation' ),
			__( 'Help', 'migla-donation' ),
			'manage_options',
			'migla_donation_help',
			array( $this, 'menu_page' )
		);
	}
	
	function menu_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.', 'migla-donation' ) );
		}
 
 echo "<div class='wrap'><div class='container-fluid'>";                		
 
                echo "<h2 class='migla'>". esc_html__('Help', 'migla-donation'). "</h2>";
               
		
		 echo "";
		 
		 echo "<div class='row'>";



echo "<div class='col-md-6 col-lg-6 col-xl-12'>
									<section class='panel panel-featured-left panel-featured-primary'>
										<div class='panel-body'><h2 class='panel-title'>Advanced/Technical Settings</h2>";


/*
	echo "<div class='row'><br><div class='col-sm-8'><label class='check-control '>";
	
        if( get_option('migla_show_recover') == 'yes' ){
           echo "<input type='checkbox' id='migla_show_recover' class='mg-settings' checked />". __('Show Recovery Buttons', 'migla-donation')."</label>";
        }else{
           echo "<input type='checkbox' id='migla_show_recover' class='mg-settings' />". __('Show Recovery Buttons', 'migla-donation')."</label>";
        }
        echo "</div><div class='col-sm-4' id='migla_show_recover_'></div><span class='help-control col-sm-12'>". __('This buttons will show on edit form report','migla-donation')."</span></div>";
*/



        
	
        echo "<div class='row'><br><div class='col-sm-12'>Erase Total Donation's transient cache for data more than one day old.</div>";

echo "<div class='col-sm-12 '><br><button id='miglaEraseCache' style='width:120px' class='btn btn-info obutton ' value='save'><i class='fa fa-fw fa-times'></i>". __(" erase","migla-donation"). "</button></div>";

echo "<br><span class='help-control col-sm-12'>". __('Clicking this button will erase old cache data stored in WordPress by Total Donations','migla-donation')."</span></div>";


	echo "<div class='row'><div class='col-sm-8'><label class='check-control '>";
	
        if( get_option('migla_use_nonce') == 'yes' ){
              echo "<input type='checkbox' class='mg-settings' id='migla_use_nonce' checked />". __('Use nonce security on frontend form', 'migla-donation')."</label>";
        }else{
              echo "<input type='checkbox' class='mg-settings' id='migla_use_nonce' />". __('Use nonce security on frontend form', 'migla-donation')."</label>";
        }
        echo "</div><div class='col-sm-4  col-xs-12' id='migla_use_nonce_'></div><span class='col-sm-12 help-control'>". __('This is security against xss attacks. Disable if you have issues with compatibility','migla-donation')."</span></div>";


	echo "<div class='row'><div class='col-sm-8'><label class='check-control '>";
	
        if( get_option('migla_delete_settings') == 'yes' ){
              echo "<input type='checkbox' class='mg-settings' id='migla_delete_settings' checked />". __('Reset all settings to default when plugin is deactivated', 'migla-donation')."</label>";
        }else{
              echo "<input type='checkbox' class='mg-settings' id='migla_delete_settings' />". __('Reset all settings to default when plugin is deactivated', 'migla-donation')."</label>";
        }
        echo "</div><div class='col-sm-4  col-xs-12' id='migla_delete_settings_'></div><div class='col-sm-12 col-xs-12'><div class='help-control'>". __('When the plugin is activated again it will use the default settings','migla-donation')."</span></div>";

echo "</div></section></div>";


echo "<div class='col-md-6 col-lg-6 col-xl-12'>
									<section class='panel panel-featured-left panel-featured-primary'>
										<div class='panel-body'>

<div class='widget-summary'>
											
											
                      <h2 class='panel-title'>Documentation</h2>



<br><br>
 <i class='fa fa-fw fa-plane'></i>&nbsp;".__("Visit ","migla-donation")."<a href='http://calmar-webmedia.com/testing-area/wp-plugin-dev/documentation'>".__("here for complete documentation ","migla-donation")."</a>. 
<br><br>
<i class='fa fa-fw fa-question'></i>&nbsp;".__("Visit ","migla-donation")."<a href='http://calmar-webmedia.com/testing-area/wp-plugin-dev/shortcode-examples/'>".__("here for shortcode examples","migla-donation")."</a>. 
<br><br>
</div>

</div>
									</section>
								</div>";
		 
	
echo "<div class='col-md-6 col-lg-6 col-xl-12'>
									<section class='panel panel-featured-left panel-featured-primary'>
										<div class='panel-body'>

<div class='widget-summary shortcode-list'>
						

<h2 class='panel-title'>".__("ShortCode List","migla-donation")."<span class='panel-subtitle'>".__("These are all the shortcodes available currently","migla-donation")."</span></h2>";

							  echo  "";
									
									
									
      global $shortcode_tags;
      $arr = $shortcode_tags;
      foreach( (array)$arr as $key => $value ) {
        if( substr($key, 0, 14) == 'totaldonations' ){
         echo "<p><code>[". $key ."]</code></p>";
        }
      }
			
		echo  "</div>









</div>
									</section>
								</div>
"; 

                                                     echo "</div></div></div>";			
							
							
							
				
							
							
							
							
							
							
							
							
							
							
							
							
							
							
               
               
               
                }
              }
?>