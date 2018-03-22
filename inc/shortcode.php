<?php

defined( 'ABSPATH' ) or die( 'Blank Space' );

/*
	FRONT-END STUFF
*/
final class Emkk_shortcode {
	/* SINGLETON */
	private static $instance = null;

	public static function get_instance() {
		if (self::$instance === null)
			self::$instance = new self();

		return self::$instance;
	}

	private function __construct() {

	}
}