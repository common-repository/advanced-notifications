<?php
/*
*
*	Advanced Notifications V 1
*	------------------------------------------------
* 	Powered by  - https://wiliba.com
*
*/
global $an_core;
class AdvancedNotificationsCore
{
    function __construct() {
        add_action( 'plugins_loaded', array($this, 'an_load_textdomain' ) );
    	add_action( 'admin_menu', array($this, 'an_admin_menu' ) );
        add_action( 'wp_footer', array( $this, 'register_advanced_notifications' ) );
        add_action( 'wp_loaded', array( $this, 'an_after_wp_loaded' ) );
        add_action( 'add_meta_boxes', array( $this, 'an_add_meta_box' ) );
        add_action( 'save_post', array( $this, 'an_save_meta_box' ), 10, 3);
        // filters
        add_filter( 'template_include', array( $this, 'an_page_template' ) );
        add_filter( 'hidden_meta_boxes', array( $this, 'an_hide_meta_boxes' ), 10, 3 );
    }

    /* LANGUAGES
    ================================================== */
    function an_load_textdomain() {
        load_plugin_textdomain( 'advanced-notifications', false, plugin_basename( ADVANCED_NOTIFICATIONS_ROOT_PATH . 'languages' ) );
    }

    /* ADMIN MENU
    ================================================== */
    function an_admin_menu() {
        do_action ( 'an_admin_menu' );
    }
    /* NOTIFICATION PAGE TEMPLATE
    ================================================== */
    function an_page_template($page_template) {
    	if (is_singular('a_notifications')) {
    		$page_template = ADVANCED_NOTIFICATIONS_ROOT_PATH . 'includes/an-single.php';
    	}
    	return $page_template;
    }
    /* HIDE META BOXS
    ================================================== */
    function an_hide_meta_boxes( $hidden, $screen, $use_defaults ) {
    	global $wp_meta_boxes, $an_functions;
    	if($an_functions->is_an_admin_page()) {
            $cpt = $screen->id;
    		$tmp = array();
    		foreach($wp_meta_boxes[$cpt] as $context_key => $context_item) {
    			foreach( $context_item as $priority_key => $priority_item ) {
    				foreach( $priority_item as $metabox_key => $metabox_item ) {
    					if ( $metabox_key != 'submitdiv' && $metabox_key != $cpt ) {
    						$tmp[] = $metabox_key;
    					}
    				}
    			}
    		}
    		$hidden = $tmp;
    	}
    	return $hidden;
    }
    /* REGISTER ADVANCED NOTIFICATION
    ================================================== */
    function register_advanced_notifications() {
        global $an_functions, $an_designs, $an_locations;
        if ($an_functions->is_elementor_preview_mode()) return;
        $item_id = get_queried_object_id();
        $notifications_list = $an_functions->get_page_notifications($item_id);
        $locations_list = $an_locations->locations_list();
        $designs_list = $an_designs->designs_list();
        if (!empty($notifications_list)) {
            $fun_print = '<div ' . $this->main_classes() . $this->main_attributes() . '>';
            $notifications_html_arr = array();
            foreach ($notifications_list as $notification) {
                $notification = apply_filters('an_pre_notification_print', $notification);
                if ($an_functions->is_show($notification)) {
                    $notification_id = $notification['id'];
                    $notification_design_name = isset($designs_list[$notification['design']]) ? $notification['design'] : 'default';
                    if (isset($designs_list[$notification_design_name])) {
                        $design = $designs_list[$notification_design_name];
                        $close_button = $an_functions->an_close_button($design);
                    }
                    $notification_html = '     <div class="an-container an-' . esc_attr($notification_id . ' an-delay an-design-' . $notification_design_name . $notification['class']) . '"  id="an-' . esc_attr($notification_id) . '">';
                    ob_start(); // start capturing output.
                    do_action('an_container_start', $notification, $item_id);
                    do_action('an_container_start_{$notification_id}', $notification, $item_id);
                    $notification_html .= ob_get_contents(); // the actions output will now be stored in the variable as a string!
                    ob_end_clean(); // never forget this or you will keep capturing output.
                    $notification_html .=   $close_button;
                    $notification_html .= '         <div class="an-header">';
                    ob_start(); // start capturing output.
                    do_action('an_header', $notification, $item_id);
                    do_action('an_header_{$notification_id}', $notification, $item_id);
                    $notification_html .= ob_get_contents(); // the actions output will now be stored in the variable as a string!
                    ob_end_clean(); // never forget this or you will keep capturing output.
                    $notification_html .= '         </div>';
                    if ($notification['type'] == 'html') {
                        $an_content = do_shortcode(wp_kses_post($notification['type_html']));
                    } elseif ($notification['type'] == 'image') {
                        $an_content = '<img src="' . esc_url($notification['type_image']) . '" alt="' . esc_attr($notification['label']) . '">';
                    } else {
                        $an_content = do_shortcode(wp_kses_post($notification['content']));
                    }
                    $notification_html .= '         <div class="an-content">' . apply_filters('an_content', $an_content, $notification) . '</div>';
                    $notification_html .= '         <div class="an-footer">';
                    ob_start(); // start capturing output.
                    do_action('an_footer_{$notification_id}', $notification, $item_id);
                    do_action('an_footer', $notification, $item_id);
                    $notification_html .= ob_get_contents(); // the actions output will now be stored in the variable as a string!
                    ob_end_clean(); // never forget this or you will keep capturing output.
                    $notification_html .= '         </div>';
                    ob_start(); // start capturing output.
                    do_action('an_container_end', $notification, $item_id);
                    do_action('an_container_end_{$notification_id}', $notification, $item_id);
                    $notification_html .= ob_get_contents(); // the actions output will now be stored in the variable as a string!
                    ob_end_clean(); // never forget this or you will keep capturing output.
                    $notification_html .= '     </div>';
                    $notifications_html_arr[$notification['location']][] = $notification_html;
                }
            }
            foreach ($locations_list as $location) {
                if (isset($notifications_html_arr[$location['id']])) {
                    $location = apply_filters('an_pre_print_location', $location);
                    $fun_print .= ' <div class="an-location location-' . esc_attr($location['id'] . $location['class']) . '" id="an-location-' . esc_attr($location['id']) . '">';
                        foreach ($notifications_html_arr[$location['id']] as $notification_html) {
                            $fun_print .= $notification_html;
                        }
                        $fun_print .= ' </div>';
                    }
                }
                $fun_print .= '</div>';
                echo $fun_print;
        }
    }
    /* LICENSE TYPE
    ================================================== */
    function license_type() {
        return apply_filters('an_license_type', 'basic');
    }
    /* MAIN CLASSES
    ================================================== */
    function main_classes($echo=false) {
        $classes = apply_filters('an_main_classes', array('an-main-container'));
        if ($echo) {
            echo 'class="' . implode(', ', $classes) . '"';
        } else {
            return 'class="' . implode(', ', $classes) . '"';
        }
    }
    /* MAIN ATTRIBUTES
    ================================================== */
    function main_attributes($echo=false) {
        $attributes = array(
            'data-tablet_breakpoint' => esc_attr(eis_get_option('an_settings', 'tablet_breakpoint')),
            'data-mobile_breakpoint' => esc_attr(eis_get_option('an_settings', 'mobile_breakpoint')),
        );
        $attributes = apply_filters('an_main_attributes', $attributes);
        $return = '';
        foreach ($attributes as $attribute_name => $attribute_val) {
            $return .= ' ' . $attribute_name . '="' . $attribute_val . '"';
        }
        if ($echo) {
            echo $return;
        } else {
            return $return;
        }
    }
    /* FIRE AFTER WP LOADED
    ================================================== */
    function an_after_wp_loaded() {
    	do_action('an_loaded');
    }
    /* CACHE
    ================================================== */
    function an_add_cache($name,$value) {
        global $an_cache;
        $an_cache[$name] = $value;
    }

    function an_get_cache($name) {
        global $an_cache;
        return (isset($an_cache[$name]) ? $an_cache[$name] : false);
    }
    /* ITEM META BOX
    ================================================== */
    function an_add_meta_box() {
        global $wp_post_types;
        $current_cpt_name = get_post_type();
        $is_cpt_public = (isset($wp_post_types[$current_cpt_name])) ? $wp_post_types[$current_cpt_name]->public : false;
        if ($is_cpt_public) {
            add_meta_box("an_meta_box", __( 'A notification settings', 'advanced-notifications' ), array($this, 'an_meta_box'), null, "side", "high", null);
        }
    }
    function an_meta_box() {
        global $an_api;
        $notifications_list = $an_api->notifications_list();
        $post_id = get_the_ID();
        $an_item_data = get_post_meta($post_id, 'an_item_data', true);
        $disable_all = 'an_disable_all' . $post_id;
        $get_an_disable_all = (isset($an_item_data['an_disable_all'])) ? $an_item_data['an_disable_all'] : false;
        $fun_print = '<div class="an-meta-box-container">';
        $fun_print .= ' <p>';
        $fun_print .= '     <input type="hidden" name="an_prevent_delete_meta_movetotrash" id="an_prevent_delete_meta_movetotrash" value="' . esc_attr(wp_create_nonce($post_id)) . '" />';
        $fun_print .= '     <input name="an_item_data[an_disable_all]" type="checkbox" value="1" id="an_disable_all" ' . checked($get_an_disable_all, 1, false ) . '><label for="an_disable_all"><strong>' . __( 'Turn off all notification for this page', 'advanced-notifications' ) . '</strong></label>';
        $fun_print .= ' </p>';
        $fun_print .= ' <p>';
        $fun_print .= '     <label><strong>' . __( 'Select notifications to display on this page', 'advanced-notifications' ) . '</strong></label>';
        foreach ($notifications_list as $notification) {
            $notification_id = $notification['id'];
            $get_an_item = (isset($an_item_data[$notification_id])) ? $an_item_data[$notification_id] : 0;
            $fun_print .= '     <div><input name="an_item_data[' . esc_attr($notification_id) . ']" type="checkbox" value="1" id="an-' . esc_attr($notification_id) . '" ' . checked($get_an_item, 1, false ) . '><label for="an-' . esc_attr($notification_id) . '">' . esc_html($notification['label']) . '</label></div>';
        }
        $fun_print .= ' </p>';
        $fun_print .= '</div>';
        echo $fun_print;
    }
    function an_save_meta_box($post_id, $post, $update) {
        $nonce = (isset($_POST['an_prevent_delete_meta_movetotrash']) ? sanitize_text_field($_POST['an_prevent_delete_meta_movetotrash']) : null);
        if (!wp_verify_nonce($nonce, $post_id)) { return $post_id; }
        update_post_meta( $post_id, 'an_item_data', (isset($_POST['an_item_data']) ? array_map('sanitize_text_field', $_POST['an_item_data']) : null) );
    }
}
$an_core = new AdvancedNotificationsCore();
