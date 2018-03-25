<?php 
defined( 'ABSPATH' ) or die( 'Blank Space' );

final class Emkk_settings {
	/* SINGLETON */
	private static $instance = null;

	public static function get_instance() {
		if (self::$instance === null)
			self::$instance = new self();

		return self::$instance;
	}

	private function __construct() {
		add_action('admin_menu', array($this, 'add_menu'));
	}

	public function add_menu() {
		add_submenu_page('edit.php?post_type=emkort', 'Settings', 'Settings', 'manage_options', 'emkort_settings', array($this, 'menu_callback'));
	}

	public function menu_callback() {
		echo 'hello';

		/* add link to external css sprite */
	}
}