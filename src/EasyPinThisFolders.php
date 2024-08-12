<?php

namespace EasyPinThis;

class EasyPinThisFolders {

    public function __construct() {
        $this->register_post_type();
        $this->register_metaboxes();
    }

    private function register_post_type() {
        $labels = [
            'name'                     => _x('Pin Folders', 'Post Type General Name', 'easy-pinthis'),
            'singular_name'            => _x('Pin Folder', 'Post Type Singular Name', 'easy-pinthis'),
            'menu_name'                => __('Pin Folders', 'easy-pinthis'),
            'name_admin_bar'           => __('Pin Folder', 'easy-pinthis'),
            'archives'                 => __('Pin Folder Archives', 'easy-pinthis'),
            'attributes'               => __('Pin Folder Attributes', 'easy-pinthis'),
            'parent_item_colon'        => __('Parent Pin Folder:', 'easy-pinthis'),
            'all_items'                => __('All Pin Folders', 'easy-pinthis'),
            'add_new_item'             => __('Add New Pin Folder', 'easy-pinthis'),
            'add_new'                  => __('Add New', 'easy-pinthis'),
            'new_item'                 => __('New Pin Folder', 'easy-pinthis'),
            'edit_item'                => __('Edit Pin Folder', 'easy-pinthis'),
            'update_item'              => __('Update Pin Folder', 'easy-pinthis'),
            'view_item'                => __('View Pin Folder', 'easy-pinthis'),
            'view_items'               => __('View Pin Folders', 'easy-pinthis'),
            'search_items'             => __('Search Pin Folder', 'easy-pinthis'),
            'not_found'                => __('Not found', 'easy-pinthis'),
            'not_found_in_trash'       => __('Not found in Trash', 'easy-pinthis'),
            'featured_image'           => __('Featured Image', 'easy-pinthis'),
            'set_featured_image'       => __('Set featured image', 'easy-pinthis'),
            'remove_featured_image'    => __('Remove featured image', 'easy-pinthis'),
            'use_featured_image'       => __('Use as featured image', 'easy-pinthis'),
            'insert_into_item'         => __('Insert into Pin Folder', 'easy-pinthis'),
            'uploaded_to_this_item'    => __('Uploaded to this Pin Folder', 'easy-pinthis'),
            'items_list'               => __('Pin Folders list', 'easy-pinthis'),
            'items_list_navigation'    => __('Pin Folders list navigation', 'easy-pinthis'),
            'filter_items_list'        => __('Filter Pin Folders list', 'easy-pinthis'),
        ];

        $options = get_option('easy_pin_this_options');
        $slug_pin_folder = isset($options['slug_pin_folder']) ? esc_attr($options['slug_pin_folder']) : '_quick_saves';

        register_post_type('ez_pin_folder', [
            'labels' => $labels,
            'public' => true,
            'supports' => ['title', 'thumbnail'],
            'has_archive' => true,
            'rewrite' => [
                'slug' => $slug_pin_folder,
            ],
            'menu_icon' => 'dashicons-portfolio',
        ]);
    }

    private function register_metaboxes() {
        add_action('add_meta_boxes', function() {
            add_meta_box(
                'ez_pt_folder_settings',
                __('Settings', 'easy-pinthis'),
                [$this, 'render_folder_metabox'],
                'ez_pin_folder',
                'normal'
            );
        });

        add_action('save_post', [$this, 'save_folder_metabox']);
    }

    public function render_folder_metabox($post) {
        $pins = get_posts(['post_type' => 'ez_pin', 'posts_per_page' => -1]);
        $selected_pins = get_post_meta($post->ID, 'pin_ids', true) ?: [];
        $selected_pins = is_array($selected_pins) ? $selected_pins : [];

        foreach ($pins as $pin) {
            $checked = in_array($pin->ID, $selected_pins) ? 'checked' : '';
            $thumb = get_the_post_thumbnail($pin->ID, [150, 150]);

            echo '<p><label>';
            echo '<input type="checkbox" name="pin_ids[]" value="' . $pin->ID . '" ' . $checked . ' />';
            echo $thumb;
            echo '</label></p>';
        }
    }

    public function save_folder_metabox($post_id) {
        if (isset($_POST['pin_ids'])) {
            update_post_meta($post_id, 'pin_ids', array_map('intval', $_POST['pin_ids']));
        }
    }
}
