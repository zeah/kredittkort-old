<?php

/**
	Plugin Name: EM-kredittkort
	Description: kredittkortliste for effektiv markedsforing
	Version: 1.0.1
*/

defined( 'ABSPATH' ) or die(' Blank Space' );

define( 'PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once 'inc/em-admin.php';
require_once 'inc/em-shortcode.php';

function emkk_init() {

	if (is_admin())
		/* admin page */
		Emkk_admin::get_instance();
	else
		/* front page */
		Emkk_shortcode::get_instance();


}
add_action('plugins_loaded', 'emkk_init');