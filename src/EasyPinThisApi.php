<?php

namespace EasyPinThis;

class EasyPinThisApi {

    public function __construct() {
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    public function register_routes() {
        register_rest_route('easy-pinthis/v1', '/create-folder/', [
            'methods' => 'POST',
            'callback' => [$this, 'create_folder'],
            'permission_callback' => function () {
                return current_user_can('edit_posts');
            }
        ]);

        register_rest_route('easy-pinthis/v1', '/update-folder/', [
            'methods' => 'POST',
            'callback' => [$this, 'update_folder'],
            'permission_callback' => function () {
                return current_user_can('edit_posts');
            }
        ]);

        register_rest_route('easy-pinthis/v1', '/remove-pin-from-folder/', [
            'methods' => 'POST',
            'callback' => [$this, 'remove_pin_from_folder'],
            'permission_callback' => function () {
                return current_user_can('edit_posts');
            }
        ]);
    }

    public function create_folder($request) {
        $title = sanitize_text_field($request['title']);

        if (!$title) {
            return new \WP_Error('no_title', __('No title provided', 'easy-pinthis'), ['status' => 400]);
        }

        $folder_id = wp_insert_post([
            'post_title' => $title,
            'post_type' => 'ez_pin_folder',
            'post_status' => 'publish'
        ]);

        if (is_wp_error($folder_id)) {
            return $folder_id;
        }

        return rest_ensure_response(['folder_id' => $folder_id]);
    }

    public function update_folder($request) {
        $folder_id = intval($request['folder_id']);
        $pin_id = intval($request['pin_id']);

        if (!$folder_id || !$pin_id) {
            return new \WP_Error('missing_ids', __('Missing folder or pin ID', 'easy-pinthis'), ['status' => 400]);
        }

        $pin_ids = get_post_meta($folder_id, 'pin_ids', true) ?: [];
        $pin_ids = is_array($pin_ids) ? $pin_ids : [];

        if (!in_array($pin_id, $pin_ids)) {
            $pin_ids[] = $pin_id;
        }

        update_post_meta($folder_id, 'pin_ids', $pin_ids);

        return rest_ensure_response(['success' => true]);
    }

    public function remove_pin_from_folder($request) {
        $folder_id = intval($request['folder_id']);
        $pin_id = intval($request['pin_id']);
    
        if (!$folder_id || !$pin_id) {
            return new \WP_Error('missing_ids', __('Missing folder or pin ID', 'easy-pinthis'), ['status' => 400]);
        }
    
        $pin_ids = get_post_meta($folder_id, 'pin_ids', true) ?: [];
        $pin_ids = is_array($pin_ids) ? $pin_ids : [];
    
        if (in_array($pin_id, $pin_ids)) {
            $pin_ids = array_diff($pin_ids, [$pin_id]);
        }
    
        update_post_meta($folder_id, 'pin_ids', $pin_ids);
    
        return rest_ensure_response(['success' => true]);
    }
    
}
