<?php

add_action('wp_ajax_get_selected_color', 'hl_get_selected_color');
add_action('wp_ajax_nopriv_get_selected_color', 'hl_get_selected_color');

function hl_get_selected_color() {
    // var_dump($_POST);
    $codelighter_optn = get_option( 'codelighter' );
    echo $codelighter_optn['selected-color'];

    wp_die();
}