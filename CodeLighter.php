<?php
/*
  Plugin Name: CodeLighter
  Plugin URI:
  Description: Simple plugin for highlight code in all post types
  Version: 0.9
  Author:
  Author URI:
  License: GPLv2
 */

 if(!defined('ABSPATH')) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit;
 }

 $start = microtime(true);

/*
 *  *******************************************************************************
 * Activation hook
 *  *******************************************************************************
 */

$post_types = get_post_types(['public' => true]);

register_activation_hook( __FILE__, 'codelighter_by_bmrrr_activate' );

function codelighter_by_bmrrr_activate() {
	// delete_option( 'codelighter' );
	global $post_types;
	if ( !get_option( 'codelighter' ) ) {
		add_option( 'codelighter', array( 'style' => 'default', 'post-types' => $post_types, 'selected-color' => '', 'favorites' => '' ) );
	}
}


/*
 *  *******************************************************************************
 * For translate plugin name and description
 *  *******************************************************************************
 */
__( 'CodeLighter', 'codelighter' );
__( 'Lightweight WordPress plugin for highlighting code snippets in posts, pages and other custom post types', 'codelighter' );


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

function codelighter_by_bmrrr_check_post ($post_types) {
	foreach($post_types as $post_type) {
		if ( is_singular( $post_type ) ) {
			return true;
			break;
		}
	}
}


/*
 *  *******************************************************************************
 * Connection scripts and styles in front-end and admin panel
 *  *******************************************************************************
 */

add_action('wp_enqueue_scripts', 'codelighter_enqueue_front', PHP_INT_MAX);
function codelighter_enqueue_front() {
	global $codelighter_options;
	global $post_types;
	if (isset($codelighter_options['post-types']) && $codelighter_options['post-types'] === $post_types) {
		wp_enqueue_style('highlight', '//cdn.jsdelivr.net/gh/highlightjs/cdn-release@11.4.0/build/styles/'.$codelighter_options['style'].'.min.css');
		wp_enqueue_script('highlight', '//cdn.jsdelivr.net/gh/highlightjs/cdn-release@11.4.0/build/highlight.min.js', [], '11.4.0', 'in_footer');
		wp_enqueue_script('highlight-call', plugins_url( 'public/js/hl_call.js', __FILE__), ['highlight'], '11.4.0', 'in_footer');
		
		wp_localize_script( 'highlight', 'hlajax',
			array(
				'url' => admin_url('admin-ajax.php')
			)
		);
	} elseif (isset($codelighter_options['post-types']) && codelighter_by_bmrrr_check_post($codelighter_options['post-types'])) {
		wp_enqueue_style('highlight', '//cdn.jsdelivr.net/gh/highlightjs/cdn-release@11.4.0/build/styles/'.$codelighter_options['style'].'.min.css');
		wp_enqueue_script('highlight', '//cdn.jsdelivr.net/gh/highlightjs/cdn-release@11.4.0/build/highlight.min.js', [], '11.4.0', 'in_footer');
		wp_enqueue_script('highlight-call', plugins_url( 'public/js/hl_call.js', __FILE__), ['highlight'], '11.4.0', 'in_footer');
		
		wp_localize_script( 'highlight', 'hlajax',
			array(
				'url' => admin_url('admin-ajax.php')
			)
		);
	}
}

// add_action('admin_enqueue_scripts', 'codelighter_enqueue_back');
function codelighter_enqueue_back() {
	global $codelighter_options;
	wp_enqueue_script('highlight', '//cdn.jsdelivr.net/gh/highlightjs/cdn-release@11.4.0/build/highlight.min.js', [], '11.4.0', 'in_footer');
	wp_enqueue_script('highlight-call', plugins_url( 'admin/js/hl_call.js', __FILE__), ['highlight'], '11.4.0', 'in_footer');
	wp_enqueue_style('highlight', '//cdn.jsdelivr.net/gh/highlightjs/cdn-release@11.4.0/build/styles/'.$codelighter_options['style'].'.min.css');
	wp_enqueue_style('highlight-admin-styles', plugins_url( 'admin/styles/styles.css', __FILE__));
}


/*
 *  *******************************************************************************
 * Updater plugin from GitHub config
 *  *******************************************************************************
 */
$config = array(
	'slug' => plugin_basename(__FILE__), // this is the slug of your plugin
	'proper_folder_name' => 'CodeLighter', // this is the name of the folder your plugin lives in
	'api_url' => 'https://api.github.com/repos/byBmrrr/codelighter', // the GitHub API url of your GitHub repo
	'raw_url' => 'https://raw.github.com/byBmrrr/codelighter/main', // the GitHub raw url of your GitHub repo
	'github_url' => 'https://github.com/byBmrrr/codelighter', // the GitHub url of your GitHub repo
	'zip_url' => 'https://github.com/byBmrrr/codelighter/zipball/main', // the zip url of the GitHub repo
	'sslverify' => true, // whether WP should check the validity of the SSL cert when getting an update, see https://github.com/jkudish/WordPress-GitHub-Plugin-Updater/issues/2 and https://github.com/jkudish/WordPress-GitHub-Plugin-Updater/issues/4 for details
	'requires' => '5.0', // which version of WordPress does your plugin require?
	'tested' => '5.9.3', // which version of WordPress is your plugin tested up to?
	'readme' => 'README.txt', // which file to use as the readme for the version number
	// 'access_token' => '', // Access private repositories by authorizing under Plugins > GitHub Updates when this example plugin is installed
);


/*
*  *******************************************************************************
* Included files
*  *******************************************************************************
*/
// add_action( 'current_screen', 'codelighter_require_files' );
// function codelighter_require_files( $current_screen ) {
// 	if( 'options-general.php' == $current_screen->parent_file && 'codelighter' == $_GET['page'] ) {
// 		require_once 'admin/index.php';
// 		require_once 'admin/ajax.php';
// 		require_once 'admin/updater/updater.php';
// 		add_action( 'admin_enqueue_scripts', 'codelighter_enqueue_back' );
// 	}
// }


if (is_admin()) { // note the use of is_admin() to double check that this is happening in the admin
	require_once 'admin/updater/updater.php';
	new WP_GitHub_Updater($config);
	require_once 'admin/index.php';
	require_once 'admin/ajax.php';
	if (isset($_GET['page']) && $_GET['page'] === 'codelighter') {		
		add_action('admin_enqueue_scripts', 'codelighter_enqueue_back');
	}
} else {
	require_once 'public/index.php';
}
do_action( 'qm/debug', 'Script execution time: '.round(microtime(true) - $start, 4).' sec' );