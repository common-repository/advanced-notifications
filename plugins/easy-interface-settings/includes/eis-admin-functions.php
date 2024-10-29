<?php
/*
*
*	Easy Interface Settings V 1
*	------------------------------------------------
* 	Copyright Linker  - https://linker.co.il
*
*/

if (!function_exists('eis_interfaces')) {
	function eis_interfaces() {
		do_action('before_eis_register_interfaces');
		return (get_option('eis_register_interfaces') != null) ? get_option('eis_register_interfaces') : array();
	}
}

if (!function_exists('eis_is_plugin_active')) {
	function eis_is_plugin_active($plugin) {
		return in_array($plugin, (array) get_option('active_plugins', array()));
	}
}

function eis_build_function($function, $option) {
	if ( is_string( $function ) ) {
		if (function_exists($function)) {
			return $function($option);
		}
	}
	if ( is_object( $function ) ) {
		// Closures are currently implemented as objects.
		$function = array( $function, '' );
	} else {
		$function = (array) $function;
	}
	if ( is_object( $function[0] ) ) {
		// Object class calling.
		if (method_exists($function[0], $function[1])) {
			return $function[0]->{$function[1]}($option);
		}
	} elseif ( is_string( $function[0] ) ) {
		// Static calling.
		return $function[0] . '::' . $function[1]($option);
	}
}

function eis_sanitize_text_or_array_field($array_or_string) {
    if ( is_string($array_or_string) ) {
        $array_or_string = sanitize_text_field($array_or_string);
    } elseif ( is_array($array_or_string) ) {
        foreach ( $array_or_string as $key => &$value ) {
            if ( is_array( $value ) ) {
                $value = eis_sanitize_text_or_array_field($value);
            } else {
                $value = (strpos($key, '_eis_html') !== false) ? wp_kses_post($value) : sanitize_text_field( $value );
            }
        }
    }
    return $array_or_string;
}

function eis_interface_array($interface_id,$args=null) {
	$interface_array = array();
	$settings_title = (isset($args['settings_title'])) ? $args['settings_title'] : __('Settings', 'easy-interface-settings');
	$interface_array = array(
		'interface_id' => $interface_id,
		'type' => (isset($args['type'])) ? $args['type'] : 'page',
		'post_type' => (isset($args['post_type'])) ? $args['post_type'] : null,
		'meta_box_position' => (isset($args['meta_box_position'])) ? $args['meta_box_position'] : 'normal',
		'meta_box_priority' => (isset($args['meta_box_priority'])) ? $args['meta_box_priority'] : 'core',
		'show_tabs' => (isset($args['show_tabs'])) ? $args['show_tabs'] : true,
		'tab_dir' => (isset($args['tab_dir'])) ? $args['tab_dir'] : 'horizontal',
		'tab_save' => (isset($args['tab_save'])) ? $args['tab_save'] : true,
		'export_import' => (isset($args['export_import'])) ? $args['export_import'] : true,
		'theme' => (isset($args['theme'])) ? $args['theme'] : 'default',
		'settings_title' => $settings_title,
		'admin_menu_name' => (isset($args['admin_menu_name'])) ? $args['admin_menu_name'] : $settings_title,
		'admin_menu_parent' => (isset($args['admin_menu_parent'])) ? $args['admin_menu_parent'] : null,
		'admin_menu_icon' => (isset($args['admin_menu_icon'])) ? $args['admin_menu_icon'] : null,
		'capability' => (isset($args['capability'])) ? $args['capability'] : 'manage_options',
		'order' => (isset($args['order'])) ? $args['order'] : null,
	);
	return $interface_array;
}

function eis_register_interface($interface_id, $args=null) {
	global $eis_interfaces_global;
	$interface_id = preg_replace('!\s+!', '_', $interface_id);
	if(preg_match('/^[a-z0-9_\-]+$/i', $interface_id)) {
		$interface_array = eis_interface_array($interface_id, $args);
		$interface_type = $interface_array['type'];
		$eis_interfaces_global[$interface_type][$interface_id] = $interface_array;
		update_option('eis_register_interfaces', $eis_interfaces_global);
	}
}

function eis_register_setting() {
	$eis_interfaces = eis_interfaces();
	if (isset($eis_interfaces) && !empty($eis_interfaces)) {
		$eis_interface_pages = (isset($eis_interfaces['page'])) ? $eis_interfaces['page'] : array();
		if (!empty($eis_interface_pages)) {
			foreach ($eis_interface_pages as $eis_interface_page) {
				$interface_id = $eis_interface_page['interface_id'];
				register_setting( $interface_id, $interface_id . '_eis_options' );
			}
		}
	}
}
add_action( 'admin_init', 'eis_register_setting' );

function eis_include_interfaces() {
	$eis_child_theme_directory = get_stylesheet_directory() . '/easy-interface-settings/interfaces';
	if (file_exists($eis_child_theme_directory)) {
		foreach(glob($eis_child_theme_directory . '/*.php') as $filename) {
			$filename = basename($filename, ".php");
			include( $eis_child_theme_directory . '/' . $filename . '.php' );
		}
	}
}
eis_include_interfaces();

function is_eis_page() {
	$is_eis_page = false;
	$page_interface_id = (isset($_GET['page'])) ? sanitize_text_field($_GET['page']) : 'empty';
	if (!empty(get_eis_interfaces($page_interface_id))) {
		$is_eis_page = $page_interface_id;
	} else {
		$eis_interfaces = eis_interfaces();
		$eis_interfaces_meta_box = (!empty($eis_interfaces['meta_box'])) ? $eis_interfaces['meta_box'] : array();
		foreach ($eis_interfaces_meta_box as $interface) {
			$interface_id = $interface['interface_id'];
			$post_type = $interface['post_type'];
			$screen = (function_exists('get_current_screen')) ? get_current_screen() : null;
			if ( isset($screen->id) && $screen->id == $post_type ) {
				$is_eis_page = $interface_id;
			}
		}
	}
	return $is_eis_page;
}

function eis_interface_tab_option_html($interface_id, $option) {
	$input_id = $option['input_id'];
	$input_type = $option['input_type'];
	$placeholder = $option['placeholder'];
	$current_val = (strpos($input_id, '_eis_encrypt') !== false && !empty($option['value']) && !is_array($option['value'])) ? eis_openssl_decrypt($option['value']) : $option['value'];
	$current_val = ($current_val == null && $option['default'] != null) ? $option['default'] : $current_val;
	$current_val = (is_array($current_val)) ? array_map("esc_attr", $current_val) : esc_attr($current_val);
	if (is_array($current_val)) {
		$current_val = array_map("esc_attr", $current_val);
		if (strpos($input_id, '_eis_encrypt') !== false) {
			$current_val = array_map("eis_openssl_decrypt", $current_val);
		}
	} else {
		$current_val = esc_attr($current_val);
	}
	if ($option['parent_name'] != null && $option['show_on_value'] != null) {
		$show_on_value_arr = ($option['show_on_value'] != null) ? array_map('trim',explode(",",$option['show_on_value'])) : null;
		$show_on_value_arr = (is_array($show_on_value_arr)) ? preg_filter('/^/', $option['parent_name'] . '_', $show_on_value_arr) : null;
		$do_parent_option = ' data-parent-option="' . $option['parent_name'] . '" data-parent-value="' . implode(",", $show_on_value_arr) . '"';
	} elseif ($option['parent_name'] != null && $option['hide_on_value'] != null) {
		$hide_on_value_arr = ($option['hide_on_value'] != null) ? array_map('trim',explode(",",$option['hide_on_value'])) : null;
		$hide_on_value_arr = (is_array($hide_on_value_arr)) ? preg_filter('/^/', $option['parent_name'] . '_', $hide_on_value_arr) : null;
		$do_parent_option = ' data-parent-option="' . $option['parent_name'] . '" data-hide-value="' . implode(",", $hide_on_value_arr) . '"';
	} else {
		$do_parent_option = '';
	}
	if (is_eis_option_hidden($interface_id, $input_id)) {
		$hidden = ' hidden';
	} else {
		$hidden = '';
	}
	$data_option_name = ($input_id) ? ' data-option-name="' . $input_id . '"' : '';
	$languages = (EIS_IS_POLY) ? pll_languages_list() : array();
	$print_fun = '<tr class="form-field' . $hidden .'"' . $data_option_name . $do_parent_option . '>';
	$print_fun .= '	<th valign="top" scope="row">';
	$print_fun .= '		<b>' . $option['label'] . '</b>';
	$print_fun .= '		<p>' . $option['description'] . '</p>';
	$print_fun .= '	</th>';
	$print_fun .= '	<td>';
	if ($input_type == 'select') {
		$print_fun .= '		<select name="' . $interface_id . '_eis_options[' . $input_id . ']">';
		foreach ( $option['input_select_options'] as $select_value => $select_option ) {
			$selected = ($input_id . '_' . $select_value == $current_val || $select_value == $current_val) ? ' selected' : '';
			$disabled = (strpos($select_value, 'eis_disabled') !== false) ? ' disabled' : '';
			$print_fun .= '<option value="' . $input_id . '_' . $select_value . '"' . $selected . $disabled . '>' . $select_option . '</option>';
		}
		$print_fun .= '</select>';
	} elseif ($input_type == 'text') {
		if (EIS_IS_POLY && $option['multilingual']) {
			foreach ($languages as $language_name) {
				$print_fun .= '<span class="eis-language-slug">' . $language_name . '</span><input name="' . $interface_id . '_eis_options[' . $input_id . '_' . $language_name . ']" type="text" value="' . $current_val[$language_name] . '" placeholder="' . $placeholder . '">';
			}
		} else {
			$print_fun .= '<input name="' . $interface_id . '_eis_options[' . $input_id . ']" type="text" value="' . $current_val . '" placeholder="' . $placeholder . '">';
		}
	} elseif ($input_type == 'function') {
		$print_fun .= eis_build_function($option['function_name'], $option);
	} elseif ($input_type == 'image_preview' && $option['image_preview_url'] != null) {
		$print_fun .= '<img src="' . $option['image_preview_url'] . '" alt="" class="image-preview-url">';
	} elseif ($input_type == 'editor') {
		if (EIS_IS_POLY && $option['multilingual']) {
			foreach ($languages as $language_name) {
				$print_fun .= '<span class="eis-language-slug">' . $language_name . '</span>';
				$settings = array( 'textarea_name' => $interface_id . '_eis_options[' . $input_id . '_' . $language_name . ']' );
				ob_start(); // Start output buffer
				// Print the editor
				wp_editor( htmlspecialchars_decode($current_val[$language_name]), $input_id, $settings );
				// Store the printed data in $editor variable
				$print_fun .= ob_get_clean();
			}
		} else {
			$settings = array( 'textarea_name' => $interface_id . '_eis_options[' . $input_id . ']' );
			ob_start(); // Start output buffer
			// Print the editor
			wp_editor( htmlspecialchars_decode($current_val), $input_id, $settings );
			// Store the printed data in $editor variable
			$print_fun .= ob_get_clean();
		}
	} elseif ($input_type == 'password') {
		$print_fun .= '<input name="' . $interface_id . '_eis_options[' . $input_id . ']" type="password" value="' . $current_val . '" placeholder="' . $placeholder . '" autocomplete="new-password">';
	} elseif ($input_type == 'color') {
		$print_fun .= '<input name="' . $interface_id . '_eis_options[' . $input_id . ']" type="text" value="' . $current_val . '"  class="eis-color-picker">';
	} elseif ($input_type == 'image') {
		if (EIS_IS_POLY && $option['multilingual']) {
			foreach ($languages as $language_name) {
				if ($current_val[$language_name] != null && strpos($current_val[$language_name], 'data:') !== false) {
					$eis_file_name = '<a href="' . $current_val[$language_name] . '" target="_blank">' . mb_strimwidth($current_val[$language_name],0,70,'...','utf-8') . '</a>';
				} else {
					$eis_file_name = ($current_val[$language_name] != null) ? basename($current_val[$language_name]) : null;
				}
				$print_fun .= '<div class="image-field-container">';
				$print_fun .= '	<span class="eis-language-slug">' . $language_name . '</span>';
				$print_fun .= '	<input name="' . $interface_id . '_eis_options[' . $input_id . '_' . $language_name . ']" type="text" value="' . $current_val[$language_name] . '"  class="image-url" hidden />';
				$print_fun .= '	<img src="' . $current_val[$language_name] . '" alt="" target="_blank" rel="external" class="image-preview"><br>';
				$print_fun .= '	<input type="button" class="upload-button button" value="' . __('Upload Image', 'easy-interface-settings') . '" />';
				$print_fun .= '	<span class="button remove-image" rel="logo_upload">' . __('Remove', 'easy-interface-settings') . '</span>';
				$print_fun .= '	<div class="eis-file-name">' . $eis_file_name . '</div>';
				$print_fun .= '	</div>';
			}
		} else {
			if ($current_val != null && strpos($current_val, 'data:') !== false) {
				$eis_file_name = '<a href="' . $current_val . '" target="_blank">' . mb_strimwidth($current_val,0,70,'...','utf-8') . '</a>';
			} else {
				$eis_file_name = ($current_val != null) ? basename($current_val) : null;
			}
			$print_fun .= '<div class="image-field-container">';
			$print_fun .= '	<input name="' . $interface_id . '_eis_options[' . $input_id . ']" type="text" value="' . $current_val . '"  class="image-url" hidden />';
			$print_fun .= '	<img src="' . $current_val . '" alt="" target="_blank" rel="external" class="image-preview"><br>';
			$print_fun .= '	<input type="button" class="upload-button button" value="' . __('Upload Image', 'easy-interface-settings') . '" />';
			$print_fun .= '	<span class="button remove-image" rel="logo_upload">' . __('Remove', 'easy-interface-settings') . '</span>';
			// $eis_file_name = ($current_val != null) ? basename($current_val) : null;
			$print_fun .= '	<div class="eis-file-name">' . $eis_file_name . '</div>';
			$print_fun .= '	</div>';
		}
	} elseif ($input_type == 'gallery') {
		$print_fun .= '<div class="gallery-field-container">';
		$print_fun .= '	<input name="' . $interface_id . '_eis_options[' . $input_id . ']" type="text" value="' . $current_val . '"  id="gallery_ids" hidden />';
		$print_fun .= '	<div class="eis-gallery-src">';
		$print_fun .= '		<div class="eis-gallery-container">';
		$meta_array = array_filter(explode(',', $current_val));
		if (!empty($current_val)) {
			foreach ($meta_array as $meta_gall_item) {
				$print_fun .= '			<div class="eis-image-gallery-container"><span class="eis-gallery-close"><img id="' . esc_attr($meta_gall_item) . '" src="' . wp_get_attachment_thumb_url($meta_gall_item) . '"></span></div>';
			}
		}
		$print_fun .= '		</div>';
		$print_fun .= '	</div>';
		$print_fun .= '	<input type="button" class="gallery-button button" value="' . __('Upload Image', 'easy-interface-settings') . '" />';
		$print_fun .= '	<span class="button remove-gallery" rel="logo_upload">' . __('Remove', 'easy-interface-settings') . '</span>';
		$print_fun .= '</div>';
	} elseif ($input_type == 'number') {
		$min = ($option['min'] != null) ? ' min="' . $option['min'] . '"' : null;
		$max = ($option['max'] != null) ? ' max="' . $option['max'] . '"' : null;
		if (EIS_IS_POLY && $option['multilingual']) {
			foreach ($languages as $language_name) {
				$print_fun .= '<span class="eis-language-slug">' . $language_name . '</span><input name="' . $interface_id . '_eis_options[' . $input_id . '_' . $language_name . ']" type="number"' . $min . $max . ' value="' . $current_val[$language_name] . '" placeholder="' . $placeholder . '">';
			}
		} else {
			$print_fun .= '<input name="' . $interface_id . '_eis_options[' . $input_id . ']" type="number" step="any"' . $min . $max . ' value="' . $current_val . '" placeholder="' . $placeholder . '">';
		}
	} elseif ($input_type == 'checkbox') {
		if ($current_val == null) {
			$checked = ($option['checkbox_checked']) ? " checked" : null;
			$checkbox_val = ($option['checkbox_checked']) ? $input_id . '_checked' : 'no';
			$print_fun .= '<input name="' . $input_id . '_display_eis_checkbox" id="' . $input_id . '_display_eis_checkbox" type="checkbox" value="' . $input_id . '_checked"' . $checked . '><label for="' . $input_id . '_display_eis_checkbox">' . __('Yes', 'easy-interface-settings') . '</label>';
			$print_fun .= '<input name="' . $interface_id . '_eis_options[' . $input_id . ']" type="hidden" value="' . $checkbox_val . '">';
		} else {
			$checked = ($current_val == $input_id . '_checked') ? " checked" : null;
			$print_fun .= '<input name="' . $input_id . '_display_eis_checkbox" id="' . $input_id . '_display_eis_checkbox" type="checkbox" value="' . $input_id . '_checked"' . $checked . '><label for="' . $input_id . '_display_eis_checkbox">' . __('Yes', 'easy-interface-settings') . '</label>';
			$print_fun .= '<input name="' . $interface_id . '_eis_options[' . $input_id . ']" type="hidden" value="' . $current_val . '">';
		}
	} elseif ($input_type == 'textarea') {
		if (EIS_IS_POLY && $option['multilingual']) {
			foreach ($languages as $language_name) {
				$print_fun .= '<span class="eis-language-slug">' . $language_name . '</span><textarea rows="10" name="' . $interface_id . '_eis_options[' . $input_id . '_' . $language_name . ']" placeholder="' . $placeholder . '" style="direction: ltr;">' . $current_val[$language_name] . '</textarea>';
			}
		} else {
			$print_fun .= '<textarea rows="10" name="' . $interface_id . '_eis_options[' . $input_id . ']" placeholder="' . $placeholder . '" style="direction: ltr;">' . $current_val . '</textarea>';
		}
	} elseif ($input_type == 'html') {
		if (EIS_IS_POLY && $option['multilingual']) {
			foreach ($languages as $language_name) {
				$print_fun .= '<span class="eis-language-slug">' . $language_name . '</span><input name="' . $interface_id . '_eis_options[' . $input_id . '_' . $language_name . ']" type="text" value="' . $current_val[$language_name] . '" placeholder="' . $placeholder . '" dir="ltr">';
			}
		} else {
			$print_fun .= '<input name="' . $interface_id . '_eis_options[' . $input_id . ']" type="text" value="' . $current_val . '" placeholder="' . $placeholder . '" dir="ltr">';
		}
	} elseif ($input_type == 'date') {
		$print_fun .= '<div class= "eis-date-container"><input name="' . $interface_id . '_eis_options[' . $input_id . ']" type="text" value="' . $current_val . '" class="eis-date-input" placeholder="' . $placeholder . '"></div>';
	}
	$print_fun .= ($option['note'] != null) ? '<p class="description">' . $option['note'] . '</p>' : null;
	$print_fun .= '	</td>';
	$print_fun .= '</tr>';
	return $print_fun;
}

function eis_interface_tab_child_option_loop($interface_id, $get_eis_interface_options, $child_input_id) {
	global $eis_interface_tabs_options;
	$languages = (EIS_IS_POLY) ? pll_languages_list() : array();
	$site_lang = mb_substr(get_locale(), 0, 2, "UTF-8");
	$print_fun ='';
	foreach ($eis_interface_tabs_options[$interface_id] as $tab_id => $tab_options) {
		foreach ($tab_options as $option) {
			if ($child_input_id == $option['parent_name'] && $option['show_on_value'] != null) {
				$input_id = $option['input_id'];
				if ($option['input_type'] == 'password' || $option['encrypt']) {
					$input_id = $input_id . '_eis_encrypt';
					$option['input_id'] = $input_id;
				}
				if ($option['input_type'] == 'editor' || $option['input_type'] == 'html' || $option['input_type'] == 'textarea') {
					$input_id = $input_id . '_eis_html';
					$option['input_id'] = $input_id;
				}
				if (EIS_IS_POLY && $option['multilingual']) {
					$current_val = array();
					foreach ($languages as $language_name) {
						if (isset($get_eis_interface_options[$input_id . '_' . $language_name])) {
							$current_val[$language_name] = $get_eis_interface_options[$input_id . '_' . $language_name];

						} elseif (isset($get_eis_interface_options[$input_id])) {
							$current_val[$language_name] = $get_eis_interface_options[$input_id];
						} else {
							$current_val[$language_name] = null;
						}
					}
				} else {
					if (isset($get_eis_interface_options[$input_id])) {
						$current_val = $get_eis_interface_options[$input_id];
					} elseif (isset($get_eis_interface_options[$input_id . '_' . $site_lang])) {
						$current_val = $get_eis_interface_options[$input_id . '_' . $site_lang];
					} else {
						$current_val = null;
					}
				}
				$option['value'] = $current_val;
				$print_fun .= eis_interface_tab_option_html($interface_id, $option);
				$print_fun .= eis_interface_tab_child_option_loop($interface_id, $get_eis_interface_options, $input_id);
			}
		}
	}
	return $print_fun;
}

function eis_tabs_sort($eis_tabs) {
	$eis_tabs_sort = array();
	foreach ($eis_tabs as $interface_id => $interface_tabs) {
		usort($interface_tabs, function ($tab1, $tab2) {
			$order = $tab1['order'] <=> $tab2['order'];
			if ($order == 0) {
				$order = $tab1['sys_order'] <=> $tab2['sys_order'];
			}
			return $order;
		});
		$eis_tabs_sort[$interface_id] = $interface_tabs;
	}
	return $eis_tabs_sort;
}

function eis_tabs_options_sort($eis_interface_tabs_options) {
	$eis_interface_tabs_options_sort = array();
	foreach ($eis_interface_tabs_options as $interface_id => $interface_tabs) {
		foreach ($interface_tabs as $tab_id => $tab_options) {
			usort($tab_options, function ($option1, $option2) {
				$order = $option1['order'] <=> $option2['order'];
				if ($order == 0) {
					$order = $option1['sys_order'] <=> $option2['sys_order'];
				}
				return $order;
			});
			$eis_interface_tabs_options_sort[$interface_id][$tab_id] = $tab_options;
		}
	}
	return $eis_interface_tabs_options_sort;
}

function eis_tab($interface_id, $tab_id, $tab_name, $order=1, $hidden=false) {
	global $eis_tabs;
	global $eis_tabs_sys_order;
	$eis_tabs[$interface_id][] = array(
		'tab_id' => $tab_id,
		'tab_name' => $tab_name,
		'order' => $order,
		'sys_order' => $eis_tabs_sys_order,
		'hidden' => $hidden,
	);
	$eis_tabs_sys_order++;
	return $eis_tabs;
}

function eis_hidden_tab($interface_id,$tab_id,$hidden=true) {
	global $eis_hidden_tabs;
	$eis_hidden_tabs[$interface_id][$tab_id] = $hidden;
	return $eis_hidden_tabs;
}

function eis_remove_tab($interface_id,$tab_id) {
	global $eis_remove_tabs;
	$eis_remove_tabs[$interface_id][$tab_id] = $tab_id;
	return $eis_remove_tabs;
}

function eis_removing_tabs($eis_tabs,$eis_remove_tabs) {
	foreach ($eis_remove_tabs as $interface_id => $tabs_id_remove_arr) {
		if (isset($eis_tabs[$interface_id])) {
			foreach ($eis_tabs[$interface_id] as $key => $tabs_arr) {
				foreach ($tabs_id_remove_arr as $tab_id_remove => $tab_id_remove_value) {
					if ($tabs_arr['tab_id'] == $tab_id_remove) {
						unset($eis_tabs[$interface_id][$key]);
					}
				}
			}
		}
	}
	return $eis_tabs;
}

function eis_tab_option($args) {
	global $eis_interface_tabs_options;
	global $eis_options_sys_order;
	$eis_interface_tabs_options[$args['interface_id']][$args['tab_id']][] = array(
		'input_id' => (isset($args['input_id'])) ? $args['input_id'] : null,
		'input_type' => (isset($args['input_type'])) ? $args['input_type'] : null,
		'label' => (isset($args['label'])) ? $args['label'] : null,
		'description' => (isset($args['description'])) ? $args['description'] : null,
		'note' => (isset($args['note'])) ? $args['note'] : null,
		'placeholder' => (isset($args['placeholder'])) ? $args['placeholder'] : null,
		'input_select_options' => (isset($args['input_select_options'])) ? $args['input_select_options'] : null,
		'checkbox_checked' => (isset($args['checkbox_checked'])) ? $args['checkbox_checked'] : false,
		'parent_name' => (isset($args['parent_name'])) ? $args['parent_name'] : null,
		'show_on_value' => (isset($args['show_on_value'])) ? $args['show_on_value'] : null,
		'hidden_parent' => (isset($args['hidden_parent'])) ? $args['hidden_parent'] : null,
		'hide_on_value' => (isset($args['hide_on_value'])) ? $args['hide_on_value'] : null,
		'order' => (isset($args['order'])) ? $args['order'] : 5,
		'sys_order' => $eis_options_sys_order,
		'css_var' => (isset($args['css_var'])) ? $args['css_var'] : null,
		'css_units' => (isset($args['css_units'])) ? $args['css_units'] : null,
		'css_property' => (isset($args['css_property'])) ? $args['css_property'] : null,
		'css_element' => (isset($args['css_element'])) ? $args['css_element'] : null,
		'multilingual' => (isset($args['multilingual'])) ? $args['multilingual'] : false,
		'default' => (isset($args['default'])) ? $args['default'] : false,
		'min' => (isset($args['min'])) ? $args['min'] : null,
		'max' => (isset($args['max'])) ? $args['max'] : null,
		'shortcode' => (isset($args['shortcode'])) ? $args['shortcode'] : false,
		'encrypt' => (isset($args['encrypt'])) ? $args['encrypt'] : false,
		'hidden' => (isset($args['hidden'])) ? $args['hidden'] : false,
		'function_name' => (isset($args['function_name'])) ? $args['function_name'] : null,
		'function_args' => (isset($args['function_args'])) ? $args['function_args'] : array(),
		'image_preview_url' => (isset($args['image_preview_url'])) ? $args['image_preview_url'] : null,
	);
	$eis_options_sys_order++;
	return $eis_interface_tabs_options;
}

function eis_option_default() {
	global $eis_interface_tabs_options;
	$default_arr = array();
	$is_default = false;
	foreach ($eis_interface_tabs_options as $interface_id => $interface) {
		foreach ($interface as $tab_id => $options) {
			foreach ($options as $input) {
				$input_id = $input['input_id'];
				$input_default = $input['default'];
				if ($input_default != null) {
					$default_arr[$interface_id][$input_id] = $input_default;
					$is_default = true;
				}
			}
		}
	}
	if ($is_default) {
		update_option('eis_interfaces_default', $default_arr);
	} else {
		delete_option('eis_interfaces_default');
	}
}

function eis_option_shortcode() {
	global $eis_interface_tabs_options;
	$shortcode_arr = array();
	$is_shortcode = false;
	foreach ($eis_interface_tabs_options as $interface_id => $interface) {
		foreach ($interface as $tab_id => $options) {
			foreach ($options as $input) {
				$input_id = $input['input_id'];
				$input_shortcode = $input['shortcode'];
				if ($input_shortcode) {
					$shortcode_arr[$interface_id][$input_id] = $input_shortcode;
					$is_shortcode = true;
				}
			}
		}
	}
	if ($is_shortcode) {
		update_option('eis_interfaces_shortcode', $shortcode_arr);
	} else {
		delete_option('eis_interfaces_shortcode');
	}
}

function eis_remove_tab_option($interface_id,$input_id) {
	global $eis_remove_interface_tabs_options;
	$eis_remove_interface_tabs_options[$interface_id][$input_id] = $input_id;
	return $eis_remove_interface_tabs_options;
}

function eis_removing_tabs_options($eis_interface_tabs_options,$eis_remove_interface_tabs_options) {
	foreach ($eis_remove_interface_tabs_options as $interface_id => $inputs_id_remove_arr) {
		if (isset($eis_interface_tabs_options[$interface_id])) {
			foreach ($eis_interface_tabs_options[$interface_id] as $tab_id => $tab_arr) {
				foreach ($tab_arr as $key => $inputs_arr) {
					foreach ($inputs_id_remove_arr as $input_id_remove => $input_remove_value) {
						if ($inputs_arr['input_id'] == $input_id_remove) {
							unset($eis_interface_tabs_options[$interface_id][$tab_id][$key]);
						}
					}
				}
			}
		}
	}
	return $eis_interface_tabs_options;
}

function eis_interface_tabs_options($interface_id) {
	global $eis_interface_tabs_options;
	global $eis_remove_interface_tabs_options;
	if (is_array($eis_remove_interface_tabs_options)) {
		$eis_interface_tabs_options = eis_removing_tabs_options($eis_interface_tabs_options,$eis_remove_interface_tabs_options);
	}
	$eis_interface_tabs_options = eis_tabs_options_sort($eis_interface_tabs_options);
	$eis_interface_tabs_options_interface_id = (isset($eis_interface_tabs_options[$interface_id])) ? $eis_interface_tabs_options[$interface_id] : array();
	$eis_interface_tabs_options_print = array();
	$interface_type = get_eis_interfaces($interface_id)['type'];
	if ($interface_type == 'page') {
		$get_eis_interface_options = get_option($interface_id . '_eis_options');
	} elseif ($interface_type == 'meta_box') {
		$get_eis_interface_options = get_post_meta(get_the_ID(), $interface_id . '_eis_options', true);
		// $get_eis_interface_options = (isset($get_eis_interface_options[0])) ? $get_eis_interface_options[0] : null;
	}
	$languages = (EIS_IS_POLY) ? pll_languages_list() : array();
	$site_lang = mb_substr(get_locale(), 0, 2, "UTF-8");
	eis_option_default();
	eis_option_shortcode();
	foreach ($eis_interface_tabs_options_interface_id as $tab_id => $tab_options) {
		foreach ($tab_options as $option) {
			$input_id = (isset($option['input_id'])) ? $option['input_id'] : null;
			if ($option['input_type'] == 'password' || $option['encrypt']) {
				$input_id = $input_id . '_eis_encrypt';
				$option['input_id'] = $input_id;
			}
			if ($option['input_type'] == 'editor' || $option['input_type'] == 'html' || $option['input_type'] == 'textarea') {
				$input_id = $input_id . '_eis_html';
				$option['input_id'] = $input_id;
			}
			if ($option['show_on_value'] == null) {
				if (EIS_IS_POLY && $option['multilingual']) {
					$current_val = array();
					foreach ($languages as $language_name) {
						if (isset($get_eis_interface_options[$input_id . '_' . $language_name])) {
							$current_val[$language_name] = $get_eis_interface_options[$input_id . '_' . $language_name];

						} elseif (isset($get_eis_interface_options[$input_id])) {
							$current_val[$language_name] = $get_eis_interface_options[$input_id];
						} else {
							$current_val[$language_name] = null;
						}
					}
				} else {
					if (isset($get_eis_interface_options[$input_id])) {
						$current_val = $get_eis_interface_options[$input_id];
					} elseif (isset($get_eis_interface_options[$input_id . '_' . $site_lang])) {
						$current_val = $get_eis_interface_options[$input_id . '_' . $site_lang];
					} else {
						$current_val = null;
					}
				}
				$option['value'] = $current_val;
				$print_fun = eis_interface_tab_option_html($interface_id, $option);
				if ($input_id != null) {
					$print_fun .= eis_interface_tab_child_option_loop($interface_id, $get_eis_interface_options, $input_id);
				}
				$eis_interface_tabs_options_print[$tab_id][] = $print_fun;
			}
		}
	}
	return $eis_interface_tabs_options_print;
}

function eis_interface_tabs_options_print($interface_id) {
	global $eis_tabs;
	global $eis_hidden_tabs;
	global $eis_remove_tabs;
	if (is_array($eis_remove_tabs)) {
		$eis_tabs = eis_removing_tabs($eis_tabs,$eis_remove_tabs);
	}
	$eis_tabs = eis_tabs_sort($eis_tabs);
	$eis_interface_tabs_options_print = eis_interface_tabs_options($interface_id);
	$interface_array = get_eis_interfaces($interface_id);
	$eis_current_tab = ($interface_array['tab_save']) ? get_user_option($interface_id . '_eis_current_tab', get_current_user_id()) : null;
	if ($interface_array['type'] == 'page') {
		settings_fields( $interface_id );
		do_settings_sections( $interface_id );
	}
	$print_fun = '';
	if ($interface_array['show_tabs']) {
		$print_fun .= '<ul class="tabs" data-interface-id="' . esc_attr($interface_id) . '" data-save-tab="' . esc_attr($interface_array['tab_save']) . '">';
		if (!isset($eis_tabs[$interface_id])) return;
		foreach ($eis_tabs[$interface_id] as $key => $eis_tab) {
			if ($eis_current_tab == null && $key == 0) {
				$current_tab = ' active';
			} elseif ($eis_current_tab == 'eis-' . $eis_tab['tab_id']) {
				$current_tab = ' active';
			} else {
				$current_tab = '';
			}
			$eis_hidden_tab = (isset($eis_hidden_tabs[$interface_id][$eis_tab['tab_id']])) ? $eis_hidden_tabs[$interface_id][$eis_tab['tab_id']] : 'unset';
			$eis_hidden_tab = ($eis_hidden_tab != 'unset') ? $eis_hidden_tab : $eis_tab['hidden'];
			$admin_hidden = ($eis_hidden_tab) ? ' style="display: none;"' : null;
			$print_fun .= '<li' . $admin_hidden . '><a href="#" class="eis-' . esc_attr($eis_tab['tab_id']) . $current_tab . '" data-tab-id="eis-' . esc_attr($eis_tab['tab_id']) . '">' . esc_attr($eis_tab['tab_name']) . '</a></li>';
		}
		$print_fun .= '</ul>';
	}
	foreach ($eis_tabs[$interface_id] as $key => $eis_tab) {
		$tab_options = (isset($eis_interface_tabs_options_print[$eis_tab['tab_id']])) ? $eis_interface_tabs_options_print[$eis_tab['tab_id']] : array();
		$eis_hidden_tab = (isset($eis_hidden_tabs[$interface_id][$eis_tab['tab_id']])) ? $eis_hidden_tabs[$interface_id][$eis_tab['tab_id']] : 'unset';
		$eis_hidden_tab = ($eis_hidden_tab != 'unset') ? $eis_hidden_tab : $eis_tab['hidden'];
		if ($eis_current_tab == null && $key == 0 && !$eis_hidden_tab || !$interface_array['show_tabs']) {
			$current_tab = ' style="display: block;"';
		} elseif ($eis_current_tab == 'eis-' . $eis_tab['tab_id'] && !$eis_hidden_tab) {
			$current_tab = ' style="display: block;"';
		} else {
			$current_tab = ' style="display: none;"';
		}
		$print_fun .= '<div id="eis-' . $eis_tab['tab_id'] . '" class="' . ($interface_array['show_tabs'] ? 'tab-content' : 'eis-page-content') . '"' . $current_tab . '>';
		if ($interface_array == 'page') {
			$print_fun .= '	<h2>' . esc_html($eis_tab['tab_name']) . '</h2>';
		}
		$print_fun .= '	<table cellspacing="2" cellpadding="5" style="width: 100%;" class="form-table">';
		$print_fun .= '		<tbody>';
		foreach ($tab_options as $option) {
			$print_fun .= $option;
		}
		$print_fun .= '		</tbody>';
		$print_fun .= '	</table>';
		$print_fun .= '</div>';
	}
	echo $print_fun;
}

function get_register_eis_interfaces_options($interface_id, $input_id=null) {
	global $eis_interface_tabs_options;
	$register_eis_interfaces_options = array();
	if ($input_id == null && !empty($eis_interface_tabs_options[$interface_id])) {
		foreach ($eis_interface_tabs_options[$interface_id] as $tab_id => $tab_options) {
			foreach ($tab_options as $option) {
				$register_eis_interfaces_options[$tab_id][] = $option;
			}
		}
	} elseif ($input_id != null && !empty($eis_interface_tabs_options[$interface_id])) {
		foreach ($eis_interface_tabs_options[$interface_id] as $tab_id => $tab_options) {
			foreach ($tab_options as $option) {
				if ($input_id == $option['input_id']) {
					$register_eis_interfaces_options = $option;
					$register_eis_interfaces_options['tab_id'] = $tab_id;
				}
			}
		}
	}
	return $register_eis_interfaces_options;
}

function is_eis_option_hidden($current_interface_id, $current_input_id) {
	global $post;
	$post_id = (isset($post->ID)) ? $post->ID : null;
	$return = false;
	$current_input_register_info = get_register_eis_interfaces_options($current_interface_id, $current_input_id);
	$current_input_parent_name = (isset($current_input_register_info['parent_name'])) ? $current_input_register_info['parent_name'] : null;
	$current_input_show_on_value = (isset($current_input_register_info['show_on_value'])) ? array_map('trim',explode(",",$current_input_register_info['show_on_value'])) : null;
	$current_input_hide_on_value = (isset($current_input_register_info['hide_on_value'])) ? array_map('trim',explode(",",$current_input_register_info['hide_on_value'])) : null;
	if ($current_input_parent_name != null && $current_input_show_on_value != null) {
		$current_input_parent_name_info = get_register_eis_interfaces_options($current_interface_id, $current_input_parent_name);
		$current_input_parent_name_val = eis_get_option($current_interface_id, $current_input_parent_name, $post_id);
		$current_input_parent_name_val = str_replace(' ', '_', $current_input_parent_name_val);
		if ($current_input_parent_name_val != null && !in_array($current_input_parent_name_val, $current_input_show_on_value)) {
			$return = true;
		} elseif ($current_input_parent_name_val == null && isset($current_input_parent_name_info['input_type']) && $current_input_parent_name_info['input_type'] == 'select') {
			$current_input_parent_name_first_select = (!empty($current_input_parent_name_info['input_select_options']) && function_exists('array_key_first')) ? array_key_first($current_input_parent_name_info['input_select_options']) : null;
			if (!in_array($current_input_parent_name_first_select, $current_input_show_on_value)) {
				$return = true;
			}
		}
	} elseif ($current_input_parent_name != null && $current_input_hide_on_value != null) {
		$current_input_parent_name_info = get_register_eis_interfaces_options($current_interface_id, $current_input_parent_name);
		$current_input_parent_name_val = eis_get_option($current_interface_id, $current_input_parent_name, $post_id);
		$current_input_parent_name_val = str_replace(' ', '_', $current_input_parent_name_val);
		if ($current_input_parent_name_val != null && in_array($current_input_parent_name_val, $current_input_hide_on_value)) {
			$return = true;
		} elseif ($current_input_parent_name_val == null && isset($current_input_parent_name_info['input_type']) && $current_input_parent_name_info['input_type'] == 'select') {
			$current_input_parent_name_first_select = (!empty($current_input_parent_name_info['input_select_options']) && function_exists('array_key_first')) ? array_key_first($current_input_parent_name_info['input_select_options']) : null;
			if (in_array($current_input_parent_name_first_select, $current_input_hide_on_value)) {
				$return = true;
			}
		}
	}
	return $return;
}

function eis_encrypt_options_before_save($value, $option, $old_value) {
	if (strpos($option, '_eis_options') !== false) {
		if (is_array($value)) {
			foreach ($value as $key => $eis_option) {
				if (strpos($key, '_eis_encrypt') !== false) {
					$value[$key] = eis_openssl_encrypt($eis_option);
				}
			}
		}
	}
	return $value;
}
add_filter('pre_update_option', 'eis_encrypt_options_before_save', 10, 3);

function eis_hide_interface($interface_id) {
	global $eis_hide_interface;
	$eis_hide_interface[$interface_id] = $interface_id;
	return $eis_hide_interface;
}

function eis_admin_menu() {
	global $interface_capability;
    $eis_interfaces = eis_interfaces();
    $register_pages = (isset($eis_interfaces['page'])) ? $eis_interfaces['page'] : array();
    if (!empty($register_pages)) {
        foreach ($eis_interfaces['page'] as $register_page) {
            $order = isset($register_page['order']) ? $register_page['order'] : null;
            $admin_menu_icon = ($register_page['admin_menu_parent'] != null && $register_page['admin_menu_icon'] != null) ? '<span class="dashicons ' . $register_page['admin_menu_icon'] . '" style="font-size: 17px"></span>' : null;
			$page_capability = isset($register_page['capability']) ? $register_page['capability'] : 'manage_options';
			if ($register_page['admin_menu_parent'] == null) {
                add_menu_page($register_page['settings_title'], $register_page['admin_menu_name'], $page_capability, $register_page['interface_id'], 'eis_page_interface', $admin_menu_icon, $order);
            } else {
                add_submenu_page($register_page['admin_menu_parent'], $register_page['settings_title'], $admin_menu_icon . $register_page['admin_menu_name'], $page_capability, $register_page['interface_id'], 'eis_page_interface', $order);
            }
			$interface_capability[$register_page['interface_id']] = $page_capability;
			add_filter( 'option_page_capability_' . $register_page['interface_id'], function( $capability ) {
				global $interface_capability;
				$interface_id = isset($_POST['option_page']) ? $_POST['option_page'] : '';
				$capability = isset($interface_capability[$interface_id]) ? $interface_capability[$interface_id] : $capability;
				return $capability;
			});
        }
    }
}
add_action( 'admin_menu', 'eis_admin_menu' );

function eis_page_interface() {
	$interface_id = sanitize_text_field($_GET['page']);
	// $page_interface_option = get_save_eis_interfaces_options($interface_id);
	// if ($page_interface_option == null) {
	// 	update_option($interface_id . '_eis_options' , null);
	// }
	$interface_info = get_eis_interfaces($interface_id);
	$interface_option = eis_get_option($interface_id);
	ob_start();
	settings_errors();
	$settings_errors = ob_get_clean();
	$file_path = eis_get_theme_file_path();
	if (file_exists($file_path)) {
		include_once $file_path;
	}
	add_action( 'admin_footer', 'eis_page_loader' );
}

function eis_create_meta_boxes($post_type, $post) {
	global $eis_hide_interface;
	$eis_interfaces = eis_interfaces();
	if (isset($eis_interfaces['meta_box']) && is_array($eis_interfaces['meta_box'])) {
		foreach ($eis_interfaces['meta_box'] as $interface) {
			$interface_id = $interface['interface_id'];
			$interface_post_type = $interface['post_type'];
			if (!isset($eis_hide_interface[$interface_id]) && $interface_post_type == $post_type) {
				$settings_title = $interface['settings_title'];
				$meta_box_position = $interface['meta_box_position'];
				$meta_box_priority = $interface['meta_box_priority'];
				add_meta_box($interface_id, $settings_title, 'eis_meta_box_interface', $interface_post_type, $meta_box_position, $meta_box_priority, null);
			}
		}
	}
}
add_action( 'add_meta_boxes', 'eis_create_meta_boxes', 10, 2 );

function eis_meta_box_interface($post_info, $meta_box_info) {
	$interface_id = $meta_box_info['id'];
	$eis_interfaces = eis_interfaces();
	$interface = $eis_interfaces['meta_box'][$interface_id];
	$post_type = $interface['post_type'];
	wp_nonce_field(basename(__FILE__) . $interface_id, "eis-meta-box-nonce_" . $interface_id);
	?>
	<div class="eis-form" data-form="<?php echo esc_attr($post_type); ?>">
		<input type="hidden" name="<?php echo esc_attr($interface_id); ?>_prevent_delete_meta_movetotrash" id="<?php echo esc_attr($interface_id); ?>_prevent_delete_meta_movetotrash" value="<?php echo wp_create_nonce(esc_attr($interface_id)); ?>" />
		<div class="eis-interface-settings eis-meta-box<?php echo (!$interface['show_tabs'] ? ' no-tabs' : null) ?>" data-ajax-url="<?php echo admin_url('admin-ajax.php'); ?>">
			<?php
			$file_path = eis_get_theme_file_path();
			if (file_exists($file_path)) {
				include_once $file_path;
			}
			?>
		</div>
	</div>
	<?php
}

function save_eis_meta_boxes( $post_id, $post) {
	$eis_interfaces = eis_interfaces();
	$post_type = get_post_type($post_id);
	if (isset($eis_interfaces['meta_box']) && is_array($eis_interfaces['meta_box'])) {
		foreach ($eis_interfaces['meta_box'] as $interface) {
			$interface_id = $interface['interface_id'];
			$meta_box_position = $interface['meta_box_position'];
			$meta_box_priority = $interface['meta_box_priority'];
			if ($interface['post_type'] == $post_type && isset($_POST[$interface_id . '_eis_options'])) {
				if (!isset($_POST["eis-meta-box-nonce_" . $interface_id]) || !wp_verify_nonce($_POST["eis-meta-box-nonce_" . $interface_id], basename(__FILE__) . $interface_id)) {
					return $post_id;
				}
				$prevent_delete_meta_movetotrash = (isset($_POST[$interface_id . '_prevent_delete_meta_movetotrash'])) ? sanitize_text_field($_POST[$interface_id . '_prevent_delete_meta_movetotrash']) : null;
				if (!wp_verify_nonce($prevent_delete_meta_movetotrash, $interface_id)) { return $post_id; }

				$get_eis_interface_form_options = eis_sanitize_text_or_array_field($_POST[$interface_id . '_eis_options']);
				if (is_array($get_eis_interface_form_options)) {
					foreach ($get_eis_interface_form_options as $key => $eis_option) {
						if (strpos($key, '_eis_encrypt') !== false) {
							$get_eis_interface_form_options[$key] = eis_openssl_encrypt($eis_option);
						}
					}
					update_post_meta($post_id, $interface_id . '_eis_options', $get_eis_interface_form_options);
				}
			}
		}
	}
}
add_action( 'save_post', 'save_eis_meta_boxes', 10, 3);
