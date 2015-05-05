=== AJAX Post Meta ===
Contributors: kawauso, pdclark
Author URI: http://adamharley.co.uk
Tags: aioseo, ajax, all in one seo, bing, custom field, custom fields, description, edit custom fields, fast, google, greg's high performance seo, headspace2, keywords, meta, meta seo pack, meta tags, metadata, platinum seo, post meta, search engine optimization, seo, seo ultimate, title, wordpress seo, wordpress seo by yoast, yahoo
Requires at least: 3.0
Tested up to: 3.6
Stable tag: 1.0.1

Edit SEO tags (or any custom field) from the All Posts page. Auto-detects SEO plugins & themes.

== Description ==

Allow any plain-text custom field to be edited via AJAX on the *All Posts* page. We use it at [Brainstorm Media](http://brainstormmedia.com) for quickly editing SEO titles, descriptions, and keywords. However, filters are provided for targeting any plain-text custom field.

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

== Screenshots ==

1. Clicking a value or blank area brings up an edit field.

== Upgrade Notice ==

* Add additional filter used on custom content type edit screen. Props @pt1985 http://go.brain.st/RHiiTh

== Changelog ==

= 1.0.1 =
* Add additional filter used on custom content type edit screen. Props @pt1985 http://go.brain.st/RHiiTh

= 1.0 =
* Initial Release
