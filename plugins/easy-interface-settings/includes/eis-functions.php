<?php
/*
*
*	Easy Interface Settings V 1
*	------------------------------------------------
* 	Copyright Linker  - https://linker.co.il
*
*/

function eis_add_cache($name,$value) {
    global $eis_cache;
    $eis_cache[$name] = $value;
}

function eis_get_cache($name) {
    global $eis_cache;
    return (isset($eis_cache[$name]) ? $eis_cache[$name] : false);
}

function eis_interfaces($cache=true) {
	$eis_register_interfaces = eis_get_cache('eis_register_interfaces');
	if (!$eis_register_interfaces || !$cache) {
		$eis_register_interfaces = get_option('eis_register_interfaces');
        if ($cache) {
            eis_add_cache('eis_register_interfaces', $eis_register_interfaces);
        }
	}
	return ($eis_register_interfaces != '') ? $eis_register_interfaces : array();
}

function get_eis_interfaces($interface_id=null) {
	$eis_interfaces = eis_interfaces();
	$eis_interfaces_arr = array();
	foreach ($eis_interfaces as $type => $eis_interface) {
		foreach ($eis_interface as $value) {
			if ($interface_id == null) {
                $eis_interfaces_arr[$value['interface_id']] = $value;
			} elseif ($interface_id == $value['interface_id']) {
				$eis_interfaces_arr = $value;
			}
		}
	}
	return $eis_interfaces_arr;
}

function eis_clean_option_value($input_value, $input_id, $lowercase=false) {
	if (!empty($input_value) && !is_array($input_value)) {
		if (strpos($input_value, $input_id) !== false) {
			$input_value = str_replace($input_id . '_' ,"" , $input_value);
			$input_value = str_replace('_' ," " , $input_value);
			$input_value = ($lowercase == true) ? lcfirst($input_value) : ucfirst($input_value);
		}
	}
	return $input_value;
}

function eis_get_option_default($interface_id=null,$input_id=null,$cache=true) {
	$return = false;
	if ($interface_id != null && $input_id != null) {
		$eis_interfaces_default = eis_get_cache('eis_option_default');
		if (!$eis_interfaces_default || !$cache) {
			$eis_interfaces_default = get_option('eis_interfaces_default');
            if ($cache) {
                eis_add_cache('eis_option_default', $eis_interfaces_default);
            }
		}
		// $eis_interfaces_default = get_option('eis_interfaces_default');
		if (isset($eis_interfaces_default[$interface_id]) && isset($eis_interfaces_default[$interface_id][$input_id])) {
			$return = $eis_interfaces_default[$interface_id][$input_id];
		}
	}
	return $return;
}

function eis_is_option_has_shortcode($interface_id=null,$input_id=null,$cache=true) {
	$return = false;
	if ($interface_id != null && $input_id != null) {
		$eis_interfaces_shortcode = eis_get_cache('eis_option_has_shortcode');
		if (!$eis_interfaces_shortcode || !$cache) {
			$eis_interfaces_shortcode = get_option('eis_interfaces_shortcode');
            if ($cache) {
                eis_add_cache('eis_option_has_shortcode', $eis_interfaces_shortcode);
            }
		}
		// $eis_interfaces_shortcode = get_option('eis_interfaces_shortcode');
		if (isset($eis_interfaces_shortcode[$interface_id]) && isset($eis_interfaces_shortcode[$interface_id][$input_id])) {
			$return = true;
		}
	}
	return $return;
}

function get_save_eis_interfaces_options($interface_id, $input_id=null, $post_id=null, $lowercase=false, $cache=true) {
	$eis_get_save_option = eis_get_cache('eis_get_save_option');
	$cache_name = ($post_id != null) ? $interface_id . '_' . $post_id : $interface_id;
	if (!isset($eis_get_save_option[$cache_name]) || !$cache) {
		$interface_type = (isset(get_eis_interfaces($interface_id)['type'])) ? get_eis_interfaces($interface_id)['type'] : null;
		if ($interface_type == 'page') {
			$option = get_option($interface_id . '_eis_options');
		} elseif ($interface_type == 'meta_box') {
			$option = get_post_meta($post_id, $interface_id . '_eis_options', true);
		} else {
            $option = null;
        }
		$eis_get_save_option = array(
            $cache_name => array(
                'site_lang' => mb_substr(get_locale(), 0, 2, "UTF-8"),
                'interface_type' => $interface_type,
                'option' => $option,
            )
        );
        if ($cache) {
            eis_add_cache('eis_get_save_option', $eis_get_save_option);
        }
	}
	$site_lang = $eis_get_save_option[$cache_name]['site_lang'];
	$interface_type = $eis_get_save_option[$cache_name]['interface_type'];
    if ($input_id == null) {
        $eis_interface_options = $eis_get_save_option[$cache_name]['option'];
    } else {
        if (isset($eis_get_save_option[$cache_name]['option'][$input_id])) {
            $eis_interface_options = $eis_get_save_option[$cache_name]['option'][$input_id];
        } elseif (isset($eis_get_save_option[$cache_name]['option'][$input_id . '_eis_html'])) {
            $eis_interface_options = $eis_get_save_option[$cache_name]['option'][$input_id . '_eis_html'];
        } elseif (isset($eis_get_save_option[$cache_name]['option'][$input_id . '_eis_html_' . $site_lang])) {
            $eis_interface_options = $eis_get_save_option[$cache_name]['option'][$input_id . '_eis_html_' . $site_lang];
        } elseif (isset($eis_get_save_option[$cache_name]['option'][$input_id . '_eis_encrypt'])) {
            $eis_interface_options = $eis_get_save_option[$cache_name]['option'][$input_id . '_eis_encrypt'];
            //Do decrypt
            $eis_interface_options = eis_openssl_decrypt($eis_interface_options);
        } elseif (isset($eis_get_save_option[$cache_name]['option'][$input_id . '_eis_encrypt_eis_html'])) {
            $eis_interface_options = $eis_get_save_option[$cache_name]['option'][$input_id . '_eis_encrypt_eis_html'];
            //Do decrypt
            $eis_interface_options = eis_openssl_decrypt($eis_interface_options);
        } elseif (isset($eis_get_save_option[$cache_name]['option'][$input_id . '_eis_encrypt_eis_html_' . $site_lang])) {
            $eis_interface_options = $eis_get_save_option[$cache_name]['option'][$input_id . '_eis_encrypt_eis_html_' . $site_lang];
            //Do decrypt
            $eis_interface_options = eis_openssl_decrypt($eis_interface_options);
        } elseif (isset($eis_get_save_option[$cache_name]['option'][$input_id . '_' . $site_lang])) {
            $eis_interface_options = $eis_get_save_option[$cache_name]['option'][$input_id . '_' . $site_lang];
        } else {
            $eis_interface_options = null;
        }
    }
	if ($input_id != null || $post_id != null) {
		$eis_interface_options = eis_clean_option_value($eis_interface_options, $input_id, $lowercase);
	}
	$eis_is_option_default = eis_get_option_default($interface_id, $input_id);
	if ($eis_is_option_default && $eis_interface_options == null) {
		$eis_interface_options = $eis_is_option_default;
	}
	if (eis_is_option_has_shortcode($interface_id, $input_id)) {
		return do_shortcode($eis_interface_options);
	} else {
		return $eis_interface_options;
	}
}

function eis_get_option($interface_id, $input_id=null, $post_id=null, $lowercase=true, $cache=true) {
	return get_save_eis_interfaces_options($interface_id, $input_id, $post_id, $lowercase, $cache);
}

function eis_get_gallery($interface_id, $input_id=null, $post_id=null, $lowercase=true) {
	$get_save_eis_interfaces_options = get_save_eis_interfaces_options($interface_id, $input_id, $post_id, $lowercase);
	$gallery_ids = explode(",", $get_save_eis_interfaces_options);
	$return = array();
	if (!empty($gallery_ids)) {
		$gallery_ids = array_filter($gallery_ids);
		if (!empty($gallery_ids)) {
			foreach ($gallery_ids as $image_id) {
				$return['id'][] = $image_id;
				$return['url'][] = wp_get_attachment_url($image_id);
			}
		}
	}
	return $return;
}

function eis_save_register_post_types() {
	$eis_register_post_types = array();
	$post_types = get_post_types();
	$post_types_remove = array("oembed_cache", "user_request", "custom_css", "attachment","nav_menu_item","customize_changeset","revision");
	foreach ($post_types as $post_type) {
		if (!in_array($post_type, $post_types_remove)) {
			$eis_register_post_types[] = $post_type;
		}
	}
	update_option('eis_register_post_types', $eis_register_post_types);
	return $eis_register_post_types;
}

function eis_get_meta_values( $meta_key,  $post_type = null ) {
	$meta_values = array();
	if ($post_type != null) {
		$posts = get_posts(
	        array(
	            'post_type' => $post_type,
	            'meta_key' => $meta_key,
	            'posts_per_page' => -1,
	        )
	    );
	    foreach( $posts as $post ) {
			$post_id = $post->ID;
	        $meta_values[] = array(
				'post_id' => $post_id,
				'post_type' => $post_type,
				'meta_values' => get_post_meta( $post_id, $meta_key, true ),
			);
	    }
	} else {
		add_action('init', 'eis_save_register_post_types');
		$eis_register_post_types = get_option('eis_register_post_types');
		$eis_register_post_types = ($eis_register_post_types != null) ? $eis_register_post_types : array();
		foreach ( $eis_register_post_types as $post_type ) {
			$posts = get_posts(
		        array(
		            'post_type' => $post_type,
		            'meta_key' => $meta_key,
		            'posts_per_page' => -1,
		        )
		    );
		    foreach( $posts as $post ) {
				$post_id = $post->ID;
		        $meta_values[] = array(
					'post_id' => $post_id,
					'post_type' => $post_type,
					'meta_values' => get_post_meta( $post_id, $meta_key, true ),
				);
		    }
		}
	}
    return $meta_values;
}

function eis_after_wp_loaded() {
	if (is_admin()) {
		do_action('add_eis_register_interface');
		do_action('add_eis_interface_options');
	}
}
add_action( 'wp_loaded', 'eis_after_wp_loaded' );

function eis_save_meta_box_options($post_id=null,$interface_id=null,$value=null) {
	if ($post_id != null && $interface_id != null) {
		update_post_meta($post_id, $interface_id . '_eis_options', $value);
	}
}

function eis_openssl_encrypt($value_to_encrypt, $method=EIS_ENCRYPTION_METHOD, $secret_key=EIS_SECRET_KEY) {
	return (!empty($value_to_encrypt)) ? @openssl_encrypt(serialize($value_to_encrypt), $method, $secret_key, 0, null) : null;
}

function eis_openssl_decrypt($value_to_decrypt, $method=EIS_ENCRYPTION_METHOD, $secret_key=EIS_SECRET_KEY) {
	return (!empty($value_to_decrypt)) ? unserialize(@openssl_decrypt($value_to_decrypt, $method, $secret_key, 0, null)) : null;
}

function eis_page_loader() {
    echo '<div class="eis-loader-container eis-page-loader"><div class="eis-loader"></div></div>';
}

function eis_save_interface_settings() {
    $interface_id = sanitize_text_field($_POST['interface_id']);
    $settings = json_decode(stripslashes(sanitize_text_field($_POST['settings'])), true);
    if (!empty($settings) && is_array($settings)) {
        update_option($interface_id . '_eis_options' , $settings);
    }
    wp_die();
}
add_action( 'wp_ajax_eis_save_interface_settings', 'eis_save_interface_settings' );
