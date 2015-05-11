<?php   

    //mimic the actual admin-ajax
    define('DOING_AJAX', true);

    if (!isset( $_POST['action']))
        die('-1');

    //make sure you update this line 
    //to the relative location of the wp-load.php
//require_once '../../../wp-config.php';
require_once '../../../wp-load.php';
require_once 'migla-donation-ajax-functions.php';

    //Typical headers
    header('Content-Type: text/html');
    send_nosniff_header();

    //Disable caching
    header('Cache-Control: no-cache');
    header('Pragma: no-cache');

    $action = esc_attr(trim($_POST['action']));
    //$prefix = substr( $action, 0, 5);

   //For logged in users
add_action("wp_ajax_".$action , $action); 
add_action("wp_ajax_nopriv_".$action , $action);

        if(is_user_logged_in())
            do_action('wp_ajax_'.$action);
        else
            do_action('wp_ajax_nopriv_'.$action);
    
?>