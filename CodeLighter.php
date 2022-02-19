<?php
/*
  Plugin Name: CodeLighter
  Plugin URI:
  Description: Simple plugin for highlight code
  Version: 1.0.0
  Author:
  Author URI:
  License: GPLv2
 */


/*
 *  *******************************************************************************
 * Activation hook
 *  *******************************************************************************
 */

register_activation_hook( __FILE__, 'codelighter_by_bmrrr_activate' );

function codelighter_by_bmrrr_activate() {
	if ( !get_option( 'codelighter' ) ) {
		add_option( 'codelighter', array( 'style' => 'default' ) );
	}
}


/*
 *  *******************************************************************************
 * For translate plugin name and description
 *  *******************************************************************************
 */
__( 'CodeLighter', 'codelighter' );
__( 'Simple plugin for highlight code', 'codelighter' );


/*
 *  *******************************************************************************
 * Loading text domain
 *  *******************************************************************************
 */
//add_action( 'plugins_loaded', function(){
//	load_plugin_textdomain( 'codelighter', false, plugins_url( 'lang/', __FILE__) );
//} );
add_action( 'plugins_loaded', 'codelighter_init' );
function codelighter_init() {
	 load_plugin_textdomain( 'codelighter', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

/*
 *  *******************************************************************************
 * Get options and save it to variable
 *  *******************************************************************************
 */
$codelighter_options = get_option( 'codelighter');


/*
 *  *******************************************************************************
 * Connection scripts and styles in front-end and admin panel
 *  *******************************************************************************
 */

add_action('wp_enqueue_scripts', 'codelighter_enqueue_front');
function codelighter_enqueue_front() {
	global $codelighter_options;
	wp_enqueue_script('highlight', '//cdn.jsdelivr.net/gh/highlightjs/cdn-release@11.4.0/build/highlight.min.js', [], '11.4.0', 'in_footer');
	wp_enqueue_script('highlight-call', plugins_url( 'public/js/hl_call.js', __FILE__), ['highlight'], '11.4.0', 'in_footer');
	wp_enqueue_style('highlight', '//cdn.jsdelivr.net/gh/highlightjs/cdn-release@11.4.0/build/styles/'.$codelighter_options['style'].'.min.css', []);
}

add_action('admin_enqueue_scripts', 'codelighter_enqueue_back');
function codelighter_enqueue_back() {
	global $codelighter_options;
	wp_enqueue_script('highlight', '//cdn.jsdelivr.net/gh/highlightjs/cdn-release@11.4.0/build/highlight.min.js', [], '11.4.0', 'in_footer');
	wp_enqueue_script('highlight-call', plugins_url( 'admin/js/hl_call.js', __FILE__), ['highlight'], '11.4.0', 'in_footer');
	wp_enqueue_style('highlight', '//cdn.jsdelivr.net/gh/highlightjs/cdn-release@11.4.0/build/styles/'.$codelighter_options['style'].'.min.css');
	wp_enqueue_style('highlight-admin-styles', plugins_url( 'admin/styles/styles.css', __FILE__));
}

require_once 'admin/index.php';
