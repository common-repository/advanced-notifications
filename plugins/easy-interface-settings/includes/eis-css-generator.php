<?php
/*
*
*	Easy Interface Settings V 1
*	------------------------------------------------
* 	Copyright Linker  - https://linker.co.il
*
*/

function eis_fix_var($property,$var) {
	switch ($property) {
	    case 'background-image': return 'url(' . $var . ')';
	    default: return $var;
	}
}

function get_eis_pages_css_vars() {
	global $eis_interface_tabs_options;
	$get_eis_interfaces = get_eis_interfaces();
	$css_vars_arr = array();
	foreach ($get_eis_interfaces as $interface_id => $eis_interface) {
		if ($eis_interface['type'] == 'page') {
			foreach ($eis_interface_tabs_options[$interface_id] as $tab_id => $tab_options) {
				foreach ($tab_options as $key => $value) {
					$input_id = $value['input_id'];
					$css_var = $value['css_var'];
					$css_units = $value['css_units'];
					$css_property = $value['css_property'];
					$css_element = $value['css_element'];
					if (!is_eis_option_hidden($interface_id, $input_id)) {
						$input_value = eis_get_option($interface_id, $input_id, $post_id=null, $lowercase=false, $cache=false);
						$css_vars_arr[] = array(
							'input_id' => $input_id,
							'input_value' => $input_value,
							'css_var' => $css_var,
							'css_units' => $css_units,
							'css_property' => $css_property,
							'css_element' => $css_element,
						);
					}
				}
			}
		}
	}
	create_eis_pages_css_vars_file($css_vars_arr);
}

function create_eis_pages_css_vars_file($vars) {
	if (!empty($vars)) {
		$vars_list = array();
		$properties_list = array();
		foreach ($vars as $var) {
			if ($var['css_var'] != null && $var['input_value'] != null) {
				$vars_list[$var['css_var']]['var'] = $var['input_value'];
				$vars_list[$var['css_var']]['units'] = $var['css_units'];
			}
		}
		$i_vars = 0;
		foreach ($vars as $var) {
			if ($var['css_property'] != null && $var['css_element'] != null && $var['input_value'] != null) {
				$properties_list[$i_vars]['css_element']= $var['css_element'];
				$properties_list[$i_vars]['css_property']= $var['css_property'];
				$properties_list[$i_vars]['input_value']= $var['input_value'];
				$properties_list[$i_vars]['css_units']= $var['css_units'];
				$i_vars++;
			}
		}
		$upload_dir = wp_upload_dir();
		if (!empty($vars_list) || !empty($properties_list)) {
			if (!file_exists($upload_dir['basedir'] . '/easy-interface-settings/css-vars')) {
				mkdir($upload_dir['basedir'] . '/easy-interface-settings/css-vars', 0777, true);
			}
			$css_file = fopen( $upload_dir['basedir'] . '/easy-interface-settings/css-vars/css-vars.css', 'w') or die("Unable to open file!");
			$css_code = '';
			if (!empty($vars_list)) {
				$css_code .= ':root {'.PHP_EOL;
					foreach ($vars_list as $key => $var) {
						$css_code .= '	--' . $key . ': ' . eis_fix_var($key,$var['var']) . $var['units'] . ';'.PHP_EOL;
					}
				$css_code .= '}'.PHP_EOL;
			}
			if (!empty($properties_list)) {
				foreach ($properties_list as $var) {
					$css_propertys = explode(",", $var['css_property']);
					$css_propertys = array_filter(array_map('trim', $css_propertys));
					$css_code .= $var['css_element'] . ' {'.PHP_EOL;
					foreach ($css_propertys as $property) {
						$css_code .= '	' . $property . ': ' . eis_fix_var($property,$var['input_value']) . $var['css_units'] . ';'.PHP_EOL;
					}
					$css_code .= '}'.PHP_EOL;
				}
			}
			fwrite($css_file, $css_code);
			fclose($css_file);
		} else {
			unlink($upload_dir['basedir'] . '/easy-interface-settings/css-vars/css-vars.css');
		}
	}
}

function do_eis_pages_css_pre_update_option($new_value, $old_value, $option) {
	get_eis_pages_css_vars();
	return $new_value;
}

function do_css_on_save_page_option() {
	$get_eis_interfaces = get_eis_interfaces();
	$css_vars_arr = array();
	foreach ($get_eis_interfaces as $interface_id => $eis_interface) {
		if ($eis_interface['type'] == 'page') {
			add_action( "update_option_{$interface_id}_eis_options", 'do_eis_pages_css_pre_update_option', 10, 3 );
		}
	}
}
do_css_on_save_page_option();

function get_eis_meta_boxes_css_vars() {
	global $eis_interface_tabs_options;
	$get_eis_interfaces = get_eis_interfaces();
	$css_vars_arr = array();
	foreach ($get_eis_interfaces as $interface_id => $eis_interface) {
		if ($eis_interface['type'] == 'meta_box') {
			$eis_get_meta_values = eis_get_meta_values($interface_id . '_eis_options');
			foreach ($eis_get_meta_values as $eis_meta_value) {
				if (!empty($eis_meta_value['meta_values'])) {
					foreach ($eis_meta_value['meta_values'] as $input_id => $input_value) {
						if (!is_eis_option_hidden($interface_id, $input_id)) {
							$eis_interfaces_options = get_register_eis_interfaces_options($interface_id, $input_id);
							$css_var = isset($eis_interfaces_options['css_var']) ? $eis_interfaces_options['css_var'] : null;
							$css_units = isset($eis_interfaces_options['css_units']) ? $eis_interfaces_options['css_units'] : null;
							$css_property = isset($eis_interfaces_options['css_property']) ? $eis_interfaces_options['css_property'] : null;
							$css_element = isset($eis_interfaces_options['css_element']) ? $eis_interfaces_options['css_element'] : null;
							$post_id = isset($eis_meta_value['post_id']) ? $eis_meta_value['post_id'] : null;
							if ($css_var != null || $css_property != null || $css_element != null) {
								$css_vars_arr[$post_id][] = array(
									'post_type' => $eis_meta_value['post_type'],
									'input_id' => $input_id,
									'input_value' => eis_clean_option_value($input_value, $input_id, true),
									'css_var' => $css_var,
									'css_units' => $css_units,
									'css_property' => $css_property,
									'css_element' => $css_element,
								);
							}
						}
					}
				}
			}
		}
	}
	create_eis_posts_css_files($css_vars_arr);
	return $css_vars_arr;
}

function create_eis_posts_css_files($vars) {
	$upload_dir = wp_upload_dir();
	if (!file_exists($upload_dir['basedir'] . '/easy-interface-settings/css-posts')) {
		mkdir($upload_dir['basedir'] . '/easy-interface-settings/css-posts', 0777, true);
	}
	foreach ($vars as $post_id => $post_vars) {
		if (!empty($post_vars)) {
			$vars_list = array();
			$properties_list = array();
			foreach ($post_vars as $var) {
				if ($var['css_var'] != null && $var['input_value'] != null) {
					$vars_list[$var['css_var']]['var'] = $var['input_value'];
					$vars_list[$var['css_var']]['units'] = $var['css_units'];
				}
			}
			$i_vars = 0;
			foreach ($post_vars as $var) {
				if ($var['css_property'] != null && $var['css_element'] != null && $var['input_value'] != null) {
					$properties_list[$i_vars]['css_element']= $var['css_element'];
					$properties_list[$i_vars]['css_property']= $var['css_property'];
					$properties_list[$i_vars]['input_value']= $var['input_value'];
					$properties_list[$i_vars]['css_units']= $var['css_units'];
					$i_vars++;
				}
			}

			if (!empty($vars_list) || !empty($properties_list)) {
				$post_type = $post_vars[0]['post_type'];
				$css_file = fopen( $upload_dir['basedir'] . '/easy-interface-settings/css-posts/' . $post_type . '-' . $post_id . '-eis-post-css.css', 'w') or die("Unable to open file!");
				$css_code = '';
				if (!empty($vars_list)) {
					$css_code .= ':root {'.PHP_EOL;
						foreach ($vars_list as $key => $var) {
							$css_code .= '	--' . $key . ': ' . eis_fix_var($key,$var['var']) . $var['units'] . ';'.PHP_EOL;
						}
					$css_code .= '}'.PHP_EOL;
				}
				if (!empty($properties_list)) {
					foreach ($properties_list as $var) {
						$css_propertys = explode(",", $var['css_property']);
						$css_propertys = array_filter(array_map('trim', $css_propertys));
						$css_code .= $var['css_element'] . ' {'.PHP_EOL;
						foreach ($css_propertys as $property) {
							$css_code .= '	' . $property . ': ' . eis_fix_var($property,$var['input_value']) . $var['css_units'] . ';'.PHP_EOL;
						}
						$css_code .= '}'.PHP_EOL;
					}
				}
				fwrite($css_file, $css_code);
				fclose($css_file);
			}
		} else {
			$post_type = $post_vars[0]['post_type'];
			unlink($upload_dir['basedir'] . '/easy-interface-settings/css-posts/' . $post_type . '-' . $post_id . '-eis-post-css.css');
		}
	}
}

function eis_do_css_on_save_post_option() {
	if (is_eis_page()) {
		get_eis_meta_boxes_css_vars();
	}
}
add_action( 'save_post', 'eis_do_css_on_save_post_option', 10, 3);

function eis_do_css_on_load_options() {
	get_eis_pages_css_vars();
	get_eis_meta_boxes_css_vars();
}
