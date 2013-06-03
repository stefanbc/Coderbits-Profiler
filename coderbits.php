<?php
    /*
        Plugin Name: Coderbits Profiler
        Plugin URI: https://github.com/stefanbc/Coderbits-Profiler
        Description: Grabs Coderbits JSON data and displays it in your WordPress site.
        Version: 0.1
        Author: Stefan Cosma
        Author URI: http://coderbits.com/stefanbc
        License: MIT
    */
    
    add_action('admin_menu','coderbits_profiler');

	function coderbits_profiler() {
        add_submenu_page('options-general.php', 'Coderbits Profiler', 'Coderbits Profiler', 'edit_pages', 'coderbits_profiler', 'coderbits_profiler_options');
	}
    
    // Add settings link to plugins list
    function coderbits_add_settings_link($links) {
        $settings_link = '<a href="options-general.php?page=coderbits_profiler">Settings</a>';
        array_push($links, $settings_link);
        return $links;
    }
    
    $plugin = plugin_basename( __FILE__ );
    add_filter("plugin_action_links_$plugin", 'coderbits_add_settings_link');

    // Plugin options
    function coderbits_profiler_options(){
        global $wpdb;

        // Styling
        echo '<link href="//fonts.googleapis.com/css?family=Roboto:300,400" rel="stylesheet" type="text/css">';
        echo '<link href="' . plugins_url( 'assets/style.css' , __FILE__ ) . '" rel="stylesheet" type="text/css">';

        // Start the content part
        echo '<div class="main-wrapper">';
            echo '<span class="main-title"><img class="logo" src="' . plugins_url( 'assets/logo.png' , __FILE__ ) . '" alt="coderbits"> Profiler</span>';
            // The left part
            echo '<div class="sides">';
                // The profile part
                echo '<h2>Profile</h2>';
                echo '<form method="post">';
                    echo '<div class="field">Current active Coderbits profile: <b>' . get_option('coderbits_username') . '</b></div>';
                    echo '<div class="field">Set Coderbits profile: <input type="text" name="username" id="username" placeholder="coderbits username"><input type="submit" name="update_coderbits_profiler" value="Set Profile"></div>';
                echo '</form>';

                // The options part
                echo '<h2>Active fields</h2>';
                echo '<form method="post">';
                    echo '<div class="smaller-side">';
                        echo '<h3>Inactive Fields</h3>';
                        echo '<div class="active_fields zone">';
                        echo '</div>';
                    echo '</div>';
                    echo '<div class="smaller-side">';
                        echo '<h3>Active Fields</h3>';
                        echo '<div class="inactive_fields zone">';

                        echo '</div>';
                    echo '</div>';
                echo '<input type="submit" name="update_coderbits_profiler" value="Save Options">';
                echo '</form>';
            echo '</div>';
            // The right part
            echo '<div class="sides">';
                echo '<h2>Preview</h2>';
                echo '<div class="field">Nothing to see here, yet!</div>';
            echo '</div>';
        echo '</div>';
        
        $username = $wpdb->escape($_POST['username']);
        $active_fields = '';
        
        if($username) {
            update_option('coderbits_username', $username); 
        }
               
        add_option('coderbits_username', $username);
        add_option('coderbits_profiler_options', $options);
        add_option('coderbits_active_fields', $active_fields);
    }
    
    // Get data from JSON file
    function coderbits_profiler_data($type, $subtype = '') {
        
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
        $output = json_decode($result, true);
        
        // Output the requested field
        $return = $output[$type];

        // Check if the field has details
        if (is_array($return)) {
            foreach ($return as $items) {
                foreach($items as $key => $item){
                    // Check if the key from the loop is the chosen type
                    if ($key == $subtype) {
                        $data .= $item . ', '; 
                    }
                }  
            }
        } else {
            $data = $return;
        }
        
        return $data;
    }
    
    // We now create the widget and register it with WP
    class CoderbitsWidget extends WP_Widget {

        function CoderbitsWidget() {
    		// Instantiate the parent object
    		parent::__construct(false, 'Coderbits Profiler');
    	}
        
        // Output to frontend by the widget
    	function widget($args, $instance) {
            echo '<div class="coderbits-field" id="coderbits-avatar"><img src="http://www.gravatar.com/avatar/' . coderbits_profiler_data('gravatar_hash') . '" alt="avatar"></div>';
            echo '<div class="coderbits-field" id="coderbits-name">' . coderbits_profiler_data('name')  . '</div>';
            echo '<div class="coderbits-field" id="coderbits-title">' . coderbits_profiler_data('title') . '</div>';
            echo 'Top Skills';
            echo '<div class="coderbits-field" id="coderbits-title">' . coderbits_profiler_data('top_skills','name') . '</div>';

            $badges = coderbits_profiler_data('one_bit_badges') + coderbits_profiler_data('eight_bit_badges') + coderbits_profiler_data('sixteen_bit_badges') + coderbits_profiler_data('thirty_two_bit_badges') + coderbits_profiler_data('sixty_four_bit_badges');
            echo '<div class="coderbits-field" id="coderbits-title">Badges ' . $badges . '</div>';

            echo '<div class="coderbits-field" id="coderbits-title">Views ' . coderbits_profiler_data('views') . '</div>';
            echo '<div class="coderbits-field" id="coderbits-title">Followers ' . coderbits_profiler_data('follower_count') . '</div>';
            echo '<div class="coderbits-field" id="coderbits-title">Friends ' . coderbits_profiler_data('following_count') . '</div>';
    	}
    }
    
    function coderbits_profiler_register_widgets() {
    	register_widget('CoderbitsWidget');
    }
    
    add_action('widgets_init', 'coderbits_profiler_register_widgets');
?>