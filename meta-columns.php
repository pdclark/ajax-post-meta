<?php
/*
Plugin Name: Meta Columns
Description: Allow any plain-text custom field to be edited via AJAX on the Post listing page.
Version: 1.0.1
Author: kawauso, pdclark
*/

/**
 * Copyright (c) 2010 Your Name. All rights reserved.
 *
 * Released under the GPL license
 * http://www.opensource.org/licenses/gpl-license.php
 *
 * This is an add-on for WordPress
 * http://wordpress.org/
 *
 * **********************************************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * **********************************************************************
 */
/*
EXAMPLE USAGE

add_filter('meta_columns_post_types', 'my_meta_columns_post_types');
add_filter('meta_columns', 'my_meta_columns');

function my_meta_columns_post_types( $post_types ) {
	// Add list of post types to any previously enabled.
	// To complete override, just return an array
	return wp_parse_args( array(
		'post',
		'page',
	), $post_types );
}

function my_meta_columns($keys) {
	// Array of meta keys to enable editing for
	return wp_parse_args( array(
		// Example AIOSEO meta keys
		'_aioseop_title'       => __('Title Override'),
		'_aioseop_description' => __('Description'),
		'_aioseop_keywords'    => __('Keywords'),
		'_aioseop_titleatr'    => __('Title Attribute'),
		'_aioseop_menulabel'   => __('Menu Label'),
		
		// General syntax
		// 'meta_key'       => __('Column Title'),
	), $keys );
} 
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