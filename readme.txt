=== Meta Columns ===
Author: Kawauso (Adam Harley)
Author URI: 
Contributors: Kawauso, pdclark
Plugin URI: 
Donate link: 
Tags: edit custom fields, edit posts, edit, custom field, custom fields, ajax, post meta, meta, aioseo, keywords, search engine optimization, seo, title
Requires at least: 3.0
Tested up to: 3.1
Stable tag: 1.0.1

Add AJAX editing for arbitrary post meta.

== Description ==

Allow any plain-text custom field to be edited via AJAX on the Post listing page. One example might be quickly editing All in One SEO Pack settings:

`add_filter('meta_columns_post_types', 'my_meta_columns_post_types');
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
}`

== Installation ==

= Basic installation of Meta Columns is simple: =

1. Upload the folder `meta-columns` into the `/wp-content/plugins/` directory

2. Activate the plugin through the 'Plugins' menu in WordPress

3. Add the example filter code to your theme's functions.php, adding or removing for any custom fields you would like to be editable:

`add_filter('meta_columns_post_types', 'my_meta_columns_post_types');
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
}`

== Changelog ==

= 1.0.1 =
* Initial Release

== License ==

Copyright 2010 Adam Harley - Released under the  GNU General Public License
 - [License details](http://www.gnu.org/licenses/gpl.html)