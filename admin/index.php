<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */



require_once 'functions.php';
// require_once 'cron.php';

/**
 * Check if isset get parameter refresh_files
 * And if it isset update or download style files
 */
function get_request_parameter( $key, $default = 'false' ) {
    // If not request set
    if ( ! isset( $_REQUEST[ $key ] ) || empty( $_REQUEST[ $key ] ) ) {
        return $default;
    }
 
    // Set so process it
    return (bool) strip_tags(  wp_unslash( $_REQUEST[ $key ] ) );
}

// if ($codelighter_get_refresh = get_request_parameter('refresh_files')){
// 	add_action( 'load-'.CODELIGHTER_PAGE_HOOK, 'codelighter_plugin_loaded_hook' );
// 	function codelighter_plugin_loaded_hook() {
// 		add_action( 'all_admin_notices', 'codelighter_plugin_loaded' );
// 		function codelighter_plugin_loaded(  ){
// 			require_once 'admin/cron.php';
// 		}
// 	}
// 	do_action( 'qm/debug', CODELIGHTER_PAGE_HOOK );
// }


add_action('admin_menu', 'codelighter_add_plugin_page');
$codelighter_page_hook;

function codelighter_add_plugin_page()
{
	global $codelighter_page_hook;
	$codelighter_page_hook = add_options_page(__('Codelighter Settings', 'codelighter'), 'Codelighter', 'manage_options', 'codelighter', 'codelighter_options_page_output');
}

function codelighter_options_page_output()
{
?>
	<div class="wrap">
		<h2><?php echo get_admin_page_title() ?></h2>
		<?php settings_errors(  ); ?>
		<form action="options.php" method="POST">
			<?php
			settings_fields('codelighter_group');  // скрытые защитные поля
			do_settings_sections('codelighter_page'); // секции с настройками (опциями). У нас она всего одна 'section_id'
			submit_button();
			?>
		</form>
	</div>
<?php
}

/**
 * Регистрируем настройки.
 * Настройки будут храниться в массиве, а не одна настройка = одна опция.
 */
add_action('admin_init', 'codelighter_plugin_settings');

function codelighter_plugin_settings()
{
	// параметры: $option_group, $option_name, $sanitize_callback
	register_setting('codelighter_group', 'codelighter', 'sanitize_callback');

	// параметры: $id, $title, $callback, $page
	add_settings_section('codelighter_main_settings', __('Main settings', 'codelighter'), '', 'codelighter_page');

	// параметры: $id, $title, $callback, $page, $section, $args
	add_settings_field('codelighter_option_post_types', __('Choose post types', 'codelighter'), 'codelighter_option_post_types', 'codelighter_page', 'codelighter_main_settings');
	add_settings_field('codelighter_option_style', __('Choose highlight style', 'codelighter'), 'codelighter_option_style', 'codelighter_page', 'codelighter_main_settings');
	add_settings_field('codelighter_option_selected_color', __('Selected color', 'codelighter'), 'codelighter_option_selected_color', 'codelighter_page', 'codelighter_main_settings');
	add_settings_field('codelighter_option_update_style_files', __('Write here what you want', 'codelighter'), 'codelighter_option_update_style_files', 'codelighter_page', 'codelighter_main_settings');
}

## Selected color input

function codelighter_option_post_types()
{
	$codelighter_val = get_option('codelighter');
	$codelighter_val = $codelighter_val ? $codelighter_val['post-types'] : [];
	global $post_types;
?>
	<legend class="check-post-types"><?php esc_html_e('Choose in what post types CodeLighter will highlight code snippets', 'codelighter') ?></legend>
	<?php
	foreach ($post_types as $post_type) {?>
		<input type="checkbox" class="codelighter_post_type" id="codelighter_option_post_types-<?php echo $post_type ?>" name="codelighter[post-types][<?php echo $post_type ?>]" value="<?php echo $post_type; ?>">
		<label for="codelighter_option_post_types-<?php echo $post_type ?>"><?php echo $post_type ?></label><br>
	<?php } 
	?>
	<?php
	do_action( 'qm/debug', get_option('codelighter') );
}

## Area for choose color theme and preview it

function codelighter_option_style()
{
	$codelighter_val = get_option('codelighter');
	$codelighter_val = $codelighter_val ? $codelighter_val['style'] : 'default';

	require_once 'functions.php';
	
	$codelighter_styles_list_object = codelighter_get_styles_list_object();
	if (is_object($codelighter_styles_list_object)) {
		if ($codelighter_styles_list_object->message ?? false) {
			foreach ($codelighter_styles_list_object as $key => $value) {
				echo sanitize_text_field($key . ': ' . $value);
	?><br><?php
			}
		} else {

			?>
			<select name="codelighter[style]" id="codelighter-option-style">
				<?php
				foreach ($codelighter_styles_list_object->tree as $value) {
					$codelighter_string = $value->path;
					$html_value = explode('.', $codelighter_string);
					$codelighter_selected = $html_value[0] === $codelighter_val ? 'selected' : ''; ?>
					<option value="<?php echo sanitize_text_field($html_value[0]); ?>" <?php echo sanitize_text_field($codelighter_selected); ?>><?php echo sanitize_text_field($html_value[0]); ?></option>
				<?php
				}
				?>
			</select>
			<pre id="cl-preview-code">
<code>
function increment(span) {
	let incrBlock = span.previousElementSibling;
	incrBlock.value = parseInt(incrBlock.value) + 1;
}
function decriment(span) {
	let incrBlock = span.nextElementSibling;
	let valueInt = parseInt(incrBlock.value);
	if (valueInt > 1) {
		incrBlock.value = valueInt - 1;
	}
}
</code>
		</pre>
			<p><label for="code-test"><?php esc_html_e('Put your code here for see how it will look', 'codelighter') ?></label></p>
			<textarea id="code-test" name="codelighter[code-test]" rows="10" cols="80"></textarea>
		<?php
		}
	} elseif (is_string($codelighter_styles_list_object)) {
		?>
		<p><?php esc_html($codelighter_styles_list_object); ?></p>
	<?php
	}
}

## Selected color input

function codelighter_option_selected_color()
{
	$codelighter_val = get_option('codelighter');
	$codelighter_val = $codelighter_val ? $codelighter_val['selected-color'] : '#000000';
	?>
	<label for="cl-sc"><?php esc_html_e('Text color in selected theme', 'codelighter') ?></label><br>
	<input type="color" id="codelighter_option_selected_color" name="codelighter[selected-color]" value="<?php echo $codelighter_val; ?>"> <br>
<?php
}

## Button for update style files

function codelighter_option_update_style_files()
{
	$codelighter_val = get_option('codelighter');
	$codelighter_val = $codelighter_val ? $codelighter_val['favorites'] : '';
?>
	<p><?php esc_html_e('Press Refresh button for update style files', 'codelighter') ?></p> 
	<p><a href="<?php echo CODELIGHTER_URL.'&refresh_files=true' ?>" class="button"><?php esc_html_e('Refresh', 'codelighter') ?></a></p>
<?php
}


## Sanitize data

function sanitize_callback($options)
{
	// очищаем
	foreach ($options as $name => &$codelighter_val) {
		if ($name == 'style') {
			$codelighter_val = sanitize_text_field($codelighter_val);
		}

		if ($name == 'favorites') {
			$codelighter_val = esc_html($codelighter_val);
		}

		if ($name == 'selected-color') {
			$codelighter_val = sanitize_text_field($codelighter_val);
		}
	}


	return $options;
}

