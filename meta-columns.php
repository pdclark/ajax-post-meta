<?php
/*
Plugin Name: Meta Columns
*/

class Meta_Columns {
	
	static function setup_columns() {
		$defaults = array('post');
		$post_types = apply_filters('meta_columns_post_types', $defaults);
		
		foreach( (array)$post_types as $post_type ) {
			add_filter("manage_{$post_type}_posts_columns", 'Meta_Columns::add_columns');
			add_filter("manage_edit-{$post_type}_sortable_columns", 'Meta_Columns::sortable_columns');
		}
		
		add_action('manage_posts_custom_column', 'Meta_Columns::display_column', 10, 2);
		if(in_array('page', $post_types))
			add_action('manage_pages_custom_column', 'Meta_Columns::display_column', 10, 2);
	}
	
	static function add_columns($columns) { // Add columns
		$defaults = array(
//			'meta_key' => __('Name')
		);
		$new = apply_filters('meta_columns', $defaults);
		
		foreach((array)$new as $column => $column_nicename)
			$columns["m_$column"] = $column_nicename;
		
		return $columns;
	}
	
	static function sortable_columns($columns) { // Choose sortable columns (WP 3.1 and higher)
		$defaults = array(
//			'meta_key'
		);
		
		$sortable = apply_filters('meta_columns_sortable', $defaults);
		
		foreach((array)$sortable as $column)
			$columns["m_$column"] = "m_$column";
		
		return $columns;
	}
	
	static function enqueue_scripts($hook_suffix) { // Load scripts
		if($hook_suffix == 'edit.php') {
			$strings = array( 'ok' => __('OK'), 'cancel' => __('Cancel') );
			wp_register_script('meta-columns', plugin_dir_url(__FILE__).'js/save.js', array('jquery'), null);
			wp_localize_script('meta-columns', 'metaColumnsL10n', $strings);
			wp_enqueue_script('meta-columns');
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
		foreach($meta_values as $meta_value) {
			$js = "metaColumnEdit(this,{post_id:$post_id,meta_key:'$meta_key'";
			if(count($meta_values) != 1)
				$js .= ',prev_value_hash:\'' . md5($meta_value) . '\'';
			$js .= '})';
			if($i)
				echo '<br />';
			echo "<span onclick=\"$js\">$meta_value</span>";
			$i++;
		}
	}
	
	static function update() { // AJAX save
		if( empty($_POST['post_id']) || empty($_POST['meta_key']) || empty($_POST['meta_value']) ) // Values check
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
	add_action('init', 'Meta_Columns::setup_columns');
	add_action('admin_enqueue_scripts', 'Meta_Columns::enqueue_scripts');
	add_action('parse_query', 'Meta_Columns::parse_query');
	add_action('wp_ajax_update_meta_column', 'Meta_Columns::update');
}