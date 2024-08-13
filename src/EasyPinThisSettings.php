<?php

namespace EasyPinThis;

class EasyPinThisSettings {

    public function __construct() {
        add_action('admin_menu', [$this, 'add_settings_page']);
        add_action('admin_init', [$this, 'register_settings']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_media_scripts']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_front_scripts']);
    }

    public function add_settings_page() {
        add_options_page(
            __('Easy Pin This', 'easy-pinthis'),
            __('Easy Pin This', 'easy-pinthis'),
            'manage_options',
            'easy-pin-this-settings',
            [$this, 'render_settings_page']
        );
    }

    public function register_settings() {
        register_setting('easy_pin_this_options_group', 'easy_pin_this_options');
        
        add_settings_section(
            'easy_pin_this_settings_section',
            __('Easy Pin This Settings', 'easy-pinthis'),
            null,
            'easy-pin-this-settings'
        );

        $fields = [
            'slug_pin' => __('Slug Pin', 'easy-pinthis'),
            'slug_pin_folder' => __('Slug Pin Folder', 'easy-pinthis'),
            'slug_category' => __('Slug Category', 'easy-pinthis'),
            'slug_department' => __('Slug Department', 'easy-pinthis'),
            'slug_season' => __('Slug Season', 'easy-pinthis'),
            'slug_designer' => __('Slug Designer', 'easy-pinthis'),
            'default_image' => __('Default Image to Pin Folders', 'easy-pinthis'),
            //'new_folder_input_class' => __('New Folder Input Class', 'easy-pinthis'),
            //'new_folder_create_btn_class' => __('New Folder Create Button Class', 'easy-pinthis'),
        ];

        foreach ($fields as $name => $label) {
            add_settings_field(
                $name,
                $label,
                [$this, 'render_field'],
                'easy-pin-this-settings',
                'easy_pin_this_settings_section',
                ['name' => $name]
            );
        }
    }

    public function enqueue_media_scripts() {
        wp_enqueue_media();
        wp_enqueue_script('easy-pin-this-script', plugin_dir_url(__FILE__) . 'js/easy-pin-this.js', ['jquery'], null, true);

        wp_localize_script('easy-pin-this-script', 'easyPinThis', [
            'uploadImage' => __('Upload Image', 'easy-pinthis'),
            'selectImage' => __('Select Image', 'easy-pinthis'),
            'useImage' => __('Use this image', 'easy-pinthis')
        ]);
    }

    public function enqueue_front_scripts() {
        wp_enqueue_script('ezpt-front', plugin_dir_url(__FILE__) . 'js/ezpt-front.js', ['jquery'], null, true);

        wp_localize_script('ezpt-front', 'ezptFront', array(
            'ajax_url' => esc_url(rest_url('easy-pinthis/v1/update-folder/')),
            'nonce'    => wp_create_nonce('wp_rest')
        ));
    }

    public function render_field($args) {
        $options = get_option('easy_pin_this_options');
        $value = isset($options[$args['name']]) ? esc_attr($options[$args['name']]) : '';

        if ($args['name'] === 'default_image') {
            echo '<input type="text" id="' . esc_attr($args['name']) . '" name="easy_pin_this_options[' . esc_attr($args['name']) . ']" value="' . $value . '" />';
            echo '<input type="button" class="button button-primary" value="' . __('Upload Image', 'easy-pinthis') . '" id="upload_image_button" />';
        } else {
            echo '<input type="text" id="' . esc_attr($args['name']) . '" name="easy_pin_this_options[' . esc_attr($args['name']) . ']" value="' . $value . '" />';
        }
    }

    public function render_settings_page() {
        ?>
        <div class="wrap">
            <h1><?php _e('Easy Pin This Settings', 'easy-pinthis'); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('easy_pin_this_options_group');
                do_settings_sections('easy-pin-this-settings');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }
}
