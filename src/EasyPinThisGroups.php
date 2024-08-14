<?php

namespace EasyPinThis;

class EasyPinThisGroups {

    public function __construct() {
        $this->register_groups();
        $this->register_metaboxes();
        add_action( 'admin_enqueue_scripts', [$this, 'ez_pin_group_enqueue_scripts'] );
    }

    public function register_groups() {
        $labels = array(
            'name'               => _x('Pin Groups', 'post type general name', 'easy-pinthis'),
            'singular_name'      => _x('Pin Group', 'post type singular name', 'easy-pinthis'),
            'menu_name'          => _x('Pin Groups', 'admin menu', 'easy-pinthis'),
            'name_admin_bar'     => _x('Pin Group', 'add new on admin bar', 'easy-pinthis'),
            'add_new'            => _x('Add New', 'Pin Group', 'easy-pinthis'),
            'add_new_item'       => __('Add New Pin Group', 'easy-pinthis'),
            'new_item'           => __('New Pin Group', 'easy-pinthis'),
            'edit_item'          => __('Edit Pin Group', 'easy-pinthis'),
            'view_item'          => __('View Pin Group', 'easy-pinthis'),
            'all_items'          => __('All Pin Groups', 'easy-pinthis'),
            'search_items'       => __('Search Pin Groups', 'easy-pinthis'),
            'parent_item_colon'  => __('Parent Pin Groups:', 'easy-pinthis'),
            'not_found'          => __('No pin groups found.', 'easy-pinthis'),
            'not_found_in_trash' => __('No pin groups found in Trash.', 'easy-pinthis'),
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'supports'           => array('title'),
            'has_archive'        => true,
            'rewrite'            => ['slug' => 'pin-group'],
            'menu_icon'          => 'dashicons-format-gallery',
            'menu_position'      => 46
        );

        register_post_type('ez_pin_group', $args);
    }

    public function register_metaboxes() {
        add_action('add_meta_boxes', function() {
            add_meta_box(
                'ez_pin_group_settings',
                __('Settings'),
                array($this, 'render_meta_box_content'),
                'ez_pin_group',
                'normal',
                'high'
            );
        });

        add_action('save_post', [$this, 'save_meta_box_data']);
    }

    public function render_meta_box_content($post) {
        wp_nonce_field('ez_pin_group_save_meta_box_data', 'ez_pin_group_meta_box_nonce');
    
        $images = get_post_meta($post->ID, '_ez_pin_group_images', true);
        $item = get_post_meta($post->ID, '_ez_pin_item', true);
        $city = get_post_meta($post->ID, '_ez_pin_city', true);
        $brand = get_post_meta($post->ID, '_ez_pin_brand', true);
        $site = get_post_meta($post->ID, '_ez_pin_site', true);
    
        echo '<div style="margin-bottom: 8px;">';
        echo '<label for="ez_pin_group_images">';
        _e('Upload Images', 'easy-pinthis');
        echo '</label>';
        echo '<input type="hidden" id="ez_pin_group_images" name="ez_pin_group_images" value="' . esc_attr($images) . '" />';
        echo '</div>';
        echo '<button type="button" class="button" id="upload_images_button">' . __('Upload Images', 'easy-pinthis') . '</button>';
        echo '<div id="image_preview" style="margin-top: 16px;">';
        if ($images) {
            $image_ids = explode(',', $images);
            foreach ($image_ids as $image_id) {
                $img_url = wp_get_attachment_url($image_id);
                echo '<img src="' . esc_url($img_url) . '" style="width: 100px; height: auto; margin-right: 10px;" />';
            }
        }
        echo '</div>';
    
        $fields = ['item', 'city', 'brand', 'site'];

        foreach ($fields as $field) {
            $value = get_post_meta($post->ID, 'ez_pt_' . $field, true);
            echo '<p><label for="ez_pt_' . $field . '">' . ucfirst($field) . '</label>';
            echo '<input type="text" id="ez_pt_' . $field . '" name="ez_pt_' . $field . '" value="' . esc_attr($value) . '" class="widefat" /></p>';
        }
    }

    public function save_meta_box_data($post_id) {
        if (!isset($_POST['ez_pin_group_meta_box_nonce']) || !wp_verify_nonce($_POST['ez_pin_group_meta_box_nonce'], 'ez_pin_group_save_meta_box_data')) {
            return;
        }
    
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
    
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
    
        if (get_post_type($post_id) !== 'ez_pin_group') {
            return;
        }
    
        if (isset($_POST['ez_pin_group_images'])) {
            $images = sanitize_text_field($_POST['ez_pin_group_images']);
            update_post_meta($post_id, '_ez_pin_group_images', $images);
    
            $image_ids = explode(',', $images);
    
            $post_taxonomies = get_object_taxonomies('ez_pin_group');
            $terms = array();
            foreach ($post_taxonomies as $taxonomy) {
                $terms[$taxonomy] = wp_get_object_terms($post_id, $taxonomy, array('fields' => 'ids'));
            }
    
            $pin_group_title = get_the_title($post_id);

            $fields = ['item', 'city', 'brand', 'site'];

            foreach ($fields as $field) {
                if (isset($_POST['ez_pt_' . $field])) {
                    update_post_meta($post_id, 'ez_pt_' . $field, sanitize_text_field($_POST['ez_pt_' . $field]));
                }
            }
    
            foreach ($image_ids as $index => $image_id) {
                $existing_post = get_posts([
                    'meta_key' => '_ez_pin_image_id',
                    'meta_value' => $image_id,
                    'post_type' => 'ez_pin',
                    'post_status' => 'publish',
                    'numberposts' => 1,
                ]);
    
                if (!empty($existing_post)) {
                    continue;
                }
    
                $new_post = array(
                    'post_title'   => $pin_group_title . ' ' . ($index + 1),
                    'post_status'  => 'publish',
                    'post_type'    => 'ez_pin',
                );
    
                remove_action('save_post', [$this, 'save_meta_box_data']);
    
                $post_id_new = wp_insert_post($new_post);
    
                add_action('save_post', [$this, 'save_meta_box_data']);
    
                foreach ($terms as $taxonomy => $term_ids) {
                    if (!empty($term_ids)) {
                        wp_set_object_terms($post_id_new, $term_ids, $taxonomy);
                    }
                }
    
                update_post_meta($post_id_new, '_ez_pin_image_id', $image_id);
    
                set_post_thumbnail($post_id_new, $image_id);

                $fields = ['item', 'city', 'brand', 'site'];

                foreach ($fields as $field) {
                    if (isset($_POST['ez_pt_' . $field])) {
                        update_post_meta($post_id_new, 'ez_pt_' . $field, sanitize_text_field($_POST['ez_pt_' . $field]));
                    }
                }
            }
        }
    }

    public function ez_pin_group_enqueue_scripts($hook) {
        if ($hook !== 'post.php' && $hook !== 'post-new.php') {
            return;
        }
        wp_enqueue_media();
        wp_enqueue_script('ez-pin-group-script', plugin_dir_url(__FILE__) . '/js/ez-pin-group.js', array('jquery'), null, true);
    }
}
