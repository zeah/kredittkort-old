<?php

defined( 'ABSPATH' ) or die( 'Blank Space' );

require_once 'em-posttype.php';
require_once 'em-customizer.php';
require_once 'em-settings.php';
require_once 'em-info.php';

/*
	ADMIN SIDE STUFF
*/
final class Emkk_admin {
	/* SINGLETON */
	private static $instance = null;

	public static function get_instance() {
		if (self::$instance === null)
			self::$instance = new self();

		return self::$instance;
	}

	private function __construct() {
		/* creates custom post type */
		Emkk_posttype::get_instance();

		/* wordpress customizer */
		Emkk_customizer::get_instance();

		/* plugin settings page */
		Emkk_settings::get_instance();

		/* plugin info */
		Emkk_info::get_instance();
	}

	public function enqueue() {
		
	}
}