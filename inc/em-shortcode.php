<?php

defined( 'ABSPATH' ) or die( 'Blank Space' );

require_once 'em-taxonomy.php';

/*
	FRONT-END STUFF
*/
final class Emkk_shortcode {
	/* singleton */
	private static $instance = null;

	// if js is added to html (which adds css)
	private $added_js = false;

	// internal resources
	// private $sprite = EM_PLUGIN_URL.'assets/img/spritesheet_1.jpg';
	private $sprite = EM_PLUGIN_URL.'assets/img/css_sprites-min.png';
	// private $sprite = '';
	private $desktop = EM_PLUGIN_URL.'assets/css/emkk_style.css?v=2.0.0';
	private $mobile = EM_PLUGIN_URL.'assets/css/emkk_style_mobile.css?v=2.0.0';

	public static function get_instance() {
		if (self::$instance === null) self::$instance = new self();

		return self::$instance;
	}

	private function __construct() {
		// giving access to taxonomy
		Emkk_taxonomy::get_instance();
	
		// setting external resources
		$res = get_option('emkk_settings');
		if (! isset($res['css_disable'])) {
			if (isset($res['css_sprite']) && $res['css_sprite'] != '') $this->sprite = $res['css_sprite'];
			if (isset($res['css_desktop']) && $res['css_desktop'] != '') $this->desktop = $res['css_desktop'];
			if (isset($res['css_mobile']) && $res['css_mobile'] != '') $this->mobile = $res['css_mobile'];
		}

        add_filter('pre_get_posts', array($this, 'set_search'), 99);
        // add_filter('add_google_fonts', array($this, 'add_google_fonts'), 99);

		// [emkort name="xx"] [emkort name="xx,yy"] [emkort kort="abc"] 
		add_shortcode('emkort', array($this, 'shortcode'));

		// [embestill name=""]
		add_shortcode('embestill', array($this, 'shortcode_bestill'));
		
		// [embilde name=""]
		add_shortcode('embilde', array($this, 'shortcode_bilde'));

		// filter for theme search
		add_action('emkort_shortcode', array($this, 'emkort_shortcode'));

		// adding preload links
		// add_action('wp_head', array($this, 'head'));
	}

	/*
		adds emkort custom post to search
	*/
	public function set_search($query) {
        if ($query->is_search) {
	        if (!$query->get('post_type')) $query->set('post_type', array('page', 'post', 'emkort'));
    	    else $query->set('post_type', array_merge(array('emkort'), $query->get('post_type')));
		}
	}

	/*
		adds preload sprite to head
	*/
	public function head() {
		// echo '<link rel="preload" href="'.esc_url($this->sprite).'" as="image">';
		echo '<link rel="preload" href="'.esc_url($this->desktop).'" as="style">';
		echo '<link rel="preload" href="'.esc_url($this->mobile).'" as="style">';

		// echo '<style>.em-sprite { background-image: url("'.esc_html($this->sprite).'") }</style>';
	}

	/*
		adds css by script 
	*/
	public function footer() {
		echo '<script defer>
				(function() {
					var o = document.createElement("link");
					o.setAttribute("rel", "stylesheet");
					o.setAttribute("href", "'.esc_html($this->desktop).'");
					o.setAttribute("media", "(min-width: 1025px)");
					document.head.appendChild(o);

					var m = document.createElement("link");
					m.setAttribute("rel", "stylesheet");
					m.setAttribute("href", "'.esc_html($this->mobile).'");
					m.setAttribute("media", "(max-width: 1024px)");
					document.head.appendChild(m);

				})();
			  </script>';
	}

	/*
		[emkort] shortcode
	*/
	public function shortcode($atts, $content = null) {
		$this->add_css();

		$kort = null;
		if (isset($atts['kort'])) 		$kort = $atts['kort'];
		if (!isset($atts['name']))		return $this->do_loop(null, $kort);
		elseif (isset($atts['name']))	return $this->do_loop(explode(',', str_replace(' ', '', $atts['name'])), $kort);
	
	}

	public function shortcode_bestill($atts, $content = null) {
		$this->add_css();

		// if name attr is not set
		if (! isset($atts['name'])) return;

		// getting meta ('ignore' is the tax to ignore)
		$meta = $this->get_meta($atts['name'], 'em_sokna', 'ignore');

		if ($meta) return '<div class="emkort-sokna emkort-sokna-bestill"><a target="_blank" class="emkort-lenke emkort-sokna-lenke" href="'.esc_url($meta).'">Bestill Kortet</a></div>';
	}

	public function shortcode_bilde($atts, $content = null) {
		$this->add_css();

		// if no name given
		if (! isset($atts['name'])) return;

		// getting meta
		$meta = $this->get_meta($atts['name'], 'em_sprite');
		
		// setting alignment
		$align = ' emkort-thumbnail-center';
		if (isset ($atts[0])) 	switch ($atts[0]) { // first unnamed parameter from shortcode
									case 'left': $align = ' emkort-thumbnail-left'; break;
									case 'none': $align = ''; break;
									case 'right': $align = ' emkort-thumbnail-right';
								}

		// returning html
		// if ($meta) 	return '<div class="emkort-thumbnail'.$align.' em-sprite sprite-'.esc_attr($meta).'"></div>'; // sprite
		// else 		{																					// wordpress db
		$post = get_posts([
			'name'        => $atts['name'],
			'post_type'   => 'emkort',
			'post_status' => 'publish',
			'numberposts' => 1
		]);

		$thumbnail = get_the_post_thumbnail_url($post[0], 'full');
		if ($thumbnail) return '<div class="emkort-thumbnail'.$align.'"><img class="emkort-thumbnail-image" src="'.esc_url($thumbnail).'"></div>';
					// }
	}

	/*
		wp query
	*/
	private function do_loop($name = null, $kort = null) {
		// print_r($kort);

		// for query
		$args = [
			'post_type' => 'emkort',
			'posts_per_page' => -1,
			'orderby' => array(
				'meta_value_num' => 'ASC',
				'title' => 'ASC'
			),
			'meta_key' => 'emkort_sort'
		];

		// if [emkort kort="xx"]
		if ($kort)
			$args['tax_query'] = array(
					array(
						'taxonomy' => 'korttype',
						'field' => 'slug',
						'terms' => esc_html($kort)
					)
				);

		// if $name is not null
		if (is_array($name)) $args['post_name__in'] = $name; 

		$query = new WP_Query($args);

		// sorting posts as they are listed in [emkort name=""]
		if (is_array($name)) {
			$posts = [];

			foreach ($name as $n)
				foreach($query->posts as $p) 
					if ($n == $p->post_name) array_push($posts, $p);

			$query->posts = $posts; 
		}

		global $post;

		// container element
		$html = '<div class="emkort-kortliste" style="opacity: 0">';

		if ($query->have_posts()) 
			while ($query->have_posts()) {
				$query->the_post();

				$logo = [];

				// to ignore "ignore" and/or "duplicate" taxonomies
				$terms = wp_get_post_terms($post->ID, 'korttype');
				$ignore = false;
				foreach($terms as $term) {
					if ($term->slug == 'ignore') 		$ignore = true; // ignore all with ignore tag
					elseif ($term->slug == 'duplicate' 
						&& !$name && !$kort) 			$ignore = true; // ignore all with duplicate tag and name/kort att not used
				
					elseif ($term->slug == 'visa'
						|| $term->slug == 'mastercard') array_push($logo, $term->slug);
				}

				if ($ignore) continue;

				// getting the meta
				$meta = get_post_meta($post->ID, 'em_data');

				if (isset($meta[0])) 	$meta = $meta[0];
				else 					continue; // if no meta, then no card

				$meta['logo'] = $logo;

				// adding the meta
				$html .= $this->make_kredittkort($meta);

			}

		wp_reset_postdata();

		$html .= '</div>';
		return $html;	
	}

	/*
		returns the html version of kredittkort
		TODO visa/mastercard logo
	*/
	private function make_kredittkort($meta = null) {
		global $post;

		if (!$meta) return '';

		if (isset($meta['em_title']) && $meta['em_title']) 	$title = $meta['em_title'];
		else 												$title = get_the_title();

		$thumbnail = get_the_post_thumbnail_url($post, 'full');
		$lesmer = isset($meta['em_lesmer']) ? $meta['em_lesmer'] : '';
		$infoEn = isset($meta['em_info'][0]) ? $meta['em_info'][0] : '';
		$infoTo = isset($meta['em_info'][1]) ? $meta['em_info'][1] : '';
		$infoTre = isset($meta['em_info'][2]) ? $meta['em_info'][2] : '';
		$blurb = isset($meta['em_blurb']) ? $meta['em_blurb'] : '';
		$age = isset($meta['em_aldersgrense']) ? $meta['em_aldersgrense'] : '';
		$ageOw = isset($meta['em_alderow']) ? $meta['em_alderow'] : '';
		$mkreditt = isset($meta['em_makskreditt']) ? $meta['em_makskreditt'] : '';
		$rfkreditt = isset($meta['em_rentefrikreditt']) ? $meta['em_rentefrikreditt'] : '';
		$sokna = isset($meta['em_sokna']) ? $meta['em_sokna'] : '';
		$effrente = isset($meta['em_effrente']) ? $meta['em_effrente'] : '';
		$sprite = isset($meta['em_sprite']) ? $meta['em_sprite'] : '';

		$html = '<div class="emkort-container">'; // add class here

		// add check for mobile here?
		for ($i = 1; $i <= 6; $i++) $html .= '<div class="emkort-sep emkort-sep-'.$i.'"></div>';
			
		$html .= '<div class="emkort-title"><h2 class="emkort-title-header"><a class="emkort-title-text" href="'.$this->filter_bb(esc_url($lesmer)).'">'.esc_html($title).'</a></h2></div>';
	
		// if ($sprite) 		$html .= '<div class="emkort-thumbnail em-sprite sprite-'.esc_attr($sprite).'"></div>';
		if ($thumbnail) $html .= '<div class="emkort-thumbnail"><img class="emkort-thumbnail-image" src="'.$this->filter_bb(esc_url($thumbnail)).'"></div>';
		else 			$html .= '<div class="emkort-thumbnail"></div>';

		
		if ($infoEn) $html .= '<div class="emkort-info-0 emkort-info">'.$this->filter_bb(esc_html($infoEn)).'</div>';

		if ($infoTo) $html .= '<div class="emkort-info-1 emkort-info">'.$this->filter_bb(esc_html($infoTo)).'</div>';

		if ($infoTre) $html .= '<div class="emkort-info-2 emkort-info">'.$this->filter_bb(esc_html($infoTre)).'</div>';

		$html .= '<div class="emkort-sokna"><a class="emkort-lenke emkort-sokna-lenke" href="'.esc_url($sokna).'">Bestill Kortet</a></div>';
		$html .= '<div class="emkort-lesmer"><a class="emkort-lenke emkort-lesmer-lenke" href="'.$this->filter_bb(esc_url($lesmer)).'">Les Mer</a></div>';
		
		if ($ageOw)	$html .= '<div class="emkort-alderow">'.$this->filter_bb(esc_html($ageOw)).'</div>';
		else $html .= '<div class="emkort-aldersgrense">'.$this->filter_bb(esc_html($age)).'</div>';

		$html .= '<div class="emkort-makskreditt">'.$this->filter_bb(esc_html($mkreditt)).'</div>';
		
		$html .= '<div class="emkort-rentefrikreditt">'.$this->filter_bb(esc_html($rfkreditt)).'</div>';
		
		
		
		$html .= '<div class="emkort-effrente">'.$this->filter_bb(esc_html($effrente)).'</div>';

		// blurb and logos
		$html .= '<div class="emkort-blurb"><div class="emkort-blurb-text">'.$this->filter_bb(esc_html($blurb)).'</div>';


		$terms = wp_get_post_terms($post->ID, 'korttype');

		$html .= '<div class="emkort-logo-container">';
		foreach($terms as $term) {
			if ($term->slug == 'visa')
				$html .= '<img class="emkort-logo" src="'.EM_PLUGIN_URL.'assets/img/visa_logo.png">';
				// $html .= '<span class="em-sprite sprite-visa_logo"></span>';
			elseif ($term->slug == 'mastercard')
				$html .= '<img class="emkort-logo" src="'.EM_PLUGIN_URL.'assets/img/mastercard_logo.png">';
				// $html .= '<span class="em-sprite sprite-mastercard_logo"></span>';
		}
		$html .= '</div>';

		// $html .= '<div class="emkort-logo">'.print_r($terms, true).'</div>';

		$html .= '</div>';
		
		$html .= '</div>';

		return $html;
	}

	/*
		bb-code in meta
	*/
	private function filter_bb($text) {
		$data = $text;

		$data = str_replace('[b]', '<span class="emkort-fat">', $data);
		$data = str_replace('[/b]', '</span>', $data);
		$data = str_replace('[br]', '<br>', $data);
		$data = str_replace('[mbr]', $this->mobile ? '<br>' : '', $data);

		return $data;
	}

	/*
		helper function for adding css
		adds javascript to footer that adds css files to header
	*/
	private function add_css() {
		if (! $this->added_js) {
			add_action('wp_head', array($this, 'head'));

			add_action('wp_footer', array($this, 'footer'));
			$this->add_js = true;
		}
	}

	/*
		@slug slug-name of emkort
		@meta_name name in em_data[]
		@ignore_check matching taxonomy of emkort
	*/
	private function get_meta($slug, $meta_name, $ignore_check = null) {

		// getting post
		$post = get_posts([
			'name'        => $slug,
			'post_type'   => 'emkort',
			'post_status' => 'publish',
			'numberposts' => 1
		]);

		// if no post found
		if (! $post) return false;

		// fix return value
		$post = $post[0];
		
		// if to be ignored
		if ($ignore_check) {
			$terms = wp_get_post_terms($post->ID, 'korttype');

			foreach($terms as $t) 
				if ($t->slug == $ignore_check) return false;
		}

		// getting meta
		$meta = get_post_meta($post->ID, 'em_data');
		if (! isset($meta[0][$meta_name])) return false;

		// returning meta
		return $meta[0][$meta_name];
	}

	/*
		hook for search page
	*/
	public function emkort_shortcode($post_id) {
		add_action('wp_footer', array($this, 'footer'));

		$meta = get_post_meta($post_id, 'em_data');

		if (isset($meta[0])) echo $this->make_kredittkort($meta[0]); 
	}
}