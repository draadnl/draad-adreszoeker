<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'acf_add_local_field_group' ) ) {
    return;
}

acf_add_local_field_group( [
    'key' => 'group_draad_az_area',
    'title' => __( 'Buurtcode invoervelden', 'draad-az' ),
    'fields' => [
        [
            'key' => 'field_draad_az_neighbourhood_code_area',
            'label' => __( 'Buurtcode', 'draad-az' ),
            'name' => 'neigbourhood_code',
            'aria-label' => '',
            'type' => 'number',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => [
                'width' => '33.333',
                'class' => '',
                'id' => '',
            ],
            'default_value' => '',
            'min' => '',
            'max' => '',
            'placeholder' => '',
            'step' => '',
            'prepend' => '',
            'append' => '',
        ],
        [
            'key' => 'field_draad_az_text_number_area',
            'label' => __( 'Tekstnummer', 'draad-az' ),
            'name' => 'text_number',
            'aria-label' => '',
            'type' => 'number',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => [
                'width' => '33.333',
                'class' => '',
                'id' => '',
            ],
            'default_value' => '',
            'min' => '',
            'max' => '',
            'placeholder' => '',
            'step' => '',
            'prepend' => '',
            'append' => '',
        ],
        [
            'key' => 'field_draad_az_heat_solution_dropdown_area',
            'label' => __( 'Warmte oplossing - dropdown', 'draad-az' ),
            'name' => 'heat_solution_dropdown',
            'aria-label' => '',
            'type' => 'select',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => [
                'width' => '33.333',
                'class' => '',
                'id' => '',
            ],
            'choices' => draad_az_return_heat_solutions_acf(),
            'default_value' => false,
            'return_format' => 'array',
            'multiple' => 0,
            'allow_null' => 0,
            'ui' => 0,
            'ajax' => 0,
            'placeholder' => '',
        ],
    ],
    'location' => [
        [
            [
                'param' => 'post_type',
                'operator' => '==',
                'value' => 'draad_az_area',
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