<?php
class migla_customize_theme_class {

	function __construct() {
		add_action( 'migla_donation_menu', array( $this, 'menu_item' ), 12 );
	}
	
	function menu_item() {
		add_submenu_page(
			'migla_donation_menu_page',
			__( 'Customize Theme', 'migla-donation' ),
			__( 'Customize Theme', 'migla-donation' ),
			'manage_options',
			'migla_donation_custom_theme',
			array( $this, 'menu_page' )
		);
	}
	
function hex2RGB($hex) 
{
        preg_match("/^#{0,1}([0-9a-f]{1,6})$/i",$hex,$match);
        if(!isset($match[1]))
        {
            return false;
        }

        if(strlen($match[1]) == 6)
        {
            list($r, $g, $b) = array($hex[0].$hex[1],$hex[2].$hex[3],$hex[4].$hex[5]);
        }
        elseif(strlen($match[1]) == 3)
        {
            list($r, $g, $b) = array($hex[0].$hex[0],$hex[1].$hex[1],$hex[2].$hex[2]);
        }
        else if(strlen($match[1]) == 2)
        {
            list($r, $g, $b) = array($hex[0].$hex[1],$hex[0].$hex[1],$hex[0].$hex[1]);
        }
        else if(strlen($match[1]) == 1)
        {
            list($r, $g, $b) = array($hex.$hex,$hex.$hex,$hex.$hex);
        }
        else
        {
            return false;
        }

        $color = array();
        $color['r'] = hexdec($r);
        $color['g'] = hexdec($g);
        $color['b'] = hexdec($b);

        return $color;
}	
	
	function menu_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.', 'migla-donation' ) );
		}
		 
 echo "<div class='wrap'><div class='container-fluid'>";                		
 
                echo "<h2 class='migla'>". __("Theme Customization","migla-donation")."</h2>";
		echo "<div class='row'>";
		echo "<div class='col-xs-12'>";
                  
		//FORM
                $bgcolor2 = explode(",", get_option('migla_2ndbgcolor'));
                $bgcborder = explode(",", get_option('migla_2ndbgcolorb') );
                

echo "<section class='panel'><header class='panel-heading'><div class='panel-actions'><a class='fa fa-caret-down' data-toggle='collapse' data-parent='.panel' href='#collapseOne' aria-expanded='true'></a></div><h2 class='panel-title'><div class='dashicons dashicons-admin-appearance'></div>". __('Customize the Form','migla-donation')."</h2></header>";

echo "<div id='collapseOne' class='panel-body collapse in'>";
		


// Secondary Options
echo "<div class='row'>";

echo "<input type='hidden' class='rgba_value' value='".$bgcolor2[0].",".$bgcolor2[1]."'>";
		echo "<div class='col-sm-3  col-xs-12'><label class='control-label text-right-sm text-center-xs'>".__('Panel Background:','migla-donation')." </label></div><div class='col-sm-6 col-xs-12'>
		 <span class='input-group'><span class='input-group-addon '><button id='currentColor' style='background-color:".$bgcolor2[0].";'></button></span><input type='text' class='form-control mg-color-field' data-opacity='".$bgcolor2[1]."' value='".$bgcolor2[0]."' id='migla_backgroundcolor'></span></div>";
		
		echo "<div class='col-sm-3 col-xs-12 text-left-sm text-center-xs'><button id='migla_2ndbgcolor' name='migla_2ndbgcolor' class='btn btn-info pbutton msave' value='save'><i class='fa fa-fw fa-save'></i>".__(" save","migla-donation")."</button></div>";

		echo "<span class='help-control col-sm-12 col-sm-pull-3  text-right-sm text-center-xs'>".__("This is the background color of the panel in the form. Default is grey.","migla-donation")." </span></div>";




echo "<div class='row'>";

echo "<input type='hidden' class='rgba_value' value='".$bgcborder[0].",".$bgcborder[1]."'>";

echo "<div class='col-sm-3  col-xs-12'><label class='control-label text-right-sm text-center-xs'>".__("Panel Border","migla-donation")."</label></div><div class='col-sm-3 col-xs-12'>
		
		
		 <span class='input-group'><span class='input-group-addon '><button style='background-color:".$bgcborder[0]."' id='currentColor'></button></span><input type='text' class='form-control mg-color-field' data-opacity='".$bgcborder[1]."' autocomplete='off' style='background-image: none;' value='".$bgcborder[0]."' id='migla_panelborder'></span></div>

<div class='col-sm-1 col-xs-12'>
  <label class='control-label  text-right-sm text-center-xs'>".__("Width","migla-donation")." </label>
		
		</div>

<div class='col-sm-2 col-xs-12 text-right-sm text-center-xs'>
   <div data-plugin-spinner='' data-plugin-options='{ &quot;value&quot;:0, &quot;min&quot;: 0, &quot;max&quot;: 10 }'>
														<div class='input-group' style=''>";
												
			
echo "<input type='text' class='spinner-input form-control' maxlength='2' value='".$bgcborder[2]."' >
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
		
		</div><div class='col-sm-3 col-xs-12 text-left-sm text-center-xs'><button id='migla_2ndbgcolorb' name='migla_2ndbgcolorb' class='btn btn-info pbutton' value='save'><i class='fa fa-fw fa-save'></i>".__(" save","migla-donation")."</button></div><span class='help-control col-sm-12 col-sm-pull-3  text-right-sm text-center-xs'>
".__(" This is the panel's border color and width in the form.","migla-donation")."</span></div>";



///////////////// Donor level boxes////////////////////////////

  $levelcolor = get_option('migla_bglevelcolor');
  if( $levelcolor == false ){ 
   add_option( 'migla_bglevelcolor' , '#eeeeee' );
  }else if(  $levelcolor == '' ){ 
   update_option( 'migla_bglevelcolor', '#eeeeee' ); 
  }

// Secondary Options
echo "<div class='row'>";

echo "<input type='hidden' class='rgba_value' value='".$levelcolor."'>";

		echo "<div class='col-sm-3  col-xs-12'><label class='control-label text-right-sm text-center-xs'>
".__("Giving Level Background:","migla-donation")."</label></div><div class='col-sm-6 col-xs-12'>
		 <span class='input-group'><span class='input-group-addon '><button id='currentColor' style='background-color:". $levelcolor.";'></button></span><input type='text' class='form-control mg-color-field' data-opacity='' value='". $levelcolor."' id='migla_bglevelcolor' name='migla_bglevelcolor'></span></div>";
		
		echo "<div class='col-sm-3 col-xs-12 text-left-sm text-center-xs'><button id='migla_bgcolorLevelsSave' name='migla_bgcolorLevelsSave' class='btn btn-info pbutton msave' value='save'><i class='fa fa-fw fa-save'></i>
".__(" save","migla-donation")."</button></div>";

echo "<span class='help-control col-sm-12 col-sm-pull-3  text-right-sm text-center-xs'>".__("  This is the background color of the suggested giving level.","migla-donation")."</span></div>";


  $borderlevelcolor = get_option('migla_borderlevelcolor');
  if( $borderlevelcolor == false ){ 
   add_option( 'migla_borderlevelcolor' , '#eeeeee' );
  }else if(  $borderlevelcolor == '' ){ 
   update_option( 'migla_borderlevelcolor', '#eeeeee' ); 
  }

  $borderlevel = get_option('migla_borderlevel');
  if( $borderlevel == false ){ 
   add_option( 'migla_borderlevel' , '1' );
  }else if(  $borderlevel == '' ){ 
   update_option( 'migla_borderlevel', '1' ); 
  }

echo "<div class='row'>";

echo "<input type='hidden' class='rgba_value' value='".$borderlevelcolor ."'>";

echo "<div class='col-sm-3  col-xs-12'><label class='control-label text-right-sm text-center-xs'>".__("Giving Level Border","migla-donation")."</label></div><div class='col-sm-3 col-xs-12'>
		
		
		 <span class='input-group'><span class='input-group-addon '><button style='background-color:".$borderlevelcolor ."' id='currentColor'></button></span><input type='text' class='form-control mg-color-field' data-opacity='' autocomplete='off' style='background-image: none;' value='".$borderlevelcolor."' id='migla_borderlevelcolor' name='migla_borderlevelcolor'></span></div>

<div class='col-sm-1 col-xs-12'>
  <label class='control-label  text-right-sm text-center-xs'>".__("Width","migla-donation")."</label>
		
		</div>

<div class='col-sm-2 col-xs-12 text-right-sm text-center-xs'>
   <div data-plugin-spinner='' data-plugin-options='{ &quot;value&quot;:0, &quot;min&quot;: 0, &quot;max&quot;: 10 }'>
														<div class='input-group' style=''>";
												
			
echo "<input type='text' class='spinner-input form-control' maxlength='2' value='".$borderlevel."' >
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
		
		</div><div class='col-sm-3 col-xs-12 text-left-sm text-center-xs'><button id='migla_borderlevelsave' name='migla_borderlevelsave' class='btn btn-info pbutton' value='save'><i class='fa fa-fw fa-save'></i>".__(" save","migla-donation")."</button></div><span class='help-control col-sm-12 col-sm-pull-3  text-right-sm text-center-xs'>".__("  This is the panel's border color and width for the suggested giving level. ","migla-donation")."</span></div>";





echo "</section><br></div>";
		
		echo "<div class='col-xs-12'>";

		echo "<section class='panel'><header class='panel-heading'><div class='panel-actions'><a class='fa fa-caret-down' data-toggle='collapse' data-parent='.panel' href='#collapseTwo' aria-expanded='true'></a></div><h2 class='panel-title'><div class='dashicons dashicons-admin-appearance'></div>
".__(" Customize Progress Bar","migla-donation")."</h2></header>";
		echo "<div id='collapseTwo' class='panel-body collapse in'>";

$progbarInfo = get_option('migla_progbar_info');

		// BEFORE First Row for text
		echo "<div class='row'>";
		echo "<div class='col-sm-3  col-xs-12'><label class='control-label text-right-sm text-center-xs'>". __("Progress Bar info:","migla-donation")."</label></div><div class='col-sm-6 col-xs-12'>
		<input type='textarea' id='migla_progressbar_text' class='form-control' value='".$progbarInfo."' cols='50' rows='2'></div>";
		echo "<div class='col-sm-3 col-xs-12 text-left-sm text-center-xs'><button id='migla_progressbar_info' name='migla_progressbar_info' class='btn btn-info pbutton' value='save'><i class='fa fa-fw fa-save'></i> 
".__(" save","migla-donation")."</button></div>";
		echo "<span class='help-control col-sm-12 col-sm-pull-3  text-right-sm text-center-xs'>  
".__(" This is the information that is above the progress bar. You can use the following shortcodes:","migla-donation")."<code>[total]</code><code>[target]</code><code>[campaign]</code><code>[percentage]</code></span></div>";
					
                 //BAR
                $borderRadius = explode(",", get_option( 'migla_borderRadius' )); //4spinner
                $bar_color = explode(",", get_option( 'migla_bar_color' ));  //rgba
                $progressbar_bg = explode(",", get_option( 'migla_progressbar_background' )); //rgba
                $boxshadow_color = explode(",", get_option( 'migla_wellboxshadow' )); //rgba 4spinner 

echo"<div class='row '>
  
  <div class='form-group touching'>
  
  <div class='col-sm-3  col-xs-12'><label class='control-label text-right-sm text-center-xs'>
".__("  Well Border Radius:","migla-donation")."</label></div>

  <div class='col-sm-1'>
  <label class='control-label  text-right-sm text-center-xs'>
".__("top-left","migla-donation")."</label>
		
		</div>


<div class='col-sm-2 col-xs-12 text-right-sm text-center-xs'>
   <div data-plugin-spinner='' data-plugin-options='{ &quot;value&quot;:0, &quot;min&quot;: 0, &quot;max&quot;: 10 }'>
														<div class='input-group' style=''>
<input type='text' class='spinner-input form-control' maxlength='2' name='topleft' value='".$borderRadius[0]."' id='migla_radiustopleft'>
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



  <div class='col-sm-1'>
  <label class='control-label  text-right-sm text-center-xs'>".__("top-right","migla-donation")."</label>
		
		</div>


<div class='col-sm-2 col-xs-12 text-right-sm text-center-xs'>
   <div data-plugin-spinner='' data-plugin-options='{ &quot;value&quot;:0, &quot;min&quot;: 0, &quot;max&quot;: 10 }'>
														<div class='input-group' style=''>
	<input type='text' class='spinner-input form-control' maxlength='2' name='topright'  value='".$borderRadius[1]."' id='migla_radiustopright'>
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






    <div class='col-sm-3 hidden-xs'></div> </div>
  
  
  <div class='form-group touching'>





  <div class='col-sm-3  col-xs-12'></div>


<div class='col-sm-1'>
  <label class='control-label  text-right-sm text-center-xs'>".__("bottom-left","migla-donation")."</label>
		
		</div>


<div class='col-sm-2 col-xs-12 text-right-sm text-center-xs'>
   <div data-plugin-options='{ &quot;value&quot;:0, &quot;min&quot;: 0, &quot;max&quot;: 10 }' data-plugin-spinner=''>
														<div style='' class='input-group'>
  <input type='text' maxlength='2' class='spinner-input form-control' name='bottomleft'  value='".$borderRadius[2]."' id='migla_radiusbottomleft'>
															<div class='spinner-buttons input-group-btn'>
																<button class='btn btn-default spinner-up' type='button'>
																	<i class='fa fa-angle-up'></i>
																</button>
																<button class='btn btn-default spinner-down' type='button'>
																	<i class='fa fa-angle-down'></i>
																</button>
															</div>
														</div>
													
  
  
  
        

		
		</div>
		
		</div>



  <div class='col-sm-1'>
  <label class='control-label  text-right-sm text-center-xs'>
".__("bottom-right","migla-donation")."</label>
		
		</div>


<div class='col-sm-2 col-xs-12 text-right-sm text-center-xs'>
   <div data-plugin-spinner='' data-plugin-options='{ &quot;value&quot;:0, &quot;min&quot;: 0, &quot;max&quot;: 10 }'>
														<div class='input-group' style=''>
  <input type='text' class='spinner-input form-control' maxlength='2' name='bottomright'  value='".$borderRadius[3]."' id='migla_radiusbottomright'>
															<div class='spinner-buttons input-group-btn'>
																<button type='button' class='btn btn-default spinner-up'>
																	<i class='fa fa-angle-up'></i>
																</button>
																<button type='button' class='btn btn-default spinner-down'>
																	<i class='fa fa-angle-down'></i>
																</button>
															</div></div>		
		</div></div><div class='col-sm-3 col-xs-12 text-left-sm text-center-xs'><button value='save' class='btn btn-info pbutton' name='migla_borderRadius' id='migla_borderRadius'><i class='fa fa-fw fa-save'></i>".__(" save","migla-donation")."</button></div> <span class='help-control col-sm-12 col-sm-pull-3  text-right-sm text-center-xs'>".__("This controls the round corners of the bar.","migla-donation")."</span>
    
      </div>
</div>";


		
		// First Row
		
		
		
		
		
		echo "<div class='row'>";
echo "<input type='hidden' class='rgba_value' value='".$bar_color[0].",".$bar_color[1]."'>";
		echo "<div class='col-sm-3  col-xs-12'><label class='control-label text-right-sm text-center-xs'>".__("Bar Color:","migla-donation")."</label></div><div class='col-sm-6 col-xs-12'>
<span class='input-group'><span class='input-group-addon'><button id='currentColor' style='background-color:".$bar_color[0].";'></button></span><input type='text' class='mg-color-field form-control' value='".$bar_color[0]."' data-opacity='".$bar_color[1]."' id='migla_barcolor'></span></div>";
		echo "<div class='col-sm-3 col-xs-12 text-left-sm text-center-xs'><button id='migla_bar_color' name='migla_bar_color' class='btn btn-info pbutton msave' value='save'><i class='fa fa-fw fa-save'></i>".__(" save","migla-donation")."</button></div>";
		echo "<span class='help-control col-sm-12 col-sm-pull-3  text-right-sm text-center-xs'>".__(" This is the color of the progress bar. ","migla-donation")." </span></div>";
		

// Second Row
		echo "<div class='row'>";
echo "<input type='hidden' class='rgba_value' value='".$progressbar_bg[0].",".$progressbar_bg[1]."'>";
		echo "<div class='col-sm-3  col-xs-12'><label class='control-label text-right-sm text-center-xs'>".__("Well Background:","migla-donation")."</label></div><div class='col-sm-6 col-xs-12'>
		<span class='input-group'><span class='input-group-addon'><button id='currentColor' style='background-color:".$progressbar_bg[0].";'></button></span><input type='text' class='mg-color-field form-control' value='".$progressbar_bg[0]."' data-opacity='".$progressbar_bg[1]."' id='migla_wellcolor'></span></div>";
		echo "<div class='col-sm-3 col-xs-12 text-left-sm text-center-xs'><button id='migla_progressbar_background' name='migla_progressbar_background' class='btn btn-info pbutton msave' value='save'><i class='fa fa-fw fa-save'></i>".__(" save","migla-donation")."</button></div>";
		echo "<span class='help-control col-sm-12 col-sm-pull-3  text-right-sm text-center-xs'>".__(" This is for the background inlay of the progress bar.","migla-donation")." </span></div>";


		
		echo "<div class='row '>";
echo "<input type='hidden' class='rgba_value' value='".$boxshadow_color[0].",".$boxshadow_color[1]."'>";
echo "<div class='form-group touching'>
    
    <div class='col-sm-3'><label class='control-label text-right-sm text-center-xs'>".__("Well Box Shadow:","migla-donation")."</label></div>
  
  <div class='col-sm-6 col-xs-12'>
<span class='input-group'><span class='input-group-addon'><button style='background-color:".$boxshadow_color[0].";' id='currentColor'></button></span><input type='text' value='".$boxshadow_color[0]."' class='mg-color-field form-control' data-opacity='".$boxshadow_color[1]."' autocomplete='off' style='background-image: none;' id='migla_wellshadow'></span></div> <br>
    
     <div class='col-sm-3'></div>
    
    <br> <br>
    
  </div>
  
  <div class='form-group touching'>
  
  <div class='col-sm-3  col-xs-12'></div>

  <div class='col-sm-1'>
  <label class='control-label  text-right-sm text-center-xs'>".__("h-shadow","migla-donation")."</label>
		
		</div>


<div class='col-sm-2 col-xs-12 text-right-sm text-center-xs'>
   <div data-plugin-options='{ &quot;value&quot;:0, &quot;min&quot;: 0, &quot;max&quot;: 10 }' data-plugin-spinner=''>
														<div style='' class='input-group'><input type='text'  maxlength='2' class='spinner-input form-control' name='hshadow' value='".$boxshadow_color[2]."' id='migla_hshadow'>
															<div class='spinner-buttons input-group-btn'>
																<button class='btn btn-default spinner-up2' type='button'>
																	<i class='fa fa-angle-up'></i>
																</button>
																<button class='btn btn-default spinner-down' type='button'>
																	<i class='fa fa-angle-down'></i>
																</button>
															</div>
														</div>
													
  
  
  
        

		
		</div>
		
		</div>



  <div class='col-sm-1'>
  <label class='control-label  text-right-sm text-center-xs'>".__("v-shadow","migla-donation")."</label>
		
		</div>


<div class='col-sm-2 col-xs-12 text-right-sm text-center-xs'>
   <div data-plugin-options='{ &quot;value&quot;:0, &quot;min&quot;: 0, &quot;max&quot;: 10 }' data-plugin-spinner=''>
														<div style='' class='input-group'><input type='text'  maxlength='2' class='spinner-input form-control' name='vshadow' value='".$boxshadow_color[3]."' id='migla_vshadow'>
															<div class='spinner-buttons input-group-btn'>
																<button class='btn btn-default spinner-up2' type='button'>
																	<i class='fa fa-angle-up'></i>
																</button>
																<button class='btn btn-default spinner-down' type='button'>
																	<i class='fa fa-angle-down'></i>
																</button>
															</div>
														</div>
													
  
  
  
        

		
		</div>
		
		</div>






    <div class='col-sm-3 hidden-xs'></div> </div>
  
  
  <div class='form-group touching'>





  <div class='col-sm-3  col-xs-12'></div>


<div class='col-sm-1'>
  <label class='control-label  text-right-sm text-center-xs'>".__("Blur","migla-donation")."</label>
		
		</div>


<div class='col-sm-2 col-xs-12 text-right-sm text-center-xs'>
   <div data-plugin-spinner='' data-plugin-options='{ &quot;value&quot;:0, &quot;min&quot;: 0, &quot;max&quot;: 10 }'>
														<div class='input-group' style=''><input type='text' class='spinner-input form-control' maxlength='2' name='blur' value='".$boxshadow_color[4]."' id='migla_blur'>
															<div class='spinner-buttons input-group-btn'>
																<button type='button' class='btn btn-default spinner-up2'>
																	<i class='fa fa-angle-up'></i>
																</button>
																<button type='button' class='btn btn-default spinner-down'>
																	<i class='fa fa-angle-down'></i>
																</button>
															</div>
														</div>
													
  
  
  
        

		
		</div>
		
		</div>



  <div class='col-sm-1'>
  <label class='control-label  text-right-sm text-center-xs'>".__("Spread","migla-donation")."</label>
		
		</div>


<div class='col-sm-2 col-xs-12 text-right-sm text-center-xs'>
   <div data-plugin-options='{ &quot;value&quot;:0, &quot;min&quot;: 0, &quot;max&quot;: 10 }' data-plugin-spinner=''>
														<div style='' class='input-group'><input type='text'  maxlength='2' class='spinner-input form-control' name='spread' value='".$boxshadow_color[5]."' id='migla_spread'>
															<div class='spinner-buttons input-group-btn'>
																<button class='btn btn-default spinner-up2' type='button'>
																	<i class='fa fa-angle-up'></i>
																</button>
																<button class='btn btn-default spinner-down' type='button'>
																	<i class='fa fa-angle-down'></i>
																</button>
															</div>
														</div>
													
  
  
  
        

		
		</div>
		
		</div>


<div class='col-sm-3 col-xs-12 text-left-sm text-center-xs'><button id='migla_wellboxshadow' name='migla_wellboxshadow' class='btn btn-info pbutton' value='save'><i class='fa fa-fw fa-save'></i>".__("save","migla-donation")."</button></div>














            
        <span class='help-control col-sm-12 col-sm-pull-3  text-right-sm text-center-xs'> ".__("This controls the inlay shadow.","migla-donation")."</span>
    
      </div>
</div>";










		
		
		// Fourth row
		$effect = (array)get_option( 'migla_bar_style_effect' );
                $check['yes'] = 'checked';
                $check['no'] = '';
		echo "<div class='row'>"; 
		echo "<div class='col-sm-3  col-xs-12'><label class='control-label text-right-sm text-center-xs'>".__("Bar Styling and Effects:","migla-donation")."</label></div>";
		echo "<div class='col-sm-6 col-xs-12'>
		
		
		
		<div class='list-group'>
		
		<label class='list-group-item border-check-control '>
		
		
		
		
		
		
                                <input type='checkbox' id='inlineCheckbox1' value='option1' ". $check[ ($effect['Stripes']) ]." class='meffects'> ".__("Stripes","migla-donation")."</label><label class='list-group-item border-check-control'>";

 $e =   $check[ ($effect['Pulse']) ]; 
                echo "<input type='checkbox' id='inlineCheckbox2' value='option2' ". $check[ ($effect['Pulse']) ]." class='meffects'>".__("Pulse","migla-donation")."</label><label class=' list-group-item border-check-control'>";

 $e =   $check[ ($effect['AnimatedStripes']) ]; 
                echo "<input type='checkbox' id='inlineCheckbox3' value='option3' ".$check[ ($effect['AnimatedStripes']) ]." class='meffects'>".__("Animated Stripes","migla-donation")."<span class='text-muted'><small> ".__("(Stripes must be on)","migla-donation")."</small></span></label><label class=' list-group-item border-check-control'>";

 $e =   $check[ ($effect['Percentage']) ]; 
               echo "<input type='checkbox' value='option4' id='inlineCheckbox4' ". $check[ ($effect['Percentage']) ]." class='meffects'>".__("Percentage","migla-donation")."</label>";
                echo "</div>";
              
		echo "<span class='help-control col-sm-12 text-center-xs'> ".__("This controls the progress bar's effects and styling. Settings are automatically saved.","migla-donation")."</span></div></div>";
		
		
		
		// Five Row Progress Bar
                $effectClasses = "";
                if( strcmp( $effect['Stripes'] , "yes") == 0){
                  $effectClasses = $effectClasses . " striped";
                }
                if( strcmp( $effect['Pulse'] , "yes") == 0){
                  $effectClasses = $effectClasses . " mg_pulse";
                }
                if( strcmp( $effect['AnimatedStripes'] ,"yes") == 0){
                  $effectClasses = $effectClasses . " active animated-striped";
                }
                if( strcmp( $effect['Percentage'], "yes") == 0 ){
                  $effectClasses = $effectClasses . " mg_percentage";
                }

        $style1 = "";
        $style1 .= "box-shadow:".$boxshadow_color[2]."px ".$boxshadow_color[3]."px ".$boxshadow_color[4]."px ".$boxshadow_color[5]."px " ;
        $style1 .= $boxshadow_color[0]." inset !important;";

        $style1 .= "background-color:". $progressbar_bg[0].";";

        $style1 .= "-webkit-border-top-left-radius:".$borderRadius[0]."px; -webkit-border-top-right-radius: ".$borderRadius[1]."px;";
        $style1 .= "-webkit-border-bottom-left-radius: ".$borderRadius[2]."px; -webkit-border-bottom-right-radius:".$borderRadius[3]."px;";

        $style1 .= "-moz-border-radius-topleft:".$borderRadius[0]."px; -moz-border-radius-topright: ".$borderRadius[1]."px;";
        $style1 .= "-moz-border-radius-bottomleft: ".$borderRadius[2]."px;-moz-border-radius-bottomright:".$borderRadius[3]."px;";

        $style1 .= "border-top-left-radius:".$borderRadius[0]."px; border-top-right-radius: ".$borderRadius[1]."px;";
        $style1 .= "border-bottom-left-radius:  ".$borderRadius[2]."px;border-bottom-right-radius:".$borderRadius[3]."px;";
	
        $stylebar = "background-color:".$bar_color[0].";";

		echo "<div class='row'>"; 
		echo "<div class='col-sm-3  col-xs-12'><label class='control-label text-right-sm text-center-xs'><strong>".__("Preview:","migla-donation")."</strong></label></div>";
                
		echo "<div class='col-sm-6 col-xs-12'><div class='progress ".$effectClasses."' id='me' style='".$style1."' >";
		echo "<div id='div2previewbar' style='width: 50%;".$stylebar."' aria-valuemax='100' aria-valuemin='0' aria-valuenow='20' role='progressbar' class='progress-bar'>50%</div></div></div>";

		//RESTORE
		echo "<div class='col-sm-3  col-xs-12'></div><div class='col-sm-12  col-xs-12'>
<p><button data-target='#confirm-reset' data-toggle='modal' value='reset' class='btn btn-info rbutton ' id=''><i class='fa fa-fw fa-refresh'></i>".__("Restore to Default","migla-donation")."</button></p></div>";
		echo "</div>";
		
		echo "</div></div> <!--  -->";	


		
//RESTORE
 echo " <div class='modal fade' id='confirm-reset' tabindex='-1' role='dialog' aria-labelledby='miglaWarning' aria-hidden='true' data-backdrop='true'>
        <div class='modal-dialog'>
            <div class='modal-content'>
            
                <div class='modal-header'>


                    <button type='button' class='close' data-dismiss='modal' aria-hidden='true' data-target='#confirm-reset'><i class='fa fa-times'></i> </button>
                    <h4 class='modal-title' id='miglaConfirm'>".__("Confirm Restore","migla-donation")."</h4>
                </div>
            
<div class='modal-wrap clearfix'>

           <div class='modal-alert'>
														<i class='fa fa-exclamation-circle'></i> 
													</div>  

   <div class='modal-body'>


                    <p>".__("Are you sure you want to restore all of the styling to their default styles? This can not be undone","migla-donation")."</p>
                </div>

</div> 
                
                <div class='modal-footer'>
                    <button type='button' class='btn btn-default mbutton' data-dismiss='modal'>".__("Cancel","migla-donation")."</button>
                    <button type='button' class='btn btn-danger danger rbutton' id='miglaRestore'><i class='fa fa-fw fa-refresh'></i>".__("Restore to default","migla-donation")."</button>
                   
                </div>
            </div>
        </div>
    </div>"; 

	
		echo "</div></div> <!-- container -->";
	
	}

}

?>