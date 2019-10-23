<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CSN_Public {

    public function popups_styles_register() {
			custom_register_style(
				'custom-site-popups',
				'/assets/css/custom_site_popups.css',
				CSN_Assets::plugin_url(),
				CSN_Assets::plugin_path()
			);
    }

}
