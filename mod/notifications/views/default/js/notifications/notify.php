<?php
/**
 * Chat JavaScript extension for elgg.js
 */
?>
elgg.provide('elgg.notify');

elgg.notify.init = function() {
	if (elgg.is_logged_in()) {
        	$(document).on("click",'#notify_button', elgg.notify.getNotifications);
	}

};

/**
 * Change the color of new messages.
 */
/*elgg.notify.markMessageRead = function() {
	var activeMessages = $('.elgg-chat-messages .elgg-chat-unread');
	var message = $(activeMessages[0]);
	message.animate({backgroundColor: '#ffffff'}, 1000).removeClass('elgg-chat-unread');
};*/

/**
 * Get the number of unready messages
 * 
 */
elgg.notify.getUnreadNotifications = function() {
   
 	var url = elgg.normalize_url("mod/notifications/pages/count.php"  + '?' + Math.random());
    	
  	$.get(url, function(data) {
      		$('#notify_button').html(data);
            //console.log(data);
            //$('#notification').append(data);
     });


}

/**
 * Get notifications via AJAX.
 * 
 */
elgg.notify.getNotifications = function(e) {
   
     var url = elgg.get_site_url() + "notifications";
	console.log('polling for new notifications');    	
     $.get(url, function(data) {
      		$('#notification').html(data);
      		$('#notification .load-more').show();
            //$('#notification').append(data);
     });

     //reset the counter to 0
     $(".notification-new").hide();

}

elgg.register_hook_handler('init', 'system', elgg.notify.init);
