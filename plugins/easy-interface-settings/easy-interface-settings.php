<?php
/*
*
*	Easy Interface Settings V 1.0.7
*	------------------------------------------------
* 	Copyright Linker  - https://linker.co.il
*
*/

/* START LOADING
================================================== */
if (!function_exists('eis_interfaces')) {
	function eis_is_plugin_active($plugin) {
		return in_array($plugin, (array) get_option('active_plugins', array()));
	}

	/* GOLOBAL VARIABLE
	================================================== */
	global $eis_version;
	global $eis_interfaces_global;
	global $eis_options_sys_order;
	$eis_version = '1.0.5'; // version changed from 0 to 1.0.0
	$eis_options_sys_order = 1;

	/* VARIABLE DEFINITIONS
	================================================== */
	define('EIS_IS_POLY', (eis_is_plugin_active('polylang/polylang.php')) ? true : false);
	define('EIS_ENCRYPTION_METHOD', 'AES-256-CBC');
	define('EIS_SECRET_KEY', '25c6c7ff35b9979b151f2136cd13b0ff');

	/* REGISTER SETTINGS
	================================================== */
	// register_setting( 'eis_register_setting', 'eis_register_interfaces' );
	// register_setting( 'eis_register_setting', 'eis_register_post_types' );
	// register_setting( 'eis_register_setting', 'eis_version' );

	/* INCLUDES
	================================================== */
	include( EIS_ROOT_PATH . 'includes/eis-update.php' ); // Need to be first
	include( EIS_ROOT_PATH . 'includes/eis-functions.php' );
	if (is_admin()) {
		include( EIS_ROOT_PATH . 'includes/eis-admin-functions.php' );
		include( EIS_ROOT_PATH . 'includes/eis-css-generator.php' );
	}
	include( EIS_ROOT_PATH . 'includes/eis-templates.php' );


	/* STYLES & SCRIPTS
	================================================== */
	function eis_admin_enqueue_scripts() {
		if (is_eis_page()) {
			$js_ver = date("ymd-Gis", filemtime( EIS_ROOT_PATH . 'assets/js/eis-admin.js' ));
			wp_register_script( 'eis-admin-js', EIS_URL_PATH . 'assets/js/eis-admin.js', array(), $js_ver );
			wp_enqueue_script( 'eis-admin-js' );

			$js_ver = date("ymd-Gis", filemtime( EIS_ROOT_PATH . 'assets/js/eis-settings-fields.js' ));
			wp_register_script( 'eis-settings-fields-js', EIS_URL_PATH . 'assets/js/eis-settings-fields.js', array(), $js_ver );
			wp_enqueue_script( 'eis-settings-fields-js' );

			$css_ver = date("ymd-Gis", filemtime( EIS_ROOT_PATH . 'assets/css/eis-admin.css' ));
			wp_register_style( 'linker-admin-style', EIS_URL_PATH . 'assets/css/eis-admin.css', false, $css_ver );
			wp_enqueue_style( 'linker-admin-style' );

			if (is_rtl()) {
				$css_ver = date("ymd-Gis", filemtime( EIS_ROOT_PATH . 'assets/css/eis-admin-rtl.css' ));
				wp_register_style( 'linker-admin-rtl-style', EIS_URL_PATH . 'assets/css/eis-admin-rtl.css', false, $css_ver );
				wp_enqueue_style( 'linker-admin-rtl-style' );
			}

			// bootstrap datepicker
			wp_register_script( 'eis-bootstrap-datepicker', EIS_URL_PATH . 'assets/bootstrap-datepicker/js/bootstrap-datepicker.min.js', array(), '1.6.4' );
			wp_register_script( 'eis-bootstrap-datepicker-locales', EIS_URL_PATH . 'assets/bootstrap-datepicker/locales/bootstrap-datepicker.he.min.js', array(), '1.6.4');
			wp_enqueue_script( 'eis-bootstrap-datepicker' );
			wp_enqueue_script( 'eis-bootstrap-datepicker-locales' );

			wp_register_style( 'eis-bootstrap-datepicker', EIS_URL_PATH . 'assets/bootstrap-datepicker/css/bootstrap-datepicker3.standalone.css', false, '1.6.4' );
			wp_enqueue_style( 'eis-bootstrap-datepicker' );

			// color picker
			wp_register_script( 'eis-color-picker', EIS_URL_PATH . 'assets/cs-alpha-color-picker/cs-alpha-color-picker.js', null, null, true );
			wp_enqueue_script( array( 'wp-color-picker', 'eis-color-picker' ) );

			wp_register_style( 'eis-color-picker', EIS_URL_PATH . 'assets/cs-alpha-color-picker/cs-alpha-color-picker.css');
			wp_enqueue_style( array( 'eis-color-picker', 'wp-color-picker') );

			// media picker
			wp_enqueue_media();
		}
	}
	add_action( 'admin_enqueue_scripts', 'eis_admin_enqueue_scripts', 99 );

	function eis_enqueue_scripts() {
		$upload_dir = wp_upload_dir();
		if (file_exists($upload_dir['basedir'] . '/easy-interface-settings/css-vars/css-vars.css')) {
			$css_ver = date("ymd-Gis", filemtime( $upload_dir['basedir'] . '/easy-interface-settings/css-vars/css-vars.css' ));
			wp_register_style( 'eis-vars', $upload_dir['baseurl'] . '/easy-interface-settings/css-vars/css-vars.css', false, $css_ver );
			wp_enqueue_style( 'eis-vars' );
		}
	}
	add_action( 'wp_enqueue_scripts', 'eis_enqueue_scripts', 15);

	function eis_save_current_tab_ajax() {
		$tab_id = sanitize_text_field($_POST['tab_id']);
		$interface_id = sanitize_text_field($_POST['interface_id']);
		if (is_user_logged_in()) {
			update_user_option(get_current_user_id(), $interface_id . '_eis_current_tab', $tab_id, true);
		}
		wp_die();
	}
	add_action( 'wp_ajax_eis_save_current_tab_ajax', 'eis_save_current_tab_ajax' );

	do_action('eis_do_update_after_load'); //Need to be last
}
