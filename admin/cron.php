<?php
set_time_limit(0);

require_once 'functions.php';

$codelighter_options = get_option( 'codelighter' );

$codelighter_styles_dir = CODELIGHTER_PATH . "public/styles";


/**Check if function result is object */
$codelighter_styles_list_object = codelighter_get_styles_list_object();
if (!is_object($codelighter_styles_list_object)) {
    do_action( 'qm/error', "Variable value is not an object" );
    return;
}

/**Check if not has error in response */
if ($codelighter_styles_list_object->message ?? false) {
    foreach ( $codelighter_styles_list_object as $key => $value ) {
        // echo sanitize_text_field($key . ': ' . $value);
        do_action( 'qm/error', sanitize_text_field($key . ': ' . $value));
    }
    add_action( 'admin_notices', 'codelighter_api_error_notice' );
    function codelighter_api_error_notice() {
        ?>
        <div class="notice notice-warning is-dismissible">
            <?php _e( '<h3>CodeLighter</h3><p>API call error</p>', 'codelighter' ); ?>
        </div>
        <?php
    }
    return;
}




/**Initialize wp_filesystem and check credentials */
$access_type = get_filesystem_method();
if($access_type === 'direct')
{
    $codelighter_plugin_url_nonce = wp_nonce_url(CODELIGHTER_URL.'&refresh_files=true','Codelighter');
    if (false === ($codelighter_creds = request_filesystem_credentials($codelighter_plugin_url_nonce, '', false, $codelighter_styles_dir, null) ) ) {
        return; // stop processing here
    }
    
    
    if ( ! WP_Filesystem($codelighter_creds) ) {
        request_filesystem_credentials($codelighter_plugin_url_nonce, '', true, $codelighter_styles_dir, null);
        return;
    }
} else {
    wp_print_request_filesystem_credentials_modal();
}


/**Check if dir exist else create it */
global $wp_filesystem;
if ( !$wp_filesystem->exists( $codelighter_styles_dir ) ) {
    $codelighter_mkdir = $wp_filesystem->mkdir( $codelighter_styles_dir );
    if (false === $codelighter_mkdir) {
        add_action( 'admin_notices', 'codelighter_mkdir_error_notice' );
        function codelighter_mkdir_error_notice() {
            ?>
            <div class="notice notice-error is-dismissible">
                <?php _e( '<p>Folder styles is not create</p>', 'codelighter' ); ?>
            </div>
            <?php
        }
        do_action( 'qm/error', "Folder styles is not create");
        return;
    }
}

$codelighter_lenght = count($codelighter_styles_list_object->tree);

$codelighter_options = get_option( 'codelighter' );

ob_start();
// ob_implicit_flush();
for ($i = is_numeric($codelighter_options['styles-count']) ? $codelighter_options['styles-count'] : 0; $i < $codelighter_lenght; $i++) { 
    // ob_flush();
    $codelighter_style_url = $codelighter_styles_list_object->tree[$i]->url;
    $codelighter_style_name = $codelighter_styles_list_object->tree[$i]->path;
    $codelighter_style_obj = codelighter_get_styles_list_object( $codelighter_style_url);

    if ($codelighter_style_obj->message ?? false) {
        foreach ($codelighter_style_obj as $key => $value) {
            do_action( 'qm/error', sanitize_text_field($key . ': ' . $value));
            echo sanitize_text_field($key . ': ' . $value);
        }
        
        $codelighter_options['styles-count'] = $i;
        update_option( 'codelighter', $codelighter_options);

        add_action( 'admin_notices', 'codelighter_api_error_notice' );
        function codelighter_api_error_notice() {
            ?>
            <div class="notice notice-error is-dismissible">
                <p><?php esc_html_e( 'Plugin can`t update all styles!', 'codelighter' ); ?></p>
            </div>
            <?php
        }
        ob_flush();
        // return;
        break;
    }

    $codelighter_style_content = base64_decode($codelighter_style_obj->content, true);
    $wp_filesystem->put_contents($codelighter_styles_dir."/".$codelighter_style_name, $codelighter_style_content);
    do_action( 'qm/debug', $codelighter_style_name );
    do_action( 'qm/debug', $i );

    echo "<div class='info'><h4>".$codelighter_style_name."</h4><p>".($i+1)."/".$codelighter_lenght."</p></div>\n";
    ob_flush();
    
    if ( $i == ($codelighter_lenght - 1) ) {
        $codelighter_options['styles-count'] = 0;
        update_option( 'codelighter', $codelighter_options);
        

    }

    // if ( $i > 3 ) {
    //     break;
    // }
}

ob_get_clean();

exit("<a href='".CODELIGHTER_URL."' class='button button-primary'>Go back to plugin page</a>");