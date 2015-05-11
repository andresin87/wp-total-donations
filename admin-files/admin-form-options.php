<?php

class migla_form_settings_class {

	function __construct(  ) {
		add_action( 'migla_donation_menu', array( $this, 'menu_item' ), 12 );

	}
		
	function menu_item() {
		add_submenu_page(
			'migla_donation_menu_page',
			__( 'Form Options', 'migla-donation' ),
			__( 'Form Options', 'migla-donation' ),
			'manage_options',
			'migla_donation_form_options_page',
			array( $this, 'menu_page' )
		);
	}

function migla_campaign_section( $postCampaign , $label )
{
   $out = "";

    $campaign = (array)get_option( 'migla_campaign' );
    $undesign = get_option('migla_undesignLabel');
    $undesign = esc_attr($undesign);   
   
   if( empty($campaign[0]) ){ 

    $out .= "<select name='campaign' class='mg_form-control' id='miglaform_campaign' disabled='disabled'>";
    $b = ""; $i = 0;   
    $out .= "<option value='". esc_html($undesign)."'>".esc_html($undesign)."</option>";
	$out .= "</select>"; 
			
  }else{    
    $b = ""; $i = 0;  $out2 = ""; $campaignCount = 0;
 	
    $out2 .= "<select name='campaign' class='mg_form-control' id='miglaform_campaign' disabled='disabled'>";

   if( get_option('migla_hideUndesignated') == 'no' ){
     $out2 .= "<option value='". esc_html($undesign)."'>".esc_html($undesign)."</option>";
     $campaignCount++;
   }
	
	foreach ( (array)$campaign as $key => $value ) 
	{ 
	    if( strcmp( $campaign[$i]['show'],"1")==0 ){
                  $campaignCount++;
                  $c_name = esc_html__( $campaign[$i]['name'] );
                  $c_name = str_replace( "[q]", "'", $c_name );

                  if( strcmp($c_name, $postCampaign) == 0  ){
		    $out2 .= "<option value='".esc_html__( $campaign[$i]['name'] )."' selected >".$c_name."</option>";
                  }else{
		    $out2 .= "<option value='".esc_html__( $campaign[$i]['name'] )."' >".$c_name."</option>";
                  }
	   }
           $i++;
	}  

	$out2 .= "</select>"; 

    if( $campaignCount > 0 ){
      $out .= $out2;	
   
    }else{

       $out .= "<select name='campaign' class='mg_form-control' id='miglaform_campaign' disabled='disabled'>";
       $b = ""; $i = 0;   
       $out .= "<option value='". esc_html($undesign)."'>".esc_html($undesign)."</option>";
       $out .= "</select>"; 
	
    }
  }	   
  
  return $out;

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
			wp_die( __( 'You do not have sufficient permissions to access this page.', 'migla-donation' ) );
		}
		
echo "<div class='wrap'><div class='container-fluid'>";       
                
                echo "<h2 class='migla'>
".__(" Form options","migla-donation")."</h2>";
		echo "<div class='row'>";
		echo "<div class='col-sm-12'>";
		
		///////////////////////////////////////////////////////////////
		// Giving Levels Sections Table
		$amounts = get_option( 'migla_amounts' ) ;
                $x[0] = get_option('migla_thousandSep');
                $x[1] = get_option('migla_decimalSep');
                $showSep = get_option('migla_showDecimalSep');
                $numDecimal = 0;
		
               $curSymbol = $this->getSymbol();

		echo "<section class='panel'><header class='panel-heading'><div class='panel-actions'><a class='fa fa-caret-down' data-toggle='collapse' data-parent='.panel' href='#collapseOne' aria-expanded='true'></a></div><h2 class='panel-title'><span>".$curSymbol."</span>".__("Suggested Giving Levels","migla-donation")."</h2></header>";
		echo "<div id='collapseOne' class='panel-body collapse in'>";
		echo "<div class='row'><div class='col-sm-3 col-xs-12'><label class='control-label text-right-sm text-center-xs'>".__("Add a suggested giving level","migla-donation")."</label></div>";
		
		echo "<div class='col-sm-6 col-xs-12'><span class='input-group input-group-control'><span id='curSymbol' class='input-group-addon'>";
                echo $curSymbol."</span><input type='text' class='form-control miglaNAD2' placeholder='0' id='miglaAddAmount'></span></div>";																					echo "<div class='center-button col-sm-12'><input id='miglaAddAmountButton' class='mbutton' type='button' value='add' >";
		echo "</div></div>";
		if( sizeof($amounts) > 0 ){
            echo "<div id='miglaAmountTable'>";
			sort($amounts); 
			 foreach( (array)$amounts as $key => $value ){
			  if( $value != ''){
                          if( $showSep == 'yes' ){
                             $valLabel = str_replace(".", $x[1] , $value);
                           }else{
                             $digit = explode( ".", $value ) ;
                             $valLabel = $digit[0];
                              //$valLabel = str_replace(".", $x[1] , $value);
                           }
 
			   echo "<p id='amount".$key."'>";
                           echo "<input class='value' type=hidden id='".$value."' />";
			    echo "<label>". $valLabel. "</label>";	
			   
			   echo "<button name='miglaAmounts' class='miglaRemoveLevel obutton'><i class='fa fa-times'></i></button>";
			   echo "</p>";
			  }
			 }
			 
           }
			 echo "</div>";

		echo "<p id='warningEmptyAmounts' style='display:none'>".__("No amounts have been added. Add some amounts above.","migla-donation")."<i class='fa fa-fw fa-caret-up'></i></p>";
		
		echo "</section>";
		echo "</div>";

		///////////////////////////////////////////////////////////////		
		// Currency Section	
			echo "<div class='col-sm-6'>";
		$currencies = get_option( 'migla_currencies' ) ; $icon ='';
                $def = get_option( 'migla_default_currency' );
  
	   echo "<section class='panel'><header class='panel-heading'><div class='panel-actions'><a class='fa fa-caret-down' data-toggle='collapse' data-parent='.panel' href='#collapseTwo' aria-expanded='true'></a></div><h2 class='panel-title'><i class='fa fa-fw fa-money'></i>".__("Default Currency Selection","migla-donation")."</h2></header>";
	   echo "<div id='collapseTwo' class='panel-body  collaspe in'><div class='row'>";
	   echo "<div class='col-sm-3 col-xs-12'><label class='control-label text-right-sm text-center-xs'>". __("Set currency","migla-donation");
           echo "</label></div>";
           echo "<div class='col-sm-6 col-xs-12'><select id='miglaDefaultCurrency' name='miglaDefaultCurrency'>"; 
	   foreach ( (array)$currencies as $key => $value ) 
	   { 
	      if ( strcmp($def,$currencies[$key]['code'] ) == 0 )
              { 
                 echo "<option value='".$currencies[$key]['code']."' selected='selected' >".$currencies[$key]['code']."</option>"; 
                 if( $currencies[$key]['faicon']!='' ) { 
                     $icon = "<i class='fa ".$currencies[$key]['faicon']."'></i>";
                     //$icon = $currencies[$key]['faicon']; 
                 }else{ $icon = $currencies[$key]['symbol']; }
              }else{  
                 echo "<option value='".$currencies[$key]['code']."'>".$currencies[$key]['code']."</option>"; 
              }
	   }	   
	   echo "</select></div>";
	   

if( strcmp($showSep,"yes")==0 ){ $numDecimal = 2; } 

$num = number_format("10000", $numDecimal, $x[1], $x[0]);

$placement = get_option('migla_curplacement');
if( strtolower( $placement ) == 'before' ){
  $before = $icon; $after='';
}else{
  $before = ''; $after= $icon;
}
echo "<div style='display:none' id='sep1'>".$x[0]."</div>";
echo "<div style='display:none' id='sep2'>".$x[1]."</div>";
echo "<div style='display:none' id='placement'>".$placement."</div>";
echo "<div style='display:none' id='showDecimal'>".$showSep."</div>";
echo "<div style='display:none' id='icon'>".$icon."</div>";
 			   
           echo "<div class='col-sm-3 hidden-xs' id='currencyIcon'>";
		   echo "<label id='miglabefore'>".$before."</label><label id='miglanum'>".$num."</label>";
		   echo "<label id='miglaafter'>".$after."</label></div>"; 

echo"</div><div class='row'><div class='col-sm-3 col-xs-12'><label class='control-label text-right-sm text-center-xs'>".__("Sign Location","migla-donation")."</label></div><div class='col-sm-6 col-xs-12'>";
echo "<select name='miglaDefaultplacement' id='miglaDefaultPlacement'>";
if( strtolower( $placement ) == 'before' ){
echo "<option value='before' checked>".__("Before","migla-donation")."</option><option value='after'>".__("After","migla-donation")."</option>";
}else{
echo "<option value='before' >".__("Before","migla-donation")."</option><option value='after' checked>".__("After","migla-donation")."</option>";
}
echo "</select></div></div>";

echo "<div class='row'> <div class='col-sm-3 col-xs-12'><label class='control-label text-right-sm text-center-xs'>".__("Separators","migla-donation")."</label></div> <div class='col-sm-3 col-xs-12 text-right-sm text-center-xs'> <input type='text' placeholder='Thousands' class=' form-control' id='thousandSep' name='thousandSep'>";

echo "</div> <div class='col-sm-3 col-xs-12 text-right-sm text-center-xs'> <input type='text' placeholder='Decimal' class=' form-control' id='decimalSep' name='decimalSep'> </div> </div>";

$checkShowSep = "";
if( strcmp($showSep , "yes") == 0 ){ $checkShowSep = "checked"; }
echo "<div class='row'><div class='col-sm-3 col-xs-12'><label class='control-label text-right-sm text-center-xs'>".__("Show Decimal Place","migla-donation")."</label></div><div class='col-sm-6 col-xs-12 text-left-sm text-center-xs'><input type='checkbox' name='mHideDecimalCheck' id='mHideDecimalCheck' ".$checkShowSep." ></div><div class='col-sm-3 hidden-xs'></div></div>";

	
		echo "<div class='row'><div class='col-sm-12 center-button'><button value='save' class='btn btn-info pbutton msave' id='miglaSetCurrencyButton'><i class='fa fa-fw fa-save'></i>".__(" save","migla-donation")."</button></div>";
		echo "</div></div>";
				
		echo "</section></div>";

		///////////////////////////////////////////////////////////////
		// Country Section	
		echo "<div class='col-sm-6'>";	
		$countries = get_option( 'migla_world_countries' );
		
	   echo "<section class='panel'><header class='panel-heading'><div class='panel-actions'><a class='fa fa-caret-down' data-toggle='collapse' data-parent='.panel' href='#collapseThree' aria-expanded='true'></a></div><h2 class='panel-title'><i class='fa fa-fw fa-flag'></i>".__("Default Country Section","migla-donation")."</h2></header>";
	      echo "<div id='collapseThree' class='panel-body collapse in'><div class='row'>";
	   echo "<div class='col-sm-3 col-xs-12'><label class='control-label text-right-sm text-center-xs'>". __("Set country","migla-donation");
        echo "</label></div>";
       echo "<div class='col-sm-6 col-xs-12'><select id='miglaDefaultCountry' name='miglaDefaultCountry'>"; 
	   foreach ( (array) $countries as $key => $value ) 
	   { 
	      if ( $value == get_option( 'migla_default_country' ) )
		  { 
		    echo "<option value='".$value."' selected >".$value."</option>"; 
		  }else{  
		    echo "<option value='".$value."'>".$value."</option>"; 
		  }
	   }	   
	   echo "</select></div><div class='col-sm-3 hidden-xs'></div>"; 
		
		echo "<div class='col-sm-12 center-button'><button value='save' class='btn btn-info pbutton' id='miglaSetCountryButton'><i class='fa fa-fw fa-save'></i>".__(" save","migla-donation")."</button>";
		
		echo "</div><div></section>";	
		echo "</div>";
		

///////////////////////////////Undesignated Campaign
  $label = get_option('migla_undesignLabel');
  if( $label == false ){ add_option('migla_undesignLabel', 'undesignated'); }
  if( $label == '' ){ $label = 'undesignated'; }

  $hidelabel = get_option('migla_hideUndesignated');
  if( $hidelabel == false ){ add_option('migla_hideUndesignated', 'no'); }
  if( $hidelabel == '' ){ $hidelabel = 'no'; }

  echo "<input type='hidden' id='mg_oldUnLabel' value='".$label."'>";
		

echo "<br><div class='col-sm-6'><section class='panel'><header class='panel-heading'><div class='panel-actions'><a aria-expanded='true' href='#collapseFive' data-parent='.panel' data-toggle='collapse' class='fa fa-caret-down '></a></div><h2 class='panel-title'><i class='fa fa-fw fa-bullhorn'></i>".__("Undesignated Category Options","migla-donation")."</h2></header><div id='collapseFive' class='panel-body collapse in'><div class='row'><div class='col-sm-3'><label class='miglaCampaignTargetLabel control-label  text-right-sm text-center-xs'>".__("Label:","migla-donation")."</label></div><div class='col-sm-6 col-xs-12'><input type='text' id='mg-undesignated-default' placeholder='".$label."' class='form-control ' value='".$label."'></div><div class='col-sm-3 hidden-xs'></div></div>
<div class='row'><div class='col-sm-3 col-xs-12'><label class='control-label text-right-sm text-center-xs text-right-sm text-center-xs'>".__("Hide Category on Form","migla-donation")."</label></div><div class='col-sm-9 col-xs-12 text-left-sm text-center-xs'>";

if(  $hidelabel == 'yes' ){
echo "<input type='checkbox' name='mHideUndesignatedCheck' id='mHideUndesignatedCheck' checked='checked'></div><div class='col-sm-3 hidden-xs'></div></div>";
} else{
echo "<input type='checkbox' name='mHideUndesignatedCheck' id='mHideUndesignatedCheck' ></div><div class='col-sm-3 hidden-xs'></div></div>";
}

echo "<div class='row'><div class='col-sm-12 center-button'><button id='miglaUnLabelChange' class='btn btn-info pbutton msave' value='save'><i class='fa fa-fw fa-save'></i>".__(" save","migla-donation")."</button></div></div>
</div></section><br></div>";
		
		////////////////////////////////////////////////////////////////////////////////////////
		// Fields and Sections Table PART 3
		echo "<div class='col-sm-12 hidden-xs'>";
		echo "<section class='panel'><header class='panel-heading'><div class='panel-actions'><a class='fa fa-caret-down' data-toggle='collapse' data-parent='.panel' href='#collapseFour' aria-expanded='true'></a></div><h2 class='panel-title'><i class='fa fa-fw fa-check-square-o'></i>".__("Form Fields","migla-donation")."<span class='panel-subtitle'>".__("Drag and drop fields and groups or add new ones","migla-donation")."</span></h2></header><div id='collapseFour' class='panel-body collapse in'><div class='row'><div class='col-sm-12 groupbutton'><button value='save' class='btn btn-info pbutton miglaSaveForm' id='miglaSaveFormTop'><i class='fa fa-fw fa-save'></i>".__(" save form","migla-donation")."</button><button class='btn btn-info obutton mAddGroup' value='add'><i class='fa fa-fw fa-plus-square-o'></i>".__("Add Group","migla-donation")."</button></div>";


////////HERE
echo "<div id='divAddGroup' class='col-sm-12'  style='display:none'><div class='addAgroup'><div class='row'><div class='col-sm-4'><div class='row'><div class='col-sm-2'> <i class='fa fa-bars bar-icon-styling'></i></div>
<div class='col-sm-10'> <input type='text' id='labelNewGroup' placeholder='".__("insert new header for group","migla-donation")."' class='miglaNQ' /> </div></div>
</div><div class='col-sm-4'><div class='col-sm-5'><input type='checkbox' id='t' checked='false' class='toggle' id='toggleNewGroup' /><label>".__("Toggle","migla-donation")."</label></div></div>
<div class='col-sm-4 addfield-button-control alignright'><button type='button' class='btn btn-default mbutton' id='cancelAddGroup'>".__("Cancel","migla-donation")."</button> <button type='button' class='btn btn-info inputFieldbtn pbutton' id='saveAddGroup'><i class='fa fa-fw fa-save'></i>".__(" Save Group","migla-donation")."</button></div>
</div></div>
</div>";

//////////END OF HERE

echo "";


echo "<div class='col-sm-12'>";

$d = (get_option( 'migla_form_fields' ));
$custAmountText = get_option('migla_custamounttext');
if( $custAmountText == false ){ add_option('migla_custamounttext', 'Custom Amount'); }
if( $custAmountText == '' ){ update_option('migla_custamounttext', 'Custom Amount'); }

$selectedCampaign = get_option('migla_selectedCampaign');
if( $selectedCampaign == false ){ add_option('migla_selectedCampaign', $label ); }
if( $selectedCampaign == '' ){ update_option('migla_selectedCampaign', $label ); }

echo "<ul class='containers'>";
$id = 0; $i = 0;

if( !empty( $d ) && $d[0] !=''){
foreach ( (array) $d as $f ){

 echo "<li class='title formheader'><div class='row'><div class='col-sm-4'><div class='row'><div class='col-sm-2'>";
echo "<i class='fa fa-bars bar-icon-styling'></i></div><div class='col-sm-10'> "; 
echo "<input type='text' class='titleChange'  placeholder='" . $f['title']."' name='grouptitle' value='" . $f['title']."'>  </div> ";
echo "</div></div>
      <div class='col-sm-4'>"; 

if(  strcmp( $f['toggle'], '-1') == 0){
 echo "<div class='col-sm-5'><input type='checkbox' id='t".$id."'  class='toggle' disabled><label>".__("Toggle","migla-donation")."</label></div>";
}else if(strcmp( $f['toggle'], '1') == 0){
 echo "<div class='col-sm-5'><input type='checkbox' id='t".$id."' checked='checked' class='toggle' /><label>".__("Toggle","migla-donation")."</label></div>";
}else{
 echo "<div class='col-sm-5'><input type='checkbox' id='t".$id."'  class='toggle' disabled><label>".__("Toggle","migla-donation")."</label></div>";
}
$id++;

echo "<button value='add' class='btn btn-info obutton mAddField addfield-button-control' ><i class='fa fa-fw fa-plus-square-o'></i>".__("Add Field","migla-donation")."</button> </div>";



if(  count((array) $f['child']) > 0 )
{
 echo "<div class='col-sm-4 text-right-sm text-right-xs divDelGroup ' >  <button class='rbutton btn btn-danger mDeleteGroup pull-right disabled' ><i class='fa fa-fw fa-trash'></i>
".__("Delete Group","migla-donation")."</button>  
  </div>";
}else{
 echo "<div class='col-sm-4 text-right-sm text-right-xs divDelGroup' >  <button class='rbutton btn btn-danger mDeleteGroup pull-right' ><i class='fa fa-fw fa-trash'></i>".__("Delete Group","migla-donation")."</button>  
  </div>";
}


 echo "</div>";

 echo "<input class='mHiddenTitle' type='hidden' name='title' value='".$f['title']."' />";

 $ulId = str_replace(" ","", $f['title']);

 echo "<ul class='rows' id='".$ulId."' >";

 if ( count((array) $f['child']) > 0 ){
//print_r($f['child']);
$j = -1;
   foreach ( (array)$f['child'] as $c ){
$j++;
$arrShow = array();
$arrShow[0] = "";$arrShow[1] = "";$arrShow[2] = "";$arrShow[3] = "";$arrShow[4] = "";

     echo "<li class='ui-state-default formfield clearfix'>";
     echo "<input class='mHiddenLabel' type='hidden' name='label' value='".$f['child'][$j]['label']."' />";
     echo "<input type='hidden' name='type' value='".$f['child'][$j]['type']."' />";
     echo "<input type='hidden' name='id' value='".$f['child'][$j]['id']."' />";
     echo "<input type='hidden' name='code' value='".$f['child'][$j]['code']."' />";
     echo "<input type='hidden' name='status' value='".$f['child'][$j]['status']."' />";

     echo "<div class='clabel col-sm-1 hidden-xs'><label class='control-label'>".__("Label:","migla-donation")."</label></div>";
     echo "<div class='col-sm-3 col-xs-12'><input type='text' name='labelChange' class='labelChange'  value='".$f['child'][$j]['label']."' /></div>";
     echo "<div class='ctype col-sm-2 col-xs-12'>";
     
     if( strcmp( $f['child'][$j]['code'],"miglad_" ) == 0 ){ 
       $disabled="disabled";$op="disabled"; 
     }else{ 
       $disabled="";$op="";
     }

     echo "<select name='typeChange' class='typeChange' id='s".$f['child'][$j]['id']."' ".$disabled." >";
     if( (string)$f['child'][$j]['type'] == "text" ){
       $arrShow[0] = "selected=selected";
     }
     if( (string)$f['child'][$j]['type'] == "checkbox" ){
       $arrShow[1] = "selected=selected";
     }
     if( (string)$f['child'][$j]['type'] == "textarea" ){
       $arrShow[2] = "selected=selected";
     }
     if( (string)$f['child'][$j]['type'] == "select" ){
       $arrShow[3] = "selected=selected";
     }
     if( (string)$f['child'][$j]['type'] == "radio" ){
       $arrShow[4] = "selected=selected";
     }

      if( (string)$f['child'][$j]['code'] == "miglad_" ){
      echo "<option value='text' ".$arrShow[0].">".__("text","migla-donation")."</option>";
      echo  "<option value='checkbox' ".$arrShow[1].">".__("checkbox","migla-donation")."</option>";
     
      echo "<option value='textarea' ".$arrShow[2].">".__("textarea","migla-donation")."</option>";
      echo "<option value='radio' ".$arrShow[4].">".__("radio","migla-donation")."</option>";
      echo "<option value='select' ".$arrShow[3].">".__("select","migla-donation")."</option>";

     }else{
      echo "<option value='text' ".$arrShow[0].">".__("text","migla-donation")."</option>";
      echo  "<option value='checkbox' ".$arrShow[1].">".__("checkbox","migla-donation")."</option>";
      echo "<option value='textarea' ".$arrShow[2].">".__("textarea","migla-donation")."</option>";     
    }

     echo "</select>";
     echo "</div>";

     if( $f['child'][$j]['id'] == 'amount' ){
       echo "<div class='col-sm-2 col-xs-12'><input type='text' id='migla_custAmountTxt' value='".$custAmountText."'></div>";
     }

     if( $f['child'][$j]['id'] == 'campaign' ){
       echo "<div class='col-sm-2 col-xs-12'>"; 
       echo $this->migla_campaign_section( $selectedCampaign ,"");
       echo "</div>";
     }

    // echo "<div class='cid col-sm-2 hidden-xs'><label>ID : ".$f['child'][$j]['id']."</label></div>";
     echo "<div class='ccode' style='display:none'>".$f['child'][$j]['code']."</div>";

     if( $f['child'][$j]['id'] == 'amount' ){
       echo "<div class='control-radio-sortable col-sm-4 col-xs-12'>";
     }else{
       echo "<div class='control-radio-sortable col-sm-4 col-xs-12'>";
     }

     $iid = $f['child'][$j]['id'];


  $cekid = $f['child'][$j]['id'];
   if( $cekid == 'amount' || $cekid == 'firstname' || $cekid == 'lastname' || $cekid == 'email' )
   { 

      echo "<span><label class='".$disabled."'><input type='radio' name='".$iid."st'  value='1' class='".$disabled."' />".__(" Show","migla-donation")."</label></span>";
      echo "<span><label class='".$disabled."'><input type='radio' name='".$iid."st'  value='0' class='".$disabled."' />".__(" Hide","migla-donation")."</label></span>";
      echo "<span><label class='".$disabled."'><input type='radio' name='".$iid."st'  value='2' checked='checked' class='".$disabled."' />".__(" Mandatory","migla-donation")."</label></span>";

   }else{
     if( strcmp( $c['status'],"0") == 0 ){

      echo "<span><label><input type='radio' name='".$iid."st' value='1' />".__(" Show","migla-donation")."</label></span>";
      echo "<span><label><input type='radio' name='".$iid."st'  value='0' checked='checked' />".__(" Hide","migla-donation")."</label></span>";
      echo "<span><label><input type='radio' name='".$iid."st'  value='2' />".__(" Mandatory","migla-donation")."</label></span>";

     }else if( strcmp( $c['status'],"1") == 0){

      echo "<span><label><input type='radio' name='".$iid."st'  value='1' checked='checked' />".__(" Show","migla-donation")."</label></span>";
      echo "<span><label><input type='radio' name='".$iid."st'  value='0' />".__(" Hide","migla-donation")."</label></span>";
      echo "<span><label><input type='radio' name='".$iid."st'  value='2' />".__(" Mandatory","migla-donation")."</label></span>";

     }else if( strcmp( $c['status'],"2") == 0 || strcmp( $c['status'],"3") == 0 ){

      echo "<span><label><input type='radio' name='".$iid."st'  value='1' />".__(" Show","migla-donation")."</label></span>";
      echo "<span><label><input type='radio' name='".$iid."st'  value='0' />".__(" Hide","migla-donation")."</label></span>";
      echo "<span><label><input type='radio' name='".$iid."st'  value='2' checked='checked' />".__(" Mandatory","migla-donation")."</label></span>";

     }
/*
else if( strcmp( $c['status'],"3") == 0){
      echo "<span><label class='".$disabled."'><input type='radio' name='".$iid."st'  value='1' class='".$disabled."' />".__(" Show","migla-donation")."</label></span>";
      echo "<span><label class='".$disabled."'><input type='radio' name='".$iid."st'  value='0' class='".$disabled."' />".__(" Hide","migla-donation")."</label></span>";
      echo "<span><label class='".$disabled."'><input type='radio' name='".$iid."st'  value='2' checked='checked' class='".$disabled."' />".__(" Mandatory","migla-donation")."</label></span>";

     }
*/
      else{

      echo "<span><label><input type='radio' name='".$iid."st'  value='1' checked='checked' />".__(" Show","migla-donation")."</label></span>";
      echo "<span><label><input type='radio' name='".$iid."st'  value='0' />".__(" Hide","migla-donation")."</label></span>";
      echo "<span><label><input type='radio' name='".$iid."st'  value='2' />".__(" Mandatory","migla-donation")."</label></span>";
     }
   }


     echo "<span><button class='removeField ".$op."' ".$disabled."><i class='fa fa-fw fa-trash'></i></button></span></div>";
     echo "</li>";

$i++;

   }//foreach
 }//if

 echo "</ul>";
 echo "</li>";

}
echo "</ul>";

echo "<div class='row'><div class='col-sm-6'><button value='save' class='btn btn-info pbutton miglaSaveForm' id='miglaSaveFormBottom'><i class='fa fa-fw fa-save'></i>".__("  save form","migla-donation")."</button></div> <div class='col-sm-6'> <button id='miglaResetForm' class='btn btn-info rbutton pull-right' value='reset' data-toggle='modal' data-target='#confirm-reset'><i class='fa fa-fw fa-refresh'></i>".__("  Restore to Default","migla-donation")."</button></div></div>";
	
echo "</div></div></div></section></div>";
		

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




echo "</div></div>\n";

 echo " <div class='modal fade' id='confirm-reset' tabindex='-1' role='dialog' aria-labelledby='miglaWarning' aria-hidden='true' data-backdrop='true'>
        <div class='modal-dialog'>
            <div class='modal-content'>
            
                <div class='modal-header'>


                    <button type='button' class='close' data-dismiss='modal' aria-hidden='true' data-target='#confirm-reset'><i class='fa fa-times'></i></button>
                    <h4 class='modal-title' id='miglaConfirm'>".__(" Confirm Restore","migla-donation")."</h4>
                </div>
            
<div class='modal-wrap clearfix'>

           <div class='modal-alert'>
														<i class='fa fa-times-circle'></i>
													</div>  

   <div class='modal-body'>
 <p>".__("Are you sure you want to restore to default fields? This cannot be undone","migla-donation")."</p>
                </div>

</div> 
                
                <div class='modal-footer'>
                    <button type='button' class='btn btn-default mbutton' data-dismiss='modal'>".__("Cancel","migla-donation")."</button>
                    <button type='button' class='btn btn-danger danger rbutton' id='miglaRestore'><i class='fa fa-fw fa-refresh'></i>".__("Restore to default","migla-donation")."</button>
                   
                </div>
            </div>
        </div>
    </div>"; 

}

}
}

?>