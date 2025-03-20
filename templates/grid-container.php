<?php

if ( isset( $neighbourhood ) ) {
    extract( $neighbourhood );
    unset( $neighbourhood );
}

$neighbourhoods = get_posts( [
    'post_type' => 'draad_az_area', // Buurtcode
    'posts_per_page' => 1,
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
    
?>

    <article class="draad-adreszoeker__result">
        
        <div class="draad-adreszoeker__result-heading">
            <h2 class="draad-adreszoeker__result-title"><?= esc_html( $neighbourhood->post_title ); ?></h2>
        </div>

        <ul class="draad-adreszoeker__result-list">
            <?php
                echo ( $neighbourhood->post_title ) ? '<li class="draad-adreszoeker__result-list-item"><strong>' . __( 'Uw buurt', 'draad-az' ) . ': </strong> ' . $neighbourhood->post_title . '</li>' : '';
                echo ( $bouwjaar ) ? '<li class="draad-adreszoeker__result-list-item"><strong>' . __( 'Bouwjaar woning', 'draad-az' ) . ': </strong> ' . $bouwjaar . '</li>' : '';
                echo ( $heatSolutionLabel ) ? '<li class="draad-adreszoeker__result-list-item"><strong>' . __( 'Aardgasvrije oplossing voor uw buurt', 'draad-az' ) . ': </strong> ' . ( $heatSolutionLabel === 'Hybride warmtepomp' ? __( 'Hybride warmtepomp (tijdelijk)', 'draad-az' ) : $heatSolutionLabel ) . '</li>' : '';
                echo ( $energielabel ) ? '<li class="draad-adreszoeker__result-list-item"><strong>' . __( 'Energielabel', 'draad-az' ) . ': </strong> ' . $energielabel . '</li>' : '';
            ?>
        </ul>

        <div class="draad-adreszoeker__result-content">
        <?php
            $textNumber = ( get_field( 'text_number', $neighbourhoodID ) ) ? get_field( 'text_number', $neighbourhoodID ) : 0;

            $base = get_posts( [
                'post_type' => 'draad_az_text', // Adreszoeker basis
                'posts_per_page' => 1,
                'post_status' => 'publish',
                'meta_query' => [
                    [
                        'key' => 'text_number_main',
                        'value' => $textNumber,
                        'compare' => '='
                    ]
                ]
            ] );

            // If a post is found, get the field text number main
            if ( is_iterable( $base ) && ! empty( $base ) ) :
                
                $base = $base[0];
                $base_ID = $base->ID;
                $base_text = get_field( 'text_number_main', $base_ID );

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

                $content = get_field( 'text', $base_ID );
                $adreszoekerAddressTitle = get_field( 'address_title', 'draad_az' );
                $adreszoekerAddressTitleSanitize = ( $adreszoekerAddressTitle ) ? sanitize_title( $adreszoekerAddressTitle ) : '';
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

                                <?php
                                     endforeach; 

                                     if ( $adreszoekerAddressTitle ) :
                                ?>
                                    
                                        <li class="draad-adreszoeker__result-toc-item">
                                            <a href="#<?= $adreszoekerAddressTitleSanitize ?>"><?= $adreszoekerAddressTitle ?><i class="far fa-long-arrow-down"></i></a>
                                        </li>

                                <?php endif; ?>
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
                    echo ( $adreszoekerAddressTitle ) ? '<h3 id="' . $adreszoekerAddressTitleSanitize . '" class="draad-adreszoeker__result-advice-title">' . $adreszoekerAddressTitle . '</h3>' : '';

                    echo ( $years && array_values( $years )[0] ) ? '<p class="draad-adreszoeker__result-year">' . __( 'Informatie voor woningen uit', 'draad-az' ) . ' ' . array_values( $years )[0] . '</p>' : '';

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
                                    'taxonomy' => 'draad_az_build_period', // Bouwperiodes
                                    'field' => 'slug',
                                    'terms' => $taxonomies,
                                    'operator' => 'IN',
                                ],
                            ],
                        ];

                        $advice_2 = get_posts( $advice_2_args );

                        $tabGroup = get_field( $index, 'draad_az' ); // Get the tab group from the Adreszoeker advies 1 option page
                        $icon = ( $tabGroup && $tabGroup['icon'] ) ? $tabGroup['icon'] : ''; // Get fontawesome icon
                ?>

                        <div class="draad-tabs__tabpanel" id="draad-tabpanel-<?= $index ?>" role="tabpanel" aria-labelledby="draad-tab-<?= $index ?>">

                            <div class="draad-tabs__tabpanel-heading">
                                <h3 class="draad-tabs__tabpanel-title"><?= $icon . $tab ?></h3>
                            </div>

                        <?php 
                            if ( is_iterable( $tabGroup[ 'repeater' ] ) ) {
                                foreach ( $tabGroup[ 'repeater' ] as $repeater ) {

                                    $periodOutOfTaxonomies = $taxonomies ? array_key_first( $taxonomies ) : null;

                                    if ( ! $repeater[ 'heat_solution_dropdown' ] && ! $repeater[ 'period' ] && ! $repeater[ 'content' ] ) {
                                        continue;
                                    }
                            
                                    if ( in_array( $heatSolutionKey, $repeater[ 'heat_solution_dropdown' ], true ) && in_array( $periodOutOfTaxonomies, $repeater[ 'period' ], true ) ) {
                                        echo '<div class="draad-tabs__intro">' . $repeater[ 'content' ] . '</div>';
                                    }
                                }
                            }                        

                            if ( is_iterable( $advice_2 ) && ! empty( $advice_2 ) ) :
                        ?>

                                <div class="draad-tabs__tabpanel-grid">
                                    <div class="draad-tabs__quicklinks">
                                        <h3 class="draad-tabs__quicklinks-title"><?= __( 'Ga naar:', 'draad-az' ) ?></h3>

                                    <?php 
                                        foreach ( $advice_2 as $page ) {
                                            $id = $page->ID;

                                            $heatSolutionKey = ( $heatSolution ) ? $heatSolution['value'] : '';
                                            $heatSolutionFiltered = ( $heatSolutionKey ) ? $heatSolutionKey : '';
                                            $heatSolutionDropdown = get_field( 'heat_solution_dropdown', $id );

                                            if ( ! $heatSolutionDropdown ) {
                                                continue;
                                            }

                                            foreach ( $heatSolutionDropdown as $key => $value ) {
                                                $filteredKey = $value['value'];
                                                if ( $heatSolutionFiltered === $filteredKey ) {
                                                    global $periodCount;
                                                    $periodCount = $periodCount + 1;

                                                    $cardID = ( get_the_title( $id ) ) ? strtolower( str_replace( ' ', '-', get_the_title( $id ) ) ) : '';
                                                    echo '<a href="#'. $cardID .'" class="draad-tabs__quicklink"><i class="far fa-chevron-right"></i>'. get_the_title( $id ) .'</a>';
                                                }
                                            }
                                        }
                                    ?>
                                    </div>

                                <?php
                                    global $periodCount;
                                    echo '<span class="draad-tabs__quicklinks-total --'. $periodCount .'"></span>';
                                
                                    if ( is_iterable( $advice_2 ) ) {
                                        foreach ( $advice_2 as $advice ) {
                                            $advice = $advice->ID;

                                            include 'card.php';
                                        
                                        }
                                    }
                                ?>
                                </div>

                        <?php endif; ?>
                        </div>

                <?php endforeach; ?>
                </div>

        <?php endif; ?>
        </div>

        <button class="draad-adreszoeker__close-advice button close-button result-close">
            <span class="sr-only"><?= __( 'Resultaat sluiten', 'draad-az' ) ?></span>
            <span class="icon cross fa-solid fa-xmark"></span>
        </button>

    </article>

<?php endforeach; ?>