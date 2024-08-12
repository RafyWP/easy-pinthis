<?php

namespace EasyPinThis;

class EasyPinThisShortcodes {

    public function __construct() {
        add_shortcode('my-pins', [$this, 'my_pins_shortcode']);
        add_shortcode('list-pins', [$this, 'list_pins_shortcode']);
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
    
        // Adiciona o CSS diretamente no shortcode
        $output = '<style>
            .pin-list-item {
                width: 300px;
                margin: 0 auto; /* Centraliza o item se necessário */
                list-style-type: none; /* Remove os marcadores padrão */
            }
            .pin-list-item img {
                width: 100%; /* Ajusta a imagem para ocupar a largura máxima do item */
                height: auto; /* Mantém a proporção da imagem */
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
            $post_permalink = get_the_permalink();
            
            $options = get_option('easy_pin_this_options');
            $default_image = isset($options['default_image']) ? '<img src="'. esc_attr($options['default_image']) .'" width="300" height="300" />' : '';
            $thumbnail = has_post_thumbnail($post_id) ? get_the_post_thumbnail($post_id, [300, 300]) : $default_image;
    
            $output .= '<li class="pin-list-item">';
            $output .= '<a href="'. $post_permalink .'">';
            $output .= $thumbnail;
            $output .= $post_title;
            $output .= '</a>';
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
                margin: 0 auto; /* Centraliza o item se necessário */
                list-style-type: none; /* Remove os marcadores padrão */
            }
            .pin-list-item img {
                width: 100%; /* Ajusta a imagem para ocupar a largura máxima do item */
                height: auto; /* Mantém a proporção da imagem */
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
}
