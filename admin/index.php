<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */



require_once 'functions.php';

add_action('admin_menu', 'codelighter_add_plugin_page');

function codelighter_add_plugin_page()
{
	add_options_page(__('Codelighter Settings', 'codelighter'), 'Codelighter', 'manage_options', 'codelighter', 'codelighter_options_page_output');
}

function codelighter_options_page_output()
{
?>
	<div class="wrap">
		<h2><?php echo get_admin_page_title() ?></h2>

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
	add_settings_field('codelighter_option_favorites', __('Write here what you want', 'codelighter'), 'codelighter_option_favorites', 'codelighter_page', 'codelighter_main_settings');
}

## Selected color input

function codelighter_option_post_types()
{
	$codelighter_val = get_option('codelighter');
	$codelighter_val = $codelighter_val ? $codelighter_val['post-types'] : [];
	$post_types = get_post_types();
?>
	<legend><?php esc_html_e('Choose in what post types CodeLighter will highlight code snippets', 'codelighter') ?></legend>
	<?php
	foreach ($post_types as $post_type) {?>
		<input type="checkbox" id="codelighter_option_post_types-<?php echo $post_type ?>" name="codelighter[post-types][<?php echo $post_type ?>]" value="<?php echo $post_type; ?>">
		<label for="codelighter_option_post_types-<?php echo $post_type ?>"><?php echo $post_type ?></label><br>
	<?php } 
	// echo '<pre>';
	// print_r($_GET['page']);
	// echo '</pre>';
	// echo('<br>');
	// $current_screen = get_current_screen();
	// echo '<pre>';
	// print_r($current_screen->parent_file);
	// echo '</pre>';
	// echo '<pre>';
	// print_r(get_option('codelighter'));
	// echo '</pre>'; 
	?>
	<?php
}

## Area for choose color theme and preview it

function codelighter_option_style()
{
	$codelighter_val = get_option('codelighter');
	$codelighter_val = $codelighter_val ? $codelighter_val['style'] : 'default';
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

## Text area for save anithing

function codelighter_option_favorites()
{
	$codelighter_val = get_option('codelighter');
	$codelighter_val = $codelighter_val ? $codelighter_val['favorites'] : '';
?>
	<label for="cl-ta"><?php esc_html_e('Write here your favorite styles', 'codelighter') ?></label><br>
	<textarea id="codelighter_option_favorites" name="codelighter[favorites]" rows="10" cols="50"><?php echo $codelighter_val; ?></textarea> <br>
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

