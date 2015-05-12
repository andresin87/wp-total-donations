jQuery(document).ready(function() {

// Tooltip only Text
jQuery('.masterTooltip').hover(function(){
        // Hover over code
        var title = jQuery(this).attr('title');
        jQuery(this).data('tipText', title).removeAttr('title');
        jQuery('<p class="tooltip"></p>')
        .text(title)
        .appendTo('body')
        .fadeIn('slow');
}, function() {
        // Hover out code
        jQuery(this).attr('title', jQuery(this).data('tipText'));
        jQuery('.tooltip').remove();
}).mousemove(function(e) {
        var mousex = e.pageX + 20; //Get X coordinates
        var mousey = e.pageY + 10; //Get Y coordinates
        jQuery('.tooltip')
        .css({ top: mousey, left: mousex })
});
});