<?php

$type = elgg_extract('type', $vars, 'content-side');

switch($type){
	case 'content-side':	
		if(elgg_get_plugin_user_setting('adblock', elgg_get_page_owner_guid(), 'minds')){
			echo elgg_get_plugin_user_setting('adblock', elgg_get_page_owner_guid(), 'minds');
		} else {
			echo '<script type="text/javascript"><!--
                google_ad_client = "ca-pub-9303771378013875";
                /* Minds large block */
                google_ad_slot = "5788264423";
                google_ad_width = 336;
                google_ad_height = 280;
                //-->
                </script>
                <script type="text/javascript"
                        src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
                </script>';
		}
			echo '<script type="text/javascript"><!--
		google_ad_client = "ca-pub-9303771378013875";
		/* Minds large block */
		google_ad_slot = "5788264423";
		google_ad_width = 336;
		google_ad_height = 280;
		//-->
		</script>
		<script type="text/javascript"
			src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
		</script>';
	break;
	case 'content-side-single':
		echo '<script type="text/javascript"><!--
                google_ad_client = "ca-pub-9303771378013875";
                /* Minds large block */
                google_ad_slot = "5788264423";
                google_ad_width = 336;
                google_ad_height = 280;
                //-->
                </script>
                <script type="text/javascript"
                        src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
                </script>';
	break;
	case 'content-side-single-user':
		if(elgg_get_plugin_user_setting('adblock', elgg_get_page_owner_guid(), 'minds')){
               		echo elgg_get_plugin_user_setting('adblock', elgg_get_page_owner_guid(), 'minds');
       		} else {
		echo '<script type="text/javascript"><!--
                google_ad_client = "ca-pub-9303771378013875";
                /* Minds large block */
                google_ad_slot = "5788264423";
                google_ad_width = 336;
                google_ad_height = 280;
                //-->
                </script>
                <script type="text/javascript"
                        src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
                </script>';
        	}
	break;
	case 'content-side-single-user-2':
		if(elgg_get_plugin_user_setting('adblock2', elgg_get_page_owner_guid(), 'minds')){
                	echo elgg_get_plugin_user_setting('adblock2', elgg_get_page_owner_guid(), 'minds');
        	} else {
        	echo '<script type="text/javascript"><!--
                google_ad_client = "ca-pub-9303771378013875";
                /* Minds large block */
                google_ad_slot = "5788264423";
                google_ad_width = 336;
                google_ad_height = 280;
                //-->
                </script>
                <script type="text/javascript"
                        src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
                </script>';
        	}
	break;
	case 'content-below-banner':
		echo '<div class="banner-ad"><div class="inner">';
        	if(elgg_get_plugin_user_setting('adblock3', elgg_get_page_owner_guid(), 'minds')){
                	echo elgg_get_plugin_user_setting('adblock3', elgg_get_page_owner_guid(), 'minds');
        	} else {
                	echo '<script type="text/javascript"><!--
                google_ad_client = "ca-pub-9303771378013875";
                /* Content Bottom Banner */
                google_ad_slot = "9810862421";
                google_ad_width = 728;
                google_ad_height = 90;
                //-->
                </script>
                <script type="text/javascript"
                        src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
                </script>';
        	}
		echo '</div></div>';
	break;
	case 'content-foot-user-1':
		if(elgg_get_plugin_user_setting('adblock3', elgg_get_page_owner_guid(), 'minds')){
                	echo elgg_get_plugin_user_setting('adblock3', elgg_get_page_owner_guid(), 'minds');
        	} else {
			echo '<script type="text/javascript"><!--
                google_ad_client = "ca-pub-9303771378013875";
                /* Content Bottom Banner */
                google_ad_slot = "9810862421";
                google_ad_width = 728;
                google_ad_height = 90;
                //-->
                </script>
                <script type="text/javascript"
                        src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
                </script>';
		}
	break;
	case 'content-foot':
		echo '<script type="text/javascript"><!--
		google_ad_client = "ca-pub-9303771378013875";
		/* Content Bottom Banner */
		google_ad_slot = "9810862421";
		google_ad_width = 728;
		google_ad_height = 90;
		//-->
		</script>
		<script type="text/javascript"
			src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
		</script>';
	break;
	case 'linkad-box':
		echo '<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
		<!-- linkad-box -->
		<ins class="adsbygoogle"
     		style="display:inline-block;width:200px;height:90px"
     		data-ad-client="ca-pub-9303771378013875"
    		 data-ad-slot="7254333223"></ins>
		<script>
		(adsbygoogle = window.adsbygoogle || []).push({});
	</script>';
	break;		
	case 'news':
		echo '<script type="text/javascript"><!--
google_ad_client = "ca-pub-9303771378013875";
/* News */
google_ad_slot = "6842535224";
google_ad_width = 300;
google_ad_height = 600;
//-->
</script>
<script type="text/javascript"
src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>';
	break;
	case 'large-block':
		echo '<script type="text/javascript"><!--
	google_ad_client = "ca-pub-9303771378013875";
/* Large ad */
google_ad_slot = "1083059623";
google_ad_width = 300;
google_ad_height = 600;
//-->
</script>
<script type="text/javascript"
src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>';
	break;
	case 'small-banner':
		echo '<script type="text/javascript"><!--
			google_ad_client = "ca-pub-9303771378013875";
			/* Minds Small Banner WIKI */
			google_ad_slot = "4244373227";
			google_ad_width = 234;
			google_ad_height = 60;
			//-->
			</script>
			<script type="text/javascript"
			src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
			</script>';
	case 'search-ad':
		echo '<script type="text/javascript"><!--
			google_ad_client = "ca-pub-9303771378013875";
			/* Search ad, square */
			google_ad_slot = "1151306021";
			google_ad_width = 125;
			google_ad_height = 125;
			//-->
			</script>
			<script type="text/javascript"
			src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
			</script>';
//bellow content rotator
	break;
	case 'content-block-rotator':
		$providers = array(	'contentad' => '<div class="contentad"><div id="contentad9733"></div>
	<script type="text/javascript">
    (function() {
        var params =
        {
            id: "67c8c761-6755-4867-92be-6317f1ea173a",
            d:  "bWluZHMuY29t",
            wid: "9733",
            cb: (new Date()).getTime()
        };

        var qs="";
        for(var key in params){qs+=key+"="+params[key]+"&"}
        qs=qs.substring(0,qs.length-1);
        var s = document.createElement("script");
        s.type= "text/javascript";
        s.src = "http://api.content.ad/Scripts/widget.aspx?" + qs;
        s.async = true;
        document.getElementById("contentad9733").appendChild(s);
    })();</script> </div>',
				'toobla' => "<div id='taboola-below-main-column'></div>
<script type='text/javascript'>

window._taboola = window._taboola || [];

_taboola.push({mode:'thumbs-2r',
container:'taboola-below-main-column',
placement:'below-main-column'});

</script>
>>>>>>> master

<div id='taboola-text-2-columns-mix'></div>
<script type='text/javascript'>

	window._taboola = window._taboola || [];

	_taboola.push({mode:'text-links-2c', container:'taboola-text-2-columns-mix', placement:'text-2-columns', target_type:'mix'}); </script>"

);
	$rand = array_rand($providers);
	//$rand = get_input('show_ad', 'contentad');
	echo '<div class="content-block-ratator">' .$providers[$rand] . '</div>';
	break;	
	case 'content.ad':
	        echo '<div class="contentad"><div id="contentad9733"></div>
<script type="text/javascript">
    (function() {
        var params =
        {
            id: "67c8c761-6755-4867-92be-6317f1ea173a",
            d:  "bWluZHMuY29t",
            wid: "9733",
            cb: (new Date()).getTime()
        };

        var qs="";
        for(var key in params){qs+=key+"="+params[key]+"&"}
        qs=qs.substring(0,qs.length-1);
        var s = document.createElement("script");
        s.type= "text/javascript";
        s.src = "http://api.content.ad/Scripts/widget.aspx?" + qs;
        s.async = true;
        document.getElementById("contentad9733").appendChild(s);
    })();</script> </div>';
	break;
	case 'content.ad-side':
		echo '<div class="contentad-side"><div id="contentad11261"></div>
<script type="text/javascript">
    (function() {
        var params =
        {
            id: "6577b456-0103-46fc-b54d-aeea66a87fec",
            d:  "bWluZHMuY29t",
            wid: "11261",
            cb: (new Date()).getTime()
        };

        var qs="";
        for(var key in params){qs+=key+"="+params[key]+"&"}
        qs=qs.substring(0,qs.length-1);
        var s = document.createElement("script");
        s.type= "text/javascript";
        s.src = "http://api.content.ad/Scripts/widget.aspx?" + qs;
        s.async = true;
        document.getElementById("contentad11261").appendChild(s);
    })();
</script></div>';
	break;
	case 'toobla-side':
		echo "<div class='toobla-side'><div id='taboola-right-rail'></div>
<script type='text/javascript'>

window._taboola = window._taboola || [];

_taboola.push({mode:'thumbs-4r', container:'taboola-right-rail',
placement:'right-rail'});

</script></div>";
	break;
	default:
		echo '';
}
