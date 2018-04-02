<?php 
defined( 'ABSPATH' ) or die( 'Blank Space' );

final class Emkk_info {
	/* SINGLETON */
	private static $instance = null;

	public static function get_instance() {
		if (self::$instance === null)
			self::$instance = new self();

		return self::$instance;
	}

	private function __construct() {
		add_action('admin_menu', array($this, 'add_menu'));
		add_action('admin_enqueue_scripts', array($this, 'enqueue'));
	}

	public function add_menu() {
		add_submenu_page('edit.php?post_type=emkort', 'Info', 'Info', 'manage_options', 'emkort_info', array($this, 'menu_callback'));
	}

	public function enqueue() {
		$screen = get_current_screen();

		if ($screen->id == 'emkort_page_emkort_info') {
			wp_enqueue_style('emkk_admin_info', PLUGIN_URL . '/assets/css/emkk_admin_info.css', array(), false);
		}
	}

	public function menu_callback() {

		$html = '<div class="emkort-info-container">';
		$html .= '<h1>EM Kredittkort Plugin</h1>';
		$html .= '<h2>Shortcode</h2>';

		$html .= '<p><strong>[emkort]</strong> viser alle kort bortsett fra de som "ignore" eller "duplicate" som kategori.</p>';
		$html .= '<p><strong>[emkort name="xx,yy,etc"]</strong> viser kortene med slug-navnene xx, yy og etc i den rekkefølgen de er oppgitt i.</p>';
		$html .= '<p><strong>[emkort kort="abc"]</strong> viser alle kort i "abc" kategorien bortsett fra de som har "ignore."</p>';
		$html .= '<p><strong>[embestill name="xx"]</strong> viser kun bestillknappen fra kortet "xx."</p>';
		$html .= '<p><strong>[embilde name="yy"]</strong> viser kun bilde fra kortet "yy."<br>[embilde <strong>left</strong> name="yy"] floater bildet til venstre.<br>[embilde <strong>right</strong> name="yy"] floater bildet til høyre.<br>[embilde <strong>none</strong> name="yy"] vil vise bildet til venstre i ett block element.<br>[embilde name="yy"] viser bildet sentrert i ett block element.<br></p>';
		
		$html .= '<h3>Veiledning</h3><ul>';
		$html .= '<li class="emkort-info-li">Kort som har "ignore" kategori vil aldri bli vist i<br>[emkort], [emkort kort=""], [emkort name=""] og [embestill name=""]</li>';
		$html .= '<li class="emkort-info-li">Kort som har "duplicate" kategori vil aldri bli vist i [emkort]<br>Tips: Hvis man vil ha forskjellig tekst på "samme" kredittkort, så kan man duplisere kortet og bruke det i [emkort name="xx, yy"] eller i [emkort kort="abc"] der det kopien av kortet har kategori "abc", men ikke orginalen.</li>';
		$html .= '<li class="emkort-info-li">Bruk "add" funksjonen på kredittkort-oversiktsiden til å lage type [emkort name=""] shortcode.<br>(Hvis listen av kredittkort er på mer enn en side, så kan man skifte hvor mangen som blir vist.)</li>';
		$html .= '</ul>';

		$html .= '<h3>Copy/Paste Feature</h3>';
		$html .= '<p>Når man klikker "Copy" så vil all dataen fra "Kredittkort Info" boksen bli kopiert til windows clipboard. Wordpress-title, kategorier og Featured Image/CSS Sprite vil ikke bli kopiert.</p>';

		$html .= '<p>For å lime inn, så bruker man "Paste"-knappen. Den vil åpne et felt som man kan lime inn fra windows clipboard (ctrl+v) og feltene vil automatisk bli fylt ut.';

		$html .= '<h3>BB-Code</h3>';
		$html .= '<strong>[b]</strong>tekst<strong>[/b]</strong> for bold tekst.<br>';
		$html .= '<strong>[br]</strong> for linjeskift.<br>';
		$html .= '<strong>[mbr]</strong> for linjeskift kun på mobil.<br>';

		$html .= '</div>';

		echo $html;
	}
}