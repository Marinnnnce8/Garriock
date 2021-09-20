<?php namespace ProcessWire; 

/**
 * foot.php
 *
 * Please retain this code, integrating as necessary
 *
 */

foreach($config->scripts as $script)
	echo "<script src='$script'></script>";

if($page->hasMap && $nb->googleApiKey): ?>
	<script src='https://maps.googleapis.com/maps/api/js?key=<?= $nb->googleApiKey ?>&amp;callback=initMap' async></script>
	<script>
	function initMap() {
		$site.gmap();
	}
	</script>
<?php endif; 
?>

<?php if($nb->siteLive): ?>
	<script src="cookieControl-6.2.min.js" type="text/javascript"></script>
	<script type="text/javascript">//<![CDATA[
	cookieControl({
		t: {
			title: '<p>This site uses cookies to store information on your computer.</p>',
			intro: '<p>Some of these cookies are essential to make our site work and others help us to improve by giving us some insight into how the site is being used.</p>',
			full:'<p>These cookies are set when you submit a form, login or interact with the site by doing something that goes beyond clicking some simple links.</p><p>We also use some non-essential cookies to anonymously track visitors or enhance your experience of this site. If you\'re not happy with this, we won\'t set these cookies but some nice features on the site may be unavailable.</p><p>To control third party cookies, you can also <a class="ccc-settings" href="browser-settings" target="_blank">adjust your browser settings.</a></p><p>By using our site you accept the terms of our <a href="https://www.garriock.co.uk/privacy-policy">Privacy Policy</a>.</p>'
		},
		position:CookieControl.POS_LEFT,
		style:CookieControl.STYLE_TRIANGLE,
		theme:CookieControl.THEME_LIGHT, // light or dark
		startOpen:false,
		autoHide:7000,
		subdomains:true,
		protectedCookies: [], // list the cookies you do not want deleted, for example ['analytics', 'twitter']
		apiKey: '1a199211f0e205c23638ebed897c24ecb9c25eac',
		product: CookieControl.PROD_FREE,
		consentModel: CookieControl.MODEL_INFO,
		onAccept:function(){ccAddAnalytics()},
		onReady:function(){},
		onCookiesAllowed:function(){ccAddAnalytics()},
		onCookiesNotAllowed:function(){}
		});

		function ccAddAnalytics() {
			jQuery.getScript("https://www.google-analytics.com/ga.js", function() {
			var GATracker = _gat._createTracker('UA-40519514-1');
			GATracker._trackPageview();
			});
		}
	//]]>
	</script>
<?php endif; ?>
