<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'acf_add_local_field_group' ) ) {
    return;
}

acf_add_local_field_group( [
    'key' => 'group_draad_az_build_periods',
    'title' => __( 'Bouwperiodes', 'draad-adreszoeker' ),
    'fields' => [
        [
            'key' => 'field_draad_az_start_year_build_periods',
            'label' => __( 'Bouwperiode startjaar', 'draad-adreszoeker' ),
            'name' => 'start_year',
            'aria-label' => '',
            'type' => 'number',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => [
                'width' => '50',
                'class' => '',
                'id' => '',
            ],
            'default_value' => '',
            'min' => '',
            'max' => '',
            'placeholder' => '',
            'step' => 1,
            'prepend' => '',
            'append' => '',
        ],
        [
            'key' => 'field_draad_az_end_year_build_periods',
            'label' => __( 'Bouwperiode eindjaar', 'draad-adreszoeker' ),
            'name' => 'end_year',
            'aria-label' => '',
            'type' => 'number',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => [
                'width' => '50',
                'class' => '',
                'id' => '',
            ],
            'default_value' => '',
            'min' => '',
            'max' => '',
            'placeholder' => '',
            'step' => 1,
            'prepend' => '',
            'append' => '',
        ],
    ],
    'location' => [
        [
            [
                'param' => 'taxonomy',
                'operator' => '==',
                'value' => 'draad_az_build_period',
            ],
        ],
    ],
    'menu_order' => 0,
    'position' => 'normal',
    'style' => 'default',
    'label_placement' => 'top',
    'instruction_placement' => 'label',
    'hide_on_screen' => '',
    'active' => true,
    'description' => '',
    'show_in_rest' => 0,
] );