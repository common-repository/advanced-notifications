<?php
/*
*
*	Advanced Notifications V 1
*	------------------------------------------------
* 	Powered by  - https://wiliba.com
*
*/

global $an_api, $an_register_notifications,$an_register_locations,$an_register_designs,$an_register_triggers;
class AdvancedNotificationsAPI
{
    function __construct() {
        // add_action( 'an_admin_menu', array($this, 'an_locations_admin_menu' ), 25);
        add_filter('an_notifications_list', array($this, 'an_notifications_order'), 10);
    }

    /* ADD NOTIFICATIONS
    ================================================== */
    function add_notification($args) {
    	global $an_core, $an_functions, $an_register_notifications;
        $args = apply_filters('an_pre_add_notification', $args);
        $license_type = $an_core->license_type();
    	$notification = array(
    		'id' => (isset($args['id'])) ? $notification_id = $args['id'] : null,
            'label' => (isset($args['label'])) ? $args['label'] : __('Notification', 'advanced-notifications') . '-' . $notification_id,
            'status' => (isset($args['status'])) ? $args['status'] : 'checked',
            'type' => (isset($args['type'])) ? $args['type'] : 'editor',
            'content' => (isset($args['content'])) ? $args['content'] : null,
            'type_html' => $an_functions->an_get_multilingual_option($args,'type_html',$default=null),
            'type_image' => $an_functions->an_get_multilingual_option($args,'type_image',$default=null),
            'devices_list' => (isset($args['devices_list'])) ? $args['devices_list'] : 'all-devices',
            'location' => (isset($args['location'])) ? $args['location'] : (is_rtl() ? 'bottom_right' : 'bottom_left'),
            'design' => (isset($args['design'])) ? $args['design'] : 'success',
            'class' => (isset($args['class'])) ? ' ' . $args['class'] : null,
            'show_when' => (isset($args['show_when'])) ? $args['show_when'] : 'page_loaded_trigers',
            'animation_in' => (isset($args['animation_in'])) ? $args['animation_in'] : (is_rtl() ? 'an-bounce-in-right' : 'an-bounce-in-left'),
            'animation_in_duration' => (isset($args['animation_in_duration'])) ? $args['animation_in_duration'] : '1',
            'animation_out' => (isset($args['animation_out'])) ? $args['animation_out'] : (is_rtl() ? 'an-slide-out-right' : 'an-slide-out-left'),
            'animation_out_duration' => (isset($args['animation_out_duration'])) ? $args['animation_out_duration'] : '1',
            'delay' => (isset($args['delay'])) ? $args['delay'] : '2',
            'show_time' => (isset($args['show_time'])) ? $args['show_time'] : '8',
            'publish_type' => (isset($args['publish_type'])) ? $args['publish_type'] : 'all',
            'publish_post_types' => (isset($args['publish_post_types'])) ? $args['publish_post_types'] : array('post' => 'checked','page' => 'checked'),
            'publish_taxonomies' => (isset($args['publish_taxonomies'])) ? $args['publish_taxonomies'] : array('category' => 'checked','post_tag' => 'checked'),
            'close_button_limit' => (isset($args['close_button_limit'])) ? $args['close_button_limit'] : 'none',
            'close_button_limit_for' => array(
                'cancel_val' => (isset($args['close_button_limit_for']['cancel_val'])) ? $args['close_button_limit_for']['cancel_val'] : '10',
                'cancel_unit' => (isset($args['close_button_limit_for']['cancel_unit'])) ? $args['close_button_limit_for']['cancel_unit'] : 'days',
            ),
            'limitations' => (isset($args['limitations'])) ? $args['limitations'] : 'none',
            'custom_limitations_times' => (isset($args['custom_limitations_times'])) ? $args['custom_limitations_times'] : '10',
            'custom_limitations_for' => array(
                'limit_val' => (isset($args['custom_limitations_for']['limit_val'])) ? $args['custom_limitations_for']['limit_val'] : '10',
                'limit_unit' => (isset($args['custom_limitations_for']['limit_unit'])) ? $args['custom_limitations_for']['limit_unit'] : 'days',
            ),
            'triggers' => (isset($args['triggers'])) ? $args['triggers'] : array(),
    	);
        unset($notification['type_html_eis_html']);
        $an_register_notifications[$notification_id] = apply_filters('an_pre_register_notification', $notification, $args);
    	return apply_filters('an_register_notifications', $an_register_notifications);
    }
    /* GET NOTIFICATIONS
    ================================================== */
    function notifications_list($cache=true) {
    	global $an_functions;
        return $an_functions->notifications_list($cache);
    }
    /* NOTIFICATIONS ORDER BY DELAY
    ================================================== */
    function an_notifications_order($notifications_list) {
        $notifications_list_sort = array();
        if (!empty($notifications_list)) {
            usort($notifications_list, function ($notification1, $notification2) {
                return $notification2['delay'] <=> $notification1['delay'];
            });
            $notifications_list_sort = $notifications_list;
        }
    	return $notifications_list_sort;
    }
    /* GET NOTIFICATION
    ================================================== */
    function get_notification($notification_id) {
        $notifications_list = $this->notifications_list();
        foreach ($notifications_list as $notification) {
            if ($notification['id'] == $notification_id) {
                return $notification;
            }
        }
        return false;
    }
    /* ADD LOCATIONS
    ================================================== */
    function add_location($args) {
        global $an_register_locations;
        $args = apply_filters('an_pre_add_location', $args);
        $location_id = (isset($args['id'])) ? $args['id'] : null;
        if ($location_id != null) {
            $default_horizontal_position = (isset($args['desktop']['horizontal_position'])) ? $args['desktop']['horizontal_position'] : (is_rtl() ? 'right' : 'left');
            $default_vertical_position = (isset($args['desktop']['vertical_position'])) ? $args['desktop']['vertical_position'] : 'bottom';
            $default_desktop_class = (isset($args['desktop']['class'])) ? $args['desktop']['class'] : null;
            $an_register_locations[$location_id] = array(
                'id' => (isset($args['id'])) ? $args['id'] : null,
                'type' => (isset($args['type'])) ? $args['type'] : '',
                'class' => (isset($args['class'])) ? ' ' . $args['class'] : '',
                'label' => (isset($args['label'])) ? $args['label'] : __('Location', 'advanced-notifications') . '-' . $location_id,
                'desktop' => array(
                    'width_val' => (isset($args['desktop']['width_val'])) ? $args['desktop']['width_val'] : '350',
                    'width_unit' => (isset($args['desktop']['width_unit'])) ? $args['desktop']['width_unit'] : 'px',
                    'horizontal_position' => $default_horizontal_position,
                    'horizontal_val' => (isset($args['desktop']['horizontal_val'])) ? $args['desktop']['horizontal_val'] : '5',
                    'horizontal_unit' => (isset($args['desktop']['horizontal_unit'])) ? $args['desktop']['horizontal_unit'] : 'px',
                    'vertical_position' => $default_vertical_position,
                    'vertical_val' => (isset($args['desktop']['vertical_val'])) ? $args['desktop']['vertical_val'] : '5',
                    'vertical_unit' => (isset($args['desktop']['vertical_unit'])) ? $args['desktop']['vertical_unit'] : 'px',
                    'class' => $default_desktop_class,
                ),
                'tablet' => array(
                    'width_val' => (isset($args['tablet']['width_val'])) ? $args['tablet']['width_val'] : '350',
                    'width_unit' => (isset($args['tablet']['width_unit'])) ? $args['tablet']['width_unit'] : 'px',
                    'horizontal_position' => (isset($args['tablet']['horizontal_position'])) ? $args['tablet']['horizontal_position'] : $default_horizontal_position,
                    'horizontal_val' => (isset($args['tablet']['horizontal_val'])) ? $args['tablet']['horizontal_val'] : '5',
                    'horizontal_unit' => (isset($args['tablet']['horizontal_unit'])) ? $args['tablet']['horizontal_unit'] : 'px',
                    'vertical_position' => (isset($args['tablet']['vertical_position'])) ? $args['tablet']['vertical_position'] : $default_vertical_position,
                    'vertical_val' => (isset($args['tablet']['vertical_val'])) ? $args['tablet']['vertical_val'] : '5',
                    'vertical_unit' => (isset($args['tablet']['vertical_unit'])) ? $args['tablet']['vertical_unit'] : 'px',
                    'class' => (isset($args['tablet']['class'])) ? $args['tablet']['class'] : $default_desktop_class,
                ),
                'mobile' => array(
                    'width_val' => (isset($args['mobile']['width_val'])) ? $args['mobile']['width_val'] : '96',
                    'width_unit' => (isset($args['mobile']['width_unit'])) ? $args['mobile']['width_unit'] : '%',
                    'horizontal_position' => (isset($args['mobile']['horizontal_position'])) ? $args['mobile']['horizontal_position'] : ((isset($args['tablet']['horizontal_position']) && $args['tablet']['horizontal_position'] != null) ? : $default_horizontal_position),
                    'horizontal_val' => (isset($args['mobile']['horizontal_val'])) ? $args['mobile']['horizontal_val'] : '2',
                    'horizontal_unit' => (isset($args['mobile']['horizontal_unit'])) ? $args['mobile']['horizontal_unit'] : '%',
                    'vertical_position' => (isset($args['mobile']['vertical_position'])) ? $args['mobile']['vertical_position'] : ((isset($args['tablet']['vertical_position']) && $args['tablet']['vertical_position'] != null) ? : $default_vertical_position),
                    'vertical_val' => (isset($args['mobile']['vertical_val'])) ? $args['mobile']['vertical_val'] : '5',
                    'vertical_unit' => (isset($args['mobile']['vertical_unit'])) ? $args['mobile']['vertical_unit'] : 'px',
                    'class' => (isset($args['mobile']['class'])) ? $args['mobile']['class'] : $default_desktop_class,
                ),
                'z_index' => (isset($args['z_index'])) ? $args['z_index'] : 999,
            );
        }
        return apply_filters('an_register_locations', $an_register_locations);
    }
    /* GET LOCATIONS
    ================================================== */
    function locations_list($cache=true) {
        global $an_locations;
        return $an_locations->locations_list($cache);
    }
    /* ADD DESIGNS
    ================================================== */
    function add_design($args) {
        global $an_register_designs;
        $args = apply_filters('an_pre_add_design', $args);
        $design_id = (isset($args['id'])) ? $args['id'] : null;
        if ($design_id != null) {
            $design = array(
                'id' => (isset($args['id'])) ? $args['id'] : null,
                'label' => (isset($args['label'])) ? $args['label'] : __('Design', 'advanced-notifications') . '-' . $design_id,
                'text_color' => (isset($args['text_color'])) ? $args['text_color'] : null,
                'link_color' => (isset($args['link_color'])) ? $args['link_color'] : null,
                'background_color' => (isset($args['background_color'])) ? $args['background_color'] : null,
                'background_image' => (isset($args['background_image'])) ? $args['background_image'] : null,
                'background_size' => (isset($args['background_size'])) ? $args['background_size'] : 'auto',
                'background_repeat' => (isset($args['background_repeat'])) ? $args['background_repeat'] : 'no-repeat',
                'border_style' => (isset($args['border_style'])) ? $args['border_style'] : 'solid',
                'border_color' => (isset($args['border_color'])) ? $args['border_color'] : '',
                'desktop' => array(
                    'font_size' => $font_size = (isset($args['desktop']['font_size'])) ? $args['desktop']['font_size'] : '14',
                    'border_top' => $border_top = (isset($args['desktop']['border_top'])) ? $args['desktop']['border_top'] : '0',
                    'border_right' => $border_right = (isset($args['desktop']['border_right'])) ? $args['desktop']['border_right'] : '0',
                    'border_bottom' => $border_bottom = (isset($args['desktop']['border_bottom'])) ? $args['desktop']['border_bottom'] : '0',
                    'border_left' => $border_left = (isset($args['desktop']['border_left'])) ? $args['desktop']['border_left'] : '0',
                    'border_radius_top_left' => $border_radius_top_left = (isset($args['desktop']['border_radius_top_left'])) ? $args['desktop']['border_radius_top_left'] : '5',
                    'border_radius_top_right' => $border_radius_top_right = (isset($args['desktop']['border_radius_top_right'])) ? $args['desktop']['border_radius_top_right'] : '5',
                    'border_radius_bottom_right' => $border_radius_bottom_right = (isset($args['desktop']['border_radius_bottom_right'])) ? $args['desktop']['border_radius_bottom_right'] : '5',
                    'border_radius_bottom_left' => $border_radius_bottom_left = (isset($args['desktop']['border_radius_bottom_left'])) ? $args['desktop']['border_radius_bottom_left'] : '5',
                    'padding_top' => $padding_top = (isset($args['desktop']['padding_top'])) ? $args['desktop']['padding_top'] : '25',
                    'padding_right' => $padding_right = (isset($args['desktop']['padding_right'])) ? $args['desktop']['padding_right'] : '25',
                    'padding_bottom' => $padding_bottom = (isset($args['desktop']['padding_bottom'])) ? $args['desktop']['padding_bottom'] : '25',
                    'padding_left' => $padding_left = (isset($args['desktop']['padding_left'])) ? $args['desktop']['padding_left'] : '25',
                    'margin_top' => $margin_top = (isset($args['desktop']['margin_top'])) ? $args['desktop']['margin_top'] : '10',
                    'margin_right' => $margin_right = (isset($args['desktop']['margin_right'])) ? $args['desktop']['margin_right'] : '0',
                    'margin_bottom' => $margin_bottom = (isset($args['desktop']['margin_bottom'])) ? $args['desktop']['margin_bottom'] : '0',
                    'margin_left' => $margin_left = (isset($args['desktop']['margin_left'])) ? $args['desktop']['margin_left'] : '0',
                ),
                'tablet' => array(
                    'font_size' => $font_size = (isset($args['tablet']['font_size']) && $args['tablet']['font_size'] != null) ? $args['tablet']['font_size'] : $font_size,
                    'border_top' => $border_top = (isset($args['tablet']['border_top']) && $args['tablet']['border_top'] != null) ? $args['tablet']['border_top'] : $border_top,
                    'border_right' => $border_right = (isset($args['tablet']['border_right']) && $args['tablet']['border_right'] != null) ? $args['tablet']['border_right'] : $border_right,
                    'border_bottom' => $border_bottom = (isset($args['tablet']['border_bottom']) && $args['tablet']['border_bottom'] != null) ? $args['tablet']['border_bottom'] : $border_bottom,
                    'border_left' => $border_left = (isset($args['tablet']['border_left']) && $args['tablet']['border_left'] != null) ? $args['tablet']['border_left'] : $border_left,
                    'border_radius_top_left' => $border_radius_top_leftt = (isset($args['tablet']['border_radius_top_left']) && $args['tablet']['border_radius_top_left'] != null) ? $args['tablet']['border_radius_top_left'] : $border_radius_top_left,
                    'border_radius_top_right' => $border_radius_top_right = (isset($args['tablet']['border_radius_top_right']) && $args['tablet']['border_radius_top_right'] != null) ? $args['tablet']['border_radius_top_right'] : $border_radius_top_right,
                    'border_radius_bottom_right' => $border_radius_bottom_right = (isset($args['tablet']['border_radius_bottom_right']) && $args['tablet']['border_radius_bottom_right'] != null) ? $args['tablet']['border_radius_bottom_right'] : $border_radius_bottom_right,
                    'border_radius_bottom_left' => $border_radius_bottom_left = (isset($args['tablet']['border_radius_bottom_left']) && $args['tablet']['border_radius_bottom_left'] != null) ? $args['tablet']['border_radius_bottom_left'] : $border_radius_bottom_left,
                    'padding_top' => $padding_top = (isset($args['tablet']['padding_top']) && $args['tablet']['padding_top'] != null) ? $args['tablet']['padding_top'] : $padding_top,
                    'padding_right' => $padding_right = (isset($args['tablet']['padding_right']) && $args['tablet']['padding_right'] != null) ? $args['tablet']['padding_right'] : $padding_right,
                    'padding_bottom' => $padding_bottom = (isset($args['tablet']['padding_bottom']) && $args['tablet']['padding_bottom'] != null) ? $args['tablet']['padding_bottom'] : $padding_bottom,
                    'padding_left' => $padding_left = (isset($args['tablet']['padding_left']) && $args['tablet']['padding_left'] != null) ? $args['tablet']['padding_left'] : $padding_left,
                    'margin_top' => $margin_top = (isset($args['tablet']['margin_top']) && $args['tablet']['margin_top'] != null) ? $args['tablet']['margin_top'] : $margin_top,
                    'margin_right' => $margin_right = (isset($args['tablet']['margin_right']) && $args['tablet']['margin_right'] != null) ? $args['tablet']['margin_right'] : $margin_right,
                    'margin_bottom' => $margin_bottom = (isset($args['tablet']['margin_bottom']) && $args['tablet']['margin_bottom'] != null) ? $args['tablet']['margin_bottom'] : $margin_bottom,
                    'margin_left' => $margin_left = (isset($args['tablet']['margin_left']) && $args['tablet']['margin_left'] != null) ? $args['tablet']['margin_left'] : $margin_left,
                ),
                'mobile' => array(
                    'font_size' => (isset($args['mobile']['font_size']) && $args['mobile']['font_size'] != null) ? $args['mobile']['font_size'] : $font_size,
                    'border_top' => (isset($args['mobile']['border_top']) && $args['mobile']['border_top'] != null) ? $args['mobile']['border_top'] : $border_top,
                    'border_right' => (isset($args['mobile']['border_right']) && $args['mobile']['border_right'] != null) ? $args['mobile']['border_right'] : $border_right,
                    'border_bottom' => (isset($args['mobile']['border_bottom']) && $args['mobile']['border_bottom'] != null) ? $args['mobile']['border_bottom'] : $border_bottom,
                    'border_left' => (isset($args['mobile']['border_left']) && $args['mobile']['border_left'] != null) ? $args['mobile']['border_left'] : $border_left,
                    'border_radius_top_left' => (isset($args['mobile']['border_radius_top_left']) && $args['mobile']['border_radius_top_left'] != null) ? $args['mobile']['border_radius_top_left'] : $border_radius_top_left,
                    'border_radius_top_right' => (isset($args['mobile']['border_radius_top_right']) && $args['mobile']['border_radius_top_right'] != null) ? $args['mobile']['border_radius_top_right'] : $border_radius_top_right,
                    'border_radius_bottom_right' => (isset($args['mobile']['border_radius_bottom_right']) && $args['mobile']['border_radius_bottom_right'] != null) ? $args['mobile']['border_radius_bottom_right'] : $border_radius_bottom_right,
                    'border_radius_bottom_left' => (isset($args['mobile']['border_radius_bottom_left']) && $args['mobile']['border_radius_bottom_left'] != null) ? $args['mobile']['border_radius_bottom_left'] : $border_radius_bottom_left,
                    'padding_top' => (isset($args['mobile']['padding_top']) && $args['mobile']['padding_top'] != null) ? $args['mobile']['padding_top'] : $padding_top,
                    'padding_right' => (isset($args['mobile']['padding_right']) && $args['mobile']['padding_right'] != null) ? $args['mobile']['padding_right'] : $padding_right,
                    'padding_bottom' => (isset($args['mobile']['padding_bottom']) && $args['mobile']['padding_bottom'] != null) ? $args['mobile']['padding_bottom'] : $padding_bottom,
                    'padding_left' => (isset($args['mobile']['padding_left']) && $args['mobile']['padding_left'] != null) ? $args['mobile']['padding_left'] : $padding_left,
                    'margin_top' => (isset($args['mobile']['margin_top']) && $args['mobile']['margin_top'] != null) ? $args['mobile']['margin_top'] : $margin_top,
                    'margin_right' => (isset($args['mobile']['margin_right']) && $args['mobile']['margin_right'] != null) ? $args['mobile']['margin_right'] : $margin_right,
                    'margin_bottom' => (isset($args['mobile']['margin_bottom']) && $args['mobile']['margin_bottom'] != null) ? $args['mobile']['margin_bottom'] : $margin_bottom,
                    'margin_left' => (isset($args['mobile']['margin_left']) && $args['mobile']['margin_left'] != null) ? $args['mobile']['margin_left'] : $margin_left,

                ),
                'close_button_enable' => (isset($args['close_button_enable'])) ? $args['close_button_enable'] : 'checked',
                'close_button_type' => (isset($args['close_button_type'])) ? $args['close_button_type'] : 'icon',
                'close_icon_style' => (isset($args['close_icon_style'])) ? $args['close_icon_style'] : '✕',
                'close_text' => (isset($args['close_text'])) ? $args['close_text'] : __('Close', 'advanced-notifications'),
                'close_button_position' => (isset($args['close_button_position'])) ? $args['close_button_position'] : 'custom',
                'close_button_horizontal_position' => (isset($args['close_button_horizontal_position'])) ? $args['close_button_horizontal_position'] : (is_rtl() ? 'left' : 'right'),
                'close_button_horizontal_position_val' => (isset($args['close_button_horizontal_position_val'])) ? $args['close_button_horizontal_position_val'] : '5',
                'close_button_horizontal_position_unit' => (isset($args['close_button_horizontal_position_unit'])) ? $args['close_button_horizontal_position_unit'] : 'px',
                'close_button_vertical_position' => (isset($args['close_button_vertical_position'])) ? $args['close_button_vertical_position'] : 'top',
                'close_button_vertical_position_val' => (isset($args['close_button_vertical_position_val'])) ? $args['close_button_vertical_position_val'] : '5',
                'close_button_vertical_position_unit' => (isset($args['close_button_vertical_position_unit'])) ? $args['close_button_vertical_position_unit'] : 'px',
                'close_button_color' => (isset($args['close_button_color'])) ? $args['close_button_color'] : null,
                'close_button_background_color' => (isset($args['close_button_background_color'])) ? $args['close_button_background_color'] : null,
                'desktop_close_button' => array(
                    'font_size' => $close_button_font_size = (isset($args['desktop_close_button']['font_size'])) ? $args['desktop_close_button']['font_size'] : '18',
                ),
                'tablet_close_button' => array(
                    'font_size' => $close_button_font_size = (isset($args['tablet_close_button']['font_size']) && $args['tablet_close_button']['font_size'] != null) ? $args['tablet_close_button']['font_size'] : $close_button_font_size,
                ),
                'mobile_close_button' => array(
                    'font_size' => (isset($args['mobile_close_button']['font_size']) && $args['mobile_close_button']['font_size'] != null) ? $args['mobile_close_button']['font_size'] : $close_button_font_size,
                ),
            );
            $an_register_designs[$design_id] = apply_filters('an_pre_register_design', $design, $args);
        }
        return apply_filters('an_register_designs', $an_register_designs);
    }
    /* GET DESIGNS
    ================================================== */
    function designs_list($cache=true) {
        global $an_designs;
        return $an_designs->designs_list($cache);
    }
    /* ADD TRIGGER
    ================================================== */
    function add_trigger($args) {
        global $an_register_triggers;
        $trigger_id = (isset($args['id'])) ? $args['id'] : null;
        if ($trigger_id != null) {
            $an_register_triggers[$trigger_id] = $args;
        }
        return apply_filters('an_register_triggers', $an_register_triggers);
    }
    /* GET TRIGGERS
    ================================================== */
    function triggers_list($cache=true) {
        global $an_triggers;
        return $an_triggers->triggers_list($cache);
    }
    /* IS SHOW
    ================================================== */
    function is_show($notification, $item_id, $cache=true) {
        global $an_functions;
        return $an_functions->is_show($notification, $item_id, $cache);
    }
}
$an_api = new AdvancedNotificationsAPI();
