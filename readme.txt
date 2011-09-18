=== AJAX Post Meta ===
Author: Brainstorm Media
Author URI: http://brainstormmedia.com
Contributors: brainstormmedia, kawauso, pdclark
Plugin URI: 
Donate link: 
Tags: edit custom fields, edit posts, edit, custom field, custom fields, ajax, post meta, meta, aioseo, keywords, search engine optimization, seo, title
Requires at least: 3.0
Tested up to: 3.1.2
Stable tag: 1.0

Edit SEO tags on the All Posts page. Auto-detects SEO plugins & themes. Supports any other plain-text custom fields.

== Description ==

Allow any plain-text custom field to be edited via AJAX on the *All Posts* page. We use it at [Brainstorm Media](http://brainstormmedia.com) for quickly editing SEO titles, descriptions, and keywords, but any plain-text custom field can be targeted.

AJAX Post Meta will auto-detect and add columns for:

= Plugins =
* All in One SEO Pack
* Greg's High Performance SEO
* Headspace2
* Meta SEO Pack
* Platinum SEO
* SEO Ultimate
* WordPress SEO

= Themes =
* Builder
* Headway
* Hybrid
* Thesis

== Installation ==

1. Upload the folder `ajax-post-meta` into the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. If your plugin or theme is supported, edit columns will be automatically added to your *All Posts* page.

**Advanced**: You may add support for other custom fields and post types using the following filters in your theme's `functions.php` file:

`function my_ajax_meta_post_types( $post_types ) {
	// Add list of post types to any previously enabled.
	// To complete override, just return an array
	return wp_parse_args( array(
		'post',
		'page',
	), $post_types );
}
add_filter('ajax_meta_post_types', 'my_ajax_meta_post_types');

function my_ajax_meta_keys($keys) {
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
add_filter('ajax_meta_keys', 'my_ajax_meta_keys');`

== Changelog ==

= 1.0 =
* Initial Release

== License ==

Copyright 2010 Brainstorm Media - Released under the GNU General Public License
 - [License details](http://www.gnu.org/licenses/gpl.html)