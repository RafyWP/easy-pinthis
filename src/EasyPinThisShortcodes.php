<?php

namespace EasyPinThis;

class EasyPinThisShortcodes {

    public function __construct() {
        add_shortcode('my-pins', [$this, 'my_pins_shortcode']);
        add_shortcode('list-pins', [$this, 'list_pins_shortcode']);
        add_shortcode('add-pin', [$this, 'add_pin_shortcode']);
        add_shortcode('create-folder', [$this, 'create_folder_shortcode']);
    }

    public function my_pins_shortcode($atts) {
        $args = [
            'post_type' => 'ez_pin_folder',
            'author' => get_current_user_id(),
            'posts_per_page' => -1
        ];
    
        $folders = new \WP_Query($args);
    
        if (!$folders->have_posts()) {
            return __('No pins found.', 'easy-pinthis');
        }
    
        $output = '<style>
            .pin-list-item {
                width: 300px;
                margin: 0 auto;
                list-style-type: none;
            }
            .pin-list-item img {
                width: 100%;
                height: auto;
            }
            .pin-this {
                display: block;
                margin-top: 10px;
            }
        </style>';
    
        $output .= '<ul>';
        while ($folders->have_posts()) {
            $folders->the_post();
            $post_id = get_the_ID();
            $post_title = get_the_title();
            //$post_permalink = get_the_permalink();
            $pin_ids = get_post_meta($post_id, 'pin_ids', true);
            
            //$options = get_option('easy_pin_this_options');
            //$default_image = isset($options['default_image']) ? '<img src="'. esc_attr($options['default_image']) .'" width="300" height="300" />' : '';
            //$thumbnail = has_post_thumbnail($post_id) ? get_the_post_thumbnail($post_id, [300, 300]) : $default_image;
    
            $output .= '<li class="pin-list-item">';
            //$output .= $thumbnail;
            $output .= '<ul>';
            foreach ($pin_ids as $pin_id) {
                $thumb = get_the_post_thumbnail($pin_id, 'medium_large');
                $output .= '<li class="pin-list-item" pin-id="' . esc_attr($pin_id) . '">';
                $output .= $thumb;
                $output .= $post_title;
                $output .= '</li>';
            }
            $output .= '</ul>';
            $output .= '</li>';
        }
        $output .= '</ul>';
    
        wp_reset_postdata();
    
        return $output;
    }

    public function list_pins_shortcode($atts) {
        if (!is_singular('ez_pin_folder')) {
            return '';
        }

        $post_id = get_the_ID();
        $pin_ids = get_post_meta($post_id, 'pin_ids', true);

        if (!$pin_ids) {
            return __('No pins in this folder.', 'easy-pinthis');
        }

        $output = '<style>
            .pin-list-item {
                width: 300px;
                margin: 0 auto;
                list-style-type: none;
            }
            .pin-list-item img {
                width: 100%;
                height: auto;
            }
            .pin-this {
                display: block;
                margin-top: 10px;
            }
        </style>';

        $output .= '<ul>';
        foreach ($pin_ids as $pin_id) {
            $thumb = get_the_post_thumbnail($pin_id, 'medium_large');
            $output .= '<li class="pin-list-item">';
            $output .= $thumb;
            $output .= '<button class="pin-this" pin-id="' . esc_attr($pin_id) . '">Remove</button>';
            $output .= '</li>';
        }
        $output .= '</ul>';

        return $output;
    }

    public function add_pin_shortcode($atts) {
        $args = [
            'post_type' => 'ez_pin_folder',
            'author' => get_current_user_id(),
            'posts_per_page' => -1
        ];
    
        $folders = new \WP_Query($args);
    
        if (!$folders->have_posts()) {
            return __('No pins found.', 'easy-pinthis');
        }
    
        $output = '<style>
            .folder {
                padding: 0 10px;
                list-style: none;
            }
            .folder .item {
                display: flex;
                gap: 2rem;
                align-items: center;
                cursor: pointer;
                border-radius: 12px;
                margin-bottom: 12px;
            }
            .folder .item:hover {
                background-color: #EDEDED;
            }
            .folder .item img {
                border-radius: 12px;
            }
        </style>';
    
        $output .= '<input type="hidden" id="pin_id" />';
        $output .= '<ul class="folder">';
        while ($folders->have_posts()) {
            $folders->the_post();
            $folder_id = get_the_ID();
            $folder_title = get_the_title();
            
            $options = get_option('easy_pin_this_options');
            $default_image = isset($options['default_image']) ? '<img src="'. esc_attr($options['default_image']) .'" width="60" height="60" />' : '';
            $thumbnail = has_post_thumbnail($folder_id) ? get_the_post_thumbnail($folder_id, [60,60]) : $default_image;
    
            $output .= '<li class="item" folder-id="' . esc_attr($folder_id) . '">';
            $output .= $thumbnail;
            $output .= $folder_title;
            $output .= '</li>';
        }
        $output .= '</ul>';
    
        wp_reset_postdata();
    
        return $output;
    }

    public function create_folder_shortcode($atts) {
        $output = '<style>
        .create-folder { display: flex; gap: .5rem; flex-direction: column; }
        .btns { display: flex; justify-content: space-between; }
        </style>';

        $output .= '<div class="create-folder">';
        $output .= '<label for="title">Nome</label>';
    
        $output .= '<input type="text" id="title" name="title" placeholder="Por exemplo: Inspirações ou Tendências" />';

        $output .= '<div class="btns">';
        $output .= '<input type="button" id="cancel" value="Cancelar" />';
        $output .= '<input type="submit" id="create-folder" value="Criar" />';
        $output .= '</div>';
        $output .= '</div>';
    
        return $output;
    }
}
