<?php
/* UPDATE
================================================== */
// update_option('eis_version', '1.0.1');
$eis_installed_ver = get_option('eis_version');
if (version_compare($eis_installed_ver, $eis_version, '<')) {
	eis_update_before_load($eis_version);
	add_action( 'eis_do_update_after_load', 'eis_update_after_load' );
}

function eis_update_before_load($eis_version) {
	update_option('eis_register_interfaces', array());
	update_option('eis_version', $eis_version); //Need to be last
}

function eis_update_after_load() {
	global $eis_version;
	// from 1.0.0 to 1.0.1
	// $eis_interfaces = eis_interfaces();
	// foreach ($eis_interfaces as $type =>$eis_interface) {
	// 	foreach ($eis_interface as $value) {
	// 		delete_option( $value['interface_id'] . '_eis_current_tab' );
	// 	}
	// }

	update_option('eis_version', $eis_version); //Need to be last
}
