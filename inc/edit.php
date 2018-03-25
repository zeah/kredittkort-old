<?php 

defined('ABSPATH') or die('Blank Space');


final class Emkk_edit {
	/* singleton */
	private static $instance = null;

	public static function get_instance() {
		if (self::$instance === null)
			self::$instance = new self();

		return self::$instance;
	}

	private function __construct() {
		/* adding javascript to emkort list screen */
		add_action( 'admin_enqueue_scripts', array($this, 'enqueue_script') );

		add_action('manage_emkort_posts_columns', array($this, 'column_head'));
		add_filter('manage_emkort_posts_custom_column', array($this, 'custom_column'));
		add_filter('manage_edit-emkort_sortable_columns', array($this, 'sort_column'));
		
		/* metabox, javascript */
		add_action('add_meta_boxes_emkort', array($this, 'create_meta'));

		/* hook for page saving/updating */
		add_action('save_post', array($this, 'save'));
	}

	/*
		enqueuing when screen is on emkort list 
	*/
	public function enqueue_script() {
		$screen = get_current_screen();

		if ($screen->id == 'edit-emkort') {
			wp_enqueue_script('emkk_kredittkort_admin', PLUGIN_URL . '/assets/js/emkk_kredittkort_admin.js', array(), false, true);
			wp_enqueue_style('emkk_kredittkort_admin_style', PLUGIN_URL . '/assets/css/emkk_kredittkort_admin.css', array(), false);
		}
	}

	public function column_head($defaults) {
		$defaults['emkort_sort'] = 'Sorting Order';
		$defaults['make_list'] = 'Make List';
		return $defaults;
	}

	public function custom_column($column_name) {
		global $post;

		if ($column_name == 'emkort_sort') {
			$meta = get_post_meta($post->ID, 'emkort_sort');

			if (isset($meta[0]))
				echo $meta[0];
		}

		if ($column_name == 'make_list')
			echo '<button type="button" class="emkort-button button" data="'.$post->post_name.'">Add</button>';
	}

	public function sort_column($columns) {
		$columns['emkort_sort'] = 'emkort_sort';
		return $columns;
	}

	/*
		creates wordpress metabox
		adds javascript
	*/
	public function create_meta() {
		/* kredittkort info meta */
		add_meta_box(
			'emkort_meta', // name
			'Kredittkort Info', // title 
			array($this,'create_meta_box'), // callback
			'emkort' // page
		);

		/* css sprite metabox */
		// dropdown of images to use

		/* structured data info */
		// name of bank etc

		// javascript for creating content in metaboxes
		wp_enqueue_script('emkk_emkort_meta', PLUGIN_URL . '/assets/js/emkk_emkort_meta.js', array(), false, true);
		wp_enqueue_style('emkk_meta_style', PLUGIN_URL . '/assets/css/emkk_meta_style.css', array(), false);
	}

	/*
		creates content in metabox
	*/
	public function create_meta_box($post) {
		wp_nonce_field('em'.basename(__FILE__), 'em_nonce');

		// $tax = wp_get_post_terms($post->ID, 'korttype');
		$meta = get_post_meta($post->ID, 'em_data');
		$sort = get_post_meta($post->ID, 'emkort_sort');
		$json = [
			'meta' => isset($meta[0]) ? $this->sanitize($meta[0]) : '',
			// 'tax' => isset($tax[0]) ? $tax : '',
			// 'sort' => $this->get_sort($post)
			'sort' => isset($sort) ? $this->sanitize($sort) : ''
		];


		wp_localize_script( 'emkk_emkort_meta', 'emkk_meta', json_decode( json_encode( $json ), true) );
		echo '<div class="emkort-meta-container"></div>';
	}

	/*
		helper function
		returns meta values from sort . taxonomy
		@returns array [sort_taxonomy] => [value]
	*/
	private function get_sort($post) {
		// retrieving taxonomy
		$tax = wp_get_post_terms($post->ID, 'korttype');
		
		// getting meta
		$sort = [];
		foreach($tax as $value) {
			$meta = get_post_meta($post->ID, 'emkort_sort_'.str_replace(' ', '_', $value->name));
			$sort[$value->name] = isset($meta[0]) ? $meta[0] : '';
		}

		// getting non-taxonomy meta
		$meta = get_post_meta($post->ID, 'emkort_sort');
		$sort['default'] = isset($meta[0]) ? $meta[0] : '';

		return $sort;
	}

	public function save($post_id) {
		// post type is emkort
		if (!get_post_type($post_id) == 'emkort') return;

		// is on admin screen
		if (!is_admin()) return;

		// user is logged in and has permission
		if (!current_user_can('edit_posts')) return;

		// nonce is sent
		if (!isset($_POST['em_nonce'])) return;

		// nonce is checked
		if (!wp_verify_nonce($_POST['em_nonce'], 'em'.basename(__FILE__))) return;

		// data is sent, then sanitized and saved
		if (isset($_POST['emdata'])) update_post_meta($post_id, 'em_data', $this->sanitize($_POST['emdata']));
	}

	/*
		recursive sanitizer
	*/
	private function sanitize($data) {
		if (!is_array($data))
			return sanitize_text_field($data);

		$d = [];
		foreach($data as $key => $value)
			$d[$key] = $this->sanitize($value);

		return $d;
	}
}