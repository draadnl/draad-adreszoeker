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
        // translators: %s: singular post type name
        'archives'              => sprintf(__('%s Archives', 'draad-adreszoeker'), $singular),
        // translators: %s: singular post type name
        'attributes'            => sprintf(__('%s Attributes', 'draad-adreszoeker'), $singular),
        // translators: %s: singular post type name
        'parent_item_colon'     => sprintf(__('Parent %s:', 'draad-adreszoeker'), $singular),
        // translators: %s: plural post type name
        'all_items'             => sprintf(__('All %s', 'draad-adreszoeker'), $plural),
        // translators: %s: singular post type name
        'add_new_item'          => sprintf(__('Add New %s', 'draad-adreszoeker'), $singular),
        'add_new'               => __('Add New', 'draad-adreszoeker'),
        // translators: %s: singular post type name
        'new_item'              => sprintf(__('New %s', 'draad-adreszoeker'), $singular),
        // translators: %s: singular post type name
        'edit_item'             => sprintf(__('Edit %s', 'draad-adreszoeker'), $singular),
        // translators: %s: singular post type name
        'update_item'           => sprintf(__('Update %s', 'draad-adreszoeker'), $singular),
        // translators: %s: singular post type name
        'view_item'             => sprintf(__('View %s', 'draad-adreszoeker'), $singular),
        // translators: %s: plural post type name
        'view_items'            => sprintf(__('View %s', 'draad-adreszoeker'), $plural),
        // translators: %s: singular post type name
        'search_items'          => sprintf(__('Search %s', 'draad-adreszoeker'), $singular),
        'not_found'             => __('Not found', 'draad-adreszoeker'),
        'not_found_in_trash'    => __('Not found in Trash', 'draad-adreszoeker'),
        'featured_image'        => __('Featured Image', 'draad-adreszoeker'),
        'set_featured_image'    => __('Set featured image', 'draad-adreszoeker'),
        'remove_featured_image' => __('Remove featured image', 'draad-adreszoeker'),
        'use_featured_image'    => __('Use as featured image', 'draad-adreszoeker'),
        // translators: %s: singular post type name
        'insert_into_item'      => sprintf(__('Insert into %s', 'draad-adreszoeker'), $singular),
        // translators: %s: singular post type name
        'uploaded_to_this_item' => sprintf(__('Uploaded to this %s', 'draad-adreszoeker'), $singular),
        // translators: %s: plural post type name
        'items_list'            => sprintf(__('%s list', 'draad-adreszoeker'), $plural),
        // translators: %s: plural post type name
        'items_list_navigation' => sprintf(__('%s list navigation', 'draad-adreszoeker'), $plural),
        // translators: %s: plural post type name
        'filter_items_list'     => sprintf(__('Filter %s list', 'draad-adreszoeker'), $plural),
    ];

    $args = [
        'label'                 => $singular,
        // translators: %s: singular post type name
        'description'           => sprintf(__('%s Description', 'draad-adreszoeker'), $singular),
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
                    'slug'  => $value,
                    'with_front' => true,
                    'pages' => true,
                    'feeds' => false,
                ];
                continue;
            }

            $args[$key] = $value;
        }
    }

    $post_type = register_post_type($name, $args);
    if ( is_wp_error( $post_type ) ) {
        throw new Exception('Could not register post type.');
    }

}

function draad_az_register_tax( $name, $singular, $plural, $post_types, $args = [] ) {

    // Stop if $name is empty
    if (! $name) {
        throw new Exception('Need to add a name for this custom taxonomy.');
    }

    // Set labels for taxonomy
    $labels = [
        'name'                       => $plural,
        'singular_name'              => $singular,
        'menu_name'                  => $singular,
        // translators: %s: plural taxonomy name
        'all_items'                  => sprintf(__('All %s', 'draad-adreszoeker'), $plural),
        // translators: %s: singular taxonomy name
        'parent_item'                => sprintf(__('Parent %s', 'draad-adreszoeker'), $singular),
        // translators: %s: singular taxonomy name
        'parent_item_colon'          => sprintf(__('Parent %s:', 'draad-adreszoeker'), $singular),
        // translators: %s: singular taxonomy name
        'new_item_name'              => sprintf(__('New %s Naam', 'draad-adreszoeker'), $singular),
        // translators: %s: singular taxonomy name
        'add_new_item'               => sprintf(__('New %s Toevoegen', 'draad-adreszoeker'), $singular),
        // translators: %s: singular taxonomy name
        'edit_item'                  => sprintf(__('Edit %s', 'draad-adreszoeker'), $singular),
        // translators: %s: singular taxonomy name
        'update_item'                => sprintf(__('Update %s', 'draad-adreszoeker'), $singular),
        // translators: %s: singular taxonomy name
        'view_item'                  => sprintf(__('View %s', 'draad-adreszoeker'), $singular),
        'separate_items_with_commas' => __('Separate item with commas', 'draad-adreszoeker'),
        'add_or_remove_items'        => __('Add or remove items', 'draad-adreszoeker'),
        'choose_from_most_used'      => __('Choose from the most used', 'draad-adreszoeker'),
        // translators: %s: plural taxonomy name
        'popular_items'              => sprintf(__('Popular %s', 'draad-adreszoeker'), $plural),
        // translators: %s: plural taxonomy name
        'search_items'               => sprintf(__('Search %s', 'draad-adreszoeker'), $plural),
        'not_found'                  => __('Not Found', 'draad-adreszoeker'),
        // translators: %s: plural taxonomy name
        'no_terms'                   => sprintf(__('No %s', 'draad-adreszoeker'), $plural),
        // translators: %s: plural taxonomy name
        'items_list'                 => sprintf(__('%s list', 'draad-adreszoeker'), $plural),
        // translators: %s: plural taxonomy name
        'items_list_navigation'      => sprintf(__('%s list navigation', 'draad-adreszoeker'), $plural),
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
                    'slug' => $value,
                    'with_front' => true,
                    'hierarchical' => true,
                ];
                continue;
            }
            $params[$key] = $value;
        }

    }

    // registers taxonomy
    if ( ! register_taxonomy($name, $post_types, $params) ) {
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