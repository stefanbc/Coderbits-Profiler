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
        add_submenu_page('options-general.php', 'Coderbits Profiler', 'Coderbits Profiler', 'edit_pages', 'coderbits_profiler', 'coderbits_profiler_options');
	}
    
    function coderbits_profiler_options(){
        global $wpdb;

        echo '<link href="//fonts.googleapis.com/css?family=Roboto:300,400" rel="stylesheet" type="text/css">';
        echo '<link href="../wp-content/plugins/coderbits/assets/style.css" rel="stylesheet" type="text/css">';
        echo '<div class="main-wrapper">';
        echo '<span class="main-title"><img class="logo" src="../wp-content/plugins/coderbits/assets/logo.png" alt="coderbits"> Profiler</span>';    
        echo '<form method="post">';
        echo '<div style="padding: 10px 0;">Current set Coderbits Profile: <b>' . get_option('coderbits_username') . '</b></div>';
        echo 'Set Coderbits Profile: <input type="text" name="username" id="username" placeholder="Enter you coderbits username">';
        echo '<input type="submit" value="Set Profile">';
        echo '</form>';
        echo '</div>';
        
        $username = $wpdb->escape($_POST['username']);
        $active_fields = '';
        
        if($username) {
            update_option('coderbits_username', $username); 
        }
               
        add_option('coderbits_username', $username);
        add_option('coderbits_active_fields', $active_fields);
    }
    
    function coderbits_profiler_data() {
        
        // jSON URL which should be requested
        $json_url = 'https://coderbits.com/' . get_option('coderbits_username') . '.json';
         
        // Initializing curl
        $ch = curl_init($json_url);
         
        // Configuring curl options
        $options = array(
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => array('Content-type: application/json')
        );
         
        // Setting curl options
        curl_setopt_array($ch, $options);
         
        // Getting results
        $result = curl_exec($ch); // Getting jSON result string

        // Close request to clear up some resources
        curl_close($ch);

        // Parse the JSON file
        $output = json_decode($result);
        
        // Output the name for example
        $return = '<div class="coderbits-field" id="coderbits-name">Name: ' . $output->{'name'} . "</div>";
        $return .= '<div class="coderbits-field" id="coderbits-title">Title: ' . $output->{'title'} . "</div>";

        echo $return; 
    }
    
    
    class CoderbitsWidget extends WP_Widget {

        function CoderbitsWidget() {
    		// Instantiate the parent object
    		parent::__construct(false, 'Coderbits Profiler');
    	}
    
    	function widget($args, $instance) {
    		coderbits_profiler_data();
    	}
    }
    
    function coderbits_profiler_register_widgets() {
    	register_widget('CoderbitsWidget');
    }
    
    add_action('widgets_init', 'coderbits_profiler_register_widgets');
?>