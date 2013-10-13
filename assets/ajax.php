<?php
    $parse_uri = explode('wp-content', $_SERVER['SCRIPT_FILENAME']);
	require_once ($parse_uri[0] . 'wp-load.php');

	global $wpdb;

	$action = $wpdb->escape($_POST['action']);

	switch ($action) {

		case 'save_fields':

			$active_fields = explode(',',$_POST['active_fields']);
			$inactive_fields = explode(',',$_POST['inactive_fields']);

			$active = update_option('coderbits_profiler_active_fields', serialize($active_fields));
			$inactive = update_option('coderbits_profiler_inactive_fields', serialize($inactive_fields));
			
			if ($active && $inactive) {
				echo 'Yes';
			} else {
				echo 'No';
			}
			
		break;
        
        case 'save_options':
            
            $plugin_options = explode(',',$_POST['plugin_options']);
            
			$options = update_option('coderbits_profiler_options', serialize($plugin_options));
			
			if ($options) {
				echo 'Yes';
			} else {
				echo 'No';
			}
            
        break;

	}
?>