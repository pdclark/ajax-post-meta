<?php
/*
Plugin Name: AJAX Post Meta
Description: Allow any plain-text custom field to be edited via AJAX on the All Posts page. Auto-detects SEO plugins.
Version: 1.0.1
Author: Adam Harley, Paul Clark
Author URI: http://adamharley.co.uk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

class StormAJAXPostMeta {
	
	static function setup_columns() {
		$defaults = array('post');
		$post_types = apply_filters('ajax_meta_post_types', $defaults);
		
		foreach( (array)$post_types as $post_type ) {
			add_filter("manage_{$post_type}_posts_columns", 'StormAJAXPostMeta::add_columns');
			add_filter("manage_edit-{$post_type}_columns", 'StormAJAXPostMeta::add_columns');

			add_filter("manage_edit-{$post_type}_sortable_columns", 'StormAJAXPostMeta::sortable_columns');
		}
		
		add_action('manage_posts_custom_column', 'StormAJAXPostMeta::display_column', 10, 2);
		if(in_array('page', $post_types))
			add_action('manage_pages_custom_column', 'StormAJAXPostMeta::display_column', 10, 2);
	}
	
	static function add_columns($columns) {
		$defaults = array(
			//'meta_key' => __('Name'),
		);
		$new = apply_filters('ajax_meta_keys', $defaults);

		if ( empty( $new )) {
			$new = StormAJAXPostMeta::detect_platform_meta();
		}
		
		foreach((array)$new as $column_nicename => $column)
			$columns["m_$column"] = $column_nicename;
		
		return $columns;
	}

	static function detect_platform_meta() {
		$platform = false;

		// Plugins - Alphabetical, prioritized over themes
		     if ( class_exists('All_in_One_SEO_Pack') )     $platform = 'All in One SEO Pack';
		else if ( class_exists('gregsHighPerformanceSEO') ) $platform = 'Greg\'s High Performance SEO';
		else if ( class_exists('HeadSpace2_Admin') )        $platform = 'Headspace2';
		else if ( class_exists('MetaSeoPack') )             $platform = 'Meta SEO Pack';
		else if ( class_exists('Platinum_SEO_Pack') )       $platform = 'Platinum SEO';
		else if ( class_exists('SEO_Ultimate') )            $platform = 'SEO Ultimate';
		else if ( class_exists('WPSEO_Admin') )             $platform = 'WordPress SEO';
		// Themes - Alphabetical
		else if ( class_exists('ITCoreClass') )             $platform = 'Builder';
		// else if ( class_exists('') )             $platform = 'Catalyst';
		// else if ( class_exists('') )             $platform = 'Frugal';
		// else if ( class_exists('') )             $platform = 'Genesis';
		else if ( class_exists('HeadwaySkin') )             $platform = 'Headway';
		else if ( class_exists('Hybrid') )                  $platform = 'Hybrid';
		else if ( class_exists('thesis_site_options') )     $platform = 'Thesis';
		// else if ( class_exists('') )             $platform = 'WooFramework';

		if ( $platform ) {
			return StormAJAXPostMeta::get_platform_meta( $platform );
		}else {
			return array();
		}

	}

	static function get_platform_meta( $key ) {
		$platforms = array(
			// Plugins
			// alphabatized
			'All in One SEO Pack' => array(
				'Title' => '_aioseop_title',
				'Description' => '_aioseop_description',
				'Keywords' => '_aioseop_keywords',
			),
			'Greg\'s High Performance SEO' => array(
				'Title' => '_ghpseo_secondary_title',
				'Description' => '_ghpseo_alternative_description',
				'Keywords' => '_ghpseo_keywords',
			),
			'Headspace2' => array(
				'Title' => '_headspace_page_title',
				'Description' => '_headspace_description',
				'Keywords' => '_headspace_keywords',
				// 'Custom Scripts' => '_headspace_scripts'
			),
			'Meta SEO Pack' => array(
				'Description' => '_msp_description',
				'Keywords' => '_msp_keywords',
			),
			'Platinum SEO' => array(
				'Title' => 'title',
				'Description' => 'description',
				'Keywords' => 'keywords',
			),
			'SEO Ultimate' => array(
				'Title' => '_su_title',
				'Description' => '_su_description',
				'Keywords' => '_su_keywords',
				// 'noindex' => '_su_meta_robots_noindex',
				// 'nofollow' => '_su_meta_robots_nofollow'
			),
			'WordPress SEO' => array(
				'Title' => '_yoast_wpseo_title',
				'Description' => '_yoast_wpseo_metadesc',
				'Keywords' => '_yoast_wpseo_metakeywords',
				// 'noindex' => '_yoast_wpseo_meta-robots-noindex',
				// 'nofollow' => '_yoast_wpseo_meta-robots-nofollow',
				// 'Canonical URI' => '_yoast_wpseo_canonical',
				// 'Redirect URI' => '_yoast_wpseo_redirect'
			),
			// Themes
			'Builder' => array(
				'Title' => '_builder_seo_title',
				'Description' => '_builder_seo_description',
				'Keywords' => '_builder_seo_keywords',
			),
			'Catalyst' => array(
				'Title' => '_catalyst_title',
				'Description' => '_catalyst_description',
				'Keywords' => '_catalyst_keywords',
				// 'noindex' => '_catalyst_noindex',
				// 'nofollow' => '_catalyst_nofollow',
				// 'noarchive' => '_catalyst_noarchive'
			),
			'Frugal' => array(
				'Title' => '_title',
				'Description' => '_description',
				'Keywords' => '_keywords',
				// 'noindex' => '_noindex',
				// 'nofollow' => '_nofollow'	
			),
			'Genesis' => array(
				'Title' => '_genesis_title',
				'Description' => '_genesis_description',
				'Keywords' => '_genesis_keywords',
				// 'noindex' => '_genesis_noindex',
				// 'nofollow' => '_genesis_nofollow',
				// 'noarchive' => '_genesis_noarchive',
				// 'Canonical URI' => '_genesis_canonical_uri',
				// 'Custom Scripts' => '_genesis_scripts',
				// 'Redirect URI' => 'redirect'
			),
			'Headway' => array(
				'Title' => '_title',
				'Description' => '_description',
				'Keywords' => '_keywords'
			),
			'Hybrid' => array(
				'Title' => 'Title',
				'Description' => 'Description',
				'Keywords' => 'Keywords'
			),
			'Thesis' => array(
				'Title' => 'thesis_title',
				'Description' => 'thesis_description',
				'Keywords' => 'thesis_keywords',
				// 'Custom Scripts' => 'thesis_javascript_scripts',
				// 'Redirect URI' => 'thesis_redirect',
			),
			'WooFramework' => array(
				'Title' => 'seo_title',
				'Description' => 'seo_description',
				'Keywords' => 'seo_keywords'
			)
		);

		if ( array_key_exists($key, $platforms) ) {
			return $platforms[$key];
		}else {
			return false;
		}
	}
	
	static function sortable_columns($columns) { // Choose sortable columns (WP 3.1 and higher)
		$defaults = array(
			// 'meta_key',
		);
		
		$sortable = apply_filters('ajax_meta_sortable', $defaults);
		
		foreach((array)$sortable as $column)
			$columns["m_$column"] = "m_$column";
		
		return $columns;
	}
	
	static function enqueue_scripts($hook_suffix) { // Load scripts
		if($hook_suffix == 'edit.php') {
			$strings = array( 'ok' => __('OK'), 'cancel' => __('Cancel') );
			wp_register_script('ajax-meta', plugin_dir_url(__FILE__).'js/save.js', array('jquery'), null);
			wp_localize_script('ajax-meta', 'ajaxMetaL10n', $strings);
			wp_enqueue_script('ajax-meta');
		}
	}
	
	static function parse_query($query) { // Modify the query to sort by our meta keys
		if( empty($query->query_vars['orderby']) || substr($query->query_vars['orderby'],0,2) != 'm_' )
			return;
		
		$query->query_vars['meta_value'] = substr($query->query_vars['orderby'],2);
		$query->query_vars['orderby'] = 'meta_value';
	}
	
	static function display_column($column, $post_id) { // Display column value
		if( substr($column,0,2) != 'm_')
			return;
		
		$meta_key = substr($column,2);
		$meta_values = get_post_meta($post_id, $meta_key);
		
		$i=0;
		do {
			$js = "metaColumnEdit(this,{post_id:$post_id,meta_key:'$meta_key'";
			if(count($meta_values) > 1)
				$js .= ',prev_value_hash:\'' . md5($meta_values[$i]) . '\'';
			$js .= '})';
			echo "<div style='min-height:18px;' onclick=\"$js\">$meta_values[$i]</div>";
			$i++;
		} while ( isset( $meta_values[$i] ) );
	}
	
	static function update() { // AJAX save
		if( empty($_POST['post_id']) || empty($_POST['meta_key']) || !isset($_POST['meta_value']) ) // Values check
			die('-1');
		
		$post_id = (int) $_POST['post_id'];
		$meta_key = $_POST['meta_key'];
		$meta_value = $_POST['meta_value'];
		
		if( !($post_type = get_post_type($post_id)) || !($post_type_object = get_post_type_object($post_type)) || !current_user_can($post_type_object->cap->edit_post, $post_id) ) // Post type and permissions check
			die('-1');
		
		if( empty($_POST['prev_value_hash']) ) { // Single meta key/value pair
			if( update_post_meta($post_id, $meta_key, $meta_value) )
				die('1');
			else
				die('-1');
		}
		else { // If there is more than one value for a given meta key, things get interesting...
			$prev_value_hash = $_POST['prev_value_hash'];
			$meta_values = get_post_meta($post_id, $meta_key);
			
			foreach($meta_values as $prev_value) {
				if($prev_value_hash == md5($prev_value)) { // Find the matching meta value to replace
					if( update_post_meta($post_id, $meta_key, $meta_value, $prev_value) )
						die(md5($meta_value)); // Return the new hash
					else
						die('-1');
				}
			}
			
			die('-1');
		}
	}
	
}

if(is_admin()) {
	add_action('init', 'StormAJAXPostMeta::setup_columns');
	add_action('admin_enqueue_scripts', 'StormAJAXPostMeta::enqueue_scripts');
	add_action('parse_query', 'StormAJAXPostMeta::parse_query');
	add_action('wp_ajax_update_meta_column', 'StormAJAXPostMeta::update');
}
