<?php
/*
*
*	Easy Interface Settings V 1
*	------------------------------------------------
* 	Copyright Linker  - https://linker.co.il
*
*/

/* FIRST CHECK IF EXISTS
================================================== */
if (function_exists('eis_interfaces')) return;

/* VARIABLE DEFINITIONS
================================================== */
// needed to define where framework root location is
if (!defined('EIS_ROOT_PATH')) {
	define('EIS_ROOT_PATH', plugin_dir_path( __FILE__ ));
}
if (!defined('EIS_URL_PATH')) {
	define('EIS_URL_PATH', plugin_dir_url(__FILE__));
}

/* INCLUDES
================================================== */
if (LF_EIS) {
	include_once( EIS_ROOT_PATH . 'easy-interface-settings.php' );
}
