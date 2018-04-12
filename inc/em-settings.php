<?php 
defined( 'ABSPATH' ) or die( 'Blank Space' );

final class Emkk_settings {
	/* SINGLETON */
	private static $instance = null;

	public static function get_instance() {
		if (self::$instance === null) self::$instance = new self();

		return self::$instance;
	}

	private function __construct() {
		add_action('admin_menu', array($this, 'add_menu'));

		add_action('admin_init', array($this, 'settings'));
		add_action('admin_init', array($this, 'page'));
	}

	public function add_menu() {
		add_submenu_page('edit.php?post_type=emkort', 'Settings', 'Settings', 'manage_options', 'emkk_settings_page', array($this, 'menu_callback'));
	}

	public function settings() {
		// $args = ['sanitize_callback' => array($this, 'sanitize')];

		register_setting('emkk_settings_group', 'emkk_settings', ['sanitize_callback' => array($this, 'sanitize')]);
	}

	public function page() {
		add_settings_section('emkk_settings_field', 'External Resources', array($this, 'settings_field'), 'emkk_settings_page');
		add_settings_field('emkk_css_disable', 'Disable External Resources', array($this, 'css_disable'), 'emkk_settings_page', 'emkk_settings_field');
		add_settings_field('emkk_css_sprite', 'Sprite Image', array($this, 'css_sprite'), 'emkk_settings_page', 'emkk_settings_field');
		add_settings_field('emkk_css_desktop', 'CSS Desktop', array($this, 'css_desktop'), 'emkk_settings_page', 'emkk_settings_field');
		add_settings_field('emkk_css_mobile', 'CSS Mobile', array($this, 'css_mobile'), 'emkk_settings_page', 'emkk_settings_field');
		// add_settings_field('emkk_css_tablet', 'CSS Tablet', array($this, 'css_tablet'), 'emkk_settings_page', 'emkk_settings_field');
	}

	public function settings_field() {
		echo 'Hvis feltene er satt, så vil de overkjøre lokale filer.';
	}

	public function css_disable() {
		echo '<input type="checkbox" name="emkk_settings[css_disable]"'.($this->get_setting('css_disable') ? ' checked' : '').'>';
	}	

	public function css_sprite() {
		echo '<input type="url" style="width: 500px" name="emkk_settings[css_sprite]" value="'.$this->get_setting('css_sprite').'">
				<br>Hvis forskjellig fra lokal versjon, så må css/js oppdateres.';
	}
	public function css_desktop() {
		echo '<input type="url" style="width: 500px" name="emkk_settings[css_desktop]" value="'.$this->get_setting('css_desktop').'">';
	}

	public function css_mobile() {
		echo '<input type="url" style="width: 500px" name="emkk_settings[css_mobile]" value="'.$this->get_setting('css_mobile').'">';
	}

	// public function css_tablet() {
	// 	echo '<input type="url" style="width: 500px" name="emkk_settings[css_tablet]" value="'.$this->get_setting('css_tablet').'">';
	// }

	/*
		echos the page
	*/
	public function menu_callback() {
		echo '<h1>EM Kredittkort Plugin</h1>';
		echo '<form action="options.php" method="POST">';
		settings_fields('emkk_settings_group');
		do_settings_sections('emkk_settings_page');
		submit_button('save');
		echo '</form>';
	}

	/*
		sanitizing data to be saved
	*/
	public function sanitize($data) {
		if (!is_array($data)) return esc_url($data);

		$d = [];
		foreach($data as $key => $value)
			$d[$key] = esc_url($value);

		return $d;
	}

	/*
		retrieves data from settings array in database
		and sanitizes it for use as html attribute
	*/
	private function get_setting($name) {
		$opt = get_option('emkk_settings');
		if (isset($opt[$name])) return esc_attr($opt[$name]);
	}
}