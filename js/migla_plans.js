function mg_get_structure(){
  var list = []; var i = 0;

  jQuery('li.mg_reoccuring-field').each(function(){
    var item = {};
         item.name           = jQuery(this).find(".name").val();
         item.id             = "BasicTD_" + item.name;
         item.interval_count = jQuery(this).find(".time").val();
         item.interval       = jQuery(this).find(".period").val();
         item.payment_method = jQuery(this).find("input[name='method" + i + "'] option:selected").val();
         item.status         = jQuery(this).find("input[name='status" + i + "']:checked").val();
         list.push(item);
    i++;
  });
  return list;
}

function remove_plan(){
  jQuery('.removeField').bind('click', function(){

      var parent  = jQuery(this).closest('li.mg_reoccuring-field');
      //var method  = parent.find('.method').attr('name');
      var plan_id = "BasicTD_" +   parent.find('.name').val();

      //alert( parent.find(".method").val()  + plan_id + method );
    
      if( parent.find(".method").val() == 'paypal-stripe' ){
         //Delete the current plan
         jQuery.ajax({
             type : "post",
             url :  miglaAdminAjax.ajaxurl, 
             data : {action: "miglaA_stripe_deletePlan", id:plan_id },
             success: function(msg) {  
             }
         }); //ajax 
      }
 
      parent.remove(); //Remove us

      //Ok save the structure
      var send_list = [];
      send_list = mg_get_structure();

      //alert( JSON.stringify(send_list) );

      jQuery.ajax({
        type : "post",
        url :  miglaAdminAjax.ajaxurl, 
        data : {action: "miglaA_update_me", key:'migla_recurring_plans' , value:send_list },
        success: function(msg) {  
        }
      }); //ajax 
  
  });
}

jQuery(document).ready( function() {

   jQuery('#miglaAddPlan').click(function(){

      var lines = "";
      var Pname = jQuery('#migla_planName').val();
      var Pid = Pname;
      var Pt = jQuery('#migla_planTime').val();
      var Pp = jQuery('#migla_planPeriod').val();
      var Pmethod = jQuery("input[name='method']:checked").val();
      var row =  jQuery('li.mg_reoccuring-field').length ;

    if( Pname != ''){

      lines = lines + "<li class='mg_reoccuring-field clearfix title formheader ui-sortable-handle '>  <div class='col-sm-1 clabel'><label class='control-label '>Name</label></div>";
      lines = lines + "<div class='col-sm-3 col-xs-12'><input type='text' class='name' name='' placeholder='' value='" + Pname + "'></div>";

       lines = lines + " <div class='col-sm-2 hidden-xs'><label class='control-label'>Interval Count</label></div>";
       lines = lines + "<div class='col-sm-1 col-xs-12'><input type='text' class='time' name='' placeholder='' value='" + Pt + "'></div>";
       lines = lines + "<div class='col-sm-1 col-xs-12'>";

       var is_period = {};
       is_period['day'] = '';  is_period['week'] = '';  is_period['month'] = '';  is_period['year'] = '';
       is_period[Pp] = 'selected';
       
       lines = lines + " <select id='migla_planPeriod' class='period' name=''>";
       lines = lines + "  <option value='day' " + is_period['day'] + ">Day</option>";
       lines = lines + " <option value='week' " + is_period['week'] + ">Week</option>";
       lines = lines + " <option value='month' " + is_period['month'] + ">Month</option>";
       lines = lines + " <option value='year' " + is_period['year'] + ">Year</option>";
       lines = lines + " </select> </div>";

      if( Pmethod == 'paypal' ){
        lines = lines + "<div class='control-radio-sortable col-sm-3 col-xs-12'><span><label><input type='radio' class='method' value='paypal' name='method"+row+"' checked> Paypal</label></span>";
        lines = lines + "<span><label><input type='radio' class='method' value='paypal-stripe' name='method"+row+"'> Paypal & Stripe</label></span><span></span></div> "; 
      }else{
        lines = lines + "<div class='control-radio-sortable col-sm-3 col-xs-12'><span><label><input type='radio' class='' value='paypal' name='method"+row+"'> Paypal</label></span>";
        lines = lines + "<span><label><input type='radio' class=''value='paypal-stripe' name='method"+row+"' checked > Paypal & Stripe</label></span><span></span></div> "; 
      }

      lines = lines + "<div class='control-radio-sortable col-sm-3 col-xs-12'><span><label><input type='radio' class='' value='1' name='mg_RS0' checked> Show</label></span>";
      lines = lines + "<span><label><input type='radio' class='' value='-1' name='mg_RS0'> Deactivate</label></span>";

      lines = lines + "<span><button data-target='#confirm-delete' data-toggle='modal' class='removeField'><i class='fa fa-fw fa-trash'></i></button></span></div>";
      lines = lines + "</li>";

      //jQuery('ul.mg_recurring_row') .empty();
      jQuery(lines).prependTo( jQuery('ul.mg_recurring_row') );

      var send_list = [];
      send_list = mg_get_structure();

     //alert( JSON.stringify(send_list) );

      jQuery.ajax({
        type : "post",
        url :  miglaAdminAjax.ajaxurl, 
        data : {action: "miglaA_update_me", key:'migla_recurring_plans' , value:send_list },
        success: function(msg) {  
        }
      }); //ajax

      if( Pmethod == 'paypal-stripe' ){
          Pid = "BasicTD_" + Pid ;
    	  jQuery.ajax({
		type : "post",
		url : miglaAdminAjax.ajaxurl, 
		data : {action: "miglaA_stripe_addBasicPlan", 
                         amount:1,
                         interval:Pp,
                         interval_count:Pt,
                         name:Pname,
                         id:Pid
                        },
		success: function(msg) { 
                   saved('#miglaAddPlan');
		}
	  })  ; //ajax	
      }else{
             saved('#miglaAddPlan');
      }

   }else{
      alert('Data is not complete');
   }

   });

   remove_plan();

});
//