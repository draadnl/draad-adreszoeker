<?php
$advice = ( $advice ) ? $advice : null;

if ( ! $advice ) {
    return;
}

$heatSolutionFiltered = ( $heatSolutionKey ) ? $heatSolutionKey : '';
$heatSolutionDropdown = get_field( 'heat_solution_dropdown', $advice );

if ( ! $heatSolutionDropdown ) {
    return;
}

$heatSolutionKeyNew = '';
foreach ( $heatSolutionDropdown as $key => $value ) {
    $filteredKey = ( $value ) ? $value['value'] : '';
    if ( $filteredKey === $heatSolutionFiltered ) {
        $heatSolutionKeyNew = $filteredKey;
    }
}

if ( $heatSolutionFiltered !== $heatSolutionKeyNew ) {
    return;
}

$link = ( get_field( 'link', $advice ) ) ? get_field( 'link', $advice ) : null;

$content = ( get_field( 'content', $advice ) ) ? get_field( 'content', $advice ) : ( get_the_excerpt( $advice ) ? get_the_excerpt( $advice ) : null );
$excerpt = $content;

$linkUrl = ( $link ) ? 'href="'. esc_attr( $link['url'] ) .'"' : null;
$linkTitle = ( $link ) ? $link['title'] : null;
$linkTarget = ( $link && $link['target'] ) ? $link['target'] : null;

$tag = ( $link && $link['url'] ) ? 'a' : 'div';

$thumbnail = get_post_thumbnail_id( $advice );
$thumbnailClass = ( $thumbnail ) ? '--has-image' :  '--no-image';

$cardID = ( get_the_title( $advice ) ) ? 'id="'. esc_attr( strtolower( str_replace( ' ', '-', get_the_title( $advice ) ) ) ) .'"' : '';
?>

<article <?= $cardID ?> class="card --advice-2 <?= esc_attr( $thumbnailClass ) ?>">
    <<?= $tag ?> class="card__link" <?= $linkUrl ?> <?= $linkTarget ?>>
    <?php
        echo ( get_the_title( $advice ) ) ? '<h3 class="card__title">' . get_the_title( $advice ) . '</h3>' : '';

        echo ( ! empty( $excerpt ) ) ? '<p class="card__excerpt">' . esc_html( $excerpt ) . '</p>' : '';

        if ( $link ) {
            echo '<div class="button button--faux">
                <span class="button__title button-title">'. esc_html( $linkTitle ) .'</span>
                <span class="icon button__icon fa-solid fa-angle-right"></span>
            </div>';
        }

        echo wp_get_attachment_image( $thumbnail, 'large', false, [ 'class' => 'card__image' ] );
    ?>
    </<?= $tag ?>>
</article>