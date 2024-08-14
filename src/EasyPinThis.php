<?php

namespace EasyPinThis;

class EasyPinThis {

    public function __construct() {
        new EasyPinThisGroups();
        new EasyPinThisTaxonomies();
        new EasyPinThisFolders();
        new EasyPinThisApi();
        new EasyPinThisShortcodes();
        new EasyPinThisSettings();
    }

    public function register_post_type() {
        $labels = [
            'name'                     => _x('Pins', 'Post Type General Name', 'easy-pinthis'),
            'singular_name'            => _x('Pin', 'Post Type Singular Name', 'easy-pinthis'),
            'menu_name'                => __('Pins', 'easy-pinthis'),
            'name_admin_bar'           => __('Pin', 'easy-pinthis'),
            'archives'                 => __('Pin Archives', 'easy-pinthis'),
            'attributes'               => __('Pin Attributes', 'easy-pinthis'),
            'parent_item_colon'        => __('Parent Pin:', 'easy-pinthis'),
            'all_items'                => __('All Pins', 'easy-pinthis'),
            'add_new_item'             => __('Add New Pin', 'easy-pinthis'),
            'add_new'                  => __('Add New', 'easy-pinthis'),
            'new_item'                 => __('New Pin', 'easy-pinthis'),
            'edit_item'                => __('Edit Pin', 'easy-pinthis'),
            'update_item'              => __('Update Pin', 'easy-pinthis'),
            'view_item'                => __('View Pin', 'easy-pinthis'),
            'view_items'               => __('View Pins', 'easy-pinthis'),
            'search_items'             => __('Search Pin', 'easy-pinthis'),
            'not_found'                => __('Not found', 'easy-pinthis'),
            'not_found_in_trash'       => __('Not found in Trash', 'easy-pinthis'),
            'featured_image'           => __('Image for this Pin', 'easy-pinthis'),
            'set_featured_image'       => __('Choose the image', 'easy-pinthis'),
            'remove_featured_image'    => __('Remove featured image', 'easy-pinthis'),
            'use_featured_image'       => __('Use as featured image', 'easy-pinthis'),
            'insert_into_item'         => __('Insert into pin', 'easy-pinthis'),
            'uploaded_to_this_item'    => __('Uploaded to this pin', 'easy-pinthis'),
            'items_list'               => __('Pins list', 'easy-pinthis'),
            'items_list_navigation'    => __('Pins list navigation', 'easy-pinthis'),
            'filter_items_list'        => __('Filter pins list', 'easy-pinthis'),
        ];

        $options = get_option('easy_pin_this_options');
        $slug_pin = isset($options['slug_pin']) ? esc_attr($options['slug_pin']) : 'pin';
        
        register_post_type('ez_pin', [
            'labels' => $labels,
            'public' => true,
            'supports' => ['title', 'thumbnail'],
            'has_archive' => true,
            'rewrite' => [
                'slug' => $slug_pin,
            ],
            'menu_icon' => 'dashicons-format-image',
            'menu_position'      => 45
        ]);
    }

    public function register_metaboxes() {
        add_action('add_meta_boxes', function() {
            add_meta_box(
                'ez_pt_pin_settings',
                __('Settings', 'easy-pinthis'),
                [$this, 'render_pin_metabox'],
                'ez_pin',
                'normal'
            );
        });

        add_action('save_post', [$this, 'save_pin_metabox']);
    }

    public function render_pin_metabox($post) {
        $fields = ['item', 'city', 'brand', 'site'];

        foreach ($fields as $field) {
            $value = get_post_meta($post->ID, 'ez_pt_' . $field, true);
            echo '<p><label for="ez_pt_' . $field . '">' . ucfirst($field) . '</label>';
            echo '<input type="text" id="ez_pt_' . $field . '" name="ez_pt_' . $field . '" value="' . esc_attr($value) . '" class="widefat" /></p>';
        }
    }

    public function save_pin_metabox($post_id) {
        $fields = ['item', 'city', 'brand', 'site'];

        foreach ($fields as $field) {
            if (isset($_POST['ez_pt_' . $field])) {
                update_post_meta($post_id, 'ez_pt_' . $field, sanitize_text_field($_POST['ez_pt_' . $field]));
            }
        }
    }

    public function move_featured_image_meta_box() {
        remove_meta_box('postimagediv', 'ez_pin', 'side');

        add_meta_box(
            'postimagediv',
            __('Image for this Pin', 'easy-pinthis'),
            'post_thumbnail_meta_box',
            'ez_pin',
            'normal',
            'high'
        );
    }
}
