<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Prevent direct access
}

function draad_az_register_cpt( $name, $singular, $plural, $params = [] ) {

    // Stop if $name is empty
    if (!$name) {
        throw new Exception('Need to add a name for this custom post type.');
        return;
    }

    $labels = [
        'name'                  => $plural,
        'singular_name'         => $singular,
        'menu_name'             => $plural,
        'name_admin_bar'        => $singular,
        'archives'              => __("{$singular} Archives", 'draad-adreszoeker'),
        'attributes'            => __("{$singular} Attributes", 'draad-adreszoeker'),
        'parent_item_colon'     => __("Parent {$singular}:", 'draad-adreszoeker'),
        'all_items'             => __("All {$plural}", 'draad-adreszoeker'),
        'add_new_item'          => __("Add New {$singular}", 'draad-adreszoeker'),
        'add_new'               => __("Add New", 'draad-adreszoeker'),
        'new_item'              => __("New {$singular}", 'draad-adreszoeker'),
        'edit_item'             => __("Edit {$singular}", 'draad-adreszoeker'),
        'update_item'           => __("Update {$singular}", 'draad-adreszoeker'),
        'view_item'             => __("View {$singular}", 'draad-adreszoeker'),
        'view_items'            => __("View {$plural}", 'draad-adreszoeker'),
        'search_items'          => __("Search {$singular}", 'draad-adreszoeker'),
        'not_found'             => __("Not found", 'draad-adreszoeker'),
        'not_found_in_trash'    => __("Not found in Trash", 'draad-adreszoeker'),
        'featured_image'        => __("Featured Image", 'draad-adreszoeker'),
        'set_featured_image'    => __("Set featured image", 'draad-adreszoeker'),
        'remove_featured_image' => __("Remove featured image", 'draad-adreszoeker'),
        'use_featured_image'    => __("Use as featured image", 'draad-adreszoeker'),
        'insert_into_item'      => __("Insert into {$singular}", 'draad-adreszoeker'),
        'uploaded_to_this_item' => __("Uploaded to this {$singular}", 'draad-adreszoeker'),
        'items_list'            => __("{$plural} list", 'draad-adreszoeker'),
        'items_list_navigation' => __("{$plural} list navigation", 'draad-adreszoeker'),
        'filter_items_list'     => __("Filter {$plural} list", 'draad-adreszoeker'),
    ];

    $args = [
        'label'                 => $singular,
        'description'           => __( "{$singular} Description", 'draad-adreszoeker' ),
        'labels'                => $labels,
        'supports'              => [ 'title', 'thumbnail', 'revisions' ],
        'hierarchical'          => false,
        'public'                => false,
        'show_ui'               => true,
        'show_in_menu'          => false,
        'menu_position'         => 20,
        'menu_icon'             => 'dashicons-admin-post',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => false,
        'can_export'            => false,
        'has_archive'           => false,
        'exclude_from_search'   => true,
        'publicly_queryable'    => false,
        'capability_type'       => 'post',
        'show_in_rest'          => false,
        'taxonomies'            => [],
    ];

    if ( is_iterable( $params ) ) {
        foreach ( $params as $key => $value ) {

            if ( $key == 'rewrite' ) {
                $args[$key] = [
                    'slug'			=> $value,
                    'with_front'	=> true,
                    'pages'			=> true,
                    'feeds'			=> false,
                ];
                continue;
            }

            $args[$key] = $value;
        }
    }

    $post_type = register_post_type($name, $args);
    if ( is_wp_error( $post_type ) ) {
        $errors = $post_type->get_error_messages();
        $error_message = ( is_array( $errors ) ) ? $name . ' ' . implode( ', ', $errors ) : '';
        var_dump($error_message);
        error_log( 'Could not register post type.' . ' ' . $error_message );
        throw new Exception('Could not register post type.');
    }

}

function draad_az_register_tax( $name, $singular, $plural, $post_types, $args = [] ) {

    // Stop if $name is empty
    if (! $name) {
        error_log('Need to add a name for this custom taxonomy.');
        throw new Exception('Need to add a name for this custom taxonomy.');
    }

    // Set labels for taxonomy
    $labels = [
        'name'                       => __($plural, 'draad-adreszoeker'),
        'singular_name'              => __($singular, 'draad-adreszoeker'),
        'menu_name'                  => __($singular, 'draad-adreszoeker'),
        'all_items'                  => __("All {$plural}", 'draad-adreszoeker'),
        'parent_item'                => __("Parent {$singular}", 'draad-adreszoeker'),
        'parent_item_colon'          => __("Parent {$singular}:", 'draad-adreszoeker'),
        'new_item_name'              => __("New {$singular} Naam", 'draad-adreszoeker'),
        'add_new_item'               => __("New {$singular} Toevoegen", 'draad-adreszoeker'),
        'edit_item'                  => __("Edit {$singular}", 'draad-adreszoeker'),
        'update_item'                => __("Update {$singular}", 'draad-adreszoeker'),
        'view_item'                  => __("View {$singular}", 'draad-adreszoeker'),
        'separate_items_with_commas' => __('Separate item with commas', 'draad-adreszoeker'),
        'add_or_remove_items'        => __('Add or remove items', 'draad-adreszoeker'),
        'choose_from_most_used'      => __('Choose from the most used', 'draad-adreszoeker'),
        'popular_items'              => __("Popular {$plural}", 'draad-adreszoeker'),
        'search_items'               => __("Search {$plural}", 'draad-adreszoeker'),
        'not_found'                  => __('Not Found', 'draad-adreszoeker'),
        'no_terms'                   => __("No {$plural}", 'draad-adreszoeker'),
        'items_list'                 => __("{$plural} list", 'draad-adreszoeker'),
        'items_list_navigation'      => __("{$plural} list navigation", 'draad-adreszoeker'),
    ];

    $params = [
        'labels'                     => $labels,
        'hierarchical'               => true,
        'public'                     => false,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => false,
        'show_tagcloud'              => true,
    ];

    if ( is_array( $args ) && ! empty( $args ) ) {

        foreach ( $args as $key => $value ) {
            if ( $key == 'rewrite' && is_string( $value ) ) {
                $params[$key] = [
                    'slug'			=> $value,
                    'with_front'	=> true,
                    'hierarchical'	=> true,
                ];
                continue;
            }
            $params[$key] = $value;
        }

    }

    // registers taxonomy
    if ( ! register_taxonomy($name, $post_types, $params) ) {
        error_log('Could not register taxonomy.');
        throw new Exception('Could not register taxonomy.');
    }

}

// adreszoeker basis teksten
add_action( 'init', function () {
    draad_az_register_cpt( 'draad_az_text', __( 'Adreszoeker basis', 'draad-adreszoeker' ), __( 'Adreszoeker basis', 'draad-adreszoeker' ) );
    draad_az_register_cpt( 'draad_az_text_2', __( 'Adreszoeker advies 2', 'draad-adreszoeker' ), __( 'Adreszoeker advies 2', 'draad-adreszoeker' ) );
    draad_az_register_cpt( 'draad_az_area', __( 'Buurtcode', 'draad-adreszoeker' ), __( 'Buurtcodes', 'draad-adreszoeker' ) );

    // taxonomy
    draad_az_register_tax( 'draad_az_build_period', __( 'Bouwperiode', 'draad-adreszoeker' ), __( 'Bouwperiodes', 'draad-adreszoeker' ), [ 'draad_az_text_2' ] );
} );

add_action( 'admin_menu', function () {
    add_menu_page(
        __( 'Draad adreszoeker', 'draad-adreszoeker' ),
        __( 'Draad adreszoeker', 'draad-adreszoeker' ),
        'manage_options',
        'edit.php?post_type=draad_az_text',
        '',
        'dashicons-admin-generic',
        85
    );

    add_submenu_page(
        'edit.php?post_type=draad_az_text',
        __( 'Adreszoeker basis', 'draad-adreszoeker' ),
        __( 'Adreszoeker basis', 'draad-adreszoeker' ),
        'manage_options',
        'edit.php?post_type=draad_az_text'
    );

    add_submenu_page(
        'edit.php?post_type=draad_az_text',
        __( 'Adreszoeker advies 2', 'draad-adreszoeker' ),
        __( 'Adreszoeker advies 2', 'draad-adreszoeker' ),
        'manage_options',
        'edit.php?post_type=draad_az_text_2'
    );

    add_submenu_page(
        'edit.php?post_type=draad_az_text',
        __( 'Buurtcode', 'draad-adreszoeker' ),
        __( 'Buurtcodes', 'draad-adreszoeker' ),
        'manage_options',
        'edit.php?post_type=draad_az_area'
    );

    add_submenu_page(
        'edit.php?post_type=draad_az_text',
        __( 'Bouwperiode', 'draad-adreszoeker' ),
        __( 'Bouwperiodes', 'draad-adreszoeker' ),
        'manage_options',
        'edit-tags.php?taxonomy=draad_az_build_period&post_type=draad_az_text'
    );
}, 85 );