<?php
/**
 * Plugin Name: Easy PinThis by NeFi
 * Description: Allows you to create and manage Pins and Pin Folders with custom taxonomies, meta boxes, REST API endpoints, and user-friendly shortcodes.
 * Version: 1.4.0
 * Author: NeFi
 * Text Domain: easy-pinthis
 * Domain Path: /languages
 */

defined('ABSPATH') || exit;

require_once __DIR__ . '/vendor/autoload.php';

function ez_pt_run() {
    $ept = new EasyPinThis\EasyPinThis();

    $ept->register_post_type();
    $ept->register_metaboxes();
    add_action('add_meta_boxes', [$ept, 'move_featured_image_meta_box']);
}

add_action('init', 'ez_pt_run');

function ez_pt_load_textdomain() {
    load_plugin_textdomain('easy-pinthis', false, dirname(plugin_basename(__FILE__)) . '/languages');
}

add_action('plugins_loaded', 'ez_pt_load_textdomain');
