<?php
/*
Plugin Name: Dynamic Menu for Logged In Users
Plugin URI: http://cozmoslabs.com
Description: Serve different menus for logged in / logged out users!
Author: Cristian Antohe
Version: 1.0
Author URI: http://www.cozmoslabs.com/ 
*/

// we're cloning existing theme_locations and creating _loggedin ones. 
add_action( 'init', 'register_loggedin_menus' );
function register_loggedin_menus() {
  $default_menus = get_registered_nav_menus();
  $loggedin_menus = array();
  
  foreach ($default_menus as $slug => $name){
  $loggedin_menus[$slug . '_dmlu_loggedin'] = $name . ' Loggedin';
  }
  
  register_nav_menus(
	$loggedin_menus
  );
}

// depending on the current menu arguments, if there exists a _loggedin theme location we're using that when user is logged in.
add_filter('wp_nav_menu_args', 'serve_different_menu');
function serve_different_menu($content){

	$loggedin_theme_location = $content['theme_location'] . '_dmlu_loggedin';
	$active_menu_locations = get_nav_menu_locations();
	
	if ( is_user_logged_in() && !empty($content['theme_location']) && $active_menu_locations[$loggedin_theme_location] != 0 && array_key_exists($loggedin_theme_location, $active_menu_locations) ) { 
		$content['theme_location'] = $content['theme_location'] . '_dmlu_loggedin';
		return $content;
	} else {
		return $content;
	}
}