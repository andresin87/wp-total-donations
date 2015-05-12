<?php

class migla_plans_class {

	function __construct() {
		add_action( 'migla_donation_menu', array( $this, 'menu_item' ), 12 );
	}
	
	function menu_item() {
		add_submenu_page(
			'migla_donation_menu_page',
			__( 'Recurring Settings', 'migla-donation' ),
			__( 'Recurring Settings', 'migla-donation' ),
			'manage_options',
			'migla_plans_page',
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


echo "<div class='col-sm-12'>
   <section class='panel'>
      <header class='panel-heading'>
         <div class='panel-actions'><a aria-expanded='true' href='#collapseOne' data-parent='.panel' data-toggle='collapse' class='fa fa-caret-down'></a></div>
         <h2 class='panel-title'><i class='fa'></i>Add A New Recurring Donation Plan</h2>
      </header>
      <div class='panel-body collapse in' id='collapseOne'>";

echo "<div class='row'>";
echo "<div class='col-sm-12'>";


		echo "<div class='row'><div class='col-sm-3'><label class='control-label text-right-sm text-center-xs'>". __("Name","migla-donation"). "</label></div><div class='col-sm-6 col-xs-12'>";
echo "<input type='text' id='migla_planName' class='form-control'></div></div>";


       echo "<div class='row'><div class='col-sm-3'><label class='control-label text-right-sm text-center-xs'>". __("Interval Count","migla-donation"). "</label></div>";

        echo  "<div class='col-sm-3 col-xs-12'><input type='number' min='1' name='' placeholder='' value='1' id='migla_planTime'></div>
               <div class='col-sm-3 col-xs-12'>
                  <select id='migla_planPeriod'>
                     <option value='day' selected='selected'>". __("Day","migla-donation"). "</option>
                     <option value='week'>". __("Week","migla-donation"). "</option>
                     <option value='month'>". __("Month","migla-donation"). "</option>
                     <option value='year'>". __("Year","migla-donation"). "</option>
                  </select>
               </div>";
         echo "</div>";


		echo "<div class='row'><div class='col-sm-3'><label class='control-label text-right-sm text-center-xs'>". __("Payment Gateway","migla-donation"). "</label></div><div class='col-sm-6 col-xs-12'>";
echo "<div class='col-sm-3'><label for='method_paypal'><input type='radio' name='method' value='paypal' id='method_paypal' checked> Paypal Only</label></div>";
echo "<div class='col-sm-5'><label for='method_stripe_paypal'><input type='radio' name='method' value='paypal-stripe' id='method_stripe_paypal' > Paypal and Stripe</label></div>";
echo "</div></div>";

		echo "<div class='row'><div class='col-sm-3 col-xs-12 text-left-sm text-center-xs'></div><div class='col-sm-6'><br><button id='miglaAddPlan' class='btn btn-info pbutton' value='save'><i class='fa fa-fw fa-save'></i>". __(" save","migla-donation"). "</button></div></div>";

		echo "</div>";
      echo "</div>
   </section>
</div>";


 $plans = (array)get_option( 'migla_recurring_plans' ) ; $count = 0;

 echo "<div class='col-sm-12'>
   <section class='panel'>
      <header class='panel-heading'>
         <div class='panel-actions'><a aria-expanded='true' href='#collapseTwo' data-parent='.panel' data-toggle='collapse' class='fa fa-caret-down'></a></div>
         <h2 class='panel-title'><i class='fa'></i>The Current Recurring Donation Plans</h2>
      </header>
      <div class='panel-body collapse in' id='collapseTwo'>
         <ul class='mg_recurring_row containers ui-sortable'>";

   if( $plans[0] !='' )
   {
     $row = 0;
     foreach( (array)$plans as $plan )
     {
         $checked = array(); $checked['day'] = ""; $checked['week'] = ""; $checked['month'] = ""; $checked['year'] = "";
         switch ($plan['interval']) 
         {
           case "day": $checked['day'] = 'selected'; break;
           case "week": $checked['week'] = 'selected'; break;
           case "month": $checked['month'] = 'selected'; break;
           case "year": $checked['year'] = 'selected'; break;
         }

          echo "<li class='mg_reoccuring-field clearfix title formheader ui-sortable-handle '>  
              <div class='rows'> <div class='col-sm-1 clabel'>
                  <label class='control-label '>Name</label>
                  </div>
               <div class='col-sm-2 col-xs-12'><input type='text' class='name' name='' placeholder='' value='".$plan['name']."'></div>
               <div class='col-sm-1 hidden-xs'><label class='control-label'>". __("Interval","migla-donation"). "</label></div>
              


<div class='col-sm-2 col-xs-12 text-right-sm text-center-xs'>
   <div data-plugin-spinner='' data-plugin-options='{ }'>
														<div class='input-group' style=''><input type='text' value='".$plan['interval_count']."' class='spinner-input form-control' maxlength='2' value='1'>
     <div class='spinner-buttons input-group-btn'>
    <button type='button' class='btn btn-default spinner-up'>
    <i class='fa fa-angle-up'></i>
																</button>
																<button type='button' class='btn btn-default spinner-down'>
																	<i class='fa fa-angle-down'></i>
																</button>
															</div>
														</div>
															
		</div>
		
		</div>


<!--
 <div class='col-sm-1 col-xs-12'><input type='text' class='time' name='' placeholder='' value='".$plan['interval_count']."'></div> -->
               <div class='col-sm-2 col-xs-12  '>
                  <select id='' class='period' name=''>                 
                  <option value='day' ".$checked['day'].">Day</option>
                     <option value='week' ".$checked['week'].">Week</option>
                     <option value='month' ".$checked['month'].">Month</option>
                     <option value='year' ".$checked['year'].">Year</option>
                  </select>
               </div>

               

               <div class='control-radio-sortable col-sm-3 col-xs-12 form-group touching'><span><label><input type='radio' class='status' value='1' name='status".$row."'> Show</label></span><span><label><input type='radio' class='status' value='0' name='status".$row."'> Deactivate</label></span><span><button class='removeField'><i class='fa fa-fw fa-trash'></i></button></span></div>
           </div> 



<div class='rows'>               <div class='col-sm-1 '>
                  
                  </div>
               <div class='col-sm-2 col-xs-12'></div>
               <div class='col-sm-1 hidden-xs'><label class='control-label'>Gateways</label></div>               
               <div class='col-sm-4 col-xs-12  '>";
            
           if( $plan['payment_method'] == 'paypal' ){
                 echo  "<select name='method".$row."' class='method' id=''>                 
                         <option value='paypal' selected>PayPal</option>
                         <option value='paypal-stripe'>PayPal and Stripe</option>
                        </select>";
          }else{
                 echo  "<select name='method".$row."' class='method' id=''>                 
                         <option value='paypal'>PayPal</option>
                         <option value='paypal-stripe' selected>PayPal and Stripe</option>
                        </select>";
          }

   echo "</div>

               

               <div class='control-radio-sortable col-sm-3 col-xs-12 '>

<button value='save' class='btn btn-info pbutton migla_save_row'><i class='fa fa-fw fa-save'></i> save</button>


</div>
           
  </div>




</li>";

          $count++; $row++;
       }
    }else{
           echo "<div class='row'>
            <div class='col-sm-12'>You don't have any recurring plans</div>
         </div>";
    }

     echo "</ul>";

     if( $count > 0 ){
          echo "<div class='row'>
            <div class='col-sm-12'><br><button value='save' class='btn btn-info pbutton' id='miglaSavePlans'><i class='fa fa-fw fa-save'></i> Save Order</button></div>
         </div>";
     }

  echo  "</div> </section> </div>";

		
	} //Function Menu Page

}

?>