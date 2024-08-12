<?php
/**
 * Plugin Name: Easy PinThis
 * Description: Allows you to create and manage Pins and Pin Folders with custom taxonomies, meta boxes, REST API endpoints, and user-friendly shortcodes.
 * Version: 1.0.0
 * Author: RafaelBooster⚡
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
