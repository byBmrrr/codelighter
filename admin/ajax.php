<?php
add_action('wp_ajax_cl_get_checked_post_types', 'cl_get_selected_post_types');

function cl_get_selected_post_types() {
    $codelighter_optn = get_option( 'codelighter' );
    echo json_encode($codelighter_optn['post-types']);

    wp_die();
}