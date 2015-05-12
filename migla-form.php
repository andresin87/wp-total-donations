<?php

/***************************************************************************************/
 /*************FUNCTIONS FOR DONATION FORM******************************************************************/
/***************************************************************************************/

//decode the text
function mg_makeInputTextTag( $id, $label, $type, $code, $filter, $req, $col, $uid )
{
   $out = ""; $info = "";
   if( strcmp($req, 'required')==0 ){ 
         $info = "<span class='input-group-addon'><span class='icon'>*</span></span>"; 
  }

   $lbl = str_replace("[q]","'",$label);

   $out .= "<div class='form-group'><div class='col-sm-3'>";
   $out .= "<div class='input-group input-group-icon'>";
   $out .= "<label class='mg_control-label  '>".$lbl;
   $out .= "</label>"; 
   $out .= $info;
   $out .= "</div></div>";

   $out .= "<div class='col-sm-6 col-xs-12'>";
   $out .= "<label style='display:none' class='idfield' id='".$code.$id."'></label>";
   $out .= "<input type='".$type."' id='".$uid."' placeholder='".$label."' class='mg_form-control miglaNumAZ ".$code." ".$req."' />";
   $out .= "</div>";
   $out .= "<div class='col-sm-3 hidden-xs'></div></div>";

   return $out;
}

function mg_makeInputTextareaTag( $id, $label, $type, $code, $filter, $req, $uid )
{
   $out = ""; $info = "";
   if( strcmp($req, 'required')==0 ){ 
         $info = "<span class='input-group-addon'><span class='icon'>*</span></span>"; 
  }

   $lbl = str_replace("[q]","'",$label);


   $out .= "<div class='form-group'><div class='col-sm-3'>";
   $out .= "<div class='input-group input-group-icon'>";
   $out .= "<label class='mg_control-label  '>".$lbl;
   $out .= "</label>"; 
   $out .= $info;
   $out .= "</div></div>";

   $out .= "<div class='col-sm-6 col-xs-12'>";
   $out .= "<label style='display:none' class='idfield' id='".$code.$id."'></label>";
  
   $out .= "<textarea type='".$type."' id='".$uid."' class='mg_form-control ".$code." ".$req."  miglaNumAZ'></textarea>";

   $out .= "</div>";
   $out .= "<div class='col-sm-3 hidden-xs'></div></div>";

   return $out;
}


function mg_makeInputCheckTag( $id, $label, $type, $code, $filter, $req , $uid)
{
   $out = ""; $info = "";
   //if( strcmp($req, 'required')==0 ){ 
   //      $info = "<span class='input-group-addon'><span class='icon'>*</span></span>"; 
   //}

   $lbl = str_replace("[q]","'",$label);

   $out .= "<div class='form-group'><div class='col-sm-3'>";
   $out .= "<div class='input-group input-group-icon'>";
   $out .= "<label class='mg_control-label'>".$lbl;
   $out .= "</label>"; 
   $out .= $info;
   $out .= "</div></div>";
   $out .= "<div class='col-sm-6 col-xs-12'>";
   $out .= "<label style='display:none' class='idfield' id='".$code.$id."'></label>";
   $out .= "<div class='checkbox'><label for='".$uid."'><input type='checkbox' id='".$id."' class='check-control ".$code." ".$req."' value='".
           __("Yes", "migla-donation")."'/></label></div>";
   $out .= "</div>";
   $out .= "<div class='col-sm-3 hidden-xs'></div></div>";

   return $out;
}

function mg_makeInputDropDownTag( $id, $label, $type, $code, $filter, $req, $post_id, $meta_key )
{
   $out = ""; $info = "";
   if( strcmp($req, 'required')==0 ){ 
         $info = "<span class='input-group-addon'><span class='icon'>*</span></span>"; 
   }

   $lbl = str_replace("[q]","'",$label);

   $out .= "<div class='form-group'><div class='col-sm-3'>";
   $out .= "<div class='input-group input-group-icon'>";
   $out .= "<label class='mg_control-label  '>".$lbl;
   $out .= "</label>"; 
   $out .= $info;
   $out .= "</div></div>";

   $out .= "<div class='col-sm-6 col-xs-12'>";
   $out .= "<label style='display:none' class='idfield' id='".$code.$id."'></label>";

   //$out .= "<input type='".$type."' id='".$id."' placeholder='".$label."' class='mg_form-control miglaNumAZ ".$code." ".$req."' />";
              
              $data = get_post_meta( $post_id, 'mgval_'.$meta_key );
              //$out .= $data;
              if( !empty($data) ){
                $options = explode( ";" , (string)$data[0]  );
              }
              //print_r($options);

              $out .= "<select class='mg_form-control' id='".$meta_key."'  name='".$code.$id."'>"; 
               $out .= "<option value=''>".__("Please choose one", "migla-donation")."</option>"; 
	      foreach ( (array)$options as $op ) 
	      { 
                 $op_value = explode(  "::" , $op );
                 if( $op_value[1] != '' ){
	           $out .= "<option value='".$op_value[0]."'>".$op_value[1]."</option>"; 
                 }
	      } 	   
              $out .= "</select>";  

   $out .= "</div>";
   $out .= "<div class='col-sm-3 hidden-xs'></div></div>";

   return $out;
}

function mg_makeRepeatingTag( $id, $label, $type, $code, $filter, $req)
{
  $out = ""; $info = "";
  //if( strcmp($req, 'required')==0 ){ 
  //       $info = "<span class='input-group-addon'><span class='icon'>*</span></span>"; 
  //}

   $count = 0;

       $data = get_option( 'migla_recurring_plans' ) ; $i = 0;
       if( $data != false ){
         foreach( (array)$data as $d ){   
            if( ( strpos( (string)$d['payment_method'] , $filter ) !== false ) && ($d['status']=='1') ){
                 $i++; 
            } 
         }
       }

  if( $data == false || $i < 1){

  }else{

    $lbl = str_replace("[q]","'",$label);

    $out .= "<div class='form-group'>";
    $out .= "<div class='col-sm-3'>";
    $out .= "<label class='mg_control-label  '>".$lbl;
    $out .= "</label>"; 
    $out .= $info;
    $out .= "</div>";

    $out .= "<label style='display:none' class='idfield' id='".$code.$id."'></label>";

    $bglevelcolor = get_option('migla_bglevelcolor');
    $borderlevelcolor = get_option('migla_borderlevelcolor');
    $borderlevel = get_option('migla_borderlevel');
    $borderCSS = "border: ".$borderlevel."px solid ".$borderlevelcolor.";"; 

       if ( $i == 1 ){

          $plan_name = $data[0]['name'];
          $plan_name = str_replace( "'", "", $plan_name );
               $out .= "<div class='col-sm-6 col-xs-12'>";
               $out .= "<div class='checkbox'><label for='mg_repeating_check'>";
               $out .= "<input type='checkbox' id='mg_repeating_check' class='check-control ".$code." ".$req." ".$code.$id."' name='".$code.$id."' />";
               $out .= "<input type='hidden' value='".$data[0]['id'].";".$data[0]['interval_count'].";".$data[0]['interval'].";".$plan_name."' id='info".$code.$id."'>";
               $out .= "".$data[0]['name']."</label></div>";
               $out .= "</div>";

       }else{

               $out .= "<div class='col-sm-6 col-xs-12'>";
               $out .= "<div class='radio' id='".$meta_key.$count."' >"; 
               $out .= "<label for='radios-repeating".$count."'><input checked type='radio' value='no' id='radios-repeating".$count."' name='".$code.$id."' >";
               $out .= __( get_option('migla_none_rec_radiobtn_text') , "migla-donation");
               $out .= "<input type='hidden' value='No;0;0' id='inforadios-repeating".$count."'></label></div>"; 

	        foreach( (array)$data as $d )
	        { 
                   if( ( strpos( (string)$d['payment_method'] , $filter ) !== false ) && ($d['status']=='1') )
                   {
                       $count++;
                       $plan_name = $d['name'];
                       $plan_name = str_replace( "'", "", $plan_name );

                       $out .= "<div class='radio' id='".$meta_key.$count."'>"; 
                       $out .= "<label for='radios-repeating".$count."'><input type='radio' value='".$d['name']."' id='radios-repeating".$count."' name='".$code.$id."'>";
                       $out .= $d['name']; 
                       $out .= "<input type='hidden' value='".$d['id'].";".$d['interval_count'].";".$d['interval'].";".$plan_name."' id='inforadios-repeating".$count."' ></label>";  
                       $out .= "</div>";
                   } 
	        }

               $out .= "<!--<div class='col-sm-3 hidden-xs'></div>--></div>";

       }

    $out .= "</div>";
  }

   return $out;
}

function mg_makeInputRadioTag( $id, $label, $type, $code, $filter, $req, $post_id , $meta_key )
{
   $out = ""; $info = "";
   //if( strcmp($req, 'required')==0 ){ 
   //      $info = "<span class='input-group-addon'><span class='icon'>*</span></span>"; 
   //}

   $lbl = str_replace("[q]","'",$label);

   $out .= "<div class='form-group'><div class='col-sm-3'>";
   $out .= "<div class='input-group input-group-icon'>";
   $out .= "<label class='mg_control-label  '>".$lbl;
   $out .= "</label>"; 
   $out .= $info;
   $out .= "</div></div>";

   $out .= "<div class='col-sm-6 col-xs-12'>";
   $out .= "<label style='display:none' class='idfield' id='".$code.$id."'></label>";

   $bglevelcolor = get_option('migla_bglevelcolor');
   $borderlevelcolor = get_option('migla_borderlevelcolor');
   $borderlevel = get_option('migla_borderlevel');
   $borderCSS = "border: ".$borderlevel."px solid ".$borderlevelcolor.";"; 
    
              $data = get_post_meta( $post_id, 'mgval_'.$meta_key );

              if( !empty($data) ){
                $options = explode( ";" , (string)$data[0]  );
              }

     $name_radio = $code.$id;
     $name_radio = str_replace(" ","", $name_radio);
     $name_radio = str_replace("?","", $name_radio);

	      foreach ( (array)$options as $op ) 
	      { 
                 $op_value = explode(  "::" , $op );
                 if( $op_value[1] != '' ){
                   $count++;
                   $out .= "<div class='radio' id='".$meta_key.$count."'  >"; 
                   $out .= "<label for='radios-".$meta_key.$count."'><input type='radio'  value='".$op_value[0]."' id='radios-".$meta_key.$count."' name='".$name_radio."'>".$op_value[1]."</label></div>";
                   
                 }
	      } 	    

   $out .= "</div>";
   $out .= "<div class='col-sm-3 hidden-xs'></div></div>";

   return $out;
}

function mg_makeInputMultiCheckboxTag( $id, $label, $type, $code, $filter, $req, $post_id , $meta_key )
{
   $out = ""; $info = "";
   //if( strcmp($req, 'required')==0 ){ 
   //      $info = "<span class='input-group-addon'><span class='icon'>*</span></span>"; 
   //}

   $lbl = str_replace("[q]","'",$label);

   $out .= "<div class='form-group'><div class='col-sm-3'>";
   $out .= "<div class='input-group input-group-icon'>";
   $out .= "<label class='mg_control-label  '>".$lbl;
   $out .= "</label>"; 
   $out .= $info;
   $out .= "</div></div>";

   $out .= "<div class='col-sm-6 col-xs-12'>";
   $out .= "<label style='display:none' class='idfield' id='".$code.$id."'></label>";

   $bglevelcolor = get_option('migla_bglevelcolor');
   $borderlevelcolor = get_option('migla_borderlevelcolor');
   $borderlevel = get_option('migla_borderlevel');
   $borderCSS = "border: ".$borderlevel."px solid ".$borderlevelcolor.";"; 
    
              $data = get_post_meta( $post_id, 'mgval_'.$meta_key );

              if( !empty($data) ){
                $options = explode( ";" , (string)$data[0]  );
              }

     $name_radio = $code.$id;
     $name_radio = str_replace(" ","", $name_radio);
     $name_radio = str_replace("?","", $name_radio);

	      foreach ( (array)$options as $op ) 
	      { 
                 $op_value = explode(  "::" , $op );
                 if( $op_value[1] != '' ){
                   $count++;
                   $value_to_save = str_replace("'","", $op_value[0] );

                   $out .= "<div class='checkbox' id='".$meta_key.$count."' >"; 
                   $out .= "<label for='checkbox-".$meta_key.$count."'>";
                   $out .= "<input type='checkbox' id='checkbox-".$meta_key.$count."' name='".$name_radio."' value='".$value_to_save."'>".$op_value[1]."</label></div>";
                   
                 }
	      } 	    

   $out .= "</div>";
   $out .= "<div class='col-sm-3 hidden-xs'></div></div>";

   return $out;
}

function mg_makeInputCountry( $id, $label, $type, $code,  $filter, $req )
{
   $out = ""; $info = "";
   if( strcmp($req, 'required')==0 ){ 
         $info = "<span class='input-group-addon'><span class='icon'>*</span></span>"; 
   }

   $out = "";
   $lbl = str_replace("[q]","'",$label);
   $out .= "<div class='form-group'><div class='col-sm-3'><label class='mg_control-label'>".$lbl;
   $out .= "</label></div>";
   $out .= "<div class='col-sm-6 col-xs-12'>";
   $out .= "<div class=''>";

   $out .= "<label style='display:none' class='idfield' id='".$code.$id."'></label>";
   $countries = get_option( 'migla_world_countries' );
   $out .= "<select class='mg_form-control migla_country' id=".$id." name='".$code.$id."'>"; 
   $out .= "<option value=''>Please choose one</option>"; 
   foreach ( $countries as $key => $value ) 
   { 
	      if ( strcmp ( $value , (string)get_option( 'migla_default_country' ) ) == 0 )
		  { 
		    $out .= "<option value='".$value."' selected='selected' >".$value."</option>"; 
		  }else{  
		    $out .= "<option value='".$value."'>".$value."</option>"; 
		  }
   }	   
   $out .= "</select>"; 		
 
   $out .= $info;
   $out .= "</div></div>";
   $out .= "<div class='col-sm-3 hidden-xs'></div></div>";

   if( $id == 'country'){
      $out .= mg_makeInputState ( 'state',   'State',   'select',  'miglad_', '1', '' );
      $out .= mg_makeInputProvince( 'province', 'Province', 'select', 'miglad_', '1', '' );
   }else if( $id == 'honoreecountry'){
      $out .= mg_makeInputState ( 'honoreestate',   'State',   'select',  'miglad_', '1', '' );
      $out .= mg_makeInputProvince( 'honoreeprovince', 'Province', 'select', 'miglad_', '1', '' );   
   }
   
   return $out;
}

function mg_makeInputState( $id, $label, $type, $code,  $filter, $req )
{
   $out = "";
   $lbl = str_replace("[q]","'",$label);
   $out .= "<div class='form-group migla_state' id='".$id."'  style='display:none'><div class='col-sm-3'>";
   $out .= "<label class='mg_control-label '>".$lbl;
   $out .= "</label></div><div class='col-sm-6 col-xs-12'>";

   $out .= "<label style='display:none'  class='idfield' id='".$code.$id."'></label>";

	   $states = get_option( 'migla_US_states' );
	   $out .= "<select class='mg_form-control migla_state' name='".$code.$id."'>"; 
           $out .= "<option value=''>Please choose one</option>"; 
	   foreach ( $states as $key => $value ) 
	   { 
	       $out .= "<option value='".$value."'>".$value."</option>"; 
	   }	   
	   $out .= "</select>";    

   $out .= "</div><div class='col-sm-3 hidden-xs'></div></div>";

   return $out;
}

function mg_makeInputProvince( $id, $label, $type, $code,  $filter, $req )
{
   $out = "";
   $lbl = str_replace("[q]","'",$label);
   $out .= "<div class='form-group migla_province' id='".$id."' style='display:none'><div class='col-sm-3'>";
   $out .= "<label class='mg_control-label '>".$lbl;
   $out .= "</label></div><div class='col-sm-6 col-xs-12'>";

   $out .= "<label style='display:none' class='idfield' id='".$code.$id."'></label>";
	   $states = get_option( 'migla_Canada_provinces' );
	   $out .= "<select class='mg_form-control migla_province' name='".$code.$id."'>"; 
           $out .= "<option value=''>Please choose one</option>"; 
	   foreach ( $states as $key => $value ) 
	   { 
	      $out .= "<option value='".$value."'>".$value."</option>"; 
	   }	   
	   $out .= "</select>"; 
		
   $out .= "</div><div class='col-sm-3 hidden-xs'></div></div>";

   return $out;
}

function getCurrencySymbol()
{
   $code = (string)get_option(  'migla_default_currency'  );
   $arr = get_option( 'migla_currencies' ); 
   $icon ='';
   foreach ( $arr as $key => $value ) {
     if(  strcmp( $code, $arr[$key]['code'] ) == 0  ){
       $icon = $arr[$key]['symbol']; 
       break;
     }
   }
   return $icon;
}


function migla_drawForm( $campaign_attr ){
   $out = migla_form( $campaign_attr );

  /*** Get the payment options  ****/
  $show_stripe = get_option('migla_show_stripe');
  $show_paypal = get_option('migla_show_paypal');

  if(  $show_stripe == 'yes' && $show_paypal == 'yes' ){
        $out .= "". migla_tabs();
  }else{
        if(  $show_paypal == 'yes' )
        {
             $out .= "". migla_paypal( 'mg_row' );
        }else if( $show_stripe == 'yes' ){
             $bgclor2nd = explode("," , get_option('migla_2ndbgcolor') );
             $border = explode(",", get_option('migla_2ndbgcolorb') );     
             $borderCSS = "border: ".$border[2]."px solid ".$border[0].";";
             $bglevelcolor     = get_option('migla_bglevelcolor');

             $out .= "<div class='form-horizontal migla-payment-options' >
                     <div class='mg_tab-content' >
                     <div id='sectionStripe' class='' style='background-color:".$bgclor2nd[0].";".$borderCSS."'>
                       ".migla_stripe()."
                     </div>
                     </div>
                     </div>";
        }
  }

   return $out;
}

function migla_form( $campaign_attr )
{

  $out = "";
  $out .= "<input type='hidden' id='miglaShowDecimal' value='".get_option('migla_showDecimalSep')."'>";
  $out .= "<input type='hidden' id='miglaDecimalSep' value='".get_option('migla_decimalSep')."'>";
  $out .= "<input type='hidden' id='miglaThousandSep' value='".get_option('migla_thousandSep')."'>";

  $post_id_custom_list = migla_get_select_values_postid();

  $bgclor2nd = explode("," , get_option('migla_2ndbgcolor') );
  $bgclor2ndb = explode("," , get_option('migla_2ndbgcolorb') );
  $border = explode(",", get_option('migla_2ndbgcolorb') );     
  $borderCSS = "border: ".$border[2]."px solid ".$border[0].";"; 

  $dataField = (array)get_option('migla_form_fields');
  foreach ( (array) $dataField as $f )
  {
       $has = false;
       $index = 0;
       foreach ( (array) $f['child'] as $c ){
           if( strcmp( $c['status'], '0' ) == 0 ){ }else{ $has=true;break;}
           $index++;
       }


     if( $has )
     { //if it has children

       $lbl = str_replace("[q]","'", $f['title'] );

       $classtitle = str_replace(" ","", $f['title'] );
       $classtitle = str_replace("?","", $classtitle );
       $classtitle = "mg_".$classtitle ;

       if( strcmp( $f['toggle'], '1') == 0 ) //Check the toggle
       {     
          //section
          $out .= "<section class='migla-panel' style='background-color:".$bgclor2nd[0].";".$borderCSS."' >";
          $out .= "<header class='migla-panel-heading'>";

          $out .= "<h2 class='".$classtitle."'> ".$lbl ." <input type='checkbox' class='mtoggle'/></h2>"; 
          $out .= "</header>";
          $out .= "<div class='migla-panel-body form-horizontal' style='display:none'>";
       }else{
          //section
          $out .= "<section class='migla-panel' style='background-color:".$bgclor2nd[0].";".$borderCSS."' >";
          $out .= "<header class='migla-panel-heading'><h2 class='".$classtitle."'>".$lbl."</h2></header>";
          $out .= "<div class='migla-panel-body form-horizontal' >";
       }

     foreach ( (array) $f['child'] as $c ){

      if( strcmp( $c['status'], '0' ) == 0){ //as long as they are not shown

         if ( strcmp( $c['id'], 'campaign' ) == 0 ){ //setting with hide campaign dropdown
             $out .= migla_onecampaign_section( get_option('migla_selectedCampaign')  ,  $c['label'] , 0 );
         }	  

      }else{ 
         $req = '';
         if( strcmp( $c['status'], '2' ) == 0 || strcmp( $c['status'], '3' ) == 0) { $req = 'required'; } //is it mandatory ?

         if( strcmp( $c['id'], 'amount' ) == 0 )
         {
             $out .= migla_get_levels_section(  $c['label'] );

         }else if ( strcmp( $c['id'], 'campaign' ) == 0 ){

	     $postcampaign = "";
	     if( isset($_POST['campaign']) && $_POST['campaign'] != '' ){ 
		  $postcampaign = $_POST['campaign']; 
             }
             if( $campaign_attr == '' ){
                  $out .= migla_campaign_section( $postcampaign, $c['label'] );
             }else{
                  $out .= migla_onecampaign_section( $campaign_attr  ,  $c['label'] , 0 );
             }

         }else if( strcmp( $c['id'], 'country' ) == 0  ){    
   
             $out .= mg_makeInputCountry( $c['id'], $c['label'], $c['type'], $c['code'], "", "");

         }else if(  strcmp( $c['id'], 'honoreecountry' ) == 0 ){

             $out .= mg_makeInputCountry( $c['id'], $c['label'], $c['type'], $c['code'], "", "");

         }else if(  strcmp( $c['id'], 'repeating' ) == 0 ){

           $show_stripe = get_option('migla_show_stripe');
            $show_paypal = get_option('migla_show_paypal');

             if(  $show_stripe == 'yes' && $show_paypal == 'yes' ){
                 $out .= mg_makeRepeatingTag(  $c['id'], $c['label'], $c['type'], $c['code'], "paypal-stripe", $req );
             }else{
                if(  $show_paypal == 'yes' )
                {
                   $out .= mg_makeRepeatingTag(  $c['id'], $c['label'], $c['type'], $c['code'], "paypal", $req );
                }else if( $show_stripe == 'yes' )
                {
                   $out .= mg_makeRepeatingTag(  $c['id'], $c['label'], $c['type'], $c['code'], "stripe", $req );
                }
             }

         }else{ //not special field

            if( $c['type'] == 'text' ){

                $out .= mg_makeInputTextTag( $c['id'], $c['label'], $c['type'], $c['code'], "", $req, "" , $c['uid']);

            }else if ( $c['type'] == 'checkbox' ){

                $out .= mg_makeInputCheckTag( $c['id'], $c['label'], $c['type'], $c['code'], "", $req , $c['uid'] );  

            }else if ( $c['type'] == 'textarea' ){

                $out .= mg_makeInputTextareaTag( $c['id'], $c['label'], $c['type'], $c['code'], "", $req , $c['uid']);  

            }else if( $c['type'] == 'select' ){

                $out .=  mg_makeInputDropDownTag( $c['id'], $c['label'], $c['type'], $c['code'], "", $req, $post_id_custom_list, $c['uid'] );

            }else if( $c['type'] == 'radio' ){

                $out .=  mg_makeInputRadioTag( $c['id'], $c['label'], $c['type'], $c['code'], "", $req, $post_id_custom_list, $c['uid'] );

            }else if( $c['type'] == 'multiplecheckbox' ){

                $out .=  mg_makeInputMultiCheckboxTag( $c['id'], $c['label'], $c['type'], $c['code'], "", $req, $post_id_custom_list, $c['uid'] );
            }

        }//if check who is special

       }//as long as they are shown (Toggle)

      }//foreach child

      //end section
      $out .= "</div></section>";

    }//if it has child 

   }//foreach Section Field

  return $out;
}

/********************************* SPECIAL FIELDS ************************************************/
function migla_get_levels_section( $label )
{
   $symbol = getCurrencySymbol();

   $x = array();
   $x[0] = get_option('migla_thousandSep');
   $x[1] = get_option('migla_decimalSep');

   $showSep = get_option('migla_showDecimalSep');
   $decSep = 0;
   if( strcmp($showSep , "yes") == 0 ){ $decSep = 2; }else{ $x[1] = '';$decSep = 0; }
   
   $placement = get_option('migla_curplacement');
   if( strtolower( $placement ) == 'before' ){
      $before = $symbol; $after = "";  $toogle='icon-before';
   }else if( strtolower( $placement ) == 'after' ){
      $before = ""; $after = $symbol ; $toogle='icon-after';
   } 
   
   $out = "";
   $out .= "<div class='form-group mg_giving-levels'><div class='col-sm-12 col-xs-12'><label class='mg_control-label'>".$label;
   $out .= "</label></div><div class='col-sm-12 col-xs-12'>";

   $bglevelcolor     = get_option('migla_bglevelcolor');
   $borderlevelcolor = get_option('migla_borderlevelcolor');
   $borderlevel      = get_option('migla_borderlevel');
   $borderCSS        = "border: ".$borderlevel."px solid ".$borderlevelcolor.";"; 
    
    $out .= "<label style='display:none' class='idfield' id='miglad_amount'></label>";
    $amounts = get_option('migla_amounts');
    if( get_option('migla_sort_level')=='rsort' ){  rsort($amounts); }else{ sort($amounts); } 
    $idx = 0;

    foreach( $amounts as $key => $value )
    {
          $state ="";
          if( $idx == 0 ){ $state="checked='checked'"; }
	  $out .= "<div class='radio-inline'><label for='miglaAmount".$idx."' style='background-color:".$bglevelcolor.";". $borderCSS."' >";
          $out .= "<input type='radio' value='".$value."' id='miglaAmount".$idx."' name='miglaAmount' ".$state." class='migla_amount_choice' >";
          $out .= "<span class='currency-symbol'>".$before."</span>";
          $out .= number_format( $value, $decSep, $x[1], $x[0]);
          $out .= "<span class='currency-symbol'>".$after."</span>";
          $out .= "</label></div>";
          $idx = $idx + 1;
    }
	
   $out .= "</div><div class='col-sm-3 hidden-xs'></div></div>";	

   $out .= "<div class='form-group mg_giving-levels'>";
   $out .= "<div class='col-sm-5 col-xs-12'>";
   
   $out .= "<label style='display:none' class='idfield' id='miglad_camount'></label>";
 
	  $out .= "<div class='radio-inline miglaCustomAmount'><label for='miglaCustomAmount".$idx."' style='background-color:".$bglevelcolor.";". $borderCSS."'>";
          $out .= "<input type='radio' value='custom' id='miglaAmount".$idx."' name='miglaAmount' class='migla_amount_choice migla_custom_amount'><div>".get_option('migla_custamounttext')."</div>";

  if( strtolower( $placement ) == 'before' ){
          if( strlen($symbol) > 1 ){
              $before = ""; 
          }
          $out .= "<div class='input-group input-group-icon ".$toogle."'><span class='input-group-addon'><span class='icon'>".$before."</span></span><input type='text' value='0' id='miglaCustomAmount' class='miglaNAD2'></div></label></div>";
   }else{
          $out .= "<div class='input-group input-group-icon ".$toogle."'><input type='text' value='0' id='miglaCustomAmount' class='miglaNAD2'><span class='input-group-addon'><span class='icon'>".$after."</span></span></div></label></div>";
  }
	
   $out .= "</div><div class='col-sm-3 hidden-xs'></div></div>";
	
    return $out;
 }

function migla_campaign_section( $postCampaign , $label )
{
   $out = "";

    $campaign = (array)get_option( 'migla_campaign' );
    $undesign = get_option('migla_undesignLabel');
    $undesign = esc_attr($undesign);   
    $show_bar = get_option( 'migla_show_bar' );
   
   if( empty($campaign[0]) ){ 
   
   $out .= "<div class='form-group' style='display:none'><div class='col-sm-12 col-xs-12'><label class='mg_control-label  text-right-sm text-center-xs'>".$label;
   $out .= "</label></div><div class='col-sm-5 col-xs-12'>";
	
    $out .= "<label style='display:none' class='idfield' id='miglad_campaign'></label>";

    $out .= "<select name='campaign' class='mg_form-control' id='miglaform_campaign' style='display:none'>";
    $b = ""; $i = 0;   
    $out .= "<option value='". esc_html($undesign)."'>".esc_html($undesign)."</option>";
    $out .= "</select>"; 
	
    $out .= "</div>";
    //$out .= "<div class='col-sm-12 col-xs-12'><div id='migla_bar'></div></div>";
    $out .= "<div class='col-sm-3 hidden-xs'></div></div>";		

  }else{    
    $b = ""; $i = 0;  $out2 = ""; $campaignCount = 0;
   
    $out2 .= "<div class='form-group' ><div class='col-sm-12 col-xs-12'><label class='mg_control-label  text-right-sm text-center-xs'>".$label;
    $out2 .= "</label></div><div class='col-sm-5 col-xs-12'>";
	
    $out2 .= "<label style='display:none' class='idfield' id='miglad_campaign'></label>";
 	
    $out2 .= "<select name='campaign' class='mg_form-control' id='miglaform_campaign' >";

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
	
       $out2 .= "</div>";
       if( $show_bar == 'yes' ){  $out2 .= "<div class='col-sm-12 col-xs-12'><div id='migla_bar'></div></div>";  }
       $out2 .= "<div class='col-sm-3 hidden-xs'></div></div>";	

    if( $campaignCount > 0 ){
       $out .= $out2;	   
    }else{
       $out .= "<div class='form-group' style='display:none'><div class='col-sm-12 col-xs-12'><label class='mg_control-label  text-right-sm text-center-xs'>".$label;
       $out .= "</label></div><div class='col-sm-5 col-xs-12'>";
	
       $out .= "<label style='display:none' class='idfield' id='miglad_campaign'></label>";

       $out .= "<select name='campaign' class='mg_form-control' id='miglaform_campaign' style='display:none'>";
       $b = ""; $i = 0;   
       $out .= "<option value='". esc_html($undesign)."'>".esc_html($undesign)."</option>";
       $out .= "</select>"; 
	
       $out .= "</div>";
       if( $show_bar == 'yes' ){  $out .= "<div class='col-sm-12 col-xs-12'><div id='migla_bar'></div></div>";  }
       $out .= "<div class='col-sm-3 hidden-xs'></div></div>";
    }
  }	   
  
  return $out;

}


function migla_onecampaign_section( $postCampaign , $label , $show )
{
    $out = ""; $style = "";
    if( $show == 0 ){
       $style = 'display:none';
    }
    $out .= "<div class='form-group' style='".$style."'><div class='col-sm-12 col-xs-12'><label class='mg_control-label  '>".$label;
    $out .= "</label></div><div class='col-sm-5 col-xs-12'>";
	
    $out .= "<label style='display:none' class='idfield' id='miglad_campaign'></label>";

    $out .= "<select name='campaign' class='mg_form-control' id='miglaform_campaign' style='".$style."'>";
    $b = ""; $i = 0;   
    $out .= "<option value='". esc_html($postCampaign)."'>".esc_html($postCampaign)."</option>";
    $out .= "</select>"; 
	
    $out .= "</div>";
    $show_bar = get_option('migla_show_bar');
    if( $show_bar == 'yes' ){
       $out .= "<div class='col-sm-12 col-xs-12'><div id='migla_bar'></div></div>";
    }
    $out .= "<div class='col-sm-3 hidden-xs'></div></div>";

   return $out;
}

/********************************* END SPECIAL FIELDS ************************************************/

function migla_paypal( $add_class ) {

   $button_image_url = plugins_url( '/images/paypal_btn_donate_lg.gif', __FILE__  );
   $btnchoice = get_option('miglaPayPalButtonChoice');

   if ( $btnchoice == 'paypalButton' )
   {
        $btnlang = get_option('migla_paypalbutton');
        $btnlang = "/images/btn_donate_". $btnlang .".gif";
        $button_image_url = plugins_url( $btnlang , __FILE__ );
   }else if( $btnchoice == 'imageUpload' ){
        $btnurl = get_option('migla_paypalbuttonurl');
        $button_image_url = $btnurl;
   }else{ 
   }
   
   $out = "";
   $out .= "<div class='form-group'>";
 
  $out .= "<div class='col-sm-12 col-xs-12 ".$add_class."' >";
   if( $btnchoice == 'cssButton' || $btnchoice == '' || $btnchoice == false){
		$btnstyle = "";
		if( get_option('migla_paypalcssbtnstyle')=='Grey' ){ $btnstyle='mg-btn-grey'; }
		$out .= "<button id='miglapaypalcheckout' class=' miglacheckout ".$btnstyle." ". get_option('migla_paypalcssbtnclass') ."'>". get_option('migla_paypalcssbtntext') ."</button>";
   }else{
        //$out .= "<button id='miglapaypalcheckout' class='miglacheckout'><img src='" . esc_url( $button_image_url ) . "'></img></button>";
        //$out .= "<a href='#'><img class='mg_PayPalButton miglacheckout' id='miglapaypalcheckout'  src='" . esc_url( $button_image_url ) . "'></img></a>";    
	$out .= "<input class='mg_PayPalButton miglacheckout' id='miglapaypalcheckout' type='image' src='" . esc_url( $button_image_url ) . "' />"; 
   }
   $load = plugins_url( 'totaldonations/images/loading.gif', dirname(__FILE__) );
   
   $out .= "<div id='mg_wait_paypal' class='mg_wait' style='display:none'>".__("please wait, redirecting to paypal","migla-donation ")."&nbsp; <input  id='mg_loadingButton' type='image' src='" . esc_url( $load ) . "'></div>";

   $out .= "</div>";
   $out .= "</div>";

   return $out;
}



  function migla_hidden_form( $id ) {
	$paypalEmail = get_option( 'migla_paypal_emails' );
  
 	$payPalServer = get_option('migla_payment');
	 if ($payPalServer == "sandbox")
	 {
 		$formAction = "https://www.sandbox.paypal.com/cgi-bin/webscr";
	 }else{
		 $formAction = "https://www.paypal.com/cgi-bin/webscr";
	 }

	$notifyUrl = '';
        $successUrl = '';
        $notifyUrl = plugins_url('totaldonations/migla-donation-paypalstd-ipn.php', dirname(__FILE__) );
	$successUrl = migla_get_current_url();

	if (strpos($successUrl, "?") === false){
		$successUrl .= "?";
	}
	else{
		$successUrl .= "&";
	}

	$successUrl .= "thanks=thanks";
	$successUrl .= "&id=";
	$successUrl .= "$id";

	$currency_code = get_option( 'migla_default_currency' );
     
        $_item_ = get_option('migla_paypalitem');
        if(  $_item_ == '' || $_item_ == false ){
           $item_name = 'donation';   
        }else{
   	   $item_name = $_item_ ;               
        }
	
	$output = "";
	$output .= "<form id='migla-hidden-form' action='" . esc_attr( $formAction ) . "' method='post' >";

        $cmd_type = get_option('migla_paymentcmd');
        if(  $cmd_type == 'payment' ){
            $output .= "<input type='hidden' name='cmd' value='_xclick' >";
        }else{
   	    $output .= "<input type='hidden' name='cmd' value='_donations' >";            
        }

        $output .= "<input type='hidden' name='custom' value='".$id."' >";
	$output .= "<input type='hidden' name='business' value='" . esc_attr( $paypalEmail ) . "' >";

        if( get_option('migla_use_nonce') != 'yes' )
        {
	    $output .= "<input type='hidden' name='return' value='" . esc_attr( $successUrl ) ."' >";
	    $output .= "<input type='hidden' name='notify_url' value='" . esc_attr( $notifyUrl ) . "' >";
        }

	$output .= "<input type='hidden' name='email' value='' >";
	$output .= "<input type='hidden' name='first_name' value='' > ";
	$output .= "<input type='hidden' name='last_name' value='' >";
	$output .= "<input type='hidden' name='address1' value='' >";
        $output .= "<input type='hidden' name='address2' value=''>";
        $output .= "<input type='hidden' name='city' value=''>";
        $output .= "<input type='hidden' name='country' value=''>";
        $output .= "<input type='hidden' name='state' value=''>";
        $output .= "<input type='hidden' name='zip' value=''>";

	$output .= "<input type='hidden' name='on0' value='DisclosureName' >";
	$output .= "<input type='hidden' name='os0' value='' > ";
	$output .= "<input type='hidden' name='on1' value='DisclosureEmployerOccupation' >";
        $output .= "<input type='hidden' name='os1' value='' > ";
	$output .= "<input type='hidden' name='on2' value='Campaign' >";
        $output .= "<input type='hidden' name='os2' value='' > ";
	
	$output .= "<input type='hidden' name='item_name' value='" . esc_attr( $item_name ) . "' >";
	$output .= "<input type='hidden' name='amount' value='1.00' />";
	$output .= "<input type='hidden' name='quantity' value='1' />";
	$output .= "<input type='hidden' name='currency_code' value='".esc_attr( $currency_code )."' >";
	$output .= "<input type='hidden' name='no_note' value='1'>";

	$output .= "<input type='hidden' name='src' value='1'>"; 
	$output .= "<input type='hidden' name='p3' value='1'>";  
 	$output .= "<input type='hidden' name='t3' value='1'>";  
	$output .= "<input type='hidden' name='a3' value='1'>"; 

        $output .= "<input type='submit' id='miglaHiddenSubmit' style='display:none !important' />";

	$output .= "</form>";

	return $output;
	
  }

  function migla_modal_box(){
    $out = "";
    $out .= "<div style='display:none'><div id='mg_warning1'>". __("Please insert all the required fields","migla-donation") ."</div>";
    $out .= "<div id='mg_warning2'>". __("Please insert correct email","migla-donation") ."</div>";
    $out .= "<div id='mg_warning3'>". __("Please fill in a valid amount","migla-donation") ."</div>";
    $out .= "</div>";

    $out .= "";

    return $out;
  
  }  



/**************  STRIPE    *********************************************/
function migla_stripe(){
    $out = "";

   $out .= "<form id='mg-stripe-payment-form' ><div class='form-group'>
								<div class='col-sm-3 col-xs-12'><div class='input-group input-group-icon'><label class='mg_control-label  '>Your Name</label><span class='input-group-addon'><span class='icon'>*</span></span></div></div>



								<div class='col-sm-6 col-xs-12'>
									
										


<input type='text' aria-required='true' data-stripe='name' name='cardholder_name' class='mg_form-control touch-top' placeholder='As it appears on your card' value='' data-rule-required='true' id='mg_stripe_card_name'>
										
									 
								</div>
							</div>
							<div class='form-group'>
								<div class='col-sm-3 col-xs-12'><div class='input-group input-group-icon'><label class='mg_control-label  '>Card Number</label><span class='input-group-addon'><span class='icon'>*</span></span></div></div>
								<div class='col-sm-6 col-xs-12'>
									


							

										



<div class='input-group input-group-icon icon-after'><input type='text' id='mg_stripe_card_number' aria-required='true' data-stripe='number' class='mg_form-control touch-middle card-number' placeholder='No dashes' value='' data-rule-required='true' data-rule-creditcard='true'><span class='input-group-addon'><span class='mg_creditcardicons'></span></span></div>


 
										
																	
							
							
								</div>
							</div>";
										



      $out .= "							<div class='form-group'>
								<div class='col-sm-3 col-xs-12'><div class='input-group input-group-icon'><label class='mg_control-label  '>Expiration/CVC</label><span class='input-group-addon'><span class='icon'>*</span></span></div></div>
								

<div class='col-sm-6 col-xs-12'>


  <div class='input-group'>
    <span class='input-group-btn'>
      <select aria-required='true' id='mg_stripe_month' data-stripe='exp-month' class='mg_form-control touch-bottom card-expiry-month' data-rule-required='true'>";
       for( $m = 1; $m <=12 ; $m++ ){
               $out .= "<option value='".$m."'>".$m."</option>";
       }
                                                                                        
       $out .= " </select>";

       $out .= "</span><span class='input-group-btn'>";

       $start_year = date('Y');
		$out .= "	
					<select id='mg_stripe_year' aria-required='true' data-stripe='exp-year' class='mg_form-control touch-bottom card-expiry-year' data-rule-required='true'>";
          for( $y = $start_year; $y <= ($start_year + 15) ; $y++ ){
               $out .= "<option value='".$y."'>".$y."</option>";
          }
        $out .= "</select>";
        $out .= "</span>

<span class='input-group-btn'>


<div class='input-group input-group-icon icon-after'><input type='text' id='mg_stripe_cvc' data-rule-required='true' value='' placeholder='CVC' class='mg_form-control touch-bottom card-cvc' name='cvc' data-stripe='cvc' aria-required='true'><span class='input-group-addon'><span class='mg_creditcardicons mg_padlock'></span></span></div>


  </span>
  </div>
</div>
</div>

</form>";

   
   $btnchoice = get_option('miglaStripeButtonChoice');

   $out .= "<div class='form-group'>";
   $out .= "<div class='col-sm-12 col-xs-12'>";

   if( $btnchoice == 'cssButton' ){
         $btnstyle = "";
	 if( get_option('migla_stripecssbtnstyle')=='Grey' ){ $btnstyle='mg-btn-grey'; }
	    $out .= "<button id='miglastripecheckout' class=' miglacheckout ".$btnstyle." ". get_option('migla_stripecssbtnclass') ."'>". get_option('migla_stripecssbtntext') ."</button>";
   }else if( $btnchoice == 'imageUpload' ){
        $btnurl = get_option('migla_stripebuttonurl');
        $button_image_url = $btnurl;
        $out .= "<input class='mg_StripeButton miglacheckout' id='miglastripecheckout' type='image' src='" . esc_url( $button_image_url ) . "' />"; 
   }else{
        $btn_text = get_option("migla_stripebutton_text");
        if ( $btn_text == '' || $btn_text == false){
             $btn_text = "Pay with Card";
        }
	$out .= "<button id='miglastripecheckout' class='stripe-button-el mg_StripeButton miglacheckout'>
                <span style='display: block; min-height: 30px;'>".$btn_text."</span></button>"; 
   }
   $load = plugins_url( 'totaldonations/images/loading.gif', dirname(__FILE__) );
   
   $out .= "<div id='mg_wait_stripe' class='mg_wait' style='display:none'>".__("please wait, your donation is being processed","migla-donation ")."&nbsp; <input  id='mg_loadingButton' type='image' src='" . esc_url( $load ) . "'></div>";

   $out .= "</div>";
   $out .= "</div>";

    return $out;
  
  } 
/******** STRIPE ************************************************************************/


function migla_tabs(){

      $bgclor2nd = explode("," , get_option('migla_2ndbgcolor') );
      $border = explode(",", get_option('migla_2ndbgcolorb') );     
      $borderCSS = "border: ".$border[2]."px solid ".$border[0].";"; 
  $bglevelcolor     = get_option('migla_bglevelcolor');

  $out = ""; $add_class = "";
  $out .= "<div class='form-horizontal migla-payment-options' >
    <ul class='mg_nav mg_nav-tabs'>
        <li class='mg_active'  ><a id='_sectionPaypal' style='background-color:".$bgclor2nd[0].";".$borderCSS.";'>Paypal</a></li>
        <li ><a id='_sectionStripe' style='background-color:".$bglevelcolor.";".$borderCSS.";' >Stripe</a></li>
    </ul>
    <div class='mg_tab-content' style='".$borderCSS."' >
        <div id='sectionPaypal' class='mg_tab-pane mg_active' style='background-color:".$bgclor2nd[0]."' >
            ".migla_paypal( $add_class )."
        </div>
        <div id='sectionStripe' class='mg_tab-pane'>
            ".migla_stripe()."
        </div>
    </div>
</div>";

  return $out;
}


?>