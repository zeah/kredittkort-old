<?php 

defined( 'ABSPATH' ) or die( 'Blank Space' );

require_once 'em-taxonomy.php';
require_once 'em-edit.php';

/*
	REGISTERING CUSTOM POST TYPE
*/
final class Emkk_posttype {
	/* singleton */
	private static $instance = null;

	public static function get_instance() {
		if (self::$instance === null)
			self::$instance = new self();

		return self::$instance;
	}

	private function __construct() {
		/* creates taxonomy */
		Emkk_taxonomy::get_instance();

		/* page edit screen */
		Emkk_edit::get_instance();

		/* creates custom post type: emkort */
		add_action('init', array($this, 'create_cpt'));
	}

	/*
		create custom post type: emkort 
	*/
	public function create_cpt() {
		$plur = 'Kredittkort';
		$sing = 'Kredittkort';
	
		$labels = array(
			'name'               => __( $plur, 'text-domain' ),
			'singular_name'      => __( $sing, 'text-domain' ),
			'add_new'            => _x( 'Add New '.$sing, 'text-domain', 'text-domain' ),
			'add_new_item'       => __( 'Add New '.$sing, 'text-domain' ),
			'edit_item'          => __( 'Edit '.$sing, 'text-domain' ),
			'new_item'           => __( 'New '.$sing, 'text-domain' ),
			'view_item'          => __( 'View '.$sing, 'text-domain' ),
			'search_items'       => __( 'Search '.$plur, 'text-domain' ),
			'not_found'          => __( 'No '.$plur.' found', 'text-domain' ),
			'not_found_in_trash' => __( 'No '.$plur.' found in Trash', 'text-domain' ),
			'parent_item_colon'  => __( 'Parent '.$sing.':', 'text-domain' ),
			'menu_name'          => __( $plur, 'text-domain' ),
		);
	
		$args = array(
			'labels'              => $labels,
			'hierarchical'        => false,
			'description'         => 'description',
			'taxonomies'          => array('korttype'),
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_admin_bar'   => true,
			'menu_position'       => null,
			'menu_icon' => 'dashicons-id',
			'show_in_nav_menus'   => false,
			'publicly_queryable'  => false,
			'exclude_from_search' => false,
			'has_archive'         => false,
			'query_var'           => true,
			'can_export'          => true,
			'rewrite'             => false,
			'capability_type'     => 'post',
			'supports'            => array(
				'title',
				'thumbnail',
			),
		);

		register_post_type('emkort', $args);
	}
}