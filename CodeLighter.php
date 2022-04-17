<?php
/*
  Plugin Name: CodeLighter
  Plugin URI:
  Description: Simple plugin for highlight code in all post types
  Version: 1.0.0
  Author:
  Author URI:
  License: GPLv2
 */

 if(!defined('ABSPATH')) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit;
 }

/*
 *  *******************************************************************************
 * Activation hook
 *  *******************************************************************************
 */

register_activation_hook( __FILE__, 'codelighter_by_bmrrr_activate' );

function codelighter_by_bmrrr_activate() {
	// delete_option( 'codelighter' );
	if ( !get_option( 'codelighter' ) ) {
		$all_post_types = get_post_type();
		add_option( 'codelighter', array( 'style' => 'default', 'post-types' => [	] ) );
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

add_action('wp_enqueue_scripts', 'codelighter_enqueue_front', PHP_INT_MAX);
function codelighter_enqueue_front() {
	global $codelighter_options;
	if ($codelighter_options)
	wp_enqueue_style('highlight', '//cdn.jsdelivr.net/gh/highlightjs/cdn-release@11.4.0/build/styles/'.$codelighter_options['style'].'.min.css');
	wp_enqueue_script('highlight', '//cdn.jsdelivr.net/gh/highlightjs/cdn-release@11.4.0/build/highlight.min.js', [], '11.4.0', 'in_footer');
	wp_enqueue_script('highlight-call', plugins_url( 'public/js/hl_call.js', __FILE__), ['highlight'], '11.4.0', 'in_footer');
	
	wp_localize_script( 'highlight', 'hlajax',
		array(
			'url' => admin_url('admin-ajax.php')
		)
	);
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
require_once 'admin/ajax.php';
require_once 'public/index.php';
