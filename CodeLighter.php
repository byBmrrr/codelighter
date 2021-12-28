<?php
/*
Plugin Name: CodeLighter
Plugin URI:
Description:
Version: 1.0.0
Author:
Author URI:
License: GPLv2
*/

register_activation_hook(__FILE__, 'codelighter_by_bmrrr_activate');
function codelighter_by_bmrrr_activate () {
	if ( !get_option( 'codelighter') ) {
		add_option( 'codelighter', array('style' => 'default') );
	}
}