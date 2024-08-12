<?php

namespace EasyPinThis;

class EasyPinThisTaxonomies {

    public function __construct() {
        $this->register_taxonomies();
    }

    private function register_taxonomies() {
        $taxonomies = [
            'ez_pt_category' => __('Categories', 'easy-pinthis'),
            'ez_pt_department' => __('Departments', 'easy-pinthis'),
            'ez_pt_season' => __('Seasons', 'easy-pinthis'),
            'ez_pt_designer' => __('Designers', 'easy-pinthis')
        ];

        foreach ($taxonomies as $taxonomy => $label) {
            register_taxonomy($taxonomy, 'ez_pin', [
                'labels' => [
                    'name' => $label,
                    'singular_name' => $label,
                ],
                'public' => true,
                'hierarchical' => true,
            ]);
        }
    }
}
