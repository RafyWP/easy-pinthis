<?php

namespace EasyPinThis;

class EasyPinThisShortcodes {

    public function __construct() {
        add_shortcode('my-pins', [$this, 'my_pins_shortcode']);
        add_shortcode('list-pins', [$this, 'list_pins_shortcode']);
        add_shortcode('add-pin', [$this, 'add_pin_shortcode']);
        add_shortcode('create-folder', [$this, 'create_folder_shortcode']);
        add_shortcode('swiper-nefi', [$this, 'swiper_nefi_shortcode']);
        add_shortcode('tax-filter', [$this, 'tax_filter']);

        add_action('pre_get_posts', [$this, 'filter_tax']);
    }

    public function my_pins_shortcode($atts) {
        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
        $folder_id = isset($_GET['folder_id']) ? intval($_GET['folder_id']) : null;
    
        $args = [
            'post_type' => 'ez_pin_folder',
            'author' => get_current_user_id(),
            'posts_per_page' => 5,
            'paged' => $paged,
        ];
    
        if ($folder_id) {
            $args['p'] = $folder_id;
        }
    
        $folders = new \WP_Query($args);
    
        if (!$folders->have_posts()) {
            return __('No pins found.', 'easy-pinthis');
        }
    
        $output = '';

        $output .= '<div id="pin-list">';
        $output .= $this->select_my_pins();
        if ( $folder_id ) {
            $output .= '<ul class="pin-list">';
        
            while ($folders->have_posts()) {
                $folders->the_post();
                $post_id = get_the_ID();
                $post_title = get_the_title();
        
                $pin_ids = get_post_meta($post_id, 'pin_ids', true);
        
                if (is_array($pin_ids)) {
                    foreach ($pin_ids as $pin_id) {
                        $thumb = get_the_post_thumbnail($pin_id, 'medium_large');
                        $output .= '<li class="pin-list-item pin-click" pin-id="' . esc_attr($pin_id) . '">';
                        $output .= $thumb;
                        $output .= esc_html($post_title);
                        $output .= '</li>';
                    }
                }
            }
            $output .= '</ul>';
        }
    
        $output .= '<div class="pagination">';
        $output .= paginate_links([
            'total' => $folders->max_num_pages,
            'current' => $paged
        ]);
        $output .= '</div>';
    
        wp_reset_postdata();
    
        return $output;
    }

    public function select_my_pins() {
        $folder_id = isset($_GET['folder_id']) ? intval($_GET['folder_id']) : null;

        $select_args = [
            'post_type' => 'ez_pin_folder',
            'author' => get_current_user_id(),
            'posts_per_page' => -1,
            'fields' => 'ids',
        ];
    
        $select_folders = new \WP_Query($select_args);
        $output = '<div class="select">';
    
        if ($select_folders->have_posts()) {
            $output .= '<select id="folder-select">';
            $output .= '<option value="' . esc_url(get_permalink(get_queried_object_id())) . '"' . selected($folder_id, null, false) . '>-- selecione --</option>';
            while ($select_folders->have_posts()) {
                $select_folders->the_post();
                $post_id = get_the_ID();
                $post_title = get_the_title();

                $permalink = add_query_arg('folder_id', $post_id, get_permalink(get_queried_object_id()));
                $output .= '<option value="' . esc_url($permalink) . '"' . selected($folder_id, $post_id, false) . '>' . esc_html($post_title) . '</option>';
            }
            $output .= '</select>';
        }
        if ( $folder_id ) {
            //$output .= '<button id="excluir">Excluir</button>';
        }
        $output .= '</div>';
    
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
    
        $output = '';
    
        $output .= '<div id="folders-list">';
        $output .= '<div class="folders-list">';
        $output .= '<h4>Salvar</h4>';
        $output .= '<input type="hidden" id="pin_id" />';
        if (!$folders->have_posts()) {
            $output .= 'Não há pastas criadas.';
        } else {
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
        
                $output .= '<li class="item" folder-name="' . esc_attr($folder_title) . '" folder-id="' . esc_attr($folder_id) . '">';
                $output .= $thumbnail;
                $output .= $folder_title;
                $output .= '</li>';
            }
            $output .= '</ul>';
        }
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
    
        $current_taxonomy = get_queried_object()->taxonomy ?? null;
        $current_term = get_queried_object()->slug ?? null;

        $folder_id = isset($_GET['folder_id']) ? intval($_GET['folder_id']) : null;

        $args = array(
            'post_type' => 'ez_pin',
        );

        if ($folder_id) {
            $pin_ids = get_post_meta($folder_id, 'pin_ids', true);

            if (!empty($pin_ids) && is_array($pin_ids)) {
                $args['post__in'] = $pin_ids;
            }
        }

        if ($current_taxonomy && $current_term) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => $current_taxonomy,
                    'field'    => 'slug',
                    'terms'    => $current_term,
                ),
            );
        }

    
        $query = new \WP_Query($args);
    
        if ($query->have_posts()) {
            $output .= '<div class="swiper neFi">';
            $output .= '<div class="swiper-wrapper">';
    
            while ($query->have_posts()) {
                $query->the_post();
    
                $output .= '<div pin-id="'. get_the_ID() .'" class="swiper-slide">';
                $output .= '<div class="wrapper">';
    
                $output .= '<div>';
                if (has_post_thumbnail()) {
                    $output .= get_the_post_thumbnail(get_the_ID(), [350,0]);
                }
                $output .= '</div>';
    
                $output .= '<div id="meta-'. get_the_ID() .'" class="meta">';
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
                $pin_ids = $folder_id ? get_post_meta( $folder_id, 'pin_ids', true ) : [];
                $folder_ids = get_post_meta( get_the_ID(), 'ez_pt_folder_id', true );

                $display_excluir = in_array( get_the_ID(), $pin_ids ) ? 'display-excluir' : '';
                if ( $display_excluir ) {
                    $output .= '<button class="excluir" folder-id="'. $folder_id .'" pin-id="'. get_the_ID() .'">Excluir</button>';
                    $output .= '</div>';
                } elseif ( $folder_ids ) {
                    $output .= '</div>';
                    $folders_arr = explode(',', $folder_ids);
                    $output .= '<div class="varios">';
                    foreach ( $folders_arr as $fd ) {
                        $fold = get_post($fd);
                        $output .= '<div class="pilula"><div class="tags">' . $fold->post_title . '</div><button class="excluir peq" folder-id="'. $fd .'" pin-id="'. get_the_ID() .'">&times;</button></div>';
                    }
                    $output .= '</div>';
                } else {
                    $output .= '</div>';
                }

                //$output .= '<button class="down-pin">Baixar</button>';

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

    public function tax_filter() {
        ob_start();
        ?>
        <form method="get" action="">
            <div class="filter-group">
                <div class="season-filter">
                    <label for="filter_season">Temporada:</label>
                    <select name="filter_season" id="filter_season">
                        <option value="">Todas as Temporadas</option>
                        <?php
                        $current_season = isset($_GET['filter_season']) ? sanitize_text_field($_GET['filter_season']) : '';
                        $seasons = get_terms(['taxonomy' => 'ez_pt_season', 'hide_empty' => false]);
                        if ($seasons && !is_wp_error($seasons)) {
                            foreach ($seasons as $season) {
                                $selected = $current_season == $season->slug ? 'selected' : '';
                                echo '<option value="' . esc_attr($season->slug) . '" ' . $selected . '>' . esc_html($season->name) . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
    
                <div class="department-filter">
                    <label for="filter_department">Departamento:</label>
                    <select name="filter_department" id="filter_department">
                        <option value="">Todos os Departamentos</option>
                        <?php
                        $current_department = isset($_GET['filter_department']) ? sanitize_text_field($_GET['filter_department']) : '';
                        $departments = get_terms(['taxonomy' => 'ez_pt_department', 'hide_empty' => false]);
                        if ($departments && !is_wp_error($departments)) {
                            foreach ($departments as $department) {
                                $selected = $current_department == $department->slug ? 'selected' : '';
                                echo '<option value="' . esc_attr($department->slug) . '" ' . $selected . '>' . esc_html($department->name) . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
    
                <div class="category-filter">
                    <label for="filter_category">Categoria:</label>
                    <select name="filter_category" id="filter_category">
                        <option value="">Todas as Categorias</option>
                        <?php
                        $current_category = isset($_GET['filter_category']) ? sanitize_text_field($_GET['filter_category']) : '';
                        $categories = get_terms(['taxonomy' => 'ez_pt_category', 'hide_empty' => false]);
                        if ($categories && !is_wp_error($categories)) {
                            foreach ($categories as $category) {
                                $selected = $current_category == $category->slug ? 'selected' : '';
                                echo '<option value="' . esc_attr($category->slug) . '" ' . $selected . '>' . esc_html($category->name) . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
    
                <div class="filter-submit">
                    <button type="submit">Filtrar</button>
                </div>
            </div>
        </form>
        <?php
        return ob_get_clean();
    }

    public function filter_tax($query) {
        if (!is_admin()) {
            $tax_query = [];
    
            if (!empty($_GET['filter_season'])) {
                $tax_query[] = [
                    'taxonomy' => 'ez_pt_season',
                    'field' => 'slug',
                    'terms' => sanitize_text_field($_GET['filter_season']),
                ];
            }
    
            if (!empty($_GET['filter_department'])) {
                $tax_query[] = [
                    'taxonomy' => 'ez_pt_department',
                    'field' => 'slug',
                    'terms' => sanitize_text_field($_GET['filter_department']),
                ];
            }
    
            if (!empty($_GET['filter_category'])) {
                $tax_query[] = [
                    'taxonomy' => 'ez_pt_category',
                    'field' => 'slug',
                    'terms' => sanitize_text_field($_GET['filter_category']),
                ];
            }
    
            if (!empty($tax_query)) {
                $query->set('tax_query', [
                    'relation' => 'AND',
                    $tax_query,
                ]);
            }
        }
    }
}
