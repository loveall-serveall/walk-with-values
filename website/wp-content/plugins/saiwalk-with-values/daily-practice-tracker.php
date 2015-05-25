<?php
/*
  Name: Sadhana Tracker Workflow
  Description: Add/ Updates Sadhana Tracker data in MongoDB based on user selections.
 */
function track_my_sadhanas(&$fields, &$errors)
{
    if (is_user_logged_in()) {
        $user_id = get_current_user_id();
        if (is_user_signedup($user_id)) {
            if (!is_array($fields)) {
                $fields = array();
            }
            if (!is_wp_error($errors)) {
                $errors = new WP_Error;
            }
            //TODO: Fix the local client date. PHP always gives the server date not client date.
            //Currently using PST date which would fail if use is not in PST timezone.
            //Through javascript we can get local user date but can't pass it to server in time to get the data for that date
            // unless we use ajax call to run php code.
            date_default_timezone_set('America/Los_Angeles');
            $track_dt = new DateTime();
            if (isset($_POST['submit']) || isset($_POST['getdata'])) {
                $track_dt = $_POST['sdate'];
                $time = strtotime($track_dt);
                $track_dt = date('Y-m-d', $time);
            } else {
                $track_dt = $track_dt->format('Y-m-d');
            }
            //$user_sadhanas_track_data = get_user_practice_progress_data($user_id, $track_dt);
            $user_sadhanas_track_data = get_user_practice_progress_data_from_mongo($user_id, $track_dt);
            //var_dump($user_sadhanas_track_data);
            // Check for form submit
            if (isset($_POST['submit'])) {
                // Get fields from submitted form
                $fields = tr_get_fields();
                // Sanitize fields
                tr_sanitize($fields);
                // Validate fields and produce errors
                if (tr_validate($fields, $errors)) {
                    // If successful, update user track data for selected date
                    // update_user_progress($fields, $user_sadhanas_track_data, $track_dt);
                    update_user_progress_on_mongo($fields, $user_sadhanas_track_data, $track_dt, $user_id);
                    //TODO: Check if update succeeded and then print great job
                    echo "<h4><span style=\"color: green; \">Updates are saved. Good Job! Keep practicing !!!</span></h4>";
                }
            } else {
                // Sanitize fields
                tr_sanitize($fields);
            }
            // Display/Generate form for unregistered/ first time  user
            display_tracker_form($fields, $user_sadhanas_track_data, $errors, $user_id);
        } else {
            echo 'Please Signup for the practices first. Goto <a href="' . get_site_url() . '?page_id=185">Practice Signup</a>.<br\><br\>';
            header("Location:" .  get_site_url() . "?page_id=285");
        }
    } else {
        echo 'Please login to website first. Goto <a href="' . get_site_url() . '?page_id=178">Login Page</a>.<br\><br\>';
        header("Location:" .  get_site_url() . "?page_id=273");
    }
}
//Will be used for common recommended and user added practices since user has
//flexibility to select/ unselect these practices.
function is_check(&$chkname, $value)
{
    foreach ($chkname as $chkval) {
        if ($chkval == $value) {
            return true;
        }
    }
    return false;
}

function tr_sanitize(&$fields)
{
    if (!isset($fields['common_list'])) {
        $fields['common_list'] = '';
    }
    //$fields['healthybody_goal'] = isset($fields['healthybody_goal']) ? sanitize_text_field($_POST['healthybody_goal']) : '';
    // $fields['healthymind_goal'] = isset($fields['healthymind_goal']) ? sanitize_text_field($_POST['healthymind_goal']) : '';
    //$fields['healthybody_subgoals'] = isset($fields['healthybody_subgoals']) ? sanitize_text_field($_POST['healthybody_subgoals']) : '';
    //$fields['healthymind_subgoals'] = isset($fields['healthymind_subgoals']) ? sanitize_text_field($_POST['healthymind_subgoals']) : '';
    //echo "inside tr_sanitize() - fields : ";
    //var_dump($fields);
}

function display_tracker_form(&$fields = array(), &$user_track_data, $errors = null, $user_id = null)
{
     //echo " <br />trackdata in display form - ";
    //var_dump($user_track_data);
    // Check for wp error obj and see if it has any errors
    if (is_wp_error($errors) && count($errors->get_error_messages()) > 0) {
        // Display errors
        ?>
        <ul><?php
        foreach ($errors->get_error_messages() as $key => $val) {
            ?>
            <li>
            <?php echo $val; ?>
            </li><?php
        }
        ?></ul><?php
    }
    // Display form
    ?>
<?php
        error_reporting (0);
?>
<div id ="tracking-content">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/jquery-ui.css">
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.3.js"></script>
<script type="text/javascript" src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
<script src="https://cdn.rawgit.com/jhollingworth/bootstrap-wysihtml5/master/src/bootstrap-wysihtml5.js" type="text/javascript">
</script> 
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.7.14/js/bootstrap-datetimepicker.min.js"></script>
<script>
        var userId = '<?= $user_id ?>';
    </script>
    <script>
        jQuery(document).ready(function () {
            jQuery("#sdate").datepicker({})
                .on('change', function () {
                    $.get(ajaxurl,
                            {"sdate": $(this).val (), userId: userId, whichFunction: "get_daily_tracker_for_date"},
                            function (response) {
                                var $parent = $("#tracking-content").parent ();
                                $("#tracking-content").remove ();
                                $(response).appendTo ($parent);
                            }
                    );
                })
        });
    </script>
    <span id="display-form">
        <form class="form-horizontal" action='?page_id=287' method="post">
            <?php
            if (isset($_REQUEST['submit']) || isset($_REQUEST['getdata'])) {
                $time = strtotime($_REQUEST['sdate']);
                $track_dt = date('m/d/Y', $time);
            ?>
            <div id="sandbox-container">Pick a date: <input type="text" id="sdate" name="sdate" value="<?php echo $track_dt; ?>"/>
            
            <?php
            }
            else { ?>
                <div id="sandbox-container">Pick a date: <input type="text" id="sdate" name="sdate" value=""/>
                    <script>
                        var date = new Date(<?php echo strtotime(date('m/d/y')*1000); ?>);
                        date.toLocaleDateString();
                        month = '' + (date.getMonth() + 1);
                        day = '' + date.getDate();
                        year = date.getFullYear();
                        if (month.length < 2) month = '0' + month;
                        if (day.length < 2) day = '0' + day;
                        var formattedDate = [month, day, year].join('/');
                        document.getElementById("sdate").value = formattedDate;
                    </script>
                
            <?php
            } ?>
            <!--<input class="btn btn-primary" type="submit" name="getdata" value="GET TRACKING INFO">--> 
            <input class="btn btn-primary" type="submit" name="submit" value="UPDATE">
            </div> <!-- closes the div for sandbox container div that's in if else loop above-->
            <div>
                <h3>You are now viewing tracking data for <?= $_REQUEST['sdate'] ? $_REQUEST['sdate'] : "today" ?></h3>
            </div>
            <div>
                Here are the daily practices that you signed up to track.
                Please visit <a href="?page_id=285">"Manage My Practices"</a> page to update your practice list.<br/><br />
                <div><h4>My Daily Practices</h4></div>
                Select all the entries that you have successfully practiced today. <br/><br/>

                <?php
                $user_id = get_current_user_id();
                $type = '1';
                               
                if (isset($_POST['submit'])) {
                $selected_practices = $fields['common_list'];
                $hm_goal = $fields['healthymind_goal'];
                $hb_goal = $fields['healthybody_goal'];
                $hm_subgoals = $fields['healthymind_subgoals'];
                $hb_subgoals = $fields['healthybody_subgoals'];
                $hb_cbox = $fields['hb_cbox'];
                $hm_cbox = $fields['hm_cbox'];

                foreach ($user_track_data as $sadhana) {
                if ($sadhana[0] == 1) {
                    echo "<label>";
                    if (is_check($selected_practices, $sadhana[1])) { //current sadhana is selected in DB as well as on UI.
                        echo "<input type=\"checkbox\" name=\"common_list[]\" value=\"" . $sadhana[1] . "\" checked>" . $sadhana[2];
                    } else {
                        echo "<input type=\"checkbox\" name=\"common_list[]\" value=\"" . $sadhana[1] . "\" >" . $sadhana[2];
                    }
                    echo "</label><br/>";
                } 
                else if (($sadhana[0] == 4) && strlen($sadhana[2])>0) {
                    echo "<label>";
                    if ($sadhana[4]) {
                        echo "<input type=\"checkbox\" name=\"custom_list[]\" value=\"$sadhana[1]\" checked > $sadhana[2] (personal)";
                    } else {
                        echo "<input type=\"checkbox\" name=\"custom_list[]\" value=\"$sadhana[1]\" > $sadhana[2] (personal)";
                    }
                    echo "</label><br/>";
                }
                } ?>
                
                <br/><div><h4>My Healthy Body and Healthy Mind Goals</h4></div> 
                <?php
                foreach ($user_track_data as $sadhana) {             
                    if ($sadhana[0] == 2) { ?>
                    <div>
                        <table class="table" align="right">
                            <thead>
                            <tr>
                                <th>On Track</th>
                                <th>Healthy Body Goal/ Sub Goals</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                            <?php
                            if ($hb_cbox == $sadhana[1]) { //current sadhana is selected in DB as well as on UI
                                echo "<td><input type=\"checkbox\" name=\"hb_cbox\" value=\"" . $sadhana[1] . "\" checked></td>";
                                echo "<td><strong>" . $sadhana[2] . "</strong><br>" . $sadhana[3] . "</td>";

                            } else {
                                echo "<td><input type=\"checkbox\" name=\"hb_cbox\" value=\"" . $sadhana[1] . "\" ></td>";
                                echo "<td><strong>" . $sadhana[2] . "</strong><br>" . $sadhana[3] . "</td>";
                            } ?>
                            </tr>
                            </tbody>
                        </table> 
                    </div>
                    <?php
                    } 
                    else if ($sadhana[0] == 3) { ?>
                    <div>
                        <table class="table" align="right">
                            <thead>
                            <tr>
                                <th>On Track</th>
                                <th>Healthy Mind Goal/ Sub Goals</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                            <?php
                            if ($hm_cbox == $sadhana[1]) { //current sadhana is selected in DB as well as on UI
                                echo "<td><input type=\"checkbox\" name=\"hm_cbox\" value=\"" . $sadhana[1] . "\" checked></td>";
                                echo "<td><strong>" . $sadhana[2] . "</strong><br>" . $sadhana[3] . "</td>";

                            } else {
                                echo "<td><input type=\"checkbox\" name=\"hm_cbox\" value=\"" . $sadhana[1] . "\" ></td>";
                                echo "<td><strong>" . $sadhana[2] . "</strong><br>" . $sadhana[3] . "</td>";
                            } ?>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <?php
                    }
                }
                } //close if post true
                else { //access first time without post/submit : show state based off of mongo db.
                    foreach ($user_track_data as $sadhana) {
                if ($sadhana[0] == 1) {
                    echo "<label>";
                    if ($sadhana[4]) {
                        echo "<input type=\"checkbox\" name=\"common_list[]\" value=\"$sadhana[1]\" checked > $sadhana[2]";
                    } else {
                        echo "<input type=\"checkbox\" name=\"common_list[]\" value=\"$sadhana[1]\" > $sadhana[2]";
                    }
                    echo "</label><br/>";
                } 
                else if (($sadhana[0] == 4) && strlen($sadhana[2])>0) {
                    echo "<label>";
                    if ($sadhana[4]) {
                        echo "<input type=\"checkbox\" name=\"custom_list[]\" value=\"$sadhana[1]\" checked > $sadhana[2] (personal)";
                    } else {
                        echo "<input type=\"checkbox\" name=\"custom_list[]\" value=\"$sadhana[1]\" > $sadhana[2] (personal)";
                    }
                    echo "</label><br/>";
                }
                }
                ?>
                <br/><div><h4>My Healthy Body and Healthy Mind Goals</h4></div> 
                <?php
                foreach ($user_track_data as $sadhana) {             
                    if ($sadhana[0] == 2) { ?>
                    <div>
                        <table class="table" align="right">
                            <thead>
                            <tr>
                                <th>On Track</th>
                                <th>Healthy Body Goal/ Sub Goals</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                            <?php
                            if ($sadhana[4]) { //User previously marked this goal as "On Track" for current selected date.
                                echo "<td><input type=\"checkbox\" name=\"hb_cbox\" value=\"" . $sadhana[1] . "\" checked></td>";
                                echo "<td><strong>" . $sadhana[2] . "</strong><br>" . $sadhana[3] . "</td>";

                            } else {
                                echo "<td><input type=\"checkbox\" name=\"hb_cbox\" value=\"" . $sadhana[1] . "\" ></td>";
                                echo "<td><strong>" . $sadhana[2] . "</strong><br>" . $sadhana[3] . "</td>";
                            } ?>
                            </tr>
                            </tbody>
                        </table> 
                    </div>
                    <?php
                    } 
                    else if ($sadhana[0] == 3) { ?>
                    <div>
                        <table class="table" align="right">
                            <thead>
                            <tr>
                                <th>On Track</th>
                                <th>Healthy Mind Goal/ Sub Goals</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                            <?php
                            if ($sadhana[4]) { //User previously marked this goal as "On Track" for current selected date.
                                echo "<td><input type=\"checkbox\" name=\"hm_cbox\" value=\"" . $sadhana[1] . "\" checked></td>";
                                echo "<td><strong>" . $sadhana[2] . "</strong><br>" . $sadhana[3] . "</td>";

                            } else {
                                echo "<td><input type=\"checkbox\" name=\"hm_cbox\" value=\"" . $sadhana[1] . "\" ></td>";
                                echo "<td><strong>" . $sadhana[2] . "</strong><br>" . $sadhana[3] . "</td>";
                            } ?>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <?php
                    }
                }
                }
                ?>
                </div><!--close daily practices and HB/HM practices-->
        </form>
    </span>         
</div>
<?php
}

function tr_get_fields()
{
    //echo "get fields - ";
    //var_dump($_POST['sdate']);/
    return array(
        'sdate' => isset($_POST['sdate']) ? $_POST['sdate'] : '',
        'common_list' => isset($_POST['common_list']) ? $_POST['common_list'] : '',
        'custom_list' => isset($_POST['custom_list']) ? $_POST['custom_list'] : '',
        'healthybody_goal' => isset($_POST['healthybody_goal']) ? $_POST['healthybody_goal'] : '',
        'healthybody_subgoals' => isset($_POST['healthybody_subgoals']) ? $_POST['healthybody_subgoals'] : '',
        'healthymind_goal' => isset($_POST['healthymind_goal']) ? $_POST['healthymind_goal'] : '',
        'healthymind_subgoals' => isset($_POST['healthymind_subgoals']) ? $_POST['healthymind_subgoals'] : '',
        'hb_cbox' => isset($_POST['hb_cbox']) ? $_POST['hb_cbox'] : '',
        'hm_cbox' => isset($_POST['hm_cbox']) ? $_POST['hm_cbox'] : '',
    );
}

function tr_validate(&$fields, &$errors)
{
    // Make sure there is a proper wp error obj
    // If not, make one
    if (!is_wp_error($errors)) {
        $errors = new WP_Error;
    }
    // Validate form data
    if (isset($_POST['common_list']) == "") {
        //$errors->add('field', 'Check at least one practice in Common Practices');
    }
    if (empty($fields['healthybody_goal']) || empty($fields['healthymind_goal'])) {
        //$errors->add('field', 'Please add your healthy body & healthy mind goals. If you are not ready, you can add place-holder goals and come back later to update them.');
    }
    // If errors were produced, fail
    if (count($errors->get_error_messages()) > 0) {
        return false;
    }
    // Else, success!
    return true;
}

// Register a new shortcode: [swwv_sadhana_signup]
function sadhana_tracker_wrapper()
{
    $fields = array();
    $errors = new WP_Error();
    ob_start();
    track_my_sadhanas($fields, $errors);
    return ob_get_clean();
}

// Register a new shortcode: [swwv_sadhana_signup]
add_shortcode('swwv_track_my_sadhanas', 'sadhana_tracker_wrapper');
//add_action( 'wp_ajax_the_ajax_hook_2', 'get_tracker_form_content');
?>