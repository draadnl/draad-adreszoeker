<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Prevent direct access
}

add_action( 'admin_menu', function() {
    if ( ! function_exists( 'acf_add_options_page' ) ) {
        return;
    }

    acf_add_options_page( [
        'page_title'    => __( 'Adreszoeker advies 1', 'draad-az' ),
        'menu_title'    => __( 'Adreszoeker advies 1', 'draad-az' ),
        'menu_slug'     => 'draad-adreszoeker-instellingen',
        'parent_slug'   => 'edit.php?post_type=draad_az_text',
        'redirect'      => false,
        'post_id'       => 'draad_az',
        'autoload'      => true,
    ] );    

 } );

 function draad_az_return_heat_solutions_acf() {
    return [
        'bestaand-warmtenet' => __( 'Bestaand warmtenet', 'draad-az' ),
        'elektrische-warmtepomp' => __( 'Elektrische warmtepomp', 'draad-az' ),
        'mix-van-warmtenetten-en-warmtepompen' => __( 'Mix van warmtenetten en warmtepompen', 'draad-az' ),
        'warmtenet-na-2030' => __( 'Warmtenet na 2030', 'draad-az' ),
        'warmtenet' => __( 'Warmtenet', 'draad-az' ),
        'hybride-warmtepomp' => __( 'Hybride warmtepomp', 'draad-az' ),
    ];
 }

// acf include fields
add_action( 'acf/include_fields', function() {

    if ( ! function_exists( 'acf_add_local_field_group' ) ) {
        return;
    }

    require_once 'acf-text.php';
    require_once 'acf-text-2.php';
    require_once 'acf-area.php';
    require_once 'acf-base.php';
    require_once 'acf-build-periods.php';

}, 85 );