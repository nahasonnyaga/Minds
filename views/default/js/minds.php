<?php if (FALSE) : ?>
    <script type="text/javascript">
<?php endif; ?>
	 
	 //Main js that needs to be loaded
	 elgg.provide('minds');
	 
	 minds.init = function() {	
	 	var sidebarOpen = false;
	 	$(document).on('click', '.menu-toggle', function(){
	 		if(sidebarOpen){
	 			$('.global-sidebar').removeClass('show');
	 			$('.hero').removeClass('sidebar-active');
	 			sidebarOpen = false;
	 		} else {
	 			$('.global-sidebar').addClass('show');
	 			$('.hero').addClass('sidebar-active');
	 			sidebarOpen = true
	 		}
	 	});

		/*$('.global-sidebar .elgg-menu-page li').toggle(function(){
			if($(this).find('a')[0].hasClass('elgg-menu-opened')){
				$('.global-sidebar').width(225);
			} else {
				$('.global-sidebar').width(600);
			}
		});*/

		if(!elgg.is_logged_in() && !$.cookie('promptSignup')){
			setTimeout(function(){ $.fancybox("#minds-signup-popup"); $.cookie('promptSignup', true) }, 4000);
		}
		$(document).on('click', "#minds-signup-popup .cancel", function(){
			$.fancybox.close();
		});
		
		/**
		 * Make our newsfeed icons centred @todo make less hacky
		 */
		$(window).on("load resize", function(){
			$('.thumbnail-wrapper img.thumbnail').css('margin-top', ($('.thumbnail-wrapper').height() - $('.thumbnail-wrapper img').height()) /2); 
		});
	
	 	
	 	$(".carousel-admin-items").sortable({
			items:                'div.carousel-admin-wrapper',
			connectWith:          '.carousel-admin-items',
			helper: 			  'clone',
			handle:               '.drag',
			forcePlaceholderSize: true,
			//placeholder:          'elgg-widget-placeholder',
			opacity:              0.8,
			//revert:               500,
			distance: 				0,
			stop:                 function(e, ui){ 
									console.log('you just moved me to '+ ui.item.index()); 
									//ui.item.find('#order').val(ui.item.index());
									
									$('.carousel-admin-items > div').each(function(i,j){
										console.log($(this));
										$(this).find('#order').val($(this).index());
									});
									
									

								}
		});
		
		if($(".carousel-colorpicker").length){
			/*$(".text-color").minicolors({
					letterCase: 'uppercase',	
					change: function(){ 
							$(this).parent().find('textarea').css('color', $(this).val());
					}	
				});*/
			$(".text-color").on('keyup', function(){
				$(this).css('color', $(this).val());
				$(this).parents('.carousel-admin-wrapper').find('textarea').css('color', $(this).val());
			});
			$(".shadow-color").spectrum({
   				showAlpha: true,
				showButtons: false,
				preferredFormat: 'rgb',
				move: function(color) {
					color = color.toRgbString();
					console.log(color);
					$(this).val(color);
					$(this).parents('.carousel-admin-wrapper').find('textarea').css('background', color); 
				},
				chose: function(color){
					this.move(color);
				}
			});
		}
	 
		$('.carousel-admin-wrapper > textarea').on('keyup', function(){
			var px = $(this).val().length*36 + 20;
			console.log(px);
			if(px < 880 && px > 200){
				$(this).css('width', px);
				} else if(px < 880)  {
				$(this).css('height', 200);
			}
		});	

		if($('#fb-share').length){
			var addr = $('meta[property="og:url"]').attr('content');
			addr = addr.replace('http:', 'https:');
			var url = 'https://graph.facebook.com/?id=' + addr;
			var total = 0;
			$.get( url, function(data) { 
				var shares = data.shares; 
				if($.isNumeric(shares)){ 
					total = shares; 
				}
		
				addr = addr.replace('https:', 'http:');
				url = 'https://graph.facebook.com/?id=' + addr;

				$.get( url, function(data) { 
					var shares = data.shares; 
					 if($.isNumeric(shares)){
						total = total+shares;
					}
					$('#fb-share .count').html(total); 
				});
			});
		} 

		var $list = $('.elgg-list.mason');
		//$list.masonry();   
		$('.elgg-list.mason').imagesLoaded(function(){
			
			$list.find('.rich-image').each(function(){ 
				var image = $(this); 
				if(image.context.naturalWidth < 2 ||
				image.readyState == 'uninitialized'){    
					 /*$(image).unbind("error").attr(
					    "src", "path/to/image/no-image.gif"
					 );*/
					//       $(this).hide();
					$(this).remove();
				} 
		 	});
			$('.minds-content-loading').remove();
			$list.show();
			$('.load-more').show();
			$list.masonry(
			{
				stamp: '.minds-fixed-post-box'
			}
			);                        
		});
		
		$('.hero > .topbar').on('mouseenter', '.right', function(e){ $('.topbar .right .social-login').show(); });
		$('.hero > .body').on('click', function(e){ $('.topbar .right .social-login').hide(); });
		
		$('.elgg-form-blog-save, .elgg-form-archive-save').submit(function(e){
 			var c = confirm("Please confirm you understand the selected license, and that you either own or have permission under the terms of the license.");
			return c;
		});

		if($(window).width() > 700){
			//$('.elgg-menu li a').tipsy({gravity: 'n'}); 
			$('.progress_indicator').tipsy({gravity: 'e'});		
			$('.elgg-input-text').tipsy({gravity: 'w'});
			$('.tooltip.s').tipsy({gravity: 's'});
			$('.tooltip.n').tipsy({gravity: 'n'});
			$('.tooltip.w').tipsy({gravity: 'w'});
			$('.tooltip.e').tipsy({gravity: 'e'});
		}
	
		$('.elgg-form-wall-add textarea').focus( function(e){ $(this).parent().find('.elgg-button-submit').css('display','block');});

		$('.thumbnail-tile').hover(
			function(){
		   		$(this).children('.hover').fadeIn('fast');
			},
			function(){
		   		$(this).children('.hover').fadeOut('fast');
		}); 

		//handle cookie session messages
		var msgs = $.cookie('mindsMessages');
		if(msgs && !elgg.is_logged_in()){
			var msgs = JSON.parse(msgs);
			if(msgs.error)
				elgg.register_error(msgs.error);

			if(msgs.success)
				elgg.system_message(msgs.success);

			$.removeCookie('mindsMessages', { path: '/' });
		}
		//elgg.system_message(msg);
	
		$(document).on('click', '.load-more', minds.loadMore);
		$(document).on('trigger', '.load-more', minds.loadMore);
		/**
		 * Now make this autoscroll!
		 */
		$(window).on('scroll', minds.onScroll);
		if(elgg.is_logged_in()){
			$(document).on('click', '.subscribe-button a', minds.subscribe);
		}
		$(document).on('click', '.elgg-menu-item-feature a', minds.feature);

		$(document).on('click', 'li .elgg-menu-item-delete a', minds.remove); 
		$(document).on('click', '.elgg-menu-item-remind a', minds.remind);

		$(document).on('keydown', '.minds-search .elgg-input-text', minds.search);
	};
	 
	
	 elgg.register_hook_handler('init', 'system', minds.init);
	
	minds.wallPost = false;
	
	minds.search = function(e){
		if($(this).val().indexOf('/') == 0 && e.which == 13){
			e.preventDefault();
			var data = {};
			//data.to_guid = elgg.get_logged_in_user_guid();
			data.body = $(this).val();
			data.ref = 'news';
			
			data.body = data.body.replace('/', '');

			elgg.action('wall/add?body=' + data.body  +'&ref=' + data.ref, {
				success: function(data) {
					    $('.elgg-input-text').val("");;
						$('.elgg-list.elgg-list-river.elgg-river').first('.elgg-list.elgg-list-river.elgg-river').prepend(data.output);
						minds.wallPost = false;
				},
				error: function(data){
        	                }
	                });

		}	
	}

	minds.subscribe = function(e){
		e.preventDefault();
		var button = $(this);
		elgg.action($(this).attr('href') + '&ajax=true', {
			success: function(data) {
				if(data.output == 'subscribed'){
					button.addClass('subscribed');
					button.html('<span class="text">Subscribed</span>');
					button.attr('href', button.attr('href').replace('add', 'remove'));
				} else {
					button.removeClass('subscribed');
                                        button.html('<span class="text">Subscribe</span>');
					button.attr('href', button.attr('href').replace('remove','add'));
				}
			},
			error: function(data){
			}
		});
	}	 
	
	minds.feature = function(e){
		e.preventDefault();
		var button = $(this);
                elgg.action($(this).attr('href') + '&ajax=true', {
                        success: function(data) {
				if(data.output == 'featured'){			
                                        button.html('un-feature');
				} else {
					button.html('feature');
				}
                         },
                   
                        error: function(data){
                        }
                });
	}

	minds.remove = function(e){
		e.preventDefault();
       var button = $(this);
		var item = button.parents('.elgg-item');
		
		if($(this).hasClass('ajax-non-action')){
			elgg.get($(this).attr('href'), {
                success: function(data) {
          			item.effect('drop'); 
                 },
				error: function(data){
                }
       		 });
			
			return true;
		}
		
        elgg.action($(this).attr('href') + '&ajax=true', {
            success: function(data) {
      			item.effect('drop'); 
             },
			error: function(data){
            }
        });
	}

        minds.remind = function(e){
                e.preventDefault();
                var button = $(this);
                var item = $(this).parent('elgg-list');
		elgg.action($(this).attr('href') + '&ajax=true', {
                        success: function(data) {
				if(item.length){
				//we are in a list so we want to add a remind item in
				}
        			button.css('color', '#4690D6');        
			/*	button.effect('explode'); */      
	  	 },

                        error: function(data){
                        }
                });
        }

	 minds.onScroll = function(){
	 	$loadMoreDiv = $(document).find('.load-more');
	 	//go off the loadmore button being available
	 	if($loadMoreDiv){
	 		$list = $loadMoreDiv.parent();
	 		if($(window).scrollTop() + $(window).height() > $(document).height() * 0.1){
	 			if(!$loadMoreDiv.hasClass('loading')){
	 				$loadMoreDiv.trigger('trigger');
	 			}
	 		}
	 	}
	 	
	 }
	 
	 minds.listParams = {
	 	offset : 0,
	 	limit : 12
	 };

	 minds.loadMore = function() {
		if(window.lock_autoscroll){
			return false;
		}
		
		window.lock_autoscroll = true;
		
		$list = $(this).parent().find('.elgg-list:first').parent();
		$('.load-more').html('<p class="dots">...</p><p class="message"> loading, please hold tight </p>');
		
		$('.load-more').addClass('loading');
			
		var loc =  elgg.normalize_url(elgg.parse_url(location.href).path);
		if($list.find('.load-more').attr('next-uri'))
			loc = elgg.normalize_url(elgg.parse_url($list.find('.load-more').attr('next-uri')).path);


/*		if(loc == elgg.get_site_url()){
			loc = location.href + 'news/featured';
		}
*/
		var offset = 0;

		$params = elgg.parse_str(elgg.parse_url(location.href).query);
			
		if(loc.indexOf('trending') > -1 <?php if(elgg_is_active_plugin('analytics')){ echo "|| loc.indexOf('view') > -1"; }?> || $params.filter == 'trending' || loc.indexOf('search') > -1){
			offset = $list.find('.elgg-list').children().length;
		} else {
			offset = $('.load-more').attr('data-load-next');
		}
		//console.log(offset);
		if(!offset){
			window.lock_autoscroll = false;
			return false;
		}
		$params = $.extend($params, {
			path : loc,
			items_type: $list.find('.elgg-list').hasClass('elgg-list-entity') ? 'entity' :
				$list.find('.elgg-list').hasClass('minds-list-river') ? 'river' :
				$list.hasClass('elgg-list-annotation') ? 'annotation' : 
				$list.find('.elgg-list').hasClass('minds-search-list') ? 'search' : 'entity',
			offset:offset,
			limit:12 
		});
		url = "/ajax/view/page/components/ajax_list?" + $.param($params);
			
		elgg.get(url, {
			success: function(data) {
			//$list.toggleClass('infinite-scroll-ajax-loading', false);
				
				if($(data).contents().length == 0){
					
					$('.load-more').html('<p>Sorry, there is no more content.</p>');
					
				} else {
				
					$('.load-more').remove();
				
					var el = $(data).contents().unwrap();
					
					if(loc == elgg.get_site_url()){
						offset = $(data).find('li.elgg-item:last').attr('featured_id');
   					} else {
						offset = $(data).find('li.elgg-item:last').attr('id'); 
                   	}
                   	
                   	$list.append('<div class="news-show-more load-more" data-load-next="'+offset+'"><p>Downloading images...</p></div>');
					
					el.imagesLoaded().always(function(){
						el.find('.rich-image').each(function(){ 
							var image = $(this); 
							if(image.context.naturalWidth < 2 ||
								image.readyState == 'uninitialized'){    
								 /*$(image).unbind("error").attr(
								    "src", "path/to/image/no-image.gif"
								 );*/
								//       $(this).hide();
								$(this).remove();
							} 
						});
						$list.find('.elgg-list').append(el).masonry('appended', el);
						window.lock_autoscroll = false;
						$('.load-more').remove();
						$list.append('<div class="news-show-more load-more" data-load-next="'+offset+'">click for more</div>');
					});

					// Trigger a hook for extra tasks after content is loaded
					elgg.trigger_hook('loadMore', 'minds');
				}
			},
			error : function(data){
				$('.load-more').html('<p>Sorry, there is no more content.</p>');
			}
			
		});
		 
	 };	
     
<?php if (FALSE) : ?>
    </script>
<?php endif; ?>

