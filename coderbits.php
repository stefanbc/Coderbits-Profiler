<?php
    /*
        Plugin Name: Coderbits Profiler
        Plugin URI: https://github.com/stefanbc/Coderbits-Profiler
        Description: Grabs Coderbits JSON user data and displays it in your WordPress site as a widget.
        Version: 0.9
        Author: Stefan Cosma
        Author URI: http://coderbits.com/stefanbc
        License: MIT
    */

    add_action('admin_menu','coderbits_profiler');

    // create submenu page in the WordPress Settings menu
	function coderbits_profiler() {
        add_submenu_page('options-general.php', 'Coderbits Profiler', 'Coderbits Profiler', 'edit_posts', 'coderbits_profiler', 'coderbits_profiler_options');
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

        // Add the needed options to the wp_options table
        add_option('coderbits_profiler_username', $username);
        add_option('coderbits_profiler_active_fields', $active_fields);
        add_option('coderbits_profiler_inactive_fields', $inactive_fields);

        // Get the username
        $username = $wpdb->escape($_POST['username']);
        
        // Updated the username setting with the current set username
        if($username) {
            
            // We call for the JSON file only on username change
            // jSON URL which should be requested
            $json_url = 'https://coderbits.com/' . $username . '.json';
             
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
            
            // Save the result into a local file
            $save_file = file_put_contents(dirname(__FILE__) . '/cache/' . md5($username), $result);

            // Close request to clear up some resources
            curl_close($ch);          
            
            update_option('coderbits_profiler_username', $username);
            
        }

        // Styling and scripting
        echo '<link href="//fonts.googleapis.com/css?family=Roboto:300,400" rel="stylesheet" type="text/css">';
        echo '<link href="' . plugins_url( 'assets/style.css' , __FILE__ ) . '" rel="stylesheet" type="text/css">';
        echo '<script src="' . plugins_url( 'assets/general.js' , __FILE__ ) . '" type="text/javascript"></script>';

        // Start the content part
        echo '<div class="main-wrapper">';
            echo '<span class="main-title"><img class="logo" src="' . plugins_url( 'assets/logo.png' , __FILE__ ) . '" alt="coderbits"> Profiler</span>';
            // The left part
            echo '<div class="sides">';
                // The profile part
                echo '<h2 class="zone-title-profile">Profile <small><i>Update profile options</i></small></h2>';
                echo '<form method="post" id="profile_form">';
                    echo '<div class="row">Current active Coderbits profile: <b>' . get_option('coderbits_profiler_username') . '</b></div>';
                    echo '<div class="row">Set Coderbits profile: <input type="text" name="username" id="username" placeholder="coderbits username"><input type="submit" name="update_profile_coderbits_profiler" id="update_profile_coderbits_profiler" value="Set Profile"></div>';
                echo '</form>';

                // The fields part
                echo '<h2 class="zone-title-fields">Fields <small><i>Manage active/inactive fields</i></small></h2>';
                echo '<form method="post" id="fields_form">';
                    echo '<div class="fields_wrapper">';
                    echo '<div class="smaller-side">';
                        echo '<h3>Active</h3>';
                        echo '<div class="active-fields zone" ondrop="dropField(this, event)" ondragenter="return false" ondragover="return false">';
                            // Get the active fields
                            $query_active_fields = unserialize(get_option('coderbits_profiler_active_fields'));
                            if (!empty($query_active_fields)) {
                                foreach ($query_active_fields as $active_field) {
                                    if (!empty($active_field)) {
                                        if ($active_field == 'gravatar_hash') {
                                            echo '<span class="field" id="' . $active_field . '" draggable="true" ondragstart="dragField(this, event)">Avatar</span>';
                                        } else {
                                            echo '<span class="field" id="' . $active_field . '" draggable="true" ondragstart="dragField(this, event)">' . ucwords(str_replace("_"," ",$active_field)) . '</span>';
                                        }
                                    }
                                }
                            }
                        echo '</div>';
                    echo '</div>';
                    echo '<div class="arrows">&lt;&gt;</div>';
                    echo '<div class="smaller-side">';
                        echo '<h3>Inactive</h3>';
                        echo '<div class="inactive-fields zone" ondrop="dropField(this, event)" ondragenter="return false" ondragover="return false">';
                            // Get the inactive fields
                            $query_inactive_fields = unserialize(get_option('coderbits_profiler_inactive_fields'));
                            if (!empty($query_inactive_fields)) {
                                foreach ($query_inactive_fields as $inactive_field) {
                                    if (!empty($inactive_field)) {
                                        if ($inactive_field == 'gravatar_hash') {
                                            echo '<span class="field" id="' . $inactive_field . '" draggable="true" ondragstart="dragField(this, event)">Avatar</span>';
                                        } else {
                                            echo '<span class="field" id="' . $inactive_field . '" draggable="true" ondragstart="dragField(this, event)">' . ucwords(str_replace("_"," ",$inactive_field)) . '</span>';
                                        }
                                    }
                                }
                            } 
                            // If there are no fields set, show the default ones
                            else {
                                echo '<span id="name" class="field" draggable="true" ondragstart="dragField(this, event)">Name</span>';
                                echo '<span id="title" class="field" draggable="true" ondragstart="dragField(this, event)">Title</span>';
                                echo '<span id="location" class="field" draggable="true" ondragstart="dragField(this, event)">Location</span>';
                                echo '<span id="website_link" class="field" draggable="true" ondragstart="dragField(this, event)">Website Link</span>';
                                echo '<span id="bio" class="field" draggable="true" ondragstart="dragField(this, event)">Bio</span>';
                                echo '<span id="views" class="field" draggable="true" ondragstart="dragField(this, event)">Views</span>';
                                echo '<span id="rank" class="field" draggable="true" ondragstart="dragField(this, event)">Rank</span>';
                                echo '<span id="gravatar_hash" class="field" draggable="true" ondragstart="dragField(this, event)">Avatar</span>';
                                echo '<span id="badges_count" class="field" draggable="true" ondragstart="dragField(this, event)">Badges Count</span>';
                                echo '<span id="follower_count" class="field" draggable="true" ondragstart="dragField(this, event)">Follower Count</span>';
                                echo '<span id="following_count" class="field" draggable="true" ondragstart="dragField(this, event)">Following Count</span>';
                                echo '<span id="top_skills" class="field" draggable="true" ondragstart="dragField(this, event)">Top Skills</span>';
                                echo '<span id="top_languages" class="field" draggable="true" ondragstart="dragField(this, event)">Top Languages</span>';
                                echo '<span id="top_environments" class="field" draggable="true" ondragstart="dragField(this, event)">Top Environments</span>';
                                echo '<span id="top_frameworks" class="field" draggable="true" ondragstart="dragField(this, event)">Top Frameworks</span>';
                                echo '<span id="top_tools" class="field" draggable="true" ondragstart="dragField(this, event)">Top Tools</span>';
                                echo '<span id="top_interests" class="field" draggable="true" ondragstart="dragField(this, event)">Top Interests</span>';
                                echo '<span id="top_traits" class="field" draggable="true" ondragstart="dragField(this, event)">Top Traits</span>';
                                echo '<span id="top_areas" class="field" draggable="true" ondragstart="dragField(this, event)">Top Areas</span>';
                                echo '<span id="badges" class="field" draggable="true" ondragstart="dragField(this, event)">Badges</span>';
                            }
                        echo '</div>';
                    echo '</div>';
                    echo '</div>';
                echo '<div id="fields_submit_button" class="submit_button"><input type="submit" name="update_fields_coderbits_profiler" id="update_fields_coderbits_profiler" value="Save Fields"></div>';
                echo '</form>';
            echo '</div>';

            // The right part
            echo '<div class="sides">';
                echo '<h2 class="zone-title-preview">Preview Widget <small><i>Preview widget based on your settings</i></small></h2>';
                coderbits_profiler_output_data();
            echo '</div>';
        echo '</div>';
    }
    
    // Get data from JSON file
    function coderbits_profiler_data($type, $subtype = '') {

        $file = dirname(__FILE__) . '/cache/' . md5(get_option('coderbits_profiler_username'));
        
        if (file_exists($file)) {

            // Read the local file for data
            $json_file = file_get_contents($file);
            
            // Parse the JSON result
            $output = json_decode($json_file, true);
            
            // Output the requested field
            $return = $output[$type];

            // Check if the field is array
            if (is_array($return)) {
                // Limit counter
                $count = 0;
                foreach ($return as $items) {
                    foreach($items as $key => $item){
                        // Load only 14 entries
                        if (++$count > 14) break;

                        // Check if the key from the loop is the chosen type
                        if ($key == $subtype) {
                            $data .= $item . ', ';
                        }
                    }  
                }

            } else {
                $data = $return;
            }

        } else {
            $data = "NULL";
        }
        
        return $data;
    }

    // Output function used by preview and widget
    function coderbits_profiler_output_data(){
        // Get the active fields
        $preview_fields = unserialize(get_option('coderbits_profiler_active_fields'));
        if (!empty($preview_fields)) {
            echo '<link href="' . plugins_url( 'assets/output_styling.css' , __FILE__ ) . '" rel="stylesheet" type="text/css">';
            echo '<div class="cp_output_wrapper">';
            foreach ($preview_fields as $preview_field) {
                if (!empty($preview_field)) {
                    switch ($preview_field) {
                        // Output name with link to coderbits profile page
                        case 'name':
                            echo '<div id="' . $preview_field . '" class="' . $preview_field . ' cp_output_field"><a href="https://coderbits.com/' . get_option('coderbits_profiler_username') . '" title="' . coderbits_profiler_data($preview_field) . '" target="_blank">' . coderbits_profiler_data($preview_field) . '</a></div>';
                        break;
                        // Output for title and bio are the same
                        case 'title':
                        case 'bio':
                            echo '<div id="' . $preview_field . '" class="' . $preview_field . ' cp_output_field">' . coderbits_profiler_data($preview_field) . '</div>';
                        break;
                        // Output for location has link to Google Maps
                        case 'location':
                            echo '<div id="' . $preview_field . '" class="' . $preview_field . ' cp_output_field"><a href="https://maps.google.com/maps?q=' . coderbits_profiler_data($preview_field) . '" title="' . coderbits_profiler_data($preview_field) . '" target="_blank">' . coderbits_profiler_data($preview_field) . '</a></div>';
                        break;
                        // Output for website link
                        case 'website_link':
                            echo '<div id="' . $preview_field . '" class="' . $preview_field . ' cp_output_field"><a href="' . coderbits_profiler_data($preview_field) . '" title="Website" target="_blank">' . coderbits_profiler_data($preview_field) . '</a></div>';
                        break;
                        // Output for views and rank are the same
                        case 'views':
                        case 'rank':
                            echo '<div id="' . $preview_field . '" class="' . $preview_field . ' cp_output_field"><span class="field_text">' . ucwords($preview_field) . '</span> ' .coderbits_profiler_data($preview_field) . '</div>';
                        break;
                        // Output the avatar
                        case 'gravatar_hash':
                            echo '<div id="' . $preview_field . '" class="' . $preview_field . ' cp_output_field"><a href="https://coderbits.com/' . get_option('coderbits_profiler_username') . '" title="' . get_option('coderbits_profiler_username') . '" target="_blank"><img src="http://www.gravatar.com/avatar/' . coderbits_profiler_data($preview_field) . '" alt="' . get_option('coderbits_profiler_username') . '"></a></div>';
                        break;
                        // Output the badges count
                        case 'badges_count':
                            $badges_count = coderbits_profiler_data('one_bit_badges') + coderbits_profiler_data('eight_bit_badges') + coderbits_profiler_data('sixteen_bit_badges') + coderbits_profiler_data('thirty_two_bit_badges') + coderbits_profiler_data('sixty_four_bit_badges');

                            echo '<div id="' . $preview_field . '" class="' . $preview_field . ' cp_output_field"><span class="field_text">' . ucwords(substr($preview_field, 0, -6)) . '</span> ' . $badges_count . '</div>';
                        break;
                        // Output follower and following count are the same
                        case 'follower_count':
                        case 'following_count':
                            $text = ($preview_field == "follower_count") ? "Followers" : "Friends";
                            echo '<div id="' . $preview_field . '" class="' . $preview_field . ' cp_output_field"><span class="field_text">' . $text . '</span> ' . coderbits_profiler_data($preview_field) . '</div>';
                        break;
                        // Output top things are the same
                        case 'top_skills':
                        case 'top_languages':
                        case 'top_environments':
                        case 'top_frameworks':
                        case 'top_tools':
                        case 'top_interests':
                        case 'top_traits':
                        case 'top_areas':
                            echo '<div id="' . $preview_field . '" class="' . $preview_field . ' cp_output_field"><span class="field_text">' . ucwords(str_replace("_"," ", $preview_field)) . '</span>' . coderbits_profiler_data($preview_field, 'name') . '</div>';
                        break;
                        case 'badges':
                            echo '<div id="' . $preview_field . '" class="' . $preview_field . ' cp_output_field">' . coderbits_profiler_data($preview_field, 'name') . '</div>';
                        break;
                    }
                }
            }
            echo '</div>';
        } else {
            echo '<div class="row">No data to display, yet!</div>';
        }
    }
    
    // We now create the widget and register it with WP
    class CoderbitsProfilerWidget extends WP_Widget {

        function CoderbitsProfilerWidget() {
    		// Instantiate the parent object
    		parent::__construct(false, 'Coderbits Profiler');
    	}
        
        // Output to frontend widget
    	function widget($args, $instance) {
            coderbits_profiler_output_data();
    	}
    }
    
    // Register the widget
    function coderbits_profiler_register_widgets() {
    	register_widget('CoderbitsProfilerWidget');
    }
    
    add_action('widgets_init', 'coderbits_profiler_register_widgets');
?>