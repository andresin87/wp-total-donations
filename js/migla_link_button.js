jQuery('.migla_donate_now').click(function(e) {
   e.preventDefault();
   jQuery(this).parents('form').submit();
});