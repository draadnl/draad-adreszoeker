<?php

if ( isset( $neighbourhood ) ) {
    extract( $neighbourhood );
    unset( $neighbourhood );
}

$neighbourhoods = get_posts( [
    'post_type' => 'neighborhood',
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

if ( !is_iterable( $neighbourhoods ) || empty( $neighbourhoods ) ) {
    return;
}

foreach ( $neighbourhoods as $neighbourhood ) :
    $heatSolution = get_field( 'heat_solution_dropdown', $neighbourhood->ID );
    $heatSolutionKey = ( $heatSolution ) ? $heatSolution['value'] : '';
    $heatSolutionLabel = ( $heatSolution ) ? $heatSolution['label'] : '';

    $heatSolutions = get_field('heat_solutions', 'option');
    $anchor = ( $neighbourhood->post_title ) ? 'id="'. strtolower( str_replace( ' ', '-', $neighbourhood->post_title ) ) .'"' : '';
?>

<article class="draad-adreszoeker__result" <?= $anchor ?>>
    
    <div class="draad-adreszoeker__result-heading">
        <h2 class="draad-adreszoeker__result-title"><?= esc_html( $neighbourhood->post_title ); ?></h>
    </div>

    <ul class="draad-adreszoeker__result-list">
        <?php
            echo ( $neighbourhood->post_title ) ? '<li class="draad-adreszoeker__result-list-item"><strong>' . __( 'Uw buurt' ) . ': </strong> ' . $neighbourhood->post_title . '</li>' : '';
            echo ( $bouwjaar ) ? '<li class="draad-adreszoeker__result-list-item"><strong>' . __( 'Bouwjaar woning' ) . ': </strong> ' . $bouwjaar . '</li>' : '';
            echo ( $heatSolutionLabel ) ? '<li class="draad-adreszoeker__result-list-item"><strong>' . __( 'Aardgasvrije oplossing voor uw buurt' ) . ': </strong> ' . ( $heatSolutionLabel === 'Hybride warmtepomp' ? __( 'Hybride warmtepomp (tijdelijk)' ) : $heatSolutionLabel ) . '</li>' : '';
            echo ( $energieLabel ) ? '<li class="draad-adreszoeker__result-list-item"><strong>' . __( 'Energielabel' ) . ': </strong> ' . $energieLabel . '</li>' : '';
        ?>
    </ul>

</article>

<?php

endforeach;

?>