<?php
/**
 * Plugin Name: Advanced Notifications
 * Plugin URI: https://advanced-notifications.com
 * Description: Advanced Notifications allows you to create beautiful custom notifications
 * Version: 1.2.5
 * Author: Yehi
 * Author URI: https://profiles.wordpress.org/yehi/
 * License: GPL2
 * Text Domain: advanced-notifications


Advanced Notifications is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

Advanced Notifications is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Advanced Notifications. If not, see http://www.gnu.org/licenses/gpl-2.0.html.
*/

/* VARIABLE DEFINITIONS
================================================== */
define( 'ADVANCED_NOTIFICATIONS_ROOT_PATH', plugin_dir_path( __FILE__ ) );
define( 'ADVANCED_NOTIFICATIONS_URL_PATH',  plugin_dir_url(__FILE__) );
define( 'ADVANCED_NOTIFICATIONS_FILE_PATH', __FILE__ );
if (!defined('LF_EIS')) define('LF_EIS', true);

/* INCLUDES
================================================== */
include_once( ADVANCED_NOTIFICATIONS_ROOT_PATH . 'includes/an-functions.php' );
include_once( ADVANCED_NOTIFICATIONS_ROOT_PATH . 'includes/an-cpt.php' );
include_once( ADVANCED_NOTIFICATIONS_ROOT_PATH . 'includes/an-core.php' );
include_once( ADVANCED_NOTIFICATIONS_ROOT_PATH . 'includes/an-locations.php' );
include_once( ADVANCED_NOTIFICATIONS_ROOT_PATH . 'includes/an-designs.php' );
include_once( ADVANCED_NOTIFICATIONS_ROOT_PATH . 'includes/an-triggers.php' );
include_once( ADVANCED_NOTIFICATIONS_ROOT_PATH . 'includes/an-animations.php' );
include_once( ADVANCED_NOTIFICATIONS_ROOT_PATH . 'includes/an-interface-api.php' );
include_once( ADVANCED_NOTIFICATIONS_ROOT_PATH . 'includes/an-api.php' );

if (file_exists(ADVANCED_NOTIFICATIONS_ROOT_PATH . 'includes/an-interface-api-pro.php')) {
	include_once( ADVANCED_NOTIFICATIONS_ROOT_PATH . 'includes/an-interface-api-pro.php' );
}
if (file_exists(ADVANCED_NOTIFICATIONS_ROOT_PATH . 'includes/an-functions-pro.php')) {
	include_once( ADVANCED_NOTIFICATIONS_ROOT_PATH . 'includes/an-functions-pro.php' );
}
if (file_exists(ADVANCED_NOTIFICATIONS_ROOT_PATH . 'includes/an-api-pro.php')) {
	include_once( ADVANCED_NOTIFICATIONS_ROOT_PATH . 'includes/an-api-pro.php' );
}

/* GET AN PLUGINS
================================================== */
foreach(glob(ADVANCED_NOTIFICATIONS_ROOT_PATH . 'plugins/*', GLOB_ONLYDIR) as $dirname){
	$dirname = basename($dirname);
	include_once( realpath(ADVANCED_NOTIFICATIONS_ROOT_PATH . 'plugins/'  . $dirname . '/autoload.php') );
}
do_action( 'an_plugins_loaded');
