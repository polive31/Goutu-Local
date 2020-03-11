/* =================================================================*/
/* = GOOGLE TAG MANAGER =*/
/* =================================================================*/
<?php

/*
Plugin Name: Custom Google Tag Manager
Plugin URI: http://goutu.org/
Description: Connection and management of Google Tag service
Version: 1.0
Author: Pascal Olive
Author URI: http://goutu.org
License: GPL
*/


// Block direct requests
if (!defined('ABSPATH'))
	die('-1');


/* Add html with genesis actions
--------------------------------------------------------------------*/
//add_action('wp_head','add_gtm_container_head');
function add_gtm_container_head()
{
?>
	<!-- Google Tag Manager -->
	<script>
		(function(w, d, s, l, i) {
			w[l] = w[l] || [];
			w[l].push({
				'gtm.start': new Date().getTime(),
				event: 'gtm.js'
			});
			var f = d.getElementsByTagName(s)[0],
				j = d.createElement(s),
				dl = l != 'dataLayer' ? '&l=' + l : '';
			j.async = true;
			j.src =
				'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
			f.parentNode.insertBefore(j, f);
		})(window, document, 'script', 'dataLayer', 'GTM-5P2DT2H');
	</script>
	<!-- End Google Tag Manager -->
<?php
}

//add_action( 'genesis_before', 'add_gtm_container_body' );
function add_gtm_container_body()
{
?>
	<!-- Google Tag Manager (noscript) -->
	<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-5P2DT2H" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
	<!-- End Google Tag Manager (noscript) -->
<?php
}
