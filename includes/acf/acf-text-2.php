<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'acf_add_local_field_group' ) ) {
    return;
}

acf_add_local_field_group( [
    'key' => 'group_draad_az_text_2',
    'title' => __( 'Bouwperiode - extra velden', 'draad-az' ),
    'fields' => [
        [
            'key' => 'field_draad_az_heat_solution_dropdown_text_2',
            'label' => __( 'Warmte oplossing', 'draad-az' ),
            'name' => 'heat_solution_dropdown',
            'aria-label' => '',
            'type' => 'checkbox',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => [
                'width' => '',
                'class' => '',
                'id' => '',
            ],
            'choices' => [
                'bestaand-warmtenet' => __( 'Bestaand warmtenet', 'draad-az' ),
                'elektrische-warmtepomp' => __( 'Elektrische warmtepomp', 'draad-az' ),
                'mix-van-warmtenetten-en-warmtepompen' => __( 'Mix van warmtenetten en warmtepompen', 'draad-az' ),
                'warmtenet-na-2030' => __( 'Warmtenet na 2030', 'draad-az' ),
                'warmtenet' => __( 'Warmtenet', 'draad-az' ),
                'hybride-warmtepomp' => __( 'Hybride warmtepomp', 'draad-az' ),
            ],
            'default_value' => [],
            'return_format' => 'array',
            'allow_custom' => 0,
            'layout' => 'horizontal',
            'toggle' => 0,
            'save_custom' => 0,
            'custom_choice_button_text' => __( 'Nieuwe keuze toevoegen', 'draad-az' ),
        ],
        [
            'key' => 'field_draad_az_tab_text_2',
            'label' => __( 'In welk tabje moet deze informatie terug komen', 'draad-az' ),
            'name' => 'tab',
            'aria-label' => '',
            'type' => 'checkbox',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => [
                'width' => '50',
                'class' => '',
                'id' => '',
            ],
            'choices' => [
                'isolatie' => __( 'Isolatie', 'draad-az' ),
                'ventilatie' => __( 'Ventilatie', 'draad-az' ),
                'opwekken' => __( 'Opwekken', 'draad-az' ),
                'verwarmen' => __( 'Verwarmen', 'draad-az' ),
                'koken' => __( 'Koken', 'draad-az' ),
                'subsidies' => __( 'Subsidies', 'draad-az' ),
            ],
            'default_value' => [
            ],
            'return_format' => 'value',
            'allow_custom' => 0,
            'layout' => 'vertical',
            'toggle' => 0,
            'save_custom' => 0,
            'custom_choice_button_text' => __( 'Nieuwe keuze toevoegen', 'draad-az' ),
        ],
        [
            'key' => 'field_draad_az_link_text_2',
            'label' => __( 'Link', 'draad-az' ),
            'name' => 'link',
            'aria-label' => '',
            'type' => 'link',
            'instructions' => __( 'Vul alleen dit veld in als de tegel ergens naartoe moet linken.', 'draad-az' ),
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => [
                'width' => '50',
                'class' => '',
                'id' => '',
            ],
            'return_format' => 'array',
        ],
        [
            'key' => 'field_draad_az_content_text_2',
            'label' => __( 'Content', 'draad-az' ),
            'name' => 'content',
            'aria-label' => '',
            'type' => 'wysiwyg',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => [
                'width' => '',
                'class' => '',
                'id' => '',
            ],
            'default_value' => '',
            'tabs' => 'all',
            'toolbar' => 'full',
            'media_upload' => 0,
            'delay' => 0,
        ],
    ],
    'location' => [
        [
            [
                'param' => 'post_type',
                'operator' => '==',
                'value' => 'draad_az_text_2',
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