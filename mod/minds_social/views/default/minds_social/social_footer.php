<?php 
	$og_url = get_input('og:url');
?>
<div class="minds-social">
<!--	<div class="fb-like" data-href="<?php echo $og_url;?>" data-send="false" data-layout="button_count" data-width="450" data-show-faces="false" data-font="arial"></div> -->
	
	<a id="fb-share" style='text-decoration:none;' type="icon_link" onClick="window.open('http://www.facebook.com/sharer.php?u=' + encodeURIComponent(window.location),'sharer','toolbar=0,status=0,width=580,height=325');" href="javascript: void(0)">
	    <img src="<?php echo elgg_get_site_url();?>mod/minds_social/graphics/facebook_share.png" width="62" height="18" alt="Share"/> <span class="count">0</span>
	<script type="text/javascript">
		(function() {
			var addr = window.location.href;
			var url = 'https://graph.facebook.com/?id=' + addr;
			$.get( url, function(data) { var shares = data.shares; if($.isNumeric(shares)){ $('#fb-share .count').html(shares); } } );
		})();
	</script>
	</a>
		
	<a href="https://twitter.com/share" class="twitter-share-button" data-lang="en">Tweet</a>
	<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="https://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
	
	<!-- Place this tag where you want the +1 button to render. -->
	<div class="g-plusone" data-size="medium"></div>
	
	
	<script type="text/javascript">
	  (function() {
	    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
	    po.src = 'https://apis.google.com/js/plusone.js';
	    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
	  })();
	</script>
	
	<script src="//platform.linkedin.com/in.js" type="text/javascript"></script>
	<script type="IN/Share" data-counter="right"></script>
	
	<a href="http://www.reddit.com/submit" class='reddit-share' onclick="window.location = 'http://www.reddit.com/submit?url=' + encodeURIComponent(window.location); return false"> 
		<img src="http://www.reddit.com/static/spreddit7.gif" alt="submit to reddit" border="0" /> 
	</a>
	
</div>
