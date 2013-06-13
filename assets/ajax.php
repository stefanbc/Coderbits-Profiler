<?php
<<<<<<< HEAD
    $parse_uri = explode( 'wp-content', $_SERVER['SCRIPT_FILENAME'] );
	require_once ($parse_uri[0] . 'wp-load.php');
=======
	require_once ABSPATH . 'wp-load.php';
>>>>>>> 762db7119b84dccf7108487c585b11deb169965d

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

	}
?>