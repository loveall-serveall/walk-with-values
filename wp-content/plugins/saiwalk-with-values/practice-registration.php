<?php
/*
  Name: Transformation 9-1-1 Registration Workflow
  Description: Adds/ Updates Sadhana Signups data in MongoDB
 * based on user selections.
 */

function sadhana_signup(&$fields, &$errors) {
    if (is_user_logged_in()) {
        $userid = get_current_user_id();
        if (isset($_POST['submit'])) {
            // Get fields from submitted form
            $fields = get_form_fields();

            // Sanitize fields
            form_fields_sanitize($fields);
            $form_validation = form_fields_validate($fields, $errors);
        } else {
            form_fields_sanitize($fields);
        }
        global $is_registered;
        $registration_fields = array();
        $is_registered = is_user_signedup($userid);
        if (!$is_registered) { //First time registrant.
            // Check args and replace if necessary
            if (!is_array($fields)) {
                $fields = array();
            }
            if (!is_wp_error($errors)) {
                $errors = new WP_Error;
            }
            // Check for form submit and validation errors
            if (isset($_POST['submit']) && $form_validation) {
                // If successful, register user for sadhana program
                $registration_fields = get_registration_fields($fields);
                //var_dump($common_list_int_array);//
                //var_dump($custom_list_md_array);
                first_time_signup_mongodb($registration_fields, $userid);
            }
        } else { //Returning User who already signed up for 9-1-1 sadhana.
            if (isset($_POST['submit']) && $form_validation) {
                // If successful, register user
                $registration_fields = get_registration_fields($fields);

                //var_dump($common_list_int_array);
                //$obj->common_list = $common_list_int_array;
                //$obj->fields = $registration_fields;
                //var_dump(json_encode($obj));
                //update_sadhanas_mongodb($registration_fields, $userid);
                // And display a message
                // echo 'Your Changes To Practices Have Been Updated!<br\><br\>';
                first_time_signup_mongodb($registration_fields, $userid);
            }
        }
        //display form for firsttime or returning user
        display_sadhana_signup_form($fields, $errors, $is_registered);
    } else {
        echo 'Please Login first. Goto <a href="' . get_site_url() . '?page_id=273">Log In</a>.<br\><br\>';
        header("Location:http://www.walkwithvalues.org/test/?page_id=273");
    }
}

function get_registration_fields(&$fields) {
    $common_practice_list = $fields['common_list'];
    $common_list_int_array = array_map('intval', explode(',', implode(",", $common_practice_list)));
    $custom_practice_list = $fields['custom_list'];

    for ($i = 0; $i < sizeof($custom_practice_list); $i++) {
        $custom_list_md_array["text"][$i] = $custom_practice_list[$i];
    }

    return array(
        'common_list' => $common_list_int_array,
        'custom_list' => $custom_list_md_array,
        'healthybody_goal' => isset($_POST['healthybody_goal']) ? $_POST['healthybody_goal'] : '',
        'healthybody_subgoals' => isset($_POST['healthybody_subgoals']) ? $_POST['healthybody_subgoals'] : '',
        'healthymind_goal' => isset($_POST['healthymind_goal']) ? $_POST['healthymind_goal'] : '',
        'healthymind_subgoals' => isset($_POST['healthymind_subgoals']) ? $_POST['healthymind_subgoals'] : '',
        'affiliation' => isset($_POST['affiliation']) ? $_POST['affiliation'] : '',
        'group' => isset($_POST['group']) ? $_POST['group'] : ''
    );
}

//Will be used for user defined/added common practices since user has flexibility
//to update/add/remove the custom practices.
function is_checked(&$chkname, $value) {
    foreach ($chkname as $chkval) {
        if ($chkval == $value) {
            return true;
        }
    }
    return false;
}

function form_fields_sanitize(&$fields) {
    if (!isset($fields['common_list'])) {
        $fields['common_list'] = '';
    }
    $fields['healthybody_goal'] = isset($fields['healthybody_goal']) ? sanitize_text_field($_POST['healthybody_goal']) : '';
    $fields['healthymind_goal'] = isset($fields['healthymind_goal']) ? sanitize_text_field($_POST['healthymind_goal']) : '';
    //$fields['healthybody_subgoals'] = isset($fields['healthybody_subgoals']) ? sanitize_text_field($_POST['healthybody_subgoals']) : '';
    //$fields['healthymind_subgoals'] = isset($fields['healthymind_subgoals']) ? sanitize_text_field($_POST['healthymind_subgoals']) : '';
}

function display_sadhana_signup_form($fields = array(), $errors = null, $is_registered) {
    // Check for wp error obj and see if it has any errors
    display_errors($errors);
    $userid = get_current_user_id();
    if ($is_registered) { //returning user
        ?>
        <div><h4>Manage Your Practices Here!</h4></div>
        <?php
        $sadhanas = get_registered_practices_from_mongo($userid);
        //echo "Registered Practices : ";
        //var_dump($sadhanas);
    } else { //first time user => unregistered
        ?>
        <div><strong><h5>Please register below to track your daily progress!</strong></h5><br/>
        <p>“Sow an action, reap a tendency, <br/>Sow a tendency, reap a habit,<br/>
            Sow a habit, reap a character,<br/>
            Sow a character and reap a destiny.<br/> You are the maker of your destiny. You can do or undo it.”
            - Discourse at the 10th Convocation of Sai Institute at the Vidyagiri Stadium, on 22-11-1991.</p>
        <p>Let us collectively adopt Transformation 9-1-1 practices as an offering to our Beloved Swami for HIS 90th birthday.</p>
        </div> <?php
        $type = '1';
        $sadhanas = get_all_common_recommended_practices_mongo($type); //always returns 9 rows
    }
    display_form($fields, $sadhanas, $is_registered);
}

function display_sadhana_signup_form_for_first_timer($fields = array(), $errors = null, $is_registered) {
    // Check for wp error obj and see if it has any errors
    display_errors($errors);
    ?>
    <div><strong><h5>Please register below to track your daily progress!</strong></h5><br/>
    <p>“Sow an action, reap a tendency, <br/>Sow a tendency, reap a habit,<br/>
        Sow a habit, reap a character,<br/>
        Sow a character and reap a destiny.<br/> You are the maker of your destiny. You can do or undo it.”
        -Discourse at the 10th Convocation of Sai Institute at the Vidyagiri Stadium, on 22-11-1991.</p>
    <p>Let us collectively adopt 9-1-1 and implement it in our daily lives by Swami’s 90th birthday.
        This is and will be a continuous journey for all of us and won't be ending at Swami’s 90th birthday. </p>
    </div>
    <?php
    $type = '1';
    $sadhanas = get_all_common_recommended_practices_mongo($type);
    display_form($fields, $sadhanas, $is_registered);
}

function returning_user_display_form($fields = array(), $errors = null, $is_registered) {
// Check for wp error obj and see if it has any errors.
    display_errors($errors);
    ?>
    <div><h4>Manage Your Signed Up Practices Here!</h4></div>
    <?php
    $userid = get_current_user_id();
    $sadhanas = get_registered_practices_from_mongo($userid);
    display_form($fields, $sadhanas, $is_registered);
}

function display_form(&$fields, &$sadhanas, $is_registered) {
    ?>
    <form class="form-horizontal" action='?page_id=64' method="post">
        <div class="row">
            <?php
            display_user_profile();
            if ($is_registered) {
                echo '<div class="col-md-2"><input type="submit" class="btn btn-large btn-primary" name="submit" value="Update"></div>';
            } else {
                echo '<div class="col-md-2"><input type="submit" class="btn btn-large btn-primary" name="submit" value="Register"></div>';
            }
            ?>
        </div>
        <div class="span10">
            <div class="span4"> <h5>Recommended Common Practices</h5></div>
            <div class="span4"> <h5>My Personal Practices</h5></div>
        </div>
        <?php
        if (isset($_POST['submit'])) {
            //$selected_practices = $fields['common_list'];
            $hm_goal = $fields['healthymind_goal'];
            $hb_goal = $fields['healthybody_goal'];
            $hm_subgoals = $fields['healthymind_subgoals'];
            $hb_subgoals = $fields['healthybody_subgoals'];
        }
        display_common_and_custom_practices($fields, $sadhanas, $is_registered);
        ?>
        <?php
        if (!$is_registered) { //first time accessing the sadhana signup page. Not yet registered.
            ?>
            <div class="span10">
                <div class="form-group">
                    <label for="healthybody"><h4>Healthy Body - Describe the goal in few words below and add more details in
                            the text area.</h4></label>
                    <input type="text" class="form-control" id="hb_goal" name="healthybody_goal"
                           value="<?php echo(isset($fields['healthybody_goal']) ? $fields['healthybody_goal'] : '') ?>"
                           placeholder="describe the goal in few words">
                </div>
                <div class="form-group">
                    <textarea class="form-control custom-control" rows="16" cols="70" id= "hb_subgoals" name="healthybody_subgoals"
                              placeholder="Add list of sub goals that will help you reach the high level goal.">
                                  <?php echo(isset($fields['healthybody_subgoals']) ? $fields['healthybody_subgoals'] : '') ?>
                    </textarea>
                    <script>
                        CKEDITOR.replace('hb_subgoals');
                    </script>
                </div>
            </div>
            <div class="span10">
                <div class="form-group">
                    <label for="healthymind"><h4>Healthy Mind - Describe the goal in few words below and add more details in
                            the text area</h4></label>
                    <input type="text" class="form-control" id="hm_goal" name="healthymind_goal"
                           value="<?php echo(isset($fields['healthymind_goal']) ? $fields['healthymind_goal'] : '') ?>"
                           placeholder="describe the goal in few words">
                </div>
                <div class="form-group">
                    <textarea class="form-control custom-control" rows="16" cols="80" id="hm_subgoals"
                              name="healthymind_subgoals"
                              placeholder="Add list of sub goals that will help you reach the high level goal.">
                                  <?php echo(isset($fields['healthymind_subgoals']) ? $fields['healthymind_subgoals'] : '') ?>
                    </textarea>
                    <script>
                        CKEDITOR.replace('hm_subgoals');
                    </script>
                </div>
            </div>
            <?php
            display_personal_practices();
        } else {
            foreach ($sadhanas as $sadhana) {
                if ($sadhana[0] == 2) {
                    ?>
                    <div class="span10">
                        <div class="form-group">
                            <label for="healthybody"><h4>Healthy Body - Describe the goal in few words below and add more
                                    details in the text area.</h4></label>
                            <input type="text" class="form-control" id="hb_goal" name="healthybody_goal"
                                   value="<?php echo($sadhana[2]) ?>" placeholder="describe the goal in few words">
                        </div>
                        <div class="form-group">
                            <textarea class="form-control custom-control" rows="16" cols="70" id= "hb_subgoals"
                                      name="healthybody_subgoals"
                                      placeholder="Add list of sub goals that will help you reach the high level goal.">
                <?php echo(is_null($sadhana[3]) ? '' : $sadhana[3]) ?>
                            </textarea>
                            <script>
                                CKEDITOR.replace('hb_subgoals');
                            </script>
                        </div>
                    </div>
            <?php } else if ($sadhana[0] == 3) { ?>
                    <div class="span10">
                        <div class="form-group">
                            <label for="healthymind"><h4>Healthy Mind - Describe the goal in few words below and add more
                                    details in the text area</h4></label>
                            <input type="text" class="form-control" id="hm_goal" name="healthymind_goal"
                                   value="<?php echo($sadhana[2]) ?>" placeholder="describe the goal in few words">
                        </div>
                        <div class="form-group">
                            <textarea class="form-control custom-control" rows="16" cols="80" id="hm_subgoals"
                                      name="healthymind_subgoals"
                                      placeholder="Add list of sub goals that will help you reach the high level goal.">
                <?php echo(is_null($sadhana[3]) ? '' : $sadhana[3]) ?>
                            </textarea>
                            <script>
                                CKEDITOR.replace('hm_subgoals');
                            </script>
                        </div>
                    </div> <br/>
                <?php
                display_personal_practices(); //
            }
        }
    }
    ?>
    </form> <?php
}

function display_user_profile() {
    ?>
    <div class="col-md-5">Affiliation:
        <select name="affiliation" class="selectpicker">
            <option value="Other">Other/ Un-affiliated</option>
            <option value="Center San Jose">Center San Jose</option>
            <option value="Concord">Concord</option>
            <option value="Elk Grove">Elk Grove</option>
            <option value="Fremont">Fremont</option>
            <option value="Fresno">Fresno</option>
            <option value="Mt Tam">Mt Tam</option>
            <option value="Modesto">Modesto</option>
            <option value="Monterey Peninsula">Monterey Peninsula</option>
            <option value="Nevada City">Nevada City</option>
            <option value="Oakland">Oakland</option>
            <option value="Peninsula">Peninsula</option>
            <option value="Pleasanton">Pleasanton</option>
            <option value="Reno">Reno</option>
            <option value="Sacramento">Sacramento</option>
            <option value="San Bruno">San Bruno</option>
            <option value="San Francisco">San Francisco</option>
            <option value="San Jose">San Jose</option>
            <option value="Santa Cruz">Santa Cruz</option>
            <option value="Santa Rosa">Santa Rosa</option>
            <option value="Stockton">Stockton</option>
            <option value="Beaverton, Oregon">Beaverton, Oregon</option>
            <option value="Center of Freedom">Center of Freedom</option>
        </select>
    </div>
    <div class="col-md-4">Group: <select name="group" class="selectpicker">
            <option value="SSE">Child (17 years or below)</option>
            <option value="YA">Young Adult (18 to 35 years)</option>
            <option value="Adult">Adult (36 years and above))</option>
        </select>
    </div>
    <?php
}

function display_common_and_custom_practices(&$fields, &$sadhanas, $is_registered) {
    ?>
    <div class="span10">

        <div class="checkbox span4"> <?php
    if (!isset($_POST['submit'])) {
        if ($is_registered) {
            foreach ($sadhanas as $sadhana) {
                if ($sadhana[0] == 1) {
                    echo "<label>";
                    if ($sadhana[4]) {
                        echo "<input type=\"checkbox\" name=\"common_list[]\" value=\"" . $sadhana[1] . "\" checked>" . $sadhana[2];
                    } else {
                        echo "<input type=\"checkbox\" name=\"common_list[]\" value=\"" . $sadhana[1] . "\" >" . $sadhana[2];
                    }
                    echo "</label><br/>";
                }
            }
        } else { //still not a post but un-registered user.
            //echo " un registered user ";
            //var_dump($sadhanas);
            echo '</br></br>';

            foreach ($sadhanas as $sadhana) {//type always 1 for unregisted user.
                //var_dump($sadhana);
                //if ($sadhana[0] == 1) {
                echo "<label>";
                echo "<input type=\"checkbox\" name=\"common_list[]\" value=\"" . $sadhana[0] . "\" checked>" . $sadhana[1];
                echo "</label><br/>";
                //}
            }
        }
        ?>
            </div>
            <div class="checkbox"><?php
                if ($is_registered) { //still not a post but for custom/personal practices for registered user.
                    foreach ($sadhanas as $sadhana) {
                        if ($sadhana[0] == 4) {
                            echo "<label>";
                            echo "<input type=\"text\" name=\"custom_list[]\" value=\"" . $sadhana[1] . "\" >" . $sadhana[2];
                            //echo "getting into type 4 <br/>";
                            //if ($sadhana[4]) {
                            //echo "<input type=\"checkbox\" name=\"custom_list[]\" value=\"" . $sadhana[1] . "\" checked>" . $sadhana[2];
                            //} else {
                            //echo "<input type=\"checkbox\" name=\"custom_list[]\" value=\"" . $sadhana[1] . "\" >" . $sadhana[2];
                            //}
                            echo "</label><br/>";
                        }
                    }
                } else { //still not a post but for first time user.
                    //Nothing to loop through from DB as there are no custom practices in db yet for this user.
                    //Just show the "add new personal practice text box with a plus sign
                }
            } else { //This is beggining of post data for first time or returning user
                $common_practice_list = $fields['common_list'];
                $custom_practice_list = $fields['custom_list'];
                $type = 1;
                $all_common_sadhanas = get_all_common_recommended_practices_mongo($type); //always returns 9 rows
                foreach ($all_common_sadhanas as $sadhana) {
                    //if ($sadhana[0] == 1) {
                    echo "<label>";
                    if (is_checked($common_practice_list, $sadhana[0])) {
                        //Add the input row for the current practice and mark it selected.
                        echo "<input type=\"checkbox\" name=\"common_list[]\" value=\"" . $sadhana[0] . "\" checked>" . $sadhana[1];
                    } else {
                        //Add the input row for the current practice and mark it Un-selected.
                        echo "<input type=\"checkbox\" name=\"common_list[]\" value=\"" . $sadhana[0] . "\" >" . $sadhana[1];
                    }
                    echo "</label><br/>";
                    //}
                }
                ?>
            </div>

        </div>
        <?php
    }
}

function display_personal_practices() {
    ?>
    <div class="span10"> <?php
        /* foreach ($sadhanas as $sadhana) {
          if ($sadhana[0] == 4) {
          echo "inside type 4 again";
          echo "<label>";
          for ($i = 0; $i < sizeof($common_practice_list); $i++) {
          if (is_checked($common_practice_list, $sadhana[0])) {
          //Add the input row for the current practice and mark it selected.
          echo "<input type=\"checkbox\" name=\"custom_list[]\" value=\"" . $sadhana[0] . "\" checked>" . $sadhana[1];
          } else {
          //Add the input row for the current practice and mark it Un-selected.
          echo "<input type=\"checkbox\" name=\"custom_list[]\" value=\"" . $sadhana[0] . "\" >" . $sadhana[1];
          }
          }
          echo "</label><br/>";
          }
          } * */
        ?>
        <div>
            <div class="control-group" id="fields">
                <div class="controls">
                    <!--<form role="form" autocomplete="off"> -->
                    <div role="form-group">
                        <div id="custom-practices" class="entry input-group col-xs-3">
                            <input class="form-control" name="custom_list[]" type="text" placeholder="enter your personal practice!"/>
                            <span class="input-group-btn">
                                <button class="btn btn-success btn-add" type="button">
                                    <span class="glyphicon glyphicon-plus"></span>
                                </button>
                            </span>
                        </div>
                    </div>
                    <!-- </form> -->
                    <br>
                    <small>Press <span class="glyphicon glyphicon-plus gs"></span> to add another custom practice!
                    </small>
                </div>
            </div>
        </div>
        <style>
            .entry:not(:first-of-type) {
                margin-top: 10px;
            }

            .glyphicon {
                font-size: 12px;
            }
        </style>
        <script>
            $(function () {
                $(document).on('click', '.btn-add', function (e) {
                    e.preventDefault();

                    //var controlForm = $('.controls form:first'),
                    var controlForm = $('.controls:first'),
                            currentEntry = $(this).parents('.entry:first'),
                            newEntry = $(currentEntry.clone()).appendTo(controlForm);

                    newEntry.find('#custom-practices input').val('');
                    controlForm.find('.entry:not(:last) .btn-add')
                            .removeClass('btn-add').addClass('btn-remove')
                            .removeClass('btn-success').addClass('btn-danger')
                            .html('<span class="glyphicon glyphicon-minus"></span>');
                }).on('click', '.btn-remove', function (e) {
                    $(this).parents('.entry:first').remove();

                    e.preventDefault();
                    return false;
                });
            });

        </script>
    </div> <?php
}

function display_errors(&$errors) {
    if (is_wp_error($errors) && count($errors->get_error_messages()) > 0) {
        // Display errors
        echo "<ul>";
        foreach ($errors->get_error_messages() as $key => $val) {
            echo "<li>";
            echo $val;
            echo "</li>";
        }
        echo "</ul>";
    }
}

function get_form_fields() {
    return array(
        'common_list' => isset($_POST['common_list']) ? $_POST['common_list'] : '',
        'custom_list' => isset($_POST['custom_list']) ? $_POST['custom_list'] : '',
        'healthybody_goal' => isset($_POST['healthybody_goal']) ? $_POST['healthybody_goal'] : '',
        'healthybody_subgoals' => isset($_POST['healthybody_subgoals']) ? $_POST['healthybody_subgoals'] : '',
        'healthymind_goal' => isset($_POST['healthymind_goal']) ? $_POST['healthymind_goal'] : '',
        'healthymind_subgoals' => isset($_POST['healthymind_subgoals']) ? $_POST['healthymind_subgoals'] : '',
        'affiliation' => isset($_POST['affiliation']) ? $_POST['affiliation'] : '',
        'group' => isset($_POST['group']) ? $_POST['group'] : ''
    );
}

function form_fields_validate(&$fields, &$errors) {
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
        $errors->add('field', 'Please add your healthy body & healthy mind goals. If you are not ready, you can add place-holder goals and come back later to update them.');
    }
    // If errors were produced, fail
    if (count($errors->get_error_messages()) > 0) {
        return false;
    }
    // Else, success!
    return true;
}

// Register a new shortcode: [swwv_sadhana_signup]
function sadhana_signup_sc() {
    $fields = array();
    $errors = new WP_Error();
    ob_start();
    sadhana_signup($fields, $errors);
    return ob_get_clean();
}

// Register a new shortcode: [swwv_sadhana_signup]//
add_shortcode('swwv_sadhana_signup', 'sadhana_signup_sc');
?>