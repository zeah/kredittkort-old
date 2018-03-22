<?php

defined( 'ABSPATH' ) or die( 'Blank Space' ); 

/*
	REGISTERING TAXONOMY
*/
final class Emkk_taxonomy {
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