<?php
    /*
        Plugin Name: Coderbits Profiler
		Plugin URI: https://github.com/stefanbc/Coderbits-Profiler
		Description: Made for Wordpress
		Version: 0.1
		Author: Stefan Cosma
		Author URI: http://coderbits.com/stefanbc
		License: MIT
	*/
    
    add_action('admin_menu','coderbits_profiler');

	function coderbits_profiler() {
		add_menu_page('Coderbits','Coderbits','edit_pages', 'coderbits_profiler', 'coderbits_profiler_options');
	}
    
    function coderbits_profiler_options(){
        
        echo "<h1 style='border-bottom:1px solid #ccc;padding-bottom: 10px;width:300px;'>Coderbits Profiler</h1>";
        
        global $wpdb;
    
        echo '<form method="post">';
            echo 'Current set Coderbits Profile: <input type="text" name="username" id="username" value="' . get_option('coderbits_username') . '" placeholder="Enter you coderbits username">';
            echo '<input type="submit" value="Set Profile">';
        echo '</form>';
        
        $username = $wpdb->escape($_POST['username']);
        
        if($username) {
            
            update_option('coderbits_username', $username);
            
        }
        
        $name = "coderbits_username";
        
        add_option($name, $username);
        
    }
    
    function coderbits_profiler_data() {
        
        // jSON URL which should be requested
        $json_url = 'https://coderbits.com/' . get_option('coderbits_username') . '.json';
         
        // Initializing curl
        $ch = curl_init( $json_url );
         
        // Configuring curl options
        $options = array(
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => array('Content-type: application/json')
        );
         
        // Setting curl options
        curl_setopt_array( $ch, $options );
         
        // Getting results
        $result =  curl_exec($ch); // Getting jSON result string
        
        echo $result;
    }
    
    
    class CoderbitsWidget extends WP_Widget {

        function CoderbitsWidget() {
    		// Instantiate the parent object
    		parent::__construct( false, 'Coderbits Profiler' );
    	}
    
    	function widget( $args, $instance ) {
    		coderbits_profiler_data();
    	}
    
    }
    
    function coderbits_profiler_register_widgets() {
    	register_widget( 'CoderbitsWidget' );
    }
    
    add_action( 'widgets_init', 'coderbits_profiler_register_widgets' );
?>