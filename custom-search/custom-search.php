<?php
/*
Plugin Name: Custom Search Plugin
Description: Custom search functionality with shortcode.
Version: 0.1
Author: Nazar Mykhalus
*/

// Ensure WordPress has been loaded
if (!defined('ABSPATH')) {
	exit;
}

// Include the class files
require_once plugin_dir_path(__FILE__) . 'includes/class-shortcode.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-search.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-display-settings.php';

// Initialize the plugin
function custom_search_plugin_init() {
	$shortcode = new CustomSearch\Shortcode();
	$search = new CustomSearch\Search();
	$displaySettings = new CustomSearch\DisplaySettings();

	// Register hooks and filters
	add_shortcode('custom_search', array($shortcode, 'handle_shortcode'));
}

add_action('init', 'custom_search_plugin_init');
