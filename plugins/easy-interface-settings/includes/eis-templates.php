<?php
/*
*
*	Easy Interface Settings V 1.04
*	------------------------------------------------
* 	Copyright Linker  - https://linker.co.il
*
*/

/* PAGE
------------------------------------------*/

function eis_get_theme_name($interface_id) {
	$interface_info = get_eis_interfaces($interface_id);
	return (isset($interface_info['theme']) ? $interface_info['theme'] : 'default');
}

function eis_get_theme_dir_path($theme_name=null) {
	$interface_id = is_eis_page();
	$interface_info = get_eis_interfaces($interface_id);
	$path = null;
	if ($theme_name == null) {
		$theme_name = (isset($interface_info['theme']) ? $interface_info['theme'] : 'default');
	}
	$type = (isset($interface_info['type']) ? $interface_info['type'] : 'page');
	if ($type == 'meta_box') {
		$type = 'meta-box';
	}
	$eis_child_theme_directory = get_stylesheet_directory() . '/easy-interface-settings/templates/' . $type . '/' . $theme_name . '/';
	if (file_exists($eis_child_theme_directory)) {
		$path = $eis_child_theme_directory;
	} else {
		$eis_plugin_theme_directory = EIS_ROOT_PATH . 'templates/' . $type . '/' . $theme_name . '/';
		if (file_exists($eis_plugin_theme_directory)) {
			$path = $eis_plugin_theme_directory;
		}
	}
	return apply_filters('eis_theme_dir_path', $path, $interface_id, $interface_info);
}

function eis_get_theme_dir_url($theme_name=null) {
	$interface_id = is_eis_page();
	$interface_info = get_eis_interfaces($interface_id);
	$path = null;
	if ($theme_name == null) {
		$theme_name = (isset($interface_info['theme']) ? $interface_info['theme'] : 'default');
	}
	$type = (isset($interface_info['type']) ? $interface_info['type'] : 'page');
	if ($type == 'meta_box') {
		$type = 'meta-box';
	}
	$eis_child_theme_directory = get_stylesheet_directory() . '/easy-interface-settings/templates/' . $type . '/' . $theme_name . '/';
	if (file_exists($eis_child_theme_directory)) {
		$path = get_stylesheet_directory_uri() . '/easy-interface-settings/templates/' . $type . '/' . $theme_name . '/';
	} else {
		$eis_plugin_theme_directory = EIS_ROOT_PATH . 'templates/' . $type . '/' . $theme_name . '/';
		if (file_exists($eis_plugin_theme_directory)) {
			$path = EIS_URL_PATH . 'templates/' . $type . '/' . $theme_name . '/';
		}
	}
	return apply_filters('eis_theme_dir_url', $path, $interface_id, $interface_info);
}

function eis_get_theme_file_path($theme_name=null) {
	return eis_get_theme_dir_path($theme_name). 'eis-theme.php';
}

function eis_get_theme_scripts() {
	$interface_id = is_eis_page();
	if (!$interface_id) return;
	$theme_name = eis_get_theme_name($interface_id);
	$eis_style = eis_get_theme_dir_path($theme_name) . 'eis-style.css';
	if (file_exists($eis_style)) {
		$css_post_ver = date("ymd-Gis", filemtime(  $eis_style ) );
		wp_register_style( 'eis-style', eis_get_theme_dir_url($theme_name) . 'eis-style.css', false, $css_post_ver );
		wp_enqueue_style( 'eis-style' );
	}
	if (is_rtl()) {
		$eis_style = eis_get_theme_dir_path($theme_name) . 'eis-style-rtl.css';
		if (file_exists($eis_style)) {
			$css_post_ver = date("ymd-Gis", filemtime(  $eis_style ) );
			wp_register_style( 'eis-style-rtl', eis_get_theme_dir_url($theme_name) . 'eis-style-rtl.css', false, $css_post_ver );
			wp_enqueue_style( 'eis-style-rtl' );
		}
	}
	$eis_script = eis_get_theme_dir_path($theme_name) . 'eis-script.js';
	if (file_exists($eis_script)) {
		$css_post_ver = date("ymd-Gis", filemtime(  $eis_script ) );
		wp_register_script( 'eis-script', eis_get_theme_dir_url($theme_name) . 'eis-script.js', false, $css_post_ver );
		wp_enqueue_script( 'eis-script' );
	}
}
add_action( 'admin_enqueue_scripts', 'eis_get_theme_scripts', 99 );
