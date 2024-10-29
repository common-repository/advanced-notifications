<?php
/*
*
*	Advanced Notifications V 1
*	------------------------------------------------
* 	Powered by  - https://wiliba.com
*
*/

function an_interface() {
	// SETTINGS
	$args = array(
		'settings_title' => __( 'Settings', 'advanced-notifications' ),
		'admin_menu_name' => __( 'Settings', 'advanced-notifications' ),
		'admin_menu_parent' => 'edit.php?post_type=a_notifications',
		'order' => 99,
	);
	eis_register_interface('an_settings', $args);
	// NOTIFICATIONS
	$args = array(
		'type' => 'meta_box',
		'post_type' => 'a_notifications',
		'tab_save' => false,
		'meta_box_priority' => 'high',
		'settings_title' => __( 'Notification settings', 'advanced-notifications' ),
	);
	eis_register_interface('a_notifications', $args);
	// DESIGNS
	$args = array(
		'type' => 'meta_box',
		'post_type' => 'an_designs',
		'tab_save' => false,
		'meta_box_priority' => 'high',
		'settings_title' => __( 'Designs settings', 'advanced-notifications' ),
	);
	eis_register_interface('an_designs', $args);
	// TRIGGERS
	$args = array(
		'type' => 'meta_box',
		'post_type' => 'an_triggers',
		'tab_save' => false,
		'meta_box_priority' => 'high',
		'settings_title' => __( 'Triggers settings', 'advanced-notifications' ),
	);
	eis_register_interface('an_triggers', $args);
	// LOCATIONS
	$args = array(
		'type' => 'meta_box',
		'post_type' => 'an_locations',
		'tab_save' => false,
		'meta_box_priority' => 'high',
		'settings_title' => __( 'Location settings', 'advanced-notifications' ),
	);
	eis_register_interface('an_locations', $args);
}
add_action('add_eis_register_interface', 'an_interface');

function an_interface_options() {
	global $an_core, $an_functions, $an_api, $an_animations;
	// SETTINGS
	// eis_tab('an_settings', 'general', __('General', 'advanced-notifications') );
	eis_tab('an_settings', 'layout', __('Layout', 'advanced-notifications') );
	$yes_no = array (
		'yes' => __('Yes', 'advanced-notifications'),
		'no' => __('No', 'advanced-notifications'),
	);

	$no_yes = array (
		'no' => __('No', 'advanced-notifications'),
		'yes' => __('Yes', 'advanced-notifications'),
	);
	$no_yes_pro = array (
		'no' => __('No', 'advanced-notifications'),
		'yes_eis_disabled' => __('Yes', 'advanced-notifications') . ' - ' . __('PRO', 'advanced-notifications'),
	);
	// eis_tab_option( array(
	// 	'interface_id' => 'an_settings',
	// 	'tab_id' => 'general',
	// 	'input_id' => 'test',
	// 	'input_type' => 'date',
	// 	'label' =>  __('TEST', 'advanced-notifications'),
	// ));
	// eis_tab_option( array(
	// 	'interface_id' => 'an_settings',
	// 	'tab_id' => 'general',
	// 	'input_id' => 'test1',
	// 	'input_type' => 'date',
	// 	'label' =>  __('TEST', 'advanced-notifications'),
	// ));
	eis_tab_option( array(
		'interface_id' => 'an_settings',
		'tab_id' => 'layout',
		'input_id' => 'mobile_breakpoint',
		'input_type' => 'number',
		'label' =>  __('Mobile Breakpoint (px)', 'advanced-notifications'),
		'placeholder' => '767',
		'default' => '767',
		// 'css_var' => 'mobile_breakpoint',
		// 'css_units' => 'px',
	));
	eis_tab_option( array(
		'interface_id' => 'an_settings',
		'tab_id' => 'layout',
		'input_id' => 'tablet_breakpoint',
		'input_type' => 'number',
		'label' =>  __('Tablet Breakpoint (px)', 'advanced-notifications'),
		'placeholder' => '1024',
		'default' => '1024',
		// 'css_var' => 'tablet_breakpoint',
		// 'css_units' => 'px',
	));
	// NOTIFICATIONS
	eis_tab('a_notifications', 'general', __('General', 'advanced-notifications') );
	eis_tab('a_notifications', 'display', __('Display', 'advanced-notifications') );
	eis_tab('a_notifications', 'publish', __('Publish', 'advanced-notifications') );
	eis_tab('a_notifications', 'rules', __('Rules', 'advanced-notifications') );
	function an_position_unit() {
		return array(
			'px' => __('px', 'advanced-notifications'),
			'%' => __('%', 'advanced-notifications'),
		);
	}
	// general
	function an_add_pro_notification_types($notification_types) {
		global $an_core;
		if ($an_core->license_type() == 'basic') {
			$notification_types['elementor_eis_disabled'] = __('Elementor', 'advanced-notifications') . ' - ' . __('PRO', 'advanced-notifications');
			$notification_types['product_eis_disabled'] = __('Product', 'advanced-notifications') . ' - ' . __('PRO', 'advanced-notifications');
		}
		return $notification_types;
	}
	add_filter('an_notification_types', 'an_add_pro_notification_types');
	$notification_types = $an_functions->notification_types();
	$notification_role = array();
	foreach ($an_functions->an_notification_roles() as $key => $role_name) {
		if ($key == 'all') {
			$notification_role['all'] = $role_name;
		} else {
			$notification_role[$key . '_eis_disabled'] = $role_name . ' - ' . __('PRO', 'advanced-notifications');
		}
	}
	eis_tab_option( array(
		'interface_id' => 'a_notifications',
		'tab_id' => 'general',
		'input_id' => 'status',
		'input_type' => 'checkbox',
		'label' =>  __('Notification status', 'advanced-notifications'),
		'description' => __('Enable / disable the notification, if checked the notification is active', 'advanced-notifications'),
		'checkbox_checked' => true,
		'order' => 1,
	));
	eis_tab_option( array(
		'interface_id' => 'a_notifications',
		'tab_id' => 'general',
		'input_id' => 'type',
		'input_type' => 'select',
		'label' =>  __('Notification type', 'advanced-notifications'),
		'description' => '',
		'input_select_options' => $notification_types,
		'order' => 2,
	));
	eis_tab_option( array(
		'interface_id' => 'a_notifications',
		'tab_id' => 'general',
		'input_id' => 'type_html',
		'input_type' => 'textarea',
		'label' =>  __('Notification html', 'advanced-notifications'),
		'description' => '',
		'parent_name' => 'type',
		'show_on_value' => 'html',
		'multilingual' => true,
		'shortcode' => true,
	));
	eis_tab_option( array(
		'interface_id' => 'a_notifications',
		'tab_id' => 'general',
		'input_id' => 'type_image',
		'input_type' => 'image',
		'label' =>  __('Notification image', 'advanced-notifications'),
		'description' => '',
		'parent_name' => 'type',
		'show_on_value' => 'image',
		'multilingual' => true,
	));
	if ($an_core->license_type() == 'basic') {
		eis_tab_option( array(
			'interface_id' => 'a_notifications',
			'tab_id' => 'general',
			'input_id' => 'role',
			'input_type' => 'select',
			'label' =>  __('Who can see?', 'advanced-notifications'),
			'description' => '',
			'input_select_options' => $notification_role,
			'order' => 3,
		));
		eis_tab_option( array(
			'interface_id' => 'a_notifications',
			'tab_id' => 'general',
			'input_id' => 'scheduled',
			'input_type' => 'select',
			'label' =>  __('Scheduled notification', 'advanced-notifications'),
			'description' => '',
			'input_select_options' => $no_yes_pro,
			'order' => 4,
		));
	}
	// display
	$devices_list = $an_functions->devices_list();
	$locations = array();
	foreach ($an_api->locations_list() as $location) {
		$locations[$location['id']] = $location['label'];
	}
	$designs = array();
	foreach ($an_api->designs_list() as $design) {
		$designs[$design['id']] = $design['label'];
	}
	$show_when = $an_functions->show_when();

	$animations_in = $an_animations->an_animations_in();
	$animations_out = $an_animations->an_animations_out();

	eis_tab_option( array(
		'interface_id' => 'a_notifications',
		'tab_id' => 'display',
		'input_id' => 'devices_list',
		'input_type' => 'select',
		'label' =>  __('Devices', 'advanced-notifications'),
		'description' => '',
		'input_select_options' => $devices_list,
		'order' => 1,
	));
	eis_tab_option( array(
		'interface_id' => 'a_notifications',
		'tab_id' => 'display',
		'input_id' => 'location',
		'input_type' => 'select',
		'label' =>  __('Location', 'advanced-notifications'),
		'description' => '',
		'input_select_options' => apply_filters('an_locations_select_list', $locations),
		'default' => (is_rtl() ? 'bottom_right' : 'bottom_left'),
		'order' => 3,
		'parent_name' => 'type',
		'hide_on_value' => 'sidebar',
	));
	eis_tab_option( array(
		'interface_id' => 'a_notifications',
		'tab_id' => 'display',
		'input_id' => 'location_image',
		'input_type' => 'image_preview',
		'label' =>  __('Location preview', 'advanced-notifications'),
		'note' => __('width', 'advanced-notifications') . ': 350px',
		'image_preview_url' => ADVANCED_NOTIFICATIONS_URL_PATH . 'assets/images/bottom-left.png',
		'parent_name' => 'location',
		'show_on_value' => 'bottom_left',
	));
	eis_tab_option( array(
		'interface_id' => 'a_notifications',
		'tab_id' => 'display',
		'input_id' => 'location_image',
		'input_type' => 'image_preview',
		'label' =>  __('Location preview', 'advanced-notifications'),
		'note' => __('width', 'advanced-notifications') . ': 350px',
		'image_preview_url' => ADVANCED_NOTIFICATIONS_URL_PATH . 'assets/images/bottom-right.png',
		'parent_name' => 'location',
		'show_on_value' => 'bottom_right',
	));
	eis_tab_option( array(
		'interface_id' => 'a_notifications',
		'tab_id' => 'display',
		'input_id' => 'location_image',
		'input_type' => 'image_preview',
		'label' =>  __('Location preview', 'advanced-notifications'),
		'note' => __('width', 'advanced-notifications') . ': 350px',
		'image_preview_url' => ADVANCED_NOTIFICATIONS_URL_PATH . 'assets/images/top-left.png',
		'parent_name' => 'location',
		'show_on_value' => 'top_left',
	));
	eis_tab_option( array(
		'interface_id' => 'a_notifications',
		'tab_id' => 'display',
		'input_id' => 'location_image',
		'input_type' => 'image_preview',
		'label' =>  __('Location preview', 'advanced-notifications'),
		'note' => __('width', 'advanced-notifications') . ': 350px',
		'image_preview_url' => ADVANCED_NOTIFICATIONS_URL_PATH . 'assets/images/top-right.png',
		'parent_name' => 'location',
		'show_on_value' => 'top_right',
	));
	eis_tab_option( array(
		'interface_id' => 'a_notifications',
		'tab_id' => 'display',
		'input_id' => 'design',
		'input_type' => 'select',
		'label' =>  __('Design', 'advanced-notifications'),
		'description' => '',
		'input_select_options' => apply_filters('an_designs_select_list', $designs),
		'default' => 'success',
		'order' => 5,
		'parent_name' => 'type',
		'hide_on_value' => 'sidebar',
	));
	eis_tab_option( array(
		'interface_id' => 'a_notifications',
		'tab_id' => 'display',
		'input_id' => 'class',
		'input_type' => 'text',
		'label' =>  __('CSS Class', 'advanced-notifications'),
		'description' => '',
		'order' => 5,
	));
	eis_tab_option( array(
		'interface_id' => 'a_notifications',
		'tab_id' => 'display',
		'input_id' => 'show_when',
		'input_type' => 'select',
		'label' =>  __('Show when', 'advanced-notifications'),
		'note' => __('The notification will be displayed after the page is loaded or in the defined triggers', 'advanced-notifications'),
		'input_select_options' => $show_when,
		'default' => 'page_loaded_trigers',
		'order' => 6,
	));
	eis_tab_option( array(
		'interface_id' => 'a_notifications',
		'tab_id' => 'display',
		'input_id' => 'delay',
		'input_type' => 'number',
		'min' => '0',
		'label' =>  __('Delay before show', 'advanced-notifications'),
		'note' => __('The time it takes the notification to appear after the page is loaded (seconds)', 'advanced-notifications'),
		'default' => 2,
		'parent_name' => 'show_when',
		'show_on_value' => 'page_loaded_trigers',
		'order' => 7,
	));
	eis_tab_option( array(
		'interface_id' => 'a_notifications',
		'tab_id' => 'display',
		'input_id' => 'show_time',
		'input_type' => 'number',
		'min' => '0',
		'label' =>  __('Show time', 'advanced-notifications'),
		'note' => __('The duration of the notification will appear in seconds', 'advanced-notifications'),
		'default' => 8,
		'order' => 9,
	));
	eis_tab_option( array(
		'interface_id' => 'a_notifications',
		'tab_id' => 'display',
		'input_id' => 'animation_in',
		'input_type' => 'select',
		'label' =>  __('Animation In', 'advanced-notifications'),
		'description' => '',
		'input_select_options' => $animations_in,
		'default' => (is_rtl() ? 'an-bounce-in-right' : 'an-bounce-in-left'),
		'order' => 11,
		'parent_name' => 'type',
		'hide_on_value' => 'sidebar',
	));
	eis_tab_option( array(
		'interface_id' => 'a_notifications',
		'tab_id' => 'display',
		'input_id' => 'animation_in_duration',
		'input_type' => 'number',
		'min' => '0',
		'label' =>  __('Animation In duration', 'advanced-notifications'),
		'note' => __('The duration of the Animation will appear in seconds', 'advanced-notifications'),
		'default' => 1,
		'order' => 13,
	));
	eis_tab_option( array(
		'interface_id' => 'a_notifications',
		'tab_id' => 'display',
		'input_id' => 'animation_out',
		'input_type' => 'select',
		'label' =>  __('Animation Out', 'advanced-notifications'),
		'description' => '',
		'input_select_options' => $animations_out,
		'default' => (is_rtl() ? 'an-slide-out-right' : 'an-slide-out-left'),
		'order' => 15,
		'parent_name' => 'type',
		'hide_on_value' => 'sidebar',
	));
	eis_tab_option( array(
		'interface_id' => 'a_notifications',
		'tab_id' => 'display',
		'input_id' => 'animation_out_duration',
		'input_type' => 'number',
		'min' => '0',
		'label' =>  __('Animation Out duration', 'advanced-notifications'),
		'note' => __('The duration of the Animation will appear in seconds', 'advanced-notifications'),
		'default' => 1,
		'order' => 17,
	));
	// publish
	$publish_type = $an_functions->publish_type();
	function an_post_types_options($option) {
		global $an_functions;
		$input_id = $option['input_id'];
		$print_fun = '<div class="an-options-container">';
		foreach ($an_functions->an_post_types() as $post_type_val => $post_type_name) {
			$checkbox_val = (isset($option['value'][$post_type_val]) && $option['value'][$post_type_val] != 'no') ? $input_id . '_checked' : 'no';
			$checked = ($checkbox_val != 'no') ? " checked" : null;
			$print_fun .= '<div>';
			$print_fun .= '	<input name="' . $input_id . '_' . $post_type_val . '_display_eis_checkbox" id="' . $post_type_val . '_display_eis_checkbox" type="checkbox" value="' . $input_id . '_checked"' . $checked . '><label for="' . $input_id . '_' . $post_type_val . '_display_eis_checkbox">' . __($post_type_name, 'advanced-notifications') . '</label>';
			$print_fun .= '	<input name="a_notifications_eis_options[' . $input_id . '][' . $post_type_val . ']" type="hidden" value="' . $checkbox_val . '">';
			$print_fun .= '</div>';
		}
		$print_fun .= '<div class="an-options-bulk"><a href="#" class="an-options-all">' . __('Select all', 'advanced-notifications') . '</a><a href="#" class="an-options-none">' . __('Remove all', 'advanced-notifications') . '</span></a>';
		$print_fun .= '</div>';
		return $print_fun;
	}
	function an_taxonomies_options($option) {
		global $an_functions;
		$input_id = $option['input_id'];
		$print_fun = '<div class="an-options-container">';
		foreach ($an_functions->an_taxonomies() as $taxonomies_val => $taxonomies_name) {
			$checkbox_val = (isset($option['value'][$taxonomies_val]) && $option['value'][$taxonomies_val] != 'no') ? $input_id . '_checked' : 'no';
			$checked = ($checkbox_val != 'no') ? " checked" : null;
			$print_fun .= '<div>';
			$print_fun .= '	<input name="' . $input_id . '_' . $taxonomies_val . '_display_eis_checkbox" id="' . $taxonomies_val . '_display_eis_checkbox" type="checkbox" value="' . $input_id . '_checked"' . $checked . '><label for="' . $input_id . '_' . $taxonomies_val . '_display_eis_checkbox">' . __($taxonomies_name, 'advanced-notifications') . '</label>';
			$print_fun .= '	<input name="a_notifications_eis_options[' . $input_id . '][' . $taxonomies_val . ']" type="hidden" value="' . $checkbox_val . '">';
			$print_fun .= '</div>';
		}
		$print_fun .= '<div class="an-options-bulk"><a href="#" class="an-options-all">' . __('Select all', 'advanced-notifications') . '</a><a href="#" class="an-options-none">' . __('Remove all', 'advanced-notifications') . '</span></a>';
		$print_fun .= '</div>';
		return $print_fun;
	}
	function an_specific_pages_options($option) {
		global $an_functions;
		$input_id = $option['input_id'];
		$print_fun = '<div class="an-options-container">';

		// Front Page
		$front_page_val = (isset($option['value']['front_page']) && $option['value']['front_page'] != 'no') ? $input_id . '_checked' : 'no';
		$front_page_checked = ($front_page_val != 'no') ? " checked" : null;
		$print_fun .= '<div>';
		$print_fun .= '	<input name="' . $input_id . '_front_page_display_eis_checkbox" id="' . $input_id . '_front_page_display_eis_checkbox" type="checkbox" value="' . $input_id . '_checked"' . checked( $front_page_val, $input_id . '_checked', false ) . '><label for="' . $input_id . '_front_page_display_eis_checkbox">' . __('Front Page (Home)', 'advanced-notifications') . '</label>';
		$print_fun .= '	<input name="a_notifications_eis_options[' . $input_id . '][front_page]" type="hidden" value="' . $front_page_val . '">';
		$print_fun .= '</div>';
		// Posts Page
		$posts_page_val = (isset($option['value']['posts_page']) && $option['value']['posts_page'] != 'no') ? $input_id . '_checked' : 'no';
		$posts_page_checked = ($posts_page_val != 'no') ? " checked" : null;
		$print_fun .= '<div>';
		$print_fun .= '	<input name="' . $input_id . '_posts_page_display_eis_checkbox" id="' . $input_id . '_posts_page_display_eis_checkbox" type="checkbox" value="' . $input_id . '_checked"' . checked( $posts_page_val, $input_id . '_checked', false ) . '><label for="' . $input_id . '_posts_page_display_eis_checkbox">' . __('Posts Page (Blog)', 'advanced-notifications') . '</label>';
		$print_fun .= '	<input name="a_notifications_eis_options[' . $input_id . '][posts_page]" type="hidden" value="' . $posts_page_val . '">';
		$print_fun .= '</div>';

		// $print_fun .= '<div class="an-options-bulk"><a href="#" class="an-options-all">' . __('Select all', 'advanced-notifications') . '</a><a href="#" class="an-options-none">' . __('Remove all', 'advanced-notifications') . '</span></a>';
		$print_fun .= '</div>';
		return $print_fun;
	}
	eis_tab_option( array(
		'interface_id' => 'a_notifications',
		'tab_id' => 'publish',
		'input_id' => 'publish_type',
		'input_type' => 'select',
		'label' =>  __('Publish type', 'advanced-notifications'),
		'description' => '',
		'default' => 'all',
		'input_select_options' => apply_filters('an_publish_type', $publish_type),
		'order' => 1,
	));
	eis_tab_option( array(
		'interface_id' => 'a_notifications',
		'tab_id' => 'publish',
		'input_id' => 'publish_post_types',
		'input_type' => 'function',
		'label' =>  __('Post types pages', 'advanced-notifications'),
		'description' => '',
		'function_name' => 'an_post_types_options',
		'order' => 2,
		'parent_name' => 'publish_type',
		'show_on_value' => 'custom',
	));
	eis_tab_option( array(
		'interface_id' => 'a_notifications',
		'tab_id' => 'publish',
		'input_id' => 'publish_taxonomies',
		'input_type' => 'function',
		'label' =>  __('Taxonomies pages', 'advanced-notifications'),
		'description' => '',
		'function_name' => 'an_taxonomies_options',
		'order' => 3,
		'parent_name' => 'publish_type',
		'show_on_value' => 'custom',
	));
	eis_tab_option( array(
		'interface_id' => 'a_notifications',
		'tab_id' => 'publish',
		'input_id' => 'publish_specific_pages',
		'input_type' => 'function',
		'label' =>  __('Specific pages', 'advanced-notifications'),
		'description' => '',
		'function_name' => 'an_specific_pages_options',
		'note' => __('You can choose any page, post or post type for this notification. Go to edit the page or post and check the notification under the box "A notification settings".', 'advanced-notifications'),
		'order' => 3,
		'parent_name' => 'publish_type',
		'show_on_value' => 'specific',
	));
	// rules
	$close_button_limit = array(
		'none' => __('None', 'advanced-notifications'),
		'cancel_for' => __('Cancel for', 'advanced-notifications'),
	);
	$limitations = array(
		'none' => __('None', 'advanced-notifications'),
		'custom_limitations' => __('Custom limitations', 'advanced-notifications'),
	);
	function an_time_unit() {
		return array(
			'days' => __('Days', 'advanced-notifications'),
			'hours' => __('Hours', 'advanced-notifications'),
			'minutes' => __('Minutes', 'advanced-notifications'),
		);
	}
	function an_close_button_limit_for($option) {
		$print_fun = '<div class="an-options-container">';
		$width_val = (isset($option['value']['cancel_val'])) ? $option['value']['cancel_val'] : '10';
		$print_fun .= '	<input class="an-input-col-4" name="a_notifications_eis_options[close_button_limit_for][cancel_val]" type="number" value="' . $width_val . '" placeholder="">';
		$width_unit = (isset($option['value']['cancel_unit'])) ? $option['value']['cancel_unit'] : 'days';
		$print_fun .= '	<select class="an-input-col-4" name="a_notifications_eis_options[close_button_limit_for][cancel_unit]">';
		foreach (an_time_unit() as $key => $value) {
			$print_fun .= '	<option value="' . $key . '"' . selected($width_unit, $key, false) . '>' . $value . '</option>';
		}
		$print_fun .= '	</select>';
		$print_fun .= '</div>';
		return $print_fun;
	}
	function an_custom_limitations_for($option) {
		$print_fun = '<div class="an-options-container">';
		$width_val = (isset($option['value']['limit_val'])) ? $option['value']['limit_val'] : '10';
		$print_fun .= '	<input class="an-input-col-4" name="a_notifications_eis_options[custom_limitations_for][limit_val]" type="number" value="' . $width_val . '" placeholder="">';
		$width_unit = (isset($option['value']['limit_unit'])) ? $option['value']['limit_unit'] : 'days';
		$print_fun .= '	<select class="an-input-col-4" name="a_notifications_eis_options[custom_limitations_for][limit_unit]">';
		foreach (an_time_unit() as $key => $value) {
			$print_fun .= '	<option value="' . $key . '"' . selected($width_unit, $key, false) . '>' . $value . '</option>';
		}
		$print_fun .= '	</select>';
		$print_fun .= '</div>';
		return $print_fun;
	}
	eis_tab_option( array(
		'interface_id' => 'a_notifications',
		'tab_id' => 'rules',
		'input_id' => 'close_button_limit',
		'input_type' => 'select',
		'label' =>  __('On click close button', 'advanced-notifications'),
		'description' => 'Select how many days will be canceled the notification when click on button close',
		'default' => 'none',
		'input_select_options' => apply_filters('an_close_button_limit', $close_button_limit),
		'order' => 1,
	));
	eis_tab_option( array(
		'interface_id' => 'a_notifications',
		'tab_id' => 'rules',
		'input_id' => 'close_button_limit_for',
		'input_type' => 'function',
		'label' =>  __('Cancel for', 'advanced-notifications'),
		'function_name' => 'an_close_button_limit_for',
		'description' => '',
		'order' => 2,
		'parent_name' => 'close_button_limit',
		'show_on_value' => 'cancel_for',
	));
	eis_tab_option( array(
		'interface_id' => 'a_notifications',
		'tab_id' => 'rules',
		'input_id' => 'limitations',
		'input_type' => 'select',
		'label' =>  __('Limitations', 'advanced-notifications'),
		'description' => 'Limit the number of shows at selected time range',
		'default' => 'none',
		'input_select_options' => apply_filters('an_close_button_limit', $limitations),
		'order' => 3,
	));
	eis_tab_option( array(
		'interface_id' => 'a_notifications',
		'tab_id' => 'rules',
		'input_id' => 'custom_limitations_times',
		'input_type' => 'number',
		'min' => '1',
		'label' =>  __('Several times to limit', 'advanced-notifications'),
		'note' => __('Choose how many times the notification will appear before limit', 'advanced-notifications'),
		'default' => 10,
		'order' => 4,
		'parent_name' => 'limitations',
		'show_on_value' => 'custom_limitations',
	));
	eis_tab_option( array(
		'interface_id' => 'a_notifications',
		'tab_id' => 'rules',
		'input_id' => 'custom_limitations_for',
		'input_type' => 'function',
		'label' =>  __('Limit for', 'advanced-notifications'),
		'function_name' => 'an_custom_limitations_for',
		'note' => __('Select the time that the notification will be canceled after reaching the number of times', 'advanced-notifications'),
		'default' => 10,
		'order' => 5,
		'parent_name' => 'limitations',
		'show_on_value' => 'custom_limitations',
	));

	// DESIGNS
	eis_tab('an_designs', 'design', __('Design', 'advanced-notifications') );
	eis_tab('an_designs', 'close_button', __('Close button', 'advanced-notifications') );
	$background_size = array(
		'auto' => __('Auto', 'advanced-notifications'),
		'100% 100%' => __('Full', 'advanced-notifications'),
		'cover' => __('Cover', 'advanced-notifications'),
		'contain' => __('Contain', 'advanced-notifications'),
	);
	$background_repeat = array(
		'no-repeat' => __('No repeat', 'advanced-notifications'),
		'repeat' => __('Repeat', 'advanced-notifications'),
	);
	function an_border_radius($option) {
		$print_fun = '<div class="an-options-container">';
		$top_left_val = (isset($option['value']['top_left'])) ? $option['value']['top_left'] : '5';
		$print_fun .= '	<input class="an-input-col-4" name="an_designs_eis_options[border_radius][top_left]" type="number" value="' . $top_left_val . '" placeholder="">';
		$print_fun .= '	<span class="an-input-icon"> ↖ </span>';
		$top_right_val = (isset($option['value']['top_right'])) ? $option['value']['top_right'] : '5';
		$print_fun .= '	<input class="an-input-col-4" name="an_designs_eis_options[border_radius][top_right]" type="number" value="' . $top_right_val . '" placeholder="">';
		$print_fun .= '	<span class="an-input-icon"> ↗ </span>';
		$bottom_right_val = (isset($option['value']['bottom_right'])) ? $option['value']['bottom_right'] : '5';
		$print_fun .= '	<input class="an-input-col-4" name="an_designs_eis_options[border_radius][bottom_right]" type="number" value="' . $bottom_right_val . '" placeholder="">';
		$print_fun .= '	<span class="an-input-icon"> ↘ </span>';
		$bottom_left_val = (isset($option['value']['bottom_left'])) ? $option['value']['bottom_left'] : '5';
		$print_fun .= '	<input class="an-input-col-4" name="an_designs_eis_options[border_radius][bottom_left]" type="number" value="' . $bottom_left_val . '" placeholder="">';
		$print_fun .= '	<span class="an-input-icon"> ↙ </span>';
		$print_fun .= '</div>';
		return $print_fun;
	}
	function an_padding($option) {
		$print_fun = '<div class="an-options-container">';
		$top_val = (isset($option['value']['top'])) ? $option['value']['top'] : '25';
		$print_fun .= '	<input class="an-input-col-4" name="an_designs_eis_options[padding][top]" type="number" min="0" value="' . $top_val . '" placeholder="25">';
		$print_fun .= '	<span class="an-input-icon"> ↑ </span>';
		$right_val = (isset($option['value']['right'])) ? $option['value']['right'] : '25';
		$print_fun .= '	<input class="an-input-col-4" name="an_designs_eis_options[padding][right]" type="number" min="0" value="' . $right_val . '" placeholder="25">';
		$print_fun .= '	<span class="an-input-icon"> → </span>';
		$bottom_val = (isset($option['value']['bottom'])) ? $option['value']['bottom'] : '25';
		$print_fun .= '	<input class="an-input-col-4" name="an_designs_eis_options[padding][bottom]" type="number" min="0" value="' . $bottom_val . '" placeholder="25">';
		$print_fun .= '	<span class="an-input-icon"> ↓ </span>';
		$left_val = (isset($option['value']['left'])) ? $option['value']['left'] : '25';
		$print_fun .= '	<input class="an-input-col-4" name="an_designs_eis_options[padding][left]" type="number" min="0" value="' . $left_val . '" placeholder="25">';
		$print_fun .= '	<span class="an-input-icon"> ← </span>';
		$print_fun .= '</div>';
		return $print_fun;
	}
	function an_margin($option) {
		$print_fun = '<div class="an-options-container">';
		$top_val = (isset($option['value']['top'])) ? $option['value']['top'] : '10';
		$print_fun .= '	<input class="an-input-col-4" name="an_designs_eis_options[margin][top]" type="number" value="' . $top_val . '" placeholder="10">';
		$print_fun .= '	<span class="an-input-icon"> ↑ </span>';
		$right_val = (isset($option['value']['right'])) ? $option['value']['right'] : '0';
		$print_fun .= '	<input class="an-input-col-4" name="an_designs_eis_options[margin][right]" type="number" value="' . $right_val . '" placeholder="0">';
		$print_fun .= '	<span class="an-input-icon"> → </span>';
		$bottom_val = (isset($option['value']['bottom'])) ? $option['value']['bottom'] : '0';
		$print_fun .= '	<input class="an-input-col-4" name="an_designs_eis_options[margin][bottom]" type="number" value="' . $bottom_val . '" placeholder="0">';
		$print_fun .= '	<span class="an-input-icon"> ↓ </span>';
		$left_val = (isset($option['value']['left'])) ? $option['value']['left'] : '0';
		$print_fun .= '	<input class="an-input-col-4" name="an_designs_eis_options[margin][left]" type="number" value="' . $left_val . '" placeholder="0">';
		$print_fun .= '	<span class="an-input-icon"> ← </span>';
		$print_fun .= '</div>';
		return $print_fun;
	}
	function an_shadow($option) {
		$print_fun = '<div class="an-options-container">';
		$offset_x_val = (isset($option['value']['offset_x'])) ? $option['value']['offset_x'] : '0';
		$print_fun .= '	<lable class="an-input-col-4">offset x</lable>';
		$print_fun .= '	<input class="an-input-col-4" name="an_designs_eis_options[shadow][offset_x]" type="number" value="' . $offset_x_val . '" placeholder="0">';
		$print_fun .= '</div>';
		$print_fun .= '<div class="an-options-container">';
		$offset_y_val = (isset($option['value']['offset_y'])) ? $option['value']['offset_y'] : '0';
		$print_fun .= '	<lable class="an-input-col-4">offset y</lable>';
		$print_fun .= '	<input class="an-input-col-4" name="an_designs_eis_options[shadow][offset_y]" type="number" value="' . $offset_y_val . '" placeholder="0">';
		$print_fun .= '</div>';
		$print_fun .= '<div class="an-options-container">';
		$blur_radius_val = (isset($option['value']['blur_radius'])) ? $option['value']['blur_radius'] : '6';
		$print_fun .= '	<lable class="an-input-col-4">blur radius</lable>';
		$print_fun .= '	<input class="an-input-col-4" name="an_designs_eis_options[shadow][blur_radius]" type="number" value="' . $blur_radius_val . '" placeholder="6">';
		$print_fun .= '</div>';
		$print_fun .= '<div class="an-options-container">';
		$spread_radius_val = (isset($option['value']['spread_radius'])) ? $option['value']['spread_radius'] : '2';
		$print_fun .= '	<lable class="an-input-col-4">spread radius</lable>';
		$print_fun .= '	<input class="an-input-col-4" name="an_designs_eis_options[shadow][spread_radius]" type="number" value="' . $spread_radius_val . '" placeholder="2">';
		$print_fun .= '</div>';
		$print_fun .= '<div class="an-options-container">';
		$shadow_color_val = (isset($option['value']['shadow_color'])) ? $option['value']['shadow_color'] : 'rgba(0,0,0,0.3)';
		$print_fun .= '	<lable class="an-input-col-4">shadow color</lable>';
		$print_fun .= '	<input class="eis-color-picker" name="an_designs_eis_options[shadow][shadow_color]" type="text" value="' . $shadow_color_val . '" placeholder="rgba(0,0,0,0.3)">';
		$print_fun .= '</div>';
		$print_fun .= '<dev class="an-break-line"></div>';
		return $print_fun;
	}
	function an_design_screen($option) {
		$input_id = (isset($option['input_id']) && $option['input_id'] != '') ? $option['input_id'] : 'desktop';
		// Font size
		$print_fun = '<div class="an-options-container">';
		$print_fun .= '	<lable class="an-input-col-4">' . __('Font size', 'advanced-notifications') . ': </lable>';
		$font_size_val = (isset($option['value']['font_size'])) ? $option['value']['font_size'] : ($input_id=='desktop'?'14':'');
		$print_fun .= '	<input class="an-input-col-4" name="an_designs_eis_options['.$input_id.'][font_size]" type="number" min="0" value="' . $font_size_val . '" placeholder="">';
		$print_fun .= '</div>';
		// Border
		$print_fun .= '<div class="an-options-container">';
		$print_fun .= '	<lable class="an-input-col-4">' . __('Border', 'advanced-notifications') . ': </lable>';
		$border_top_val = (isset($option['value']['border_top'])) ? $option['value']['border_top'] : ($input_id=='desktop'?'0':'');
		$print_fun .= '	<input class="an-input-col-3" name="an_designs_eis_options['.$input_id.'][border_top]" type="number" min="0" value="' . $border_top_val . '" placeholder="">';
		$print_fun .= '	<span class="an-input-icon"> ↑ </span>';
		$border_right_val = (isset($option['value']['border_right'])) ? $option['value']['border_right'] : ($input_id=='desktop'?'0':'');
		$print_fun .= '	<input class="an-input-col-3" name="an_designs_eis_options['.$input_id.'][border_right]" type="number" min="0" value="' . $border_right_val . '" placeholder="">';
		$print_fun .= '	<span class="an-input-icon"> → </span>';
		$border_bottom_val = (isset($option['value']['border_bottom'])) ? $option['value']['border_bottom'] : ($input_id=='desktop'?'0':'');
		$print_fun .= '	<input class="an-input-col-3" name="an_designs_eis_options['.$input_id.'][border_bottom]" type="number" min="0" value="' . $border_bottom_val . '" placeholder="">';
		$print_fun .= '	<span class="an-input-icon"> ↓ </span>';
		$border_left_val = (isset($option['value']['border_left'])) ? $option['value']['border_left'] : ($input_id=='desktop'?'0':'');
		$print_fun .= '	<input class="an-input-col-3" name="an_designs_eis_options['.$input_id.'][border_left]" type="number" min="0" value="' . $border_left_val . '" placeholder="">';
		$print_fun .= '	<span class="an-input-icon"> ← </span>';
		$border_color_val = (isset($option['value']['border_color'])) ? $option['value']['border_color'] : '';
		$print_fun .= '</div>';
		// Border radius
		$print_fun .= '<div class="an-options-container">';
		$print_fun .= '	<lable class="an-input-col-4">' . __('Border radius', 'advanced-notifications') . ': </lable>';
		$border_radius_top_left_val = (isset($option['value']['border_radius_top_left'])) ? $option['value']['border_radius_top_left'] : ($input_id=='desktop'?'5':'');
		$print_fun .= '	<input class="an-input-col-3" name="an_designs_eis_options['.$input_id.'][border_radius_top_left]" type="number" min="0" value="' . $border_radius_top_left_val . '" placeholder="">';
		$print_fun .= '	<span class="an-input-icon"> ↖ </span>';
		$border_radius_top_right_val = (isset($option['value']['border_radius_top_right'])) ? $option['value']['border_radius_top_right'] : ($input_id=='desktop'?'5':'');
		$print_fun .= '	<input class="an-input-col-3" name="an_designs_eis_options['.$input_id.'][border_radius_top_right]" type="number" min="0" value="' . $border_radius_top_right_val . '" placeholder="">';
		$print_fun .= '	<span class="an-input-icon"> ↗ </span>';
		$border_radius_bottom_right_val = (isset($option['value']['border_radius_bottom_right'])) ? $option['value']['border_radius_bottom_right'] : ($input_id=='desktop'?'5':'');
		$print_fun .= '	<input class="an-input-col-3" name="an_designs_eis_options['.$input_id.'][border_radius_bottom_right]" type="number" min="0" value="' . $border_radius_bottom_right_val . '" placeholder="">';
		$print_fun .= '	<span class="an-input-icon"> ↘ </span>';
		$border_radius_bottom_left_val = (isset($option['value']['border_radius_bottom_left'])) ? $option['value']['border_radius_bottom_left'] : ($input_id=='desktop'?'5':'');
		$print_fun .= '	<input class="an-input-col-3" name="an_designs_eis_options['.$input_id.'][border_radius_bottom_left]" type="number" min="0" value="' . $border_radius_bottom_left_val . '" placeholder="">';
		$print_fun .= '	<span class="an-input-icon"> ↙ </span>';
		$print_fun .= '</div>';
		// Padding
		$print_fun .= '<div class="an-options-container">';
		$print_fun .= '	<lable class="an-input-col-4">' . __('Padding', 'advanced-notifications') . ': </lable>';
		$padding_top_val = (isset($option['value']['padding_top'])) ? $option['value']['padding_top'] : ($input_id=='desktop'?'25':'');
		$print_fun .= '	<input class="an-input-col-3" name="an_designs_eis_options['.$input_id.'][padding_top]" type="number" min="0" value="' . $padding_top_val . '" placeholder="">';
		$print_fun .= '	<span class="an-input-icon"> ↑ </span>';
		$padding_right_val = (isset($option['value']['padding_right'])) ? $option['value']['padding_right'] : ($input_id=='desktop'?'25':'');
		$print_fun .= '	<input class="an-input-col-3" name="an_designs_eis_options['.$input_id.'][padding_right]" type="number" min="0" value="' . $padding_right_val . '" placeholder="">';
		$print_fun .= '	<span class="an-input-icon"> → </span>';
		$padding_bottom_val = (isset($option['value']['padding_bottom'])) ? $option['value']['padding_bottom'] : ($input_id=='desktop'?'25':'');
		$print_fun .= '	<input class="an-input-col-3" name="an_designs_eis_options['.$input_id.'][padding_bottom]" type="number" min="0" value="' . $padding_bottom_val . '" placeholder="">';
		$print_fun .= '	<span class="an-input-icon"> ↓ </span>';
		$padding_left_val = (isset($option['value']['padding_left'])) ? $option['value']['padding_left'] : ($input_id=='desktop'?'25':'');
		$print_fun .= '	<input class="an-input-col-3" name="an_designs_eis_options['.$input_id.'][padding_left]" type="number" min="0" value="' . $padding_left_val . '" placeholder="">';
		$print_fun .= '	<span class="an-input-icon"> ← </span>';
		$print_fun .= '</div>';
		// Margin
		$print_fun .= '<div class="an-options-container">';
		$print_fun .= '	<lable class="an-input-col-4">' . __('Margin', 'advanced-notifications') . ': </lable>';
		$margin_top_val = (isset($option['value']['margin_top'])) ? $option['value']['margin_top'] : ($input_id=='desktop'?'10':'');
		$print_fun .= '	<input class="an-input-col-3" name="an_designs_eis_options['.$input_id.'][margin_top]" type="number" value="' . $margin_top_val . '" placeholder="">';
		$print_fun .= '	<span class="an-input-icon"> ↑ </span>';
		$margin_right_val = (isset($option['value']['margin_right'])) ? $option['value']['margin_right'] : ($input_id=='desktop'?'0':'');
		$print_fun .= '	<input class="an-input-col-3" name="an_designs_eis_options['.$input_id.'][margin_right]" type="number" value="' . $margin_right_val . '" placeholder="">';
		$print_fun .= '	<span class="an-input-icon"> → </span>';
		$margin_bottom_val = (isset($option['value']['margin_bottom'])) ? $option['value']['margin_bottom'] : ($input_id=='desktop'?'0':'');
		$print_fun .= '	<input class="an-input-col-3" name="an_designs_eis_options['.$input_id.'][margin_bottom]" type="number" value="' . $margin_bottom_val . '" placeholder="">';
		$print_fun .= '	<span class="an-input-icon"> ↓ </span>';
		$margin_left_val = (isset($option['value']['margin_left'])) ? $option['value']['margin_left'] : ($input_id=='desktop'?'0':'');
		$print_fun .= '	<input class="an-input-col-3" name="an_designs_eis_options['.$input_id.'][margin_left]" type="number" value="' . $margin_left_val . '" placeholder="">';
		$print_fun .= '	<span class="an-input-icon"> ← </span>';
		$print_fun .= '</div>';
		// description
		if ($input_id != 'desktop') {
			$print_fun .= '<p class="description">* ' . __('An empty field will be defined from the desktop field', 'advanced-notifications') . '</p>';
		}
		$print_fun .= '<dev class="an-break-line"></div>';
		return $print_fun;
	}
	function an_design_screen_close_button($option) {
		$input_id = (isset($option['input_id']) && $option['input_id'] != '') ? $option['input_id'] : 'desktop_close_button';
		$print_fun = '<div class="an-options-container">';
		$print_fun .= '	<lable class="an-input-col-4">' . __('Icon / Font size', 'advanced-notifications') . ': </lable>';
		$font_size_val = (isset($option['value']['font_size'])) ? $option['value']['font_size'] : ($input_id=='desktop_close_button'?'18':'');
		$print_fun .= '	<input class="an-input-col-4" name="an_designs_eis_options['.$input_id.'][font_size]" type="number" min="0" value="' . $font_size_val . '" placeholder="">';
		$print_fun .= '</div>';
		if ($input_id != 'desktop_close_button') {
			$print_fun .= '<p class="description">* ' . __('An empty field will be defined from the desktop field', 'advanced-notifications') . '</p>';
		}
		$print_fun .= '<dev class="an-break-line"></div>';
		return $print_fun;
	}
	$close_button_type = array(
		'icon' => __('Icon', 'advanced-notifications'),
		'button' => __('Button', 'advanced-notifications'),
		'text' => __('Text', 'advanced-notifications'),
	);
	$close_icon_style = array(
		'✕' => __('Style', 'advanced-notifications') . ' 1 - ✕',
		'✖' => __('Style', 'advanced-notifications') . ' 2 - ✖',
		'✗' => __('Style', 'advanced-notifications') . ' 3 - ',
		'✘' => __('Style', 'advanced-notifications') . ' 4 - ✘',
		'×' => __('Style', 'advanced-notifications') . ' 5 - ×',
		'❌' => __('Style', 'advanced-notifications') . ' 6 - ❌',
		'╳' => __('Style', 'advanced-notifications') . ' 7 - ╳',
		'🗙' => __('Style', 'advanced-notifications') . ' 8 - 🗙',
	);
	$close_button_position = array(
		'top_left' => __('Top & Left', 'advanced-notifications'),
		'top_center' => __('Top & Center', 'advanced-notifications'),
		'top_right' => __('Top & Right', 'advanced-notifications'),
		'top_site_direction' => __('Top & Site direction', 'advanced-notifications'),
		'top_opposite_site_direction' => __('Top & Opposite site direction', 'advanced-notifications'),
		'bottom_left' => __('Bottom & Left', 'advanced-notifications'),
		'bottom_center' => __('Bottom & Center', 'advanced-notifications'),
		'bottom_right' => __('Bottom & Right', 'advanced-notifications'),
		'bottom_site_direction' => __('Bottom & Site direction', 'advanced-notifications'),
		'bottom_opposite_site_direction' => __('Bottom & Opposite site direction', 'advanced-notifications'),
		'custom' => __('Custom', 'advanced-notifications'),
	);
	$close_button_horizontal_position = array(
		'right' => __('Right', 'advanced-notifications'),
		'left' => __('Left', 'advanced-notifications'),
	);
	$close_button_vertical_position = array(
		'top' => __('Top', 'advanced-notifications'),
		'bottom' => __('Bottom', 'advanced-notifications'),
	);
	$border_style = array(
		'solid' => 'solid',
		'dashed' => 'dashed',
		'dotted' => 'dotted',
		'double' => 'double',
		'groove' => 'groove',
		'ridge' => 'ridge',
		'inset' => 'inset',
		'outset' => 'outset',
	);
	eis_tab_option( array(
		'interface_id' => 'an_designs',
		'tab_id' => 'design',
		'input_id' => 'text_color',
		'input_type' => 'color',
		'label' =>  __('Text color', 'advanced-notifications'),
		'description' => '',
		'order' => 1,
	));
	eis_tab_option( array(
		'interface_id' => 'an_designs',
		'tab_id' => 'design',
		'input_id' => 'link_color',
		'input_type' => 'color',
		'label' =>  __('Link color', 'advanced-notifications'),
		'description' => '',
		'order' => 3,
	));
	eis_tab_option( array(
		'interface_id' => 'an_designs',
		'tab_id' => 'design',
		'input_id' => 'background_color',
		'input_type' => 'color',
		'label' =>  __('Background color', 'advanced-notifications'),
		'description' => '',
		'order' => 7,
	));
	eis_tab_option( array(
		'interface_id' => 'an_designs',
		'tab_id' => 'design',
		'input_id' => 'background_image',
		'input_type' => 'image',
		'label' =>  __('Background image', 'advanced-notifications'),
		'description' => '',
		'order' => 9,
	));
	eis_tab_option( array(
		'interface_id' => 'an_designs',
		'tab_id' => 'design',
		'input_id' => 'background_size',
		'input_type' => 'select',
		'label' =>  __('Background size', 'advanced-notifications'),
		'description' => '',
		'input_select_options' => $background_size,
		'order' => 11,
	));
	eis_tab_option( array(
		'interface_id' => 'an_designs',
		'tab_id' => 'design',
		'input_id' => 'background_repeat',
		'input_type' => 'select',
		'label' =>  __('Background repeat', 'advanced-notifications'),
		'description' => '',
		'input_select_options' => $background_repeat,
		'order' => 13,
	));
	eis_tab_option( array(
		'interface_id' => 'an_designs',
		'tab_id' => 'design',
		'input_id' => 'border_color',
		'input_type' => 'color',
		'label' =>  __('Border color', 'advanced-notifications'),
		'description' => '',
		'order' => 14,
	));
	eis_tab_option( array(
		'interface_id' => 'an_designs',
		'tab_id' => 'design',
		'input_id' => 'border_style',
		'input_type' => 'select',
		'label' =>  __('Border style', 'advanced-notifications'),
		'input_select_options' => $border_style,
		'default' => 'solid',
		'order' => 15,
	));
	eis_tab_option( array(
		'interface_id' => 'an_designs',
		'tab_id' => 'design',
		'input_id' => 'desktop',
		'input_type' => 'function',
		'label' =>  __('Desktop', 'advanced-notifications'),
		'description' => '',
		'function_name' => 'an_design_screen',
		'order' => 19,
	));
	eis_tab_option( array(
		'interface_id' => 'an_designs',
		'tab_id' => 'design',
		'input_id' => 'tablet',
		'input_type' => 'function',
		'label' =>  __('Tablet', 'advanced-notifications'),
		'description' => '',
		'function_name' => 'an_design_screen',
		'order' => 25,
	));
	eis_tab_option( array(
		'interface_id' => 'an_designs',
		'tab_id' => 'design',
		'input_id' => 'mobile',
		'input_type' => 'function',
		'label' =>  __('Mobile', 'advanced-notifications'),
		'description' => '',
		'function_name' => 'an_design_screen',
		'order' => 30,
	));
	eis_tab_option( array(
		'interface_id' => 'an_designs',
		'tab_id' => 'design',
		'input_id' => 'active_shadow',
		'input_type' => 'select',
		'label' =>  __('Active shadow', 'advanced-notifications'),
		'description' => '',
		'input_select_options' => $no_yes,
		'default' => 'no',
		'order' => 32,
	));
	eis_tab_option( array(
		'interface_id' => 'an_designs',
		'tab_id' => 'design',
		'input_id' => 'shadow',
		'input_type' => 'function',
		'label' =>  __('Shadow', 'advanced-notifications'),
		'description' => '',
		'function_name' => 'an_shadow',
		'parent_name' => 'active_shadow',
		'show_on_value' => 'yes',
		'order' => 34,
	));
	eis_tab_option( array(
		'interface_id' => 'an_designs',
		'tab_id' => 'close_button',
		'input_id' => 'close_button_enable',
		'input_type' => 'checkbox',
		'label' =>  __('Enable', 'advanced-notifications'),
		'description' => '',
		'checkbox_checked' => true,
		'order' => 1,
	));
	eis_tab_option( array(
		'interface_id' => 'an_designs',
		'tab_id' => 'close_button',
		'input_id' => 'close_button_type',
		'input_type' => 'select',
		'label' =>  __('Type', 'advanced-notifications'),
		'description' => '',
		'input_select_options' => $close_button_type,
		'default' => 'icon',
		'order' => 3,
	));
	eis_tab_option( array(
		'interface_id' => 'an_designs',
		'tab_id' => 'close_button',
		'input_id' => 'close_icon_style',
		'input_type' => 'select',
		'label' =>  __('Select close icon', 'advanced-notifications'),
		'description' => '',
		'input_select_options' => apply_filters('an_close_icon_style', $close_icon_style),
		'default' => '✕',
		'parent_name' => 'close_button_type',
		'show_on_value' => 'icon',
		'order' => 4,
	));
	eis_tab_option( array(
		'interface_id' => 'an_designs',
		'tab_id' => 'close_button',
		'input_id' => 'close_text',
		'input_type' => 'text',
		'label' =>  __('Close text', 'advanced-notifications'),
		'description' => '',
		'default' => __('Close', 'advanced-notifications'),
		'placeholder' => __('Close', 'advanced-notifications'),
		'parent_name' => 'close_button_type',
		'show_on_value' => 'text,button',
		'order' => 4,
	));
	eis_tab_option( array(
		'interface_id' => 'an_designs',
		'tab_id' => 'close_button',
		'input_id' => 'close_button_position',
		'input_type' => 'select',
		'label' =>  __('Position', 'advanced-notifications'),
		'description' => '',
		'input_select_options' => $close_button_position,
		'default' => (is_rtl() ? 'top_left' : 'top_right'),
		'order' => 6,
	));
	eis_tab_option( array(
		'interface_id' => 'an_designs',
		'tab_id' => 'close_button',
		'input_id' => 'close_button_horizontal_position',
		'input_type' => 'select',
		'label' =>  __('Horizontal position', 'advanced-notifications'),
		'description' => '',
		'input_select_options' => $close_button_horizontal_position,
		'default' => (is_rtl() ? 'left' : 'right'),
		'parent_name' => 'close_button_position',
		'show_on_value' => 'custom',
		'order' => 7,
	));
	eis_tab_option( array(
		'interface_id' => 'an_designs',
		'tab_id' => 'close_button',
		'input_id' => 'close_button_horizontal_position_val',
		'input_type' => 'number',
		'label' =>  __('Horizontal value', 'advanced-notifications'),
		'description' => '',
		'parent_name' => 'close_button_position',
		'show_on_value' => 'custom',
		'order' => 9,
	));
	eis_tab_option( array(
		'interface_id' => 'an_designs',
		'tab_id' => 'close_button',
		'input_id' => 'close_button_horizontal_position_unit',
		'input_type' => 'select',
		'label' =>  __('Horizontal position unit', 'advanced-notifications'),
		'description' => '',
		'input_select_options' => an_position_unit(),
		'parent_name' => 'close_button_position',
		'show_on_value' => 'custom',
		'order' => 11,
	));
	eis_tab_option( array(
		'interface_id' => 'an_designs',
		'tab_id' => 'close_button',
		'input_id' => 'close_button_vertical_position',
		'input_type' => 'select',
		'label' =>  __('Vertical position', 'advanced-notifications'),
		'description' => '',
		'input_select_options' => $close_button_vertical_position,
		'default' => 'top',
		'parent_name' => 'close_button_position',
		'show_on_value' => 'custom',
		'order' => 13,
	));
	eis_tab_option( array(
		'interface_id' => 'an_designs',
		'tab_id' => 'close_button',
		'input_id' => 'close_button_vertical_position_val',
		'input_type' => 'number',
		'label' =>  __('Vertical value', 'advanced-notifications'),
		'description' => '',
		'parent_name' => 'close_button_position',
		'show_on_value' => 'custom',
		'order' => 15,
	));
	eis_tab_option( array(
		'interface_id' => 'an_designs',
		'tab_id' => 'close_button',
		'input_id' => 'close_button_vertical_position_unit',
		'input_type' => 'select',
		'label' =>  __('Vertical position unit', 'advanced-notifications'),
		'description' => '',
		'input_select_options' => an_position_unit(),
		'parent_name' => 'close_button_position',
		'show_on_value' => 'custom',
		'order' => 17,
	));
	eis_tab_option( array(
		'interface_id' => 'an_designs',
		'tab_id' => 'close_button',
		'input_id' => 'close_button_color',
		'input_type' => 'color',
		'label' =>  __('Color', 'advanced-notifications'),
		'description' => '',
		'order' => 19,
	));
	eis_tab_option( array(
		'interface_id' => 'an_designs',
		'tab_id' => 'close_button',
		'input_id' => 'close_button_background_color',
		'input_type' => 'color',
		'label' =>  __('Background color', 'advanced-notifications'),
		'description' => '',
		'order' => 19,
	));
	eis_tab_option( array(
		'interface_id' => 'an_designs',
		'tab_id' => 'close_button',
		'input_id' => 'desktop_close_button',
		'input_type' => 'function',
		'label' =>  __('Desktop', 'advanced-notifications'),
		'description' => '',
		'function_name' => 'an_design_screen_close_button',
		'order' => 25,
	));
	eis_tab_option( array(
		'interface_id' => 'an_designs',
		'tab_id' => 'close_button',
		'input_id' => 'tablet_close_button',
		'input_type' => 'function',
		'label' =>  __('Tablet', 'advanced-notifications'),
		'description' => '',
		'function_name' => 'an_design_screen_close_button',
		'order' => 30,
	));
	eis_tab_option( array(
		'interface_id' => 'an_designs',
		'tab_id' => 'close_button',
		'input_id' => 'mobile_close_button',
		'input_type' => 'function',
		'label' =>  __('Mobile', 'advanced-notifications'),
		'description' => '',
		'function_name' => 'an_design_screen_close_button',
		'order' => 30,
	));
	// TRIGGERS
	eis_tab('an_triggers', 'trigger', __('Trigger settings', 'advanced-notifications') );
	function an_add_pro_trigger_actions($trigger_actions) {
		global $an_core;
		if ($an_core->license_type() == 'basic') {
			$trigger_actions['close_eis_disabled'] = __('Close notification - PRO', 'advanced-notifications');
		}
		return $trigger_actions;
	}
	add_filter('an_trigger_actions', 'an_add_pro_trigger_actions');
	$trigger_actions = $an_functions->trigger_actions();
	function an_add_pro_trigger_types($trigger_types) {
		global $an_core;
		if ($an_core->license_type() == 'basic') {
			$trigger_types['on_event_eis_disabled'] = __('On event - PRO', 'advanced-notifications');
			$trigger_types['url_contains_eis_disabled'] = __('URL contains - PRO', 'advanced-notifications');
		}
		return $trigger_types;
	}
	add_filter('an_trigger_types', 'an_add_pro_trigger_types');
	$trigger_types = $an_functions->trigger_types();
	$elements = array(
		'class' => __('Class', 'advanced-notifications'),
		'id' => __('ID', 'advanced-notifications'),
		'tag' => __('Tag', 'advanced-notifications'),
	);
	eis_tab_option( array(
		'interface_id' => 'an_triggers',
		'tab_id' => 'trigger',
		'input_id' => 'trigger_action',
		'input_type' => 'select',
		'label' =>  __('Trigger action', 'advanced-notifications'),
		'description' => '',
		'input_select_options' => $trigger_actions,
		'default' => 'show',
	));
	eis_tab_option( array(
		'interface_id' => 'an_triggers',
		'tab_id' => 'trigger',
		'input_id' => 'trigger_type',
		'input_type' => 'select',
		'label' =>  __('Trigger type', 'advanced-notifications'),
		'description' => '',
		'input_select_options' => $trigger_types,
	));
	eis_tab_option( array(
		'interface_id' => 'an_triggers',
		'tab_id' => 'trigger',
		'input_id' => 'trigger_element',
		'input_type' => 'select',
		'label' =>  __('Element tag / class / ID', 'advanced-notifications'),
		'input_select_options' => $elements,
		'parent_name' => 'trigger_type',
		'show_on_value' => 'on_click,on_hover',
	));
	eis_tab_option( array(
		'interface_id' => 'an_triggers',
		'tab_id' => 'trigger',
		'input_id' => 'trigger_element_name',
		'input_type' => 'text',
		'label' =>  __('Element name', 'advanced-notifications'),
		'parent_name' => 'trigger_type',
		'show_on_value' => 'on_click,on_hover',
		'note' => __('You can enter multiple values separated by commas', 'advanced-notifications'),
	));
	eis_tab_option( array(
		'interface_id' => 'an_triggers',
		'tab_id' => 'trigger',
		'input_id' => 'trigger_description',
		'input_type' => 'text',
		'label' =>  __('Trigger description', 'advanced-notifications'),
		'note' => __('Will appear when a trigger is selected on the notification settings page', 'advanced-notifications'),
		'multilingual' => true,
	));
	// LOCATIONS
	eis_tab('an_locations', 'location', __('Location settings', 'advanced-notifications') );
	function an_horizontal_position() {
		$horizontal_position = array(
			'right' => __('Right', 'advanced-notifications'),
			'left' => __('Left', 'advanced-notifications'),
			'center_eis_disabled' => __('Center', 'advanced-notifications') . ' - ' . __('PRO', 'advanced-notifications'),
		);
		return apply_filters( 'an_horizontal_position_list', $horizontal_position );
	}
	function an_vertical_position() {
		$vertical_position = array(
			'top' => __('Top', 'advanced-notifications'),
			'bottom' => __('Bottom', 'advanced-notifications'),
			'center_eis_disabled' => __('Center', 'advanced-notifications') . ' - ' . __('PRO', 'advanced-notifications'),
		);
		return apply_filters( 'an_vertical_position_list', $vertical_position );
	}
	function an_location_desktop($option) {
		$print_fun = '<div class="an-options-container">';
		$width_val = (isset($option['value']['width_val'])) ? $option['value']['width_val'] : '350';
		$print_fun .= '	<lable class="an-input-col-4">' . __('Width', 'advanced-notifications') . ': </lable>';
		$print_fun .= '	<input class="an-input-col-4" name="an_locations_eis_options[desktop][width_val]" type="number" value="' . $width_val . '" placeholder="">';
		$width_unit = (isset($option['value']['width_unit'])) ? $option['value']['width_unit'] : 'px';
		$print_fun .= '	<select class="an-input-col-4" name="an_locations_eis_options[desktop][width_unit]">';
		foreach (an_position_unit() as $key => $value) {
			$print_fun .= '	<option value="' . $key . '"' . selected($width_unit, $key, false) . '>' . $value . '</option>';
		}
		$print_fun .= '	</select>';
		$print_fun .= '</div>';
		$print_fun .= '<div class="an-options-container">';
		$desktop_horizontal_position = (isset($option['value']['horizontal_position'])) ? $option['value']['horizontal_position'] : (is_rtl() ? 'right' : 'left');
		$print_fun .= '	<lable class="an-input-col-4">' . __('Horizontal position', 'advanced-notifications') . ': </lable>';
		$print_fun .= '	<select class="an-input-col-4" name="an_locations_eis_options[desktop][horizontal_position]">';
		foreach (an_horizontal_position() as $key => $value) {
			$disabled = (strpos($key, 'eis_disabled') !== false) ? ' disabled' : '';
			$print_fun .= '	<option value="' . $key . '"' . selected($desktop_horizontal_position, $key, false) . $disabled . '>' . $value . '</option>';
		}
		$print_fun .= '	</select>';
		$desktop_horizontal_val = (isset($option['value']['horizontal_val'])) ? $option['value']['horizontal_val'] : '5';
		$print_fun .= '	<input class="an-input-col-4" name="an_locations_eis_options[desktop][horizontal_val]" type="number" value="' . $desktop_horizontal_val . '" placeholder="">';
		$desktop_horizontal_unit = (isset($option['value']['horizontal_unit'])) ? $option['value']['horizontal_unit'] : 'px';
		$print_fun .= '	<select class="an-input-col-4" name="an_locations_eis_options[desktop][horizontal_unit]">';
		foreach (an_position_unit() as $key => $value) {
			$print_fun .= '	<option value="' . $key . '"' . selected($desktop_horizontal_unit, $key, false) . '>' . $value . '</option>';
		}
		$print_fun .= '	</select>';
		$print_fun .= '</div>';
		$print_fun .= '<div class="an-options-container">';
		$desktop_vertical_position = (isset($option['value']['vertical_position'])) ? $option['value']['vertical_position'] : 'bottom';
		$print_fun .= '	<lable class="an-input-col-4">' . __('Vertical position', 'advanced-notifications') . ': </lable>';
		$print_fun .= '	<select class="an-input-col-4" name="an_locations_eis_options[desktop][vertical_position]">';
		foreach (an_vertical_position() as $key => $value) {
			$disabled = (strpos($key, 'eis_disabled') !== false) ? ' disabled' : '';
			$print_fun .= '	<option value="' . $key . '"' . selected($desktop_vertical_position, $key, false) . $disabled . '>' . $value . '</option>';
		}
		$print_fun .= '	</select>';
		$desktop_vertical_val = (isset($option['value']['vertical_val'])) ? $option['value']['vertical_val'] : '5';
		$print_fun .= '	<input class="an-input-col-4" name="an_locations_eis_options[desktop][vertical_val]" type="number" value="' . $desktop_vertical_val . '" placeholder="">';
		$desktop_vertical_unit = (isset($option['value']['vertical_unit'])) ? $option['value']['vertical_unit'] : 'px';
		$print_fun .= '	<select class="an-input-col-4" name="an_locations_eis_options[desktop][vertical_unit]">';
		foreach (an_position_unit() as $key => $value) {
			$print_fun .= '	<option value="' . $key . '"' . selected($desktop_vertical_unit, $key, false) . '>' . $value . '</option>';
		}
		$print_fun .= '	</select>';
		$print_fun .= '</div>';
		$print_fun .= '<div class="an-options-container">';
		$print_fun .= '	<lable class="an-input-col-4">' . __('Class', 'advanced-notifications') . ': </lable>';
		$desktop_class_val = (isset($option['value']['class'])) ? $option['value']['class'] : '';
		$print_fun .= '	<input class="an-input-col-10" name="an_locations_eis_options[desktop][class]" type="text" value="' . $desktop_class_val . '" placeholder="">';
		$print_fun .= '</div>';
		return $print_fun;
	}
	function an_location_tablet($option) {
		$print_fun = '<div class="an-options-container">';
		$width_val = (isset($option['value']['width_val'])) ? $option['value']['width_val'] : '350';
		$print_fun .= '	<lable class="an-input-col-4">' . __('Width', 'advanced-notifications') . ': </lable>';
		$print_fun .= '	<input class="an-input-col-4" name="an_locations_eis_options[tablet][width_val]" type="number" value="' . $width_val . '" placeholder="">';
		$width_unit = (isset($option['value']['width_unit'])) ? $option['value']['width_unit'] : 'px';
		$print_fun .= '	<select class="an-input-col-4" name="an_locations_eis_options[tablet][width_unit]">';
		foreach (an_position_unit() as $key => $value) {
			$print_fun .= '	<option value="' . $key . '"' . selected($width_unit, $key, false) . '>' . $value . '</option>';
		}
		$print_fun .= '	</select>';
		$print_fun .= '</div>';
		$print_fun .= '<div class="an-options-container">';
		$tablet_horizontal_position = (isset($option['value']['horizontal_position'])) ? $option['value']['horizontal_position'] : (is_rtl() ? 'right' : 'left');
		$print_fun .= '	<lable class="an-input-col-4">' . __('Horizontal position', 'advanced-notifications') . ': </lable>';
		$print_fun .= '	<select class="an-input-col-4" name="an_locations_eis_options[tablet][horizontal_position]">';
		foreach (an_horizontal_position() as $key => $value) {
			$print_fun .= '	<option value="' . $key . '"' . selected($tablet_horizontal_position, $key, false) . '>' . $value . '</option>';
		}
		$print_fun .= '	</select>';
		$tablet_horizontal_val = (isset($option['value']['horizontal_val'])) ? $option['value']['horizontal_val'] : '5';
		$print_fun .= '	<input class="an-input-col-4" name="an_locations_eis_options[tablet][horizontal_val]" type="number" value="' . $tablet_horizontal_val . '" placeholder="">';
		$tablet_horizontal_unit = (isset($option['value']['horizontal_unit'])) ? $option['value']['horizontal_unit'] : 'px';
		$print_fun .= '	<select class="an-input-col-4" name="an_locations_eis_options[tablet][horizontal_unit]">';
		foreach (an_position_unit() as $key => $value) {
			$print_fun .= '	<option value="' . $key . '"' . selected($tablet_horizontal_unit, $key, false) . '>' . $value . '</option>';
		}
		$print_fun .= '	</select>';
		$print_fun .= '</div>';
		$print_fun .= '<div class="an-options-container">';
		$tablet_vertical_position = (isset($option['value']['vertical_position'])) ? $option['value']['vertical_position'] : 'bottom';
		$print_fun .= '	<lable class="an-input-col-4">' . __('Vertical position', 'advanced-notifications') . ': </lable>';
		$print_fun .= '	<select class="an-input-col-4" name="an_locations_eis_options[tablet][vertical_position]">';
		foreach (an_vertical_position() as $key => $value) {
			$print_fun .= '	<option value="' . $key . '"' . selected($tablet_vertical_position, $key, false) . '>' . $value . '</option>';
		}		$print_fun .= '	</select>';
		$tablet_vertical_val = (isset($option['value']['vertical_val'])) ? $option['value']['vertical_val'] : '5';
		$print_fun .= '	<input class="an-input-col-4" name="an_locations_eis_options[tablet][vertical_val]" type="number" value="' . $tablet_vertical_val . '" placeholder="">';
		$tablet_vertical_unit = (isset($option['value']['vertical_unit'])) ? $option['value']['vertical_unit'] : 'px';
		$print_fun .= '	<select class="an-input-col-4" name="an_locations_eis_options[tablet][vertical_unit]">';
		foreach (an_position_unit() as $key => $value) {
			$print_fun .= '	<option value="' . $key . '"' . selected($tablet_vertical_unit, $key, false) . '>' . $value . '</option>';
		}
		$print_fun .= '	</select>';
		$print_fun .= '</div>';
		$print_fun .= '</div>';
		$print_fun .= '<div class="an-options-container">';
		$print_fun .= '	<lable class="an-input-col-4">' . __('Class', 'advanced-notifications') . ': </lable>';
		$tablet_class_val = (isset($option['value']['class'])) ? $option['value']['class'] : '';
		$print_fun .= '	<input class="an-input-col-10" name="an_locations_eis_options[tablet][class]" type="text" value="' . $tablet_class_val . '" placeholder="">';
		$print_fun .= '</div>';
		return $print_fun;
	}
	function an_location_mobile($option) {
		$print_fun = '<div class="an-options-container">';
		$width_val = (isset($option['value']['width_val'])) ? $option['value']['width_val'] : '96';
		$print_fun .= '	<lable class="an-input-col-4">' . __('Width', 'advanced-notifications') . ': </lable>';
		$print_fun .= '	<input class="an-input-col-4" name="an_locations_eis_options[mobile][width_val]" type="number" value="' . $width_val . '" placeholder="">';
		$width_unit = (isset($option['value']['width_unit'])) ? $option['value']['width_unit'] : '%';
		$print_fun .= '	<select class="an-input-col-4" name="an_locations_eis_options[mobile][width_unit]">';
		foreach (an_position_unit() as $key => $value) {
			$print_fun .= '	<option value="' . $key . '"' . selected($width_unit, $key, false) . '>' . $value . '</option>';
		}
		$print_fun .= '	</select>';
		$print_fun .= '</div>';
		$print_fun .= '<div class="an-options-container">';
		$mobile_horizontal_position = (isset($option['value']['horizontal_position'])) ? $option['value']['horizontal_position'] : (is_rtl() ? 'right' : 'left');
		$print_fun .= '	<lable class="an-input-col-4">' . __('Horizontal position', 'advanced-notifications') . ': </lable>';
		$print_fun .= '	<select class="an-input-col-4" name="an_locations_eis_options[mobile][horizontal_position]">';
		foreach (an_horizontal_position() as $key => $value) {
			$print_fun .= '	<option value="' . $key . '"' . selected($mobile_horizontal_position, $key, false) . '>' . $value . '</option>';
		}
		$print_fun .= '	</select>';
		$mobile_horizontal_val = (isset($option['value']['horizontal_val'])) ? $option['value']['horizontal_val'] : '2';
		$print_fun .= '	<input class="an-input-col-4" name="an_locations_eis_options[mobile][horizontal_val]" type="number" value="' . $mobile_horizontal_val . '" placeholder="">';
		$mobile_horizontal_unit = (isset($option['value']['horizontal_unit'])) ? $option['value']['horizontal_unit'] : '%';
		$print_fun .= '	<select class="an-input-col-4" name="an_locations_eis_options[mobile][horizontal_unit]">';
		foreach (an_position_unit() as $key => $value) {
			$print_fun .= '	<option value="' . $key . '"' . selected($mobile_horizontal_unit, $key, false) . '>' . $value . '</option>';
		}
		$print_fun .= '	</select>';
		$print_fun .= '</div>';
		$print_fun .= '<div class="an-options-container">';
		$mobile_vertical_position = (isset($option['value']['vertical_position'])) ? $option['value']['vertical_position'] : 'bottom';
		$print_fun .= '	<lable class="an-input-col-4">' . __('Vertical position', 'advanced-notifications') . ': </lable>';
		$print_fun .= '	<select class="an-input-col-4" name="an_locations_eis_options[mobile][vertical_position]">';
		foreach (an_vertical_position() as $key => $value) {
			$print_fun .= '	<option value="' . $key . '"' . selected($mobile_vertical_position, $key, false) . '>' . $value . '</option>';
		}		$print_fun .= '	</select>';
		$mobile_vertical_val = (isset($option['value']['vertical_val'])) ? $option['value']['vertical_val'] : '5';
		$print_fun .= '	<input class="an-input-col-4" name="an_locations_eis_options[mobile][vertical_val]" type="number" value="' . $mobile_vertical_val . '" placeholder="">';
		$mobile_vertical_unit = (isset($option['value']['vertical_unit'])) ? $option['value']['vertical_unit'] : 'px';
		$print_fun .= '	<select class="an-input-col-4" name="an_locations_eis_options[mobile][vertical_unit]">';
		foreach (an_position_unit() as $key => $value) {
			$print_fun .= '	<option value="' . $key . '"' . selected($mobile_vertical_unit, $key, false) . '>' . $value . '</option>';
		}
		$print_fun .= '	</select>';
		$print_fun .= '</div>';
		$print_fun .= '<div class="an-options-container">';
		$print_fun .= '	<lable class="an-input-col-4">' . __('Class', 'advanced-notifications') . ': </lable>';
		$mobile_class_val = (isset($option['value']['class'])) ? $option['value']['class'] : '';
		$print_fun .= '	<input class="an-input-col-10" name="an_locations_eis_options[mobile][class]" type="text" value="' . $mobile_class_val . '" placeholder="">';
		$print_fun .= '</div>';
		return $print_fun;
	}
	eis_tab_option( array(
		'interface_id' => 'an_locations',
		'tab_id' => 'location',
		'input_id' => 'desktop',
		'input_type' => 'function',
		'label' =>  __('Desktop', 'advanced-notifications'),
		'description' => '',
		'function_name' => 'an_location_desktop',
		'order' => 1,
	));
	eis_tab_option( array(
		'interface_id' => 'an_locations',
		'tab_id' => 'location',
		'input_id' => 'tablet',
		'input_type' => 'function',
		'label' =>  __('Tablet', 'advanced-notifications'),
		'description' => '',
		'function_name' => 'an_location_tablet',
		'order' => 3,
	));
	eis_tab_option( array(
		'interface_id' => 'an_locations',
		'tab_id' => 'location',
		'input_id' => 'mobile',
		'input_type' => 'function',
		'label' =>  __('Mobile', 'advanced-notifications'),
		'description' => '',
		'function_name' => 'an_location_mobile',
		'order' => 5,
	));
	eis_tab_option( array(
		'interface_id' => 'an_locations',
		'tab_id' => 'location',
		'input_id' => 'z_index',
		'input_type' => 'number',
		'label' =>  __('Front order', 'advanced-notifications'),
		'description' => '',
		'default' => '1',
		'order' => 7,
	));
	// triggers
	function an_eis_triggers($option) {
		global $an_api;
		$triggers_list = $an_api->triggers_list();
		$triggers = array('0'=>__('None', 'advanced-notifications'));
		foreach ($triggers_list as $trigger) {
			$triggers[$trigger['id']] = $trigger['label'];
		}
		$triggers_rows = (is_array($option['value'])) ? $option['value'] : array();
		$print_fun = '<div class="an-triggers-options-container">';
		$print_fun .= '	<div class="an-options-container an-trigger-default-container">';
		$print_fun .= '		<lable class="an-input-col-4">' . __('Trigger', 'advanced-notifications') . ' <span>1</span></lable>';
		$first_trigger = (isset($option['value'][0])) ? $option['value'][0]: null;
		$print_fun .= '		<select class="" name="a_notifications_eis_options[triggers][0]">';
		foreach ($triggers as $key => $value) {
			$print_fun .= '		<option value="' . $key . '"' . selected($first_trigger, $key, false) . '>' . $value . '</option>';
		}
		$print_fun .= '		</select>';
		$print_fun .= '		<input type="submit" name="an-delete-trigger-row" class="hidden button button-link-delete button-large an-delete-trigger-row" value="' . __('Delete', 'advanced-notifications') . '">';
		$print_fun .= '		<div class="an-trigger-description">' . (isset($triggers_list[$first_trigger]) ? $triggers_list[$first_trigger]['description'] : null) . '</div>';
		$print_fun .= '	</div>';
		$i = 1;
		foreach ($triggers_rows as $row_key => $row_value) {
			if ($row_key > 0) {
				$print_fun .= '	<div class="an-options-container">';
				$print_fun .= '		<lable class="an-input-col-4">' . __('Trigger', 'advanced-notifications') . ' <span>' . ($i+1) . '</span></lable>';
				$print_fun .= '		<select class="" name="a_notifications_eis_options[triggers][' . $i  . ']">';
				foreach ($triggers as $key => $value) {
					$print_fun .= '		<option value="' . $key . '"' . selected($row_value, $key, false) . '>' . $value . '</option>';
				}
				$print_fun .= '		</select>';
				$print_fun .= '		<input type="submit" name="an-delete-trigger-row" class="button button-link-delete button-large an-delete-trigger-row" value="' . __('Delete', 'advanced-notifications') . '">';
				$print_fun .= '		<div class="an-trigger-description">' . (isset($triggers_list[$row_value]) ? $triggers_list[$row_value]['description'] : null) . '</div>';
				$print_fun .= '	</div>';
				$i++;
			}
		}
		$trigger_button_name = apply_filters('an_trigger_button_name', __('Add Trigger', 'advanced-notifications') . ' (' . __('PRO', 'advanced-notifications') . ')');
		$trigger_button_disabled = apply_filters('an_trigger_button_disabled', ' disabled');
		$print_fun .= '	<input type="submit" name="an-add-trigger-row" id="an-add-trigger-row" class="button button-primary button-large an-add-trigger-row" value="' . $trigger_button_name . '"' . $trigger_button_disabled . '>';
		$print_fun .= '</div>';
		return $print_fun;
	}
	eis_tab('a_notifications', 'triggers', __('Triggers', 'advanced-notifications') );
	eis_tab_option( array(
		'interface_id' => 'a_notifications',
		'tab_id' => 'triggers',
		'input_id' => 'triggers',
		'input_type' => 'function',
		'function_name' => 'an_eis_triggers',
		'label' =>  __('Triggers', 'advanced-notifications'),
	));
}
add_action('add_eis_interface_options', 'an_interface_options');
