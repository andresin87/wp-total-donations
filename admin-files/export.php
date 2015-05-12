<?php

include_once '../../../../wp-blog-header.php';

header( "Content-type: text/csv" );
header( "Content-Disposition: attachment; filename=online_donation.csv" );

/////////////////////////////write header//////////////////////////////
//GET THE REMOVED FIELDS
 global $wpdb;
 $data = array(); $output; $idx = 0;
 $data = $wpdb->get_results( "SELECT DISTINCT meta_key FROM {$wpdb->prefix}postmeta WHERE meta_key LIKE 'migla%' AND meta_value != ''" );
 foreach( $data as $id )
 {   $output[$idx] = $id->meta_key; $idx++; }

 $formfield = (array)get_option('migla_form_fields');
 foreach( (array)$formfield as $field )
 {
    if( count($field['child']) > 0  ){
    foreach ( (array) $field['child'] as $child )
    {  
       $c = str_replace("[q]","'",$child['id']);
	echo "\"".$c."\",";   

      //Get rid same column
       $codeid = $child['code'] .$child['id'];
       $key = array_search( $codeid , $output, true);  
       if( $key != false ){ unset( $output[$key] ); }; 

       if( "country" == $child['id'] ){
          echo "\"Province\",";
          echo "\"State\",";
       } 
  }
}
}

 $key = array_search( 'miglad_province' , $output, true);  unset( $output[$key] );
 $key = array_search( 'miglad_state' , $output, true);  unset( $output[$key] );

  foreach( (array)$output as $r){
    echo "\"".substr($r,7)."\","; 
  }

echo "\n";


if( empty($_POST['miglaFilters']) || ($_POST['miglaFilters']=='') )
{
  $arr =  migla_get_ids_all() ;
   foreach( $arr as $id )
   { 
    foreach( (array)$formfield as $field )
    {
      foreach ( (array) $field['child'] as $c )
      {  
        
         $column = $c['id'];
         $t = get_post_meta( intval( $id->ID ) ,($c['code'].$c['id']), true);
         $out = str_replace("[q]","'",$t);
         echo "\"".$out."\",";  

         if( $column == "country" ){   
             echo "\"".get_post_meta( intval( $id->ID ) , $c['code']."province", true)."\",";  
             echo "\"".get_post_meta( intval( $id->ID ) , $c['code']."state", true)."\",";  
         }

      }
    }

  foreach( (array)$output as $r){
    if( strcmp($r,'miglad_paymentdata')==0 ){
        $paymentdata = implode("+", (array)get_post_meta( intval( $id->ID ) , $r, true) ); 
        echo "\"".$paymentdata."\",";   
    }else{
      echo "\"".get_post_meta( intval( $id->ID ) , $r, true)."\",";  
    }
  }
        echo "\n";  
  }  
}else{
  $arr = explode( ',' ,$_POST['miglaFilters'] ) ;
  $idx = 0;
   foreach( $arr as $id )
   { 
    foreach( (array)$formfield as $field ){
      foreach ( (array) $field['child'] as $c )
      {  
          $column = $c['id'];
          $t = get_post_meta( $arr[$idx] ,($c['code'].$c['id']), true);
          $out = str_replace("[q]","'",$t);
          echo "\"".$out."\","; 

         if( $column == "country" ){          
             echo "\"".get_post_meta( $arr[$idx] , $c['code']."province", true)."\",";  
             echo "\"".get_post_meta( $arr[$idx] , $c['code']."state", true)."\",";  
         } 
      }

    }

  foreach( (array)$output as $r){
    if( strcmp($r,'miglad_paymentdata')==0 ){
        $paymentdata = implode("+", (array)get_post_meta(  $arr[$idx] , $r, true) ); 
        echo "\"".$paymentdata."\",";   
    }else{
      echo "\"".get_post_meta(  $arr[$idx] , $r, true)."\",";  
    }
  }

    echo "\n";    $idx++;
   }
}//IF



?>