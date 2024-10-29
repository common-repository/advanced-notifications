<?php
/*
*
*	Advanced Notifications V 1
*	------------------------------------------------
* 	Powered by  - https://wiliba.com
*
*/
global $an_functions;
class AdvancedNotificationsFunctions
{
    function __construct() {
    	add_action( 'admin_enqueue_scripts', array($this, 'an_admin_enqueue' ) );
    	add_action( 'wp_enqueue_scripts', array($this, 'an_enqueue' ), 20 );
        add_action( 'wp_ajax_an_ajax', array($this, 'an_ajax') );
        add_action( 'an_add_ajax', array($this, 'an_save_notification_status'), 10, 3 );
        add_action( 'wp_footer', array($this, 'an_wp_footer') );
        add_action( 'admin_footer', array($this, 'an_wp_footer') );
    	// add_action( 'init', array($this, 'an_notification_set_cookie' ), 10 );
        add_action( 'admin_menu', array($this, 'an_pro_admin_menu' ), 100);
        // filters
        add_filter( 'an_page_notifications', array( $this, 'an_show_on_a_notifications' ), 10, 1 );
        add_filter( 'an_pre_notification_print', array( $this, 'an_pre_notification_show' ) );
        add_filter( 'an_pre_add_design', array( $this, 'an_pre_design' ) );
    }
    /* ADMIN MENU
    ================================================== */
    function an_pro_admin_menu() {
        add_submenu_page( 'edit.php?post_type=a_notifications', __( 'Pro Features', 'advanced-notifications' ), '<strong style="color: #a7ceaf;">' . __( 'Pro Features', 'advanced-notifications' ) . '</strong>', 'edit_advanced_notificationss', 'an_pro_features', array( $this, 'an_pro_features' ));
    }
    function an_pro_features() {
        wp_redirect('https://advanced-notifications.com/features/?utm_source=wp-menu&utm_campaign=an-pro&utm_medium=pro-features');
        exit;
    }
    function is_an_admin_page() {
    	$return = false;
        $screen = get_current_screen();
    	if (is_admin() && isset($screen->id)) {
            if ($screen->id == 'a_notifications_page_an_settings') {
                $return = true;
            } else {
                foreach ($this->get_an_cpt_list() as $key => $cpt) {
                    if ($screen->id == $cpt || $screen->id == 'edit-' . $cpt) {
                        $return = true;
                    }
                }
            }
    	}
        return apply_filters('is_an_admin_page', $return);
    }
    function an_admin_enqueue() {
    	if ($this->is_an_admin_page()) {
			$an_css_ver = date("ymd-Gis", filemtime(ADVANCED_NOTIFICATIONS_ROOT_PATH . 'assets/css/admin-an.css'));
			wp_register_style( 'admin-an-style', ADVANCED_NOTIFICATIONS_URL_PATH . 'assets/css/admin-an.css', false, $an_css_ver );
			wp_enqueue_style( 'admin-an-style' );
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_media();
            $an_js_ver  = date("ymd-Gis", filemtime( ADVANCED_NOTIFICATIONS_ROOT_PATH . 'assets/js/admin-an.js' ));
			wp_register_script( 'admin-an-js', ADVANCED_NOTIFICATIONS_URL_PATH . 'assets/js/admin-an.js', array(), $an_js_ver );
			wp_enqueue_script( 'admin-an-js' );
			wp_enqueue_script( 'wp-color-picker' );
	        // wp_dequeue_script( 'autosave' );
            wp_localize_script( 'admin-an-js', 'admin_an_settings', $this->an_admin_localize_script() );
        }
    }
    function an_enqueue() {
        if (!empty($this->get_page_notifications())) {
            $an_css_ver = date("ymd-Gis", filemtime(ADVANCED_NOTIFICATIONS_ROOT_PATH . 'assets/css/an.css'));
            wp_register_style( 'an-style', ADVANCED_NOTIFICATIONS_URL_PATH . 'assets/css/an.css', false, $an_css_ver );
            wp_enqueue_style( 'an-style' );
            $an_js_ver  = date("ymd-Gis", filemtime( ADVANCED_NOTIFICATIONS_ROOT_PATH . 'assets/js/an.js' ));
            wp_register_script( 'an-js', ADVANCED_NOTIFICATIONS_URL_PATH . 'assets/js/an.js', array(), $an_js_ver, true );
            wp_localize_script( 'an-js', 'an_settings', $this->an_localize_script() );
            wp_enqueue_script( 'an-js' );
            wp_enqueue_script( 'jquery' );
        }
    }
    function an_post_types() {
        $post_types = get_post_types( array( 'public' => true ) );
        if (class_exists('BuddyPress')) {
            $boddypress_typs = array(
                'bp_front' => __('BuddyPress user main page', 'advanced-notifications'),
                'bp_activity' => __('BuddyPress activities', 'advanced-notifications'),
                'bp_members' => __('BuddyPress members', 'advanced-notifications'),
                'bp_profile' => __('BuddyPress profile', 'advanced-notifications'),
            );
            $post_types = array_merge($post_types, $boddypress_typs);
        }
        return apply_filters('an_post_types', $post_types);
    }
    function an_taxonomies() {
        $taxonomies = get_taxonomies( array( 'public' => true ) );
        unset($taxonomies['post_format'], $taxonomies['product_shipping_class']);
        if (isset($taxonomies['category'])) $taxonomies['category'] = __('Category', 'advanced-notifications');
        if (isset($taxonomies['post_tag'])) $taxonomies['post_tag'] = __('Post tags', 'advanced-notifications') . ' (post_tag)';
        if (isset($taxonomies['product_cat'])) $taxonomies['product_cat'] = __('Product category', 'advanced-notifications') . ' (product_cat)';
        if (isset($taxonomies['product_tag'])) $taxonomies['product_tag'] = __('Product tags', 'advanced-notifications') . ' (product_tag)';
        return apply_filters('an_taxonomies', $taxonomies);
    }
    function an_get_multilingual_option($array,$option_name,$default='') {
        $site_lang = mb_substr(get_locale(), 0, 2, "UTF-8");
        if (isset($array[$option_name])) {
            return $array[$option_name];
        } elseif (isset($array[$option_name . '_eis_html'])) {
            return $array[$option_name . '_eis_html'];
        } elseif (isset($array[$option_name . '_' . $site_lang])) {
            return $array[$option_name . '_' . $site_lang];
        } elseif (isset($array[$option_name . '_eis_html_' . $site_lang])) {
            return $array[$option_name . '_eis_html_' . $site_lang];
        } else {
            return $default;
        }
    }
    function get_notifications() {
        $args = array(
        	'post_type'   => 'a_notifications',
        	'post_status' => 'publish',
        	'posts_per_page' => -1,
        );
        return get_posts($args);
    }
    function register_notifications() {
        global $an_api, $an_register_notifications;
        $notifications = $this->get_notifications();
        foreach ($notifications as $notification) {
            $notification_settings = (array) eis_get_option('a_notifications', null, $notification->ID);
            foreach ($notification_settings as $option_key => $option_value) {
                $notification_settings[$option_key] = str_replace($option_key . '_', '', $option_value);
            }
            $args = array(
            	'id'   => $notification->ID,
            	'label'   => ($notification->post_title != '') ? $notification->post_title : __('Notification', 'advanced-notifications') . '-' . $notification->ID,
            	'content'   => wpautop($notification->post_content),
            );
            $an_api->add_notification(array_merge($args, $notification_settings));
        }
        do_action('an_add_notification');
        return $an_register_notifications;
    }
    function notifications_list($cache=true) {
        global $an_core;
        $notifications_list = $an_core->an_get_cache('notifications_list');
        if (!$notifications_list || !$cache) {
            // must run first to get all register_notifications to global var $an_register_notifications
            $notifications_list = $this->register_notifications();
            if ($cache) {
                $an_core->an_add_cache('notifications_list', $notifications_list);
            }
        }
        return apply_filters('an_notifications_list', $notifications_list);
    }
    function active_notifications_list($cache=true) {
        global $an_core;
        $notifications_list = $an_core->an_get_cache('active_notifications_list');
        if (!$notifications_list || !$cache) {
            $notifications_list = $this->notifications_list($cache);
            foreach ($notifications_list as $key => $notification) {
                if ($notification['status'] != 'checked') {
                    unset($notifications_list[$key]);
                }
            }
            if ($cache) {
                $an_core->an_add_cache('active_notifications_list', $notifications_list);
            }
        }
        return apply_filters('an_active_notifications_list', array_filter($notifications_list));
    }
    function get_page_notifications($item_id=null,$cache=true) {
        global $an_core;
        $page_notifications_list = $an_core->an_get_cache('get_page_notifications');
        if (!$page_notifications_list || !$cache) {
            $item_id = ($item_id == null) ? get_queried_object_id() : $item_id;
            $page_notifications_list = $this->active_notifications_list();
            foreach ($page_notifications_list as $key => $notification) {
                if (!$this->is_show($notification, $item_id)) {
                    unset($page_notifications_list[$key]);
                }
            }
            if ($cache) {
                $an_core->an_add_cache('get_page_notifications', $page_notifications_list);
            }
        }
        return apply_filters('an_page_notifications', array_filter($page_notifications_list));
    }
    function an_is_pass($notification) {
        $return = true;
        // if ($notification['close_button_limit'] == 'cancel_for') {
        //     if (isset($_COOKIE['an_cancel_for_' . $notification['id']])) {
        //         $return = false;
        //     }
        // } else {
        //     if (isset($_COOKIE['an_cancel_for_' . $notification['id']])) {
        //         // setcookie('an_cancel_for_' . $notification['id'], '', -1, "/");
        //     }
        // }
        // if ($notification['limitations'] == 'custom_limitations') {
        //     $limit_time = ($notification['custom_limitations_for']['limit_unit'] == 'days') ? 86400 : (($notification['custom_limitations_for']['limit_unit'] == 'hours') ? 3600 : 60);
        //     if (isset($_COOKIE['an_limitations_' . $notification['id']])) {
        //         if ($notification['custom_limitations_times'] > $_COOKIE['an_limitations_' . $notification['id']]) {
        //             // setcookie('an_limitations_' . $notification['id'], sanitize_text_field($_COOKIE['an_limitations_' . $notification['id']])+1, time() + ($notification['custom_limitations_for']['limit_val']*$limit_time), "/");
        //         } else {
        //             $return = false;
        //         }
        //     } else {
        //         // setcookie('an_limitations_' . $notification['id'], 1, time() + ($notification['custom_limitations_for']['limit_val']*$limit_time), "/");
        //     }
        // } else {
        //     if (isset($_COOKIE['an_limitations_' . $notification['id']])) {
        //         // setcookie('an_limitations_' . $notification['id'], '', -1, "/");
        //     }
        // }
        return $return;
    }
    function an_notification_set_cookie() {
        if (is_admin()) return;
        $item_id = get_queried_object_id();
        foreach ($this->notifications_list() as $notification) {
            if ($this->is_show($notification, $item_id)) {
                if ($notification['close_button_limit'] != 'cancel_for') {
                    if (isset($_COOKIE['an_cancel_for_' . $notification['id']])) {
                        setcookie('an_cancel_for_' . $notification['id'], '', -1, "/");
                    }
                }
                if ($notification['limitations'] == 'custom_limitations') {
                    $limit_time = ($notification['custom_limitations_for']['limit_unit'] == 'days') ? 86400 : (($notification['custom_limitations_for']['limit_unit'] == 'hours') ? 3600 : 60);
                    if (isset($_COOKIE['an_limitations_' . $notification['id']])) {
                        if ($notification['custom_limitations_times'] > $_COOKIE['an_limitations_' . $notification['id']]) {
                            setcookie('an_limitations_' . $notification['id'], sanitize_text_field($_COOKIE['an_limitations_' . $notification['id']])+1, time() + ($notification['custom_limitations_for']['limit_val']*$limit_time), "/");
                        }
                    } else {
                        setcookie('an_limitations_' . $notification['id'], 1, time() + ($notification['custom_limitations_for']['limit_val']*$limit_time), "/");
                    }
                } else {
                    if (isset($_COOKIE['an_limitations_' . $notification['id']])) {
                        setcookie('an_limitations_' . $notification['id'], '', -1, "/");
                    }
                }
            }
        }
    }
    function is_show($notification, $item_id=null, $cache=true) {
        global $an_core;
        $notification = apply_filters('an_pre_notification_is_show', $notification);
        $item_id = ($item_id == null) ? get_queried_object_id() : $item_id;
        $return = true;
        $is_show_cache = $an_core->an_get_cache('is_show');
        if (!$is_show_cache || !$cache) {
            $tax = get_queried_object();
            $is_show_cache = array(
                'post_type' => get_post_type(),
                'taxonomy' => (isset($tax->taxonomy) ? $tax->taxonomy : false),
                'an_item_data' => (is_singular() ? get_post_meta($item_id, 'an_item_data', true) : null),
            );
            $an_core->an_add_cache('is_show', $is_show_cache);
        }
        if ($is_show_cache['post_type'] == 'a_notifications' && $notification['id'] == $item_id) {
            return true;
        } elseif ($is_show_cache['post_type'] == 'a_notifications' || $notification['status'] == 'no' || $notification['status'] == null) {
            return false;
        }
        if ($is_show_cache['post_type'] == 'a_notifications') return;
        if ($notification['publish_type'] == 'custom') {
            if ($is_show_cache['taxonomy']) {
                if (!isset($notification['publish_taxonomies'][$is_show_cache['taxonomy']]) || $notification['publish_taxonomies'][$is_show_cache['taxonomy']] != 'checked') {
                    $return = false;
                }
            } else {
                if (!isset($notification['publish_post_types'][$is_show_cache['post_type']]) || $notification['publish_post_types'][$is_show_cache['post_type']] != 'checked') {
                    $return = false;
                }
            }
        } elseif ($notification['publish_type'] == 'specific') {
            $return = false;
            // add specific conditions her
            if (is_front_page() && isset($notification['publish_specific_pages']['front_page']) && $notification['publish_specific_pages']['front_page'] == 'checked') {
                $return = true;
            } elseif (is_home() && isset($notification['publish_specific_pages']['posts_page']) && $notification['publish_specific_pages']['posts_page'] == 'checked') {
                $return = true;
            }
        }
        if (isset($is_show_cache['an_item_data']['an_disable_all']) && $is_show_cache['an_item_data']['an_disable_all']) {
            $return = false;
        }
        if (isset($is_show_cache['an_item_data'][$notification['id']])) {
            $return = true;
        }
        if ($return && !$this->an_is_pass($notification)) {
            $return = false;
        }
        return apply_filters('an_is_show', $return, $notification, $item_id);
    }
    function an_localize_script() {
        global $an_designs, $an_triggers, $an_locations;
        $item_id = get_queried_object_id();
        $designs_list = $an_designs->designs_list();
        $triggers_list = $an_triggers->triggers_list();
        $locations_list = $an_locations->locations_list();
        $notifications_arr = array();
        $locations_arr = array();
        $designs_arr = array();
        $triggers_arr = array();
        foreach ($this->notifications_list() as $notification) {
            $notification = apply_filters('an_pre_notification_print', $notification);
            if ($this->is_show($notification, $item_id)) {
                unset($notification['content']);
                $notifications_arr[$notification['id']] = $notification;
                if (isset($locations_list[$notification['location']]) && !isset($locations_arr[$notification['location']])) {
                    $locations_arr[$notification['location']] = $locations_list[$notification['location']];
                }
                if (isset($designs_list[$notification['design']]) && !isset($designs_arr[$notification['design']])) {
                    $designs_arr[$notification['design']] = $designs_list[$notification['design']];
                } elseif (!isset($designs_arr['default'])) {
                    $designs_arr['default'] = $designs_list['default'];
                }
                if (isset($notification['triggers']) && is_array($notification['triggers'])) {
                    foreach ($notification['triggers'] as $trigger_name) {
                        if (isset($triggers_list[$trigger_name]) && !isset($triggers_arr[$trigger_name]) ) {
                            $triggers_arr[$trigger_name] = $triggers_list[$trigger_name];
                        }
                    }
                }
            }
        }
        $an_localize_script = array(
            'notifications' => $notifications_arr,
            'locations' => $locations_arr,
            'designs' => $designs_arr,
            'triggers' => $triggers_arr,
        );
        return apply_filters('an_localize_script', $an_localize_script);
    }
    function an_admin_localize_script() {
        global $an_triggers;
        $triggers_list = $an_triggers->triggers_list();
        $triggers_arr = array();
        $locations_arr = array();
        $designs_arr = array();
        foreach ($triggers_list as $trigger) {
            $triggers_arr[] = $trigger;
        }
        $an_admin_localize_script = array(
            'triggers' => $triggers_arr,
        );
        return apply_filters('an_admin_localize_script', $an_admin_localize_script);
    }
    function an_is_plugin_active($plugin) {
		if (strpos($_SERVER['REQUEST_URI'], 'wp-admin/plugins.php?action=deactivate') !== false) {
			return false;
		} else {
			return in_array($plugin, (array) get_option('active_plugins'));
		}
	}
    function is_elementor_preview_mode() {
        $elementor_plugin = (class_exists('Elementor\Plugin')) ? \Elementor\Plugin::instance() : false;
        return ($elementor_plugin && $elementor_plugin->preview->is_preview_mode() ? true : false);
    }
    function publish_type() {
        $publish_type = array(
            'all' => __('All', 'advanced-notifications'),
            'custom' => __('Custom', 'advanced-notifications'),
            'specific' => __('Only on specific pages', 'advanced-notifications'),
        );
        return apply_filters('an_publish_type', $publish_type);
    }
    function devices_list() {
        $devices_list = array(
            'all-devices' => __('All devices', 'advanced-notifications'),
            'desktop' => __('Desktop', 'advanced-notifications'),
            'desktop_tablet' => __('Desktop & Tablet', 'advanced-notifications'),
            'tablet' => __('Tablet', 'advanced-notifications'),
            'tablet_mobile' => __('Only Tablet & Mobile', 'advanced-notifications'),
            'mobile' => __('Mobile', 'advanced-notifications'),
        );
        return apply_filters('an_devices_list', $devices_list);
    }
    function show_when() {
        $show_when = array (
    		'page_loaded_trigers' => __('Page is loaded and in trigers', 'advanced-notifications'),
    		'only_trigers_eis_disabled' => __('Only in trigers', 'advanced-notifications') . ' - ' . __('PRO', 'advanced-notifications'),
    	);
        return apply_filters('an_show_when', $show_when);
    }
    function notification_types() {
        $notification_types = array(
    		'editor' => __('Editor', 'advanced-notifications'),
    		'html' => __('HTML', 'advanced-notifications'),
    		'image' => __('Image', 'advanced-notifications'),
    	);
        return apply_filters('an_notification_types', $notification_types);
    }
    function trigger_actions() {
        $trigger_actions = array(
    		'show' => __('Show notification', 'advanced-notifications'),
    	);
        return apply_filters('an_trigger_actions', $trigger_actions);
    }
    function trigger_types() {
        $trigger_types = array(
    		'on_click' => __('On click', 'advanced-notifications'),
    		'on_hover' => __('On hover', 'advanced-notifications'),
    	);
        return apply_filters('an_trigger_types', $trigger_types);
    }
    function get_an_cpt_list() {
        return apply_filters('an_cpt_list', array('a_notifications'));
    }
    function an_ajax() {
		do_action( 'an_add_ajax', sanitize_text_field($_POST['an_action']), sanitize_text_field($_POST['notification_id']), $_POST['value']);
		wp_die();
	}
    function an_save_notification_status($an_action, $notification_id, $value) {
        if ($an_action == 'status') {
            $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
            $a_notification_settings = eis_get_option('a_notifications', null, $notification_id);
            if ($value) {
                $a_notification_settings['status'] = 'status_checked';
            } else {
                $a_notification_settings['status'] = 'no';
            }
            eis_save_meta_box_options($notification_id, 'a_notifications', $a_notification_settings);
            echo "finish";
        }
    }
    function an_wp_footer() {
    	// LOADER
    	$is_loader = apply_filters('an_page_loader', $return=false);
    	if ($is_loader) {
    		echo '<div class="an-loader-container an-page-loader"><div class="an-loader"></div></div>';
    	}
    	// ADD WP ADMIN AJAX
    	// data-ajax-url="' . admin_url('admin-ajax.php') . '"
    	$is_admin_ajax = apply_filters('an_admin_ajax', (is_user_logged_in() ? true : false));
    	if ($is_admin_ajax) {
    		echo '<script type="text/javascript" id="an_ajax_url">';
    		echo 	'var an_ajax_url = "' . admin_url('admin-ajax.php') . '";';
    		echo '</script>';
    	}
    }
    function an_show_on_a_notifications($page_notifications_list) {
        if (get_post_type() == 'a_notifications') {
            global $an_api;
            $notification_id = get_the_id();
            $notification = $an_api->get_notification($notification_id);
            if ($notification['status'] != 'checked') {
                $page_notifications_list[] = $notification;
            }
        }
        return $page_notifications_list;
    }
    function an_pre_notification_show($notification) {
        if (isset($notification['location'])) {
            if (strpos($notification['location'], 'opposite_site_direction') !== false && strpos($notification['location'], 'top') !== false) {
                $notification['location'] = (is_rtl() ? 'top_left' : 'top_right');
            } elseif (strpos($notification['location'], 'opposite_site_direction') !== false && strpos($notification['location'], 'bottom') !== false) {
                $notification['location'] = (is_rtl() ? 'bottom_left' : 'bottom_right');
            } elseif (strpos($notification['location'], 'site_direction') !== false && strpos($notification['location'], 'top') !== false) {
                $notification['location'] = (is_rtl() ? 'top_right' : 'top_left');
            } elseif (strpos($notification['location'], 'site_direction') !== false && strpos($notification['location'], 'bottom') !== false) {
                $notification['location'] = (is_rtl() ? 'bottom_right' : 'bottom_left');
            } elseif ($notification['location'] == '') {
                $notification['location'] = (is_rtl() ? 'bottom_right' : 'bottom_left');
            }
        }
        return $notification;
    }
    function an_pre_design($args) {
        if (isset($args['close_button_position'])) {
            if (strpos($args['close_button_position'], 'opposite_site_direction') !== false && strpos($args['close_button_position'], 'top') !== false) {
                $args['close_button_position'] = (is_rtl() ? 'top_left' : 'top_right');
            } elseif (strpos($args['close_button_position'], 'opposite_site_direction') !== false && strpos($args['close_button_position'], 'bottom') !== false) {
                $args['close_button_position'] = (is_rtl() ? 'bottom_left' : 'bottom_right');
            } elseif (strpos($args['close_button_position'], 'site_direction') !== false && strpos($args['close_button_position'], 'top') !== false) {
                $args['close_button_position'] = (is_rtl() ? 'top_right' : 'top_left');
            } elseif (strpos($args['close_button_position'], 'site_direction') !== false && strpos($args['close_button_position'], 'bottom') !== false) {
                $args['close_button_position'] = (is_rtl() ? 'bottom_right' : 'bottom_left');
            }
        }
        if ($args['id'] == 'an_netflix_notice' && is_rtl()) {
            $args['desktop']['border_right'] = 8;
            $args['desktop']['border_left'] = 0;
            $args['desktop']['padding_right'] = 10;
            $args['desktop']['padding_left'] = 5;
        }
        return $args;
    }
    function an_close_button($design=array()) {
        global $an_functions, $an_designs, $an_locations;
        $close_button_html = '';
        if (isset($design['close_button_enable']) && $design['close_button_enable'] == 'checked') {
            if ($design['close_button_type'] == 'button') {
                $close_button_html .= '<div class="an-close-container ' . esc_attr($design['close_button_position']) . '"><button class="an-close-button an-close-text button">' . esc_html($design['close_text']) . '</button></div>';
            } elseif ($design['close_button_type'] == 'text') {
                $close_button_html .= '<div class="an-close-container ' . esc_attr($design['close_button_position']) . '"><a href="#" class="an-close-button an-close-text">' . esc_html($design['close_text']) . '</a></div>';
            } else {
                $close_button_html .= '<div class="an-close-container ' . esc_attr($design['close_button_position']) . '"><a href="#" class="an-close-button an-close-icon">' . esc_html($design['close_icon_style']) . '</a></div>';
            }
        }
        return apply_filters('an_close_button_html', $close_button_html);
    }
    function an_notification_roles() {
        $notification_role = array(
            'all' => __('All visitors', 'advanced-notifications-pro'),
            'logged' => __('Logged users', 'advanced-notifications-pro'),
            'guests' => __('Only guests', 'advanced-notifications-pro'),
        );
        return apply_filters('an_notification_roles_list', $notification_role + wp_roles()->role_names);
    }
}
$an_functions = new AdvancedNotificationsFunctions();
