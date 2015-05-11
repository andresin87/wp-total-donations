<?php

add_action( 'admin_head', 'migla_icon_style' );

function migla_icon_style() {
?>
<style>

   #toplevel_page_migla_donation_menu_page .wp-menu-image {
        	background: url('<?php 
                 echo plugins_url( 'totaldonations/images/icons/icon-admin-migla16.png' , dirname(__FILE__)); 
                 ?>') no-repeat 9px 11px !important;                
   }
        
   #toplevel_page_migla_donation_menu_page .wp-menu-image img {
        	display: none !important;
   }
       

   #toplevel_page_migla_donation_menu_page:hover .wp-menu-image, #toplevel_page_migla_donation_menu_page.wp-has-current-submenu .wp-menu-image {
        	background: url('<?php
                  echo plugins_url( 'totaldonations/images/icons/icon-admin-migla16.png' , dirname(__FILE__));
                ?>') no-repeat 9px 11px !important;
            
   }
</style>

<?php
  }
?>