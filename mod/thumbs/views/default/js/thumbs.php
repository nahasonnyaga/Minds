//<script>
elgg.provide('elgg.thumbs');

elgg.thumbs.init = function() {
		
	$('body').on('click', '.thumbs-button-up', elgg.thumbs.action);
	$('body').on('click', '.thumbs-button-down', elgg.thumbs.action);
};


elgg.thumbs.action = function(e) {
	var link = $(this);

		elgg.action($(this).attr('href'), {
			success: function(data) {
				if(data.output == 'selected'){
					link.css('color', '#4690D6');
				} else {
					link.css('color', '#AAAAAA');
				}
				
			}
		});

	e.preventDefault();
};


elgg.register_hook_handler('init', 'system', elgg.thumbs.init);
