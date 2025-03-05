<?php

if ( isset( $neighbourhood ) ) {
    extract( $neighbourhood );
    unset( $neighbourhood );
}

$neighbourhoods = get_posts( [
    'post_type' => 'draad_az_area',
    'posts_per_page' => 5,
    'post_status' => 'publish',
    'meta_query' => [
        [
            'key' => 'neigbourhood_code',
            'value' => (int) $buurtcode ?: 0,
            'compare' => '='
        ]
    ]
] );

if ( ! is_iterable( $neighbourhoods ) || empty( $neighbourhoods ) ) {
    return;
}

foreach ( $neighbourhoods as $neighbourhood ) : 

    $neighbourhoodID = $neighbourhood->ID;

    $heatSolution = get_field( 'heat_solution_dropdown', $neighbourhoodID );
    $heatSolutionKey = ( $heatSolution ) ? $heatSolution['value'] : '';
    $heatSolutionLabel = ( $heatSolution ) ? $heatSolution['label'] : '';

    $heatSolutions = get_field( 'heat_solutions', 'option' );
    $anchor = ( $neighbourhood->post_title ) ? 'id="'. strtolower( str_replace( ' ', '-', $neighbourhood->post_title ) ) .'"' : '';

?>

<article class="draad-adreszoeker__result" <?= $anchor ?>>
    
    <div class="draad-adreszoeker__result-heading">
        <h2 class="draad-adreszoeker__result-title"><?= esc_html( $neighbourhood->post_title ); ?></h>
    </div>

    <ul class="draad-adreszoeker__result-list">
        <?php
            echo ( $neighbourhood->post_title ) ? '<li class="draad-adreszoeker__result-list-item"><strong>' . __( 'Uw buurt', 'draad-az' ) . ': </strong> ' . $neighbourhood->post_title . '</li>' : '';
            echo ( $bouwjaar ) ? '<li class="draad-adreszoeker__result-list-item"><strong>' . __( 'Bouwjaar woning', 'draad-az' ) . ': </strong> ' . $bouwjaar . '</li>' : '';
            echo ( $heatSolutionLabel ) ? '<li class="draad-adreszoeker__result-list-item"><strong>' . __( 'Aardgasvrije oplossing voor uw buurt', 'draad-az' ) . ': </strong> ' . ( $heatSolutionLabel === 'Hybride warmtepomp' ? __( 'Hybride warmtepomp (tijdelijk)', 'draad-az' ) : $heatSolutionLabel ) . '</li>' : '';
            echo ( $energieLabel ) ? '<li class="draad-adreszoeker__result-list-item"><strong>' . __( 'Energielabel', 'draad-az' ) . ': </strong> ' . $energieLabel . '</li>' : '';
        ?>
    </ul>

    <div class="draad-adreszoeker__result-content">
    <?php
        $textNumber = ( get_field( 'text_number_v3', $neighbourhoodID ) ) ? get_field( 'text_number_v3', $neighbourhoodID ) : 0;

        $base = get_posts( [
            'post_type' => 'draad_az_text', // Adreszoeker basis
            'posts_per_page' => 1,
            'post_status' => 'publish',
            'meta_query' => [
                [
                    'key' => 'text_number_main_v3',
                    'value' => $textNumber,
                    'compare' => '='
                ]
            ]
        ] );

        // If a post is found, get the field text number main v3
        if ( is_iterable( $base ) && ! empty( $base ) ) :
            
            $base = $base[0];
            $base_ID = $base->ID;
            $base_text = get_field( 'text_number_main_v3', $base_ID );

            if ( $base_text ) {

                $build_periods = get_terms( 'draad_az_build_period', [ 'hide_empty' => false ] );
                
                $bouwjaarInt = (int) $bouwjaar;

                $taxonomies = [];
                $years = [];

                if ( ! is_wp_error( $build_periods ) ) {
                    foreach ( $build_periods as $j => $year ) {
                        $startYear = (int) get_field( 'start_year', 'draad_az_build_period_' . $year->term_id );
                        $endYear = (int) get_field( 'end_year', 'draad_az_build_period_' . $year->term_id );

                        if ( ! $startYear || ! $endYear ) {
                            continue;
                        }

                        if ( $bouwjaarInt < $startYear || $bouwjaarInt > $endYear ) {
                            continue;
                        }


                        $taxonomies[$year->term_id] = $year->slug;
                        $years[] = $year->name;
                    }
                }
            }

        $content = get_field('text', $base_ID);
        if ( $content ) :
            preg_match_all( "/(<h([1-3])(.*?))>(.*?)<\/h[1-3]>/", $content, $matches, PREG_SET_ORDER );

            $anchors = [];
            
            foreach ( $matches as $match ) {
                $title = strip_tags( $match[4] );
                $anchor = sanitize_title( $title );
                $new_heading = '<h' . $match[2] . ' id="' . $anchor . '"' . $match[3] . '>' . $match[4] . '</h' . $match[2] . '>';
                $content = str_replace( $match[0], $new_heading, $content );

                $anchors[] = [
                    'anchor' => '#' . $anchor,
                    'title'  => $title,
                ];
            }

            if ( ! empty( $anchors ) ) :
    ?>

                <aside class="draad-adreszoeker__result-sidebar">
                    <div class="draad-adreszoeker__result-toc">

                        <h2 class="draad-adreszoeker__result-toc-title"><?= __( 'Direct naar', 'draad-az' ) ?></h2>

                        <ul class="draad-adreszoeker__result-toc-list">

                            <?php foreach ( $anchors as $anchor ) : ?>
                                <li class="draad-adreszoeker__result-toc-item">
                                    <a href="<?= $anchor['anchor'] ?>"><?= $anchor['title'] ?><i class="far fa-long-arrow-down"></i></a>
                                </li>
                            <?php endforeach; ?>
                            
                            <li class="draad-adreszoeker__result-toc-item">
                                <a href="#maatregelen-voor-een-aardgasvrij-huis"><?= __('Zo maakt u uw huis aardgasvrij', 'draad-az') ?><i class="far fa-long-arrow-down"></i></a>
                            </li>

                        </ul>
                    </div>
                </aside>

    <?php
            endif;

            echo '<div class="draad-adreszoeker__result-base">' . $content . '</div>';

        endif;
    ?>

            <div class="draad-adreszoeker__result-advice">
            <?php
                echo ( get_field( 'address_title', 'draad_az' ) ) ? '<h3 id="maatregelen-voor-een-aardgasvrij-huis" class="draad-adreszoeker__result-advice-title">' . get_field( 'address_title', 'draad_az' ) . '</h3>' : '';

                if ( $years && array_values( $years )[0] ) {
                    echo ( array_values( $years )[0] ) ? '<p class="draad-adreszoeker__result-year">' . __( 'Informatie voor woningen uit', 'draad-az' ) . ' ' . array_values( $years )[0] . '</p>' : '';
                }

                if ( $taxonomies && array_keys( $taxonomies )[0] ) {
                    $key = array_keys( $taxonomies )[0];
                    $term = get_term( $key, 'draad_az_build_period' );

                    echo ( $term->description ) ? '<p class="draad-adreszoeker__result-year-content">'. $term->description .'</p>' : '';
                }

                $tabs = [
                    'isolatie' => __( 'Isolatie', 'draad-az' ),
                    'ventilatie' => __( 'Ventileren', 'draad-az' ),
                    'opwekken' => __( 'Energie opwekken en opslaan', 'draad-az' ),
                    'verwarmen' => __( 'Verwarming', 'draad-az' ),
                    'koken' => __( 'Koken op inductie', 'draad-az' ),
                    'subsidies' => __( 'Leningen en subsidies', 'draad-az' ),
                ];
            ?>

                <div role="tablist" aria-labelledby="tablist-1" class="draad-tabs__tablist">
                <?php foreach ( $tabs as $index => $tab ) : ?>

                        <button class="draad-tabs__tab" id="draad-tab-<?= $index ?>" type="button" role="tab" aria-controls="draad-tabpanel-<?= $index ?>">
                            <span class="focus"><?= $tab ?></span>
                        </button>

                <?php endforeach; ?>
                </div>

            <?php
                // TODO: De Adreszpeler advies 2 tegels terug laten komen
                foreach ( $tabs as $index => $tab ) :
                    
                    $advice_2_args = [
                        'post_type' => 'draad_az_text_2', // Adreszoeker advies 2
                        'posts_per_page' => -1,
                        'post_status' => 'publish',
                        'orderby' => 'menu_order',
                        'order' => 'ASC',
                        'meta_query' => [
                            [
                                'key' => 'tab',
                                'value' => '"' . $index . '"',
                                'compare' => 'LIKE',
                            ],
                        ],
                        'tax_query' => [
                            [
                                'taxonomy' => 'draad_az_build_period',
                                'field' => 'slug',
                                'terms' => $taxonomies,
                                'operator' => 'IN',
                            ],
                        ],
                    ];

                    $advice_2 = get_posts( $advice_2_args );

                    $tabGroup = get_field( $index, 'draad_az' ); // Get the tab group from the Adreszoeker advies 1 option page
                    $icon = ( $tabGroup && $tabGroup['icon'] ) ? $tabGroup['icon'] : '';
            ?>

                    <div class="draad-tabs__tabpanel" id="draad-tabpanel-<?= $index ?>" role="tabpanel" aria-labelledby="draad-tab-<?= $index ?>">

                        <div class="draad-tabs__tabpanel-heading">
                            <h3 class="draad-tabs__tabpanel-title"><?= $icon . $tab ?></h3>
                        </div>

                    <?php 
                        // TODO: De tekst laten inladen van de $tabGroup, dit moet op basis van de warmteoplossing en bouwperiode gebeuren
                    ?>

                        <div class="draad-tabs__tabpanel-grid">
                        <?php
                            // TODO: De Adreszpeler advies 2 tegels terug laten komen, in de card checken of de warmteoplossing overeenkomt 
                            if ( is_iterable( $advice_2 ) ) {
                                foreach ( $advice_2 as $advice ) {
                                    $advice = $advice->ID;

                                    include 'card.php';
                                   
                                }
                            }
                        ?>
                        </div>

                    </div>

            <?php
                endforeach;
            ?>
            </div>

    <?php

        endif;

    ?>
    </div>

</article>

<?php

endforeach;

?>