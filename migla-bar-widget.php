<?php
/*
 * Plugin Name: Total Donations Bar Widget
 * Plugin URI: http://calmar-webmedia.com/testing-area/wp-plugin-dev
 * Description: A widget that displays the progress-bar for each campaign in Total Donations.
 * Version: 1.2.1
 * Author: Binti Brindamour and Astried Silvanie
 * Author URI: http://calmar-webmedia.com/
 * License: Licensed
 */


/*
 * Add function to widgets_init that'll load our widget.
 */
add_action( 'widgets_init', 'totaldonations_bar_widget' );

/*
 * Register widget.
 */
function totaldonations_bar_widget() {
	register_widget( 'Totaldonations_Bar_Widget' );
}


/*
 * Widget class.
 */
class totaldonations_bar_widget extends WP_Widget {

	/* ---------------------------- */
	/* -------- Widget setup -------- */
	/* ---------------------------- */
	
	function Totaldonations_Bar_Widget() {
	
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'totaldonations_bar_widget', 'description' => __('A widget that displays progress bar for total donation', 'localization') );

		 /* Widget control settings. */
		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'totaldonations_bar_widget' );

		/* Create the widget. */
		$this->WP_Widget( 'totaldonations_bar_widget', __('Total Donations Bar Widget','localization'), $widget_ops, $control_ops );
	}

	/* ---------------------------- */
	/* ------- Display Widget -------- */
	/* ---------------------------- */
	
	function widget( $args, $instance ) {
		extract( $args );

  if( wp_script_is( 'mg_progress-bar', 'registered' ) ){
  }else{
    wp_register_style( 'mg_progress-bar', plugins_url( 'totaldonations/css/mg_progress-bar.css' , dirname(__FILE__)) );
  }

  if( wp_script_is( 'mg_progress-bar', 'queue' ) ){}else{
    wp_enqueue_style( 'mg_progress-bar' );
  }

		/* Our variables from the widget settings. */
        $title = apply_filters('widget_title', $instance['title'] );
	$campaign = $instance['campaign'];
        $style = $instance['belowHTML'];
        $link = $instance['link'];
        $btnclass = $instance['btnclass'];
	$btnstyle = $instance['btnstyle'];
        $btntext = $instance['btntext'];
        
              	/* Before widget (defined by themes). */
        echo $before_widget;

        $send =  str_replace( "[q]", "'", $campaign); 
      echo "<h3 class='widget-title'>";
      echo $title. "<br>";
      echo "</h3>";

      echo  migla_widget_progressbar( $campaign );
  
	  $class2 = "";
      if( $btnstyle == 'GreyButton' ){
        $class2 = ' mg-btn-grey';	  
	  }	  

      $url = get_option( 'migla_form_url' );
	
      if( $link=='on' ){
        echo "<form action='".$url."' method='post'>";
        echo "<input type='hidden' name='campaign' value='".$campaign."' />";
		
		echo "<input type='hidden' name='thanks' value='widget_bar' />";
		
        //  if( $btntext == '' ){ $btntext = 'Donate'; }
        echo "<button class='migla_donate_now ".$btnclass . $class2."'>".$btntext."</button>";
        echo "</form>";
      }

        echo $style;
?>

<script> 
jQuery('.migla_donate_now').click(function(e) {
   e.preventDefault();
   jQuery(this).parents('form').submit();
});
</script>

<?php		

        echo $after_widget;
                
         
		
	}

	/* ---------------------------- */
	/* ------- Update Widget -------- */
	/* ---------------------------- */
	
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags for title and name to remove HTML (important for text inputs). */
        $instance['title'] = strip_tags( $new_instance['title'] );
        $instance['campaign'] = strip_tags( $new_instance['campaign'] );

		/* No need to strip tags for.. */
        $instance['belowHTML'] =  $new_instance['belowHTML'] ;		
        $instance['link'] =  strip_tags( $new_instance['link'] ) ;		
        $instance['btnclass'] =  strip_tags( $new_instance['btnclass'] );
        $instance['btnstyle'] =  strip_tags( $new_instance['btnstyle'] );
        $instance['btntext'] = $new_instance['btntext'];
		
		return $instance;
	}
	
	/* ---------------------------- */
	/* ------- Widget Settings ------- */
	/* ---------------------------- */
	
	/**
	 * Displays the widget settings controls on the widget panel.
	 * Make use of the get_field_id() and get_field_name() function
	 * when creating your form elements. This handles the confusing stuff.
	 */
	 
	function form( $instance ) {

     // Check values 
    if( $instance) { 
       $title = esc_attr($instance['title']); 
       $campaign = esc_attr($instance['campaign']); 
       $belowHTML = $instance['belowHTML']; 
       $link = esc_attr($instance['link']); 
       $btnclass = esc_attr($instance['btnclass']); 
       $btnstyle = esc_attr($instance['btnstyle']); 
       $btntext = esc_attr($instance['btntext']);
     } else { 
       $title = "Total Donations Progress Bar"; 
       $campaign = ''; 
       $belowHTML = ''; 
       $link = ''; 
       $btnclass = ''; 
       $btnstyle = ''; 
       $btntext = '';
     } 
?>

	<!-- Widget Title: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title of the progress bar:', 'localization') ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $title; ?>" />
		</p>

<label ><?php _e('Current Campaign : '  , 'localization') ?></label>  
<label ><?php $c_name = str_replace( "[q]", "'", $campaign ); echo $c_name; ?></label>  

<br><br><label ><?php _e('Choose a campaign to show :', 'localization') ?></label>   
   
<?php
    //Show select on widget 
      $out = "";
      $out .= "<select class='widefat migla_select' name='".$this->get_field_name( 'campaign' )."' id='".$this->get_field_id( 'campaign' )."'>" ;
      $b = "";
      $i = 0;   
      $fund_array = (array)get_option( 'migla_campaign' );

    if( empty($fund_array[0]) ){ 
    }else{    
       //print_r($fund_array);
       foreach ( (array)$fund_array as $key => $value ) 
	   { 
	    if( strcmp( $fund_array[$i]['show'],"1")==0 ){
                  $c1_name = esc_html__( $fund_array[$i]['name'] );
                  $c_name = str_replace( "[q]", "'", $c1_name );

         if( strcmp( $fund_array[$i]['name'], $campaign ) == 0  ){
		    $out .= "<option value='".$fund_array[$i]['name']."' selected=selected >".$c_name."</option>";
                  }else{
		     $out .= "<option value='".$fund_array[$i]['name']."' >".$c_name."</option>";
                  }
	   }
        $i++;
	   }  
      }	   
      $out .= "</select>"; 
      echo $out;
?>
       <br><br>
      <?php if( $link == 'on'){  ?> 
        <div><input type="checkbox" checked="checked" id="<?php echo $this->get_field_id( 'link' ); ?>" name="<?php echo $this->get_field_name( 'link' ); ?>">
        <label>Add link button ? </label></div>
      <?php }else{  ?> 
        <div><input type="checkbox" id="<?php echo $this->get_field_id( 'link' ); ?>" name="<?php echo $this->get_field_name( 'link' ); ?>">
        <label>Add link button ? </label></div>
      <?php }  ?> 

        <div><label>Add a css class on button: <small>(theme button only)</small></label>
        <input input='text' class='widefat' type='text' id="<?php echo $this->get_field_id( 'btnclass' ); ?>" name="<?php echo $this->get_field_name( 'btnclass' ); ?>" value="<?php echo $btnclass; ?>"></input></div>  
  
     <br><label>Choose a button style:</label> 
     <select id="<?php echo $this->get_field_id( 'btnstyle' ); ?>" name="<?php echo $this->get_field_name( 'btnstyle' ); ?>" class="widefat migla_select">
     <?php if( $btnstyle == "GreyButton" ) { ?>
 	   <option  value="themeDefault">Your Theme Default</option>
       <option selected="" value="GreyButton">Grey Button</option>
	 <?php }else{ ?>
 	   <option selected="" value="themeDefault">Your Theme Default</option>
       <option value="GreyButton">Grey Button</option>	 
	 <?php } ?>
	 </select>
	 <br><br>

      <p>
	<label for="<?php echo $this->get_field_id( 'btntext' ); ?>"><?php _e('Text of button:', 'localization') ?></label>
	<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'btntext' ); ?>" name="<?php echo $this->get_field_name( 'btntext' ); ?>" value="<?php echo $btntext; ?>" />
      </p>
      
       <label for="<?php echo $this->get_field_id('belowHTML'); ?>">Add HTML or Plain Text here :</label>
       <textarea  class="widefat"  id="<?php echo $this->get_field_id( 'belowHTML' ); ?>" name="<?php echo $this->get_field_name( 'belowHTML' ); ?>"  ><?php echo $belowHTML; ?></textarea><br><br>

	<?php
	}
}
?>