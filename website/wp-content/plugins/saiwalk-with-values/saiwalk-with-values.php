<?php
/**
 * Plugin Name: Sai Walk With Values Region7
 * Description: This plugin contains the core functions and libraries used by walkwithvalues.org portal
 * Version: 1.0
 * Author: Sai Spiritual Aspirants, CA, USA
 * License: GPL2
 */
/*  Copyright 2015  Sai Spiritual Aspirants, CA, USA  (email : walkwithvalues@gmail.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

  Note for plug-in developers.
  Some Model View Controller (MVC) development best practices:
  1. Create all the view pages (forms, charts, etc) in their own .php pages and
  just include them in this parent file.
  2. Individual view pages can in turn include references to model pages (functions,
  database related code, etc)
  3. The controllers (All the user facing pages) should have containers/ divs to hold
  the elements (forms, charts, etc) defined in the view pages.
 */
//include $_SERVER['DOCUMENT_ROOT']."/lib/httpful.phar";
include( plugin_dir_path(__FILE__) . 'mongo-db-api-calls.php');
include( plugin_dir_path(__FILE__) . 'swwv-common-lib.php');
include( plugin_dir_path(__FILE__) . 'transformation-911-details.php');
include( plugin_dir_path(__FILE__) . 'daily-practice-tracker.php');
include( plugin_dir_path(__FILE__) . 'manage-practice-signups.php');
//include( plugin_dir_path( __FILE__ ) . 'daily-practice-custom-charts.php');

ob_start();
?>

<?php
error_reporting(0);
// Same handler function...
if (defined('DOING_AJAX') && $_REQUEST["whichFunction"] == "get_daily_tracker_for_date") {
    //error_reporting(0);
    include $_SERVER['DOCUMENT_ROOT']."/lib/httpful.phar";
    $track_dt = $_REQUEST["sdate"];
    $_REQUEST["submit"] = true;
    $user_id = intval($_REQUEST["userId"]); 
    $time = strtotime($track_dt);
    $track_dt = date('Y-m-d', $time);
    $user_track_data = get_user_practice_progress_data_from_mongo($user_id, $track_dt);
    $fields = array ();
    $errors = array ();
    display_tracker_form($fields, $user_track_data, $errors, $user_id); 
    return;
}

//add_action('wp_ajax_PSURA_AJAX_ACTION', 'my_AJAX_processing_function');
//<script src="//cdn.ckeditor.com/4.4.7/standard/ckeditor.js"></script>
//<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.3.js"></script>

// register  style on initialization
add_action('init', 'register_script');
function register_script(){
    wp_register_style( 'new_style_bootstrap', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css', false, '1.0.0', 'all');
    wp_register_style('new_style_datepicker', 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.0/css/bootstrap-datepicker.css
', false, '1.0.0', 'all');
}

// use the registered style above
add_action('wp_enqueue_scripts', 'enqueue_style');
function enqueue_style(){    
    wp_enqueue_style( 'new_style_bootstrap' );
    wp_enqueue_style( 'new_style_datepicker' );
    $ajaxURL = admin_url('admin-ajax.php');
    $js = <<<EOD
            <script type="text/javascript">
 var ajaxurl = '$ajaxURL';
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment.js" type="text/javascript">
</script>
<script src="//cdn.ckeditor.com/4.4.7/standard/ckeditor.js"></script>
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.3.js"></script>
<script type="text/javascript" src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
<script src="https://cdn.rawgit.com/jhollingworth/bootstrap-wysihtml5/master/src/bootstrap-wysihtml5.js" type="text/javascript">
</script> 
<!--<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>-->
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.7.14/js/bootstrap-datetimepicker.min.js"></script>

EOD;
    echo $js;
    
}
?>