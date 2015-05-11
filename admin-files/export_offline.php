<?php
include ('../../../../wp-config.php');
include ('../../../../wp-load.php');

header( "Content-type: text/csv" );
header( "Content-Disposition: attachment; filename=offline_donation.csv" );

/////////////////////////////write header//////////////////////////////
$formfield = get_option('migla_form_fields');
foreach( (array)$formfield as $field ){
if( count($field['child']) > 0  ){
  foreach ( (array) $field['child'] as $child )
  {  
    $c = str_replace("[q]","'",$child['label']);
	echo "\"".$c."\",";    

    if( "country" == $child['id'] ){
        echo "\"Province\",";
        echo "\"State\",";
    } 
 
  }
}
}
echo "\n";


if( empty($_POST['miglaFilters']) || ($_POST['miglaFilters']=='') )
{
  $arr = migla_get_oflineids_all();
   foreach( $arr as $id )
   { 
    foreach( (array)$formfield as $field )
    {
      foreach ( (array) $field['child'] as $c )
      {  
        
         $t = get_post_meta( intval( $id->ID ) ,($c['code'].$c['id']), true);
         $out = str_replace("[q]","'",$t);
         echo "\"".$out."\",";  

         $column = $c['id'];
         if( $column == "country" ){   
             echo "\"".get_post_meta( intval( $id->ID ) , $c['code']."province", true)."\",";  
             echo "\"".get_post_meta( intval( $id->ID ) , $c['code']."state", true)."\",";  
         }

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

          $t = get_post_meta( $arr[$idx] ,($c['code'].$c['id']), true);
          $out = str_replace("[q]","'",$t);
          echo "\"".$out."\",";
  
          $column = $c['id'];
         if( $column == "country" ){   
             echo "\"".get_post_meta( $arr[$idx] , $c['code']."province", true)."\",";  
             echo "\"".get_post_meta( $arr[$idx], $c['code']."state", true)."\",";  
         }
      }

    }
    echo "\n";    $idx++;
   }
}//IF



?>