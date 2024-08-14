<?php

namespace EasyPinThis;

class EasyPinThisShortcodes {

    public function __construct() {
        add_shortcode('my-pins', [$this, 'my_pins_shortcode']);
        add_shortcode('list-pins', [$this, 'list_pins_shortcode']);
        add_shortcode('add-pin', [$this, 'add_pin_shortcode']);
        add_shortcode('create-folder', [$this, 'create_folder_shortcode']);
        add_shortcode('swiper-nefi', [$this, 'swiper_nefi_shortcode']);
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
    
        $output = '';
    
        $output .= '<div id="folders-list">';
        $output .= '<div class="folders-list">';
        $output .= '<h4>Salvar</h4>';
        $output .= '<input type="hidden" id="pin_id" />';
        $output .= '<div id="folders-search">';
        $output .= '<input type="text" placeholder="Busque uma pasta" />';
        $output .= '</div>';
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
        $output .= '<div id="folders-add">';
        $output .= '<button class="folder-create">Criar nova pasta</button>';
        $output .= '</div>';
        $output .= '</div>';
        $output .= '</div>';
    
        wp_reset_postdata();
    
        return $output;
    }

    public function create_folder_shortcode($atts) {
        $output = '';

        $output .= '<div id="create-folder-wraper">';
        $output .= '<div class="create-folder wrapper">';
        $output .= '<h4>Criar pasta</h4>';
        $output .= '<label for="title">Nome</label>';
    
        $output .= '<input type="text" id="title" name="title" placeholder="Por exemplo: Inspirações ou Tendências" />';

        $output .= '<div class="buttons btns">';
        $output .= '<button id="cancel">Cancelar</button>';
        $output .= '<button id="create-folder">Criar</button>';
        $output .= '</div>';
        $output .= '</div>';
        $output .= '</div>';
    
        return $output;
    }

    public function swiper_nefi_shortcode($atts) {
        $output = '<div id="nefi">';
    
        $current_taxonomy = get_queried_object()->taxonomy;
        $current_term = get_queried_object()->slug;
    
        $args = array(
            'post_type' => 'ez_pin',
            'tax_query' => array(
                array(
                    'taxonomy' => $current_taxonomy,
                    'field'    => 'slug',
                    'terms'    => $current_term,
                ),
            ),
        );
    
        $query = new \WP_Query($args);
    
        if ($query->have_posts()) {
            $output .= '<div class="swiper neFi">';
            $output .= '<div class="swiper-wrapper">';
    
            while ($query->have_posts()) {
                $query->the_post();
    
                $output .= '<div class="swiper-slide">';
                $output .= '<div class="wrapper">';
    
                $output .= '<div>';
                if (has_post_thumbnail()) {
                    $output .= get_the_post_thumbnail(get_the_ID(), [350,0]);
                }
                $output .= '</div>';
    
                $output .= '<div class="meta">';
                $output .= '<h4>' . get_the_title() . '</h4>';
    
                $output .= '<div class="season">';
                $terms = get_the_terms(get_the_ID(), 'ez_pt_season');
                if ($terms && !is_wp_error($terms)) {
                    foreach ($terms as $term) {
                        $output .= 'TEMPORADA: ' . esc_html($term->name);
                    }
                }
                $output .= '</div>';

                $output .= '<div class="department">';
                $terms = get_the_terms(get_the_ID(), 'ez_pt_department');
                if ($terms && !is_wp_error($terms)) {
                    foreach ($terms as $term) {
                        $output .= 'DEPARTAMENTO: ' . esc_html($term->name);
                    }
                }
                $output .= '</div>';

                $output .= '<div class="category">';
                $terms = get_the_terms(get_the_ID(), 'ez_pt_category');
                if ($terms && !is_wp_error($terms)) {
                    $output .= 'CATEGORIA: ';
                    foreach ($terms as $term) {
                        $output .= esc_html($term->name);
                    }
                }
                $output .= '</div>';

                $output .= '<div class="item">';
                $output .= 'ITEM: ' . get_post_meta(get_the_ID(), 'ez_pt_item', true);
                $output .= '</div>';

                $output .= '<div class="city">';
                $output .= 'CIDADE: ' . get_post_meta(get_the_ID(), 'ez_pt_city', true);
                $output .= '</div>';

                $output .= '<div class="brand">';
                $output .= 'MARCA: ' . get_post_meta(get_the_ID(), 'ez_pt_brand', true);
                $output .= '</div>';

                $output .= '<div class="site">';
                $output .= 'SITE: ' . get_post_meta(get_the_ID(), 'ez_pt_site', true);
                $output .= '</div>';

                $output .= '<div class="buttons">';
                $output .= '<button class="open-folders" pin-id="'. get_the_ID() .'">Salvar</button>';
                $output .= '<button class="excluir" folder-id="" pin-id="'. get_the_ID() .'">Excluir</button>';
                //$output .= '<button class="down-pin">Baixar</button>';
                $output .= '</div>';

                $output .= '</div>';
                $output .= '</div>';
                $output .= '</div>';
            }
    
            $output .= '</div>';
            $output .= '<div class="swiper-button-next"></div>';
            $output .= '<div class="swiper-button-prev"></div>';
            $output .= '</div>';
            $output .= '</div>';
    
            wp_reset_postdata();
        }
    
        return $output;
    }
}
