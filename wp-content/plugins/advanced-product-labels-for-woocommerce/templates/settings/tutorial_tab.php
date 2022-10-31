<?php
$tutorials = array(
    'beginning' => array(
        'title' => __('Beginning', 'BeRocket_products_label_domain'),
        'elements' => array(
            'how_to_configure' => array(
                'title' => __('How to configure the plugin', 'BeRocket_products_label_domain'),
                'type'  => 'youtube',
                'video' => 'NOQE8P5WBZ0'
            ),
            'instalation' => array(
                'title' => __('Installation and Activation', 'BeRocket_products_label_domain'),
                'type'  => 'youtube',
                'video' => 'QF9hDJIBFcQ'
            ),
            'how_to_use_conditions' => array(
                'title' => __('How to use conditions', 'BeRocket_products_label_domain'),
                'type'  => 'youtube',
                'video' => 'HsGP8qIIatw'
            ),
        )
    ),
    'advanced' => array(
        'title' => __('Advanced', 'BeRocket_products_label_domain'),
        'elements' => array(
            'features' => array(
                'title' => __('Features', 'BeRocket_products_label_domain'),
                'type'  => 'youtube',
                'video' => 'AEa4LcP_9jI'
            ),
            'how_to_use_attributes' => array(
                'title' => __('How to use attributes', 'BeRocket_products_label_domain'),
                'type'  => 'youtube',
                'video' => 'Jioa2FnUGBo'
            ),
            'how_to_use_timers' => array(
                'title' => __('How to use timers', 'BeRocket_products_label_domain'),
                'type'  => 'youtube',
                'video' => 'g7PVTua6FqU'
            ),
            'how_to_use_variations' => array(
                'title' => __('How to use variations', 'BeRocket_products_label_domain'),
                'type'  => 'youtube',
                'video' => 'h6_6Yv6_K9s'
            ),
        )
    ),
);
echo berocket_tutorial_tab($tutorials);