<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

//include( plugin_dir_path(__FILE__) . 'swwv-common-lib.php');

function manage_practice_sign_ups(&$fields, &$errors) {
    if (is_user_logged_in()) {
        $userid = get_current_user_id();
        if (isset($_POST['submit'])) {
            // Get fields from submitted form
            $fields = get_practice_sign_up_form_fields();

            // Sanitize fields
            sanitize_practice_sign_up_form_fields($fields);
            $form_validation = validate_practice_sign_up_form_fields($fields, $errors);
        } else {
            sanitize_practice_sign_up_form_fields($fields);
        }
        global $is_registered;
        $registration_fields = array();
        $is_registered = is_user_signedup($userid);
        if (!$is_registered) { //First time registrant
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
                $registration_fields = get_user_signedup_practices($fields);
                //var_dump($common_list_int_array);//
                //var_dump($custom_list_md_array);
                echo "firsttime registration - ";
                var_dump($registration_fields);
                first_time_signup_mongodb($registration_fields, $userid);
            }
        } else { //Returning User who already signed up for 9-1-1 sadhana.
            if (isset($_POST['submit']) && $form_validation) {
                // If successful, register user
                $registration_fields = get_user_signedup_practices($fields);

                //var_dump($common_list_int_array);
                //$obj->common_list = $common_list_int_array;
                //$obj->fields = $registration_fields;
                //var_dump(json_encode($obj));
                //update_sadhanas_mongodb($registration_fields, $userid);
                // And display a message
                // echo 'Your Changes To Practices Have Been Updated!<br\><br\>';
                first_time_signup_mongodb($registration_fields, $userid);
                update_sadhanas_mongodb($registration_fields, $userid);
            }
        }
        //display form for firsttime or returning user
        display_practice_sign_up_form($fields, $errors, $is_registered);
    } else {
        echo 'Please Login first. Goto <a href="' . get_site_url() . '?page_id=273">Log In</a>.<br\><br\>';
        header("Location:http://www.walkwithvalues.org/test/?page_id=273");
    }
}

function get_user_signedup_practices(&$fields) {
    $common_practice_list = $fields['common_list'];
    //echo 'clist - ';
    //var_dump($common_practice_list);
    $common_list_int_array = array_map('intval', explode(',', implode(",", $common_practice_list)));
    //echo "clist int array - ";
    //var_dump($common_list_int_array);
    $custom_practice_list = $fields['custom_list'];
    $custom_list_md_array = array();
    if (!empty($custom_practice_list)) {
        for ($i = 0; $i < sizeof($custom_practice_list); $i++) {
            $custom_list_md_array["text"][$i] = $custom_practice_list[$i];
        }
    } else {
        // $custom_list_md_array = null;
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

function display_practice_sign_up_form(&$fields, $errors, $is_registered) {

    $sadhanas = display_practice_sign_up_title($fields, $errors, $is_registered);
    ?>
    <form class="form-horizontal" action='?page_id=285' method="post">
        <div class="row">
            <?php
            display_user_profile($fields, $sadhanas, $is_registered);
            if ($is_registered) {
                echo '<div class="col-md-2"><input type="submit" class="btn btn-large btn-primary" name="submit" value="Update"></div>';
            } else {
                echo '<div class="col-md-2"><input type="submit" class="btn btn-large btn-primary" name="submit" value="Register"></div>';
            }
            ?>
        </div>

        <!-- Collapsable panel -->
        <div id="accordion" class="panel-group">
            <div class="panel panel-default">
                <div id="headingOne" class="panel-heading">
                    <h4 class="panel-title"><a href="#collapseOne" data-toggle="collapse" data-parent="#accordion">Manage Your Daily Practices
                        </a></h4>
                </div>
                <div id="collapseOne" class="panel-collapse collapse in">
                    <div class="panel-body">
                        <div>
                            <h4>Recommended 9 Common Practices</h4>
                            <?php
                            if (isset($_POST['submit'])) {
                                //$selected_practices = $fields['common_list'];
                                $hm_goal = $fields['healthymind_goal'];
                                $hb_goal = $fields['healthybody_goal'];
                                $hm_subgoals = $fields['healthymind_subgoals'];
                                $hb_subgoals = $fields['healthybody_subgoals'];
                            }
                            display_common_practices($fields, $sadhanas, $is_registered);
                            ?>
                        </div>
                        <div>
                            <h4>My Personal Practices</h4>
                            <?php
                            display_custom_practices($fields, $sadhanas, $is_registered)
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel panel-default">
                <div id="headingTwo" class="panel-heading">
                    <h4 class="panel-title"><a class="collapsed" href="#collapseTwo" data-toggle="collapse" data-parent="#accordion">Healthy Body Goal
                        </a></h4>
                </div>
                <div id="collapseTwo" class="panel-collapse collapse">
                    <div class="panel-body">
                        <?php
                        if (isset($_POST['submit'])) {
                            //$selected_practices = $fields['common_list'];
                            $hm_goal = $fields['healthymind_goal'];
                            $hb_goal = $fields['healthybody_goal'];
                            $hm_subgoals = $fields['healthymind_subgoals'];
                            $hb_subgoals = $fields['healthybody_subgoals'];
                        }
                        //display_common_and_custom_practices($fields, $sadhanas, $is_registered)
                        if (!$is_registered) { //first time accessing the sadhana signup page. Not yet registered.
                            ?>
                            <div>
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
                        <?php
                        } else {
                            foreach ($sadhanas as $sadhana) {
                                if ($sadhana[0] == 2) {
                                    ?>
                                    <div>
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
                                <?php
                                }
                            }
                        }
                        ?>

                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div id="headingThree" class="panel-heading">
                    <h4 class="panel-title"><a class="collapsed" href="#collapseThree" data-toggle="collapse" data-parent="#accordion">Healthy Mind Goal
                        </a></h4>
                </div>
                <div id="collapseThree" class="panel-collapse collapse">
                    <div class="panel-body">
                        <?php
                        if (isset($_POST['submit'])) {
                            //$selected_practices = $fields['common_list'];
                            $hm_goal = $fields['healthymind_goal'];
                            $hb_goal = $fields['healthybody_goal'];
                            $hm_subgoals = $fields['healthymind_subgoals'];
                            $hb_subgoals = $fields['healthybody_subgoals'];
                        }
                        //display_common_and_custom_practices($fields, $sadhanas, $is_registered)
                        if (!$is_registered) { //first time accessing the sadhana signup page. Not yet registered.
                            ?>
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
                        } else {
                            foreach ($sadhanas as $sadhana) {
                                if ($sadhana[0] == 3) {
                                    ?>
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
                                }
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </form> <?php
}

function display_practice_sign_up_title($fields = array(), $errors = null, $is_registered) {
    // Check for wp error obj and see if it has any errors
    display_form_errors($errors);
    $sadhanas = array();
    $userid = get_current_user_id();
    if ($is_registered) { //returning user
        ?>
        <div><h4>Manage Your Practices Here!</h4></div>
        <?php
        $sadhanas = get_registered_practices_from_mongo($userid);
        //echo "Registered Practices : ";
    } else { //first time user => unregistered
        ?>
        <div><strong><h5>Please register below to track your daily progress!</strong></h5><br/>
        <p>“Sow an action, reap a tendency, <br/>Sow a tendency, reap a habit,<br/>
            Sow a habit, reap a character,<br/>
            Sow a character and reap a destiny.<br/> You are the maker of your destiny. You can do or undo it.”<br/>
            - Discourse at the 10th Convocation of Sai Institute at the Vidyagiri Stadium, on 22-11-1991.</p>
        <p>Let us collectively adopt Transformation 9-1-1 practices as an offering to our Beloved Swami for HIS 90th birthday.</p>
        </div> <?php
        $type = '1';
        $sadhanas = get_all_common_recommended_practices_mongo($type); //always returns 9 rows
    }
    return $sadhanas;
    //display_form($fields, $sadhanas, $is_registered);
}

function display_user_profile(&$fields, &$sadhanas, $is_registered) {
     
               if (!$is_registered) { //still not a post but for custom/personal practices for registered user.
            ?> 
                <div class="span10">
                 <div class="col-md-5">Affiliation:
                 <select name="affiliation" class="selectpicker"> 
                  <?php 
                  $affiliations = array('other', 'Center San Jose', 'Concord','Elk Grove','Fremont','Fresno','Mt Tam','Modesto','Monterey Peninsula','Nevada City','Oakland','Peninsula','Pleasanton','Reno','Sacramento','San Bruno',
                    'San Jose','Santa Cruz','Santa Rosa','Stockton','Beaverton', 'Oregon','Center of Freedom');
                 foreach($affiliations as $affiliation )
                       {
                   echo '<option value="' . $affiliation . '"' . (isset($fields['affiliation']) && $fields['affiliation'] == $affiliation? ' selected' : '') . '>' . $affiliation . '</option>';
                   } ?>     
                </select>
            </div>
                 <!--echo "<option values=\"$sadhana[1]\" name=\"affiliation\" >";-->
          
                    <div class="col-md-5">
                      Group: <select name="group" class="selectpicker">
                   <?php
                   $groups = array('SSE', 'YA', 'Adult'); 
                   foreach( $groups as $group )
                       {
                   echo '<option value="' . $group . '"' . (isset($fields['group']) && $fields['group'] == $group ? ' selected' : '') . '>' . $group . '</option>';
                   } ?>
                    </select>
                  </div>
                            </div>
                            <?php
                        }
                     else { //still not a post but for first time user.
        //Nothing to loop through from DB as there are no custom practices in db yet for this user.
        //$affiliation = $fields['affiliation'];
        
        ?>
        <div class="span10">
            <div class="col-md-5">Affiliation:
                <select name="affiliation" class="selectpicker"> 
                  <?php 
                  $affiliations = array('other', 'Center San Jose', 'Concord','Elk Grove','Fremont','Fresno','Mt Tam','Modesto','Monterey Peninsula','Nevada City','Oakland','Peninsula','Pleasanton','Reno','Sacramento','San Bruno',
                    'San Jose','Santa Cruz','Santa Rosa','Stockton','Beaverton', 'Oregon','Center of Freedom');
                 foreach($affiliations as $affiliation )
                       {
                   echo '<option value="' . $affiliation . '"' . ($sadhanas[0][1] == $affiliation? ' selected' : '') . '>' . $affiliation . '</option>';
                   } ?>     
                </select>
              </div>
                
                
<!--                <select name="affiliation[]" class="selectpicker">
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
                </select>-->
               
            <div class="col-md-5">Group:
                     <select name="groups">
                                <?php
                                //$groups = array('SSE' => 'Child (17 years or below)', 'YA' => 'Young Adult (18 to 35 years)', 'Adult' => 'Adult (36 years and above)');
                                $groups = array('SSE', 'YA', 'Adult');
                                foreach ($groups as $group) {
                                echo '<option value="' . $group . '"' . ($sadhanas[0][0] == $group ? ' selected' : '') . '>' . $group. '</option>';
                            }
                            ?>
                         </select>
                    </div>
                      </div>

        <?php
    }
}

function display_common_practices(&$fields, &$sadhanas, $is_registered) {
    ?>
    <div class="checkbox"> 
        <?php
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
                foreach ($sadhanas as $sadhana) {//type always 1 for unregisted user.
                    //var_dump($sadhana);
                    //if ($sadhana[0] == 1) {
                    echo "<label>";
                    echo "<input type=\"checkbox\" name=\"common_list[]\" value=\"" . $sadhana[0] . "\" checked>" . $sadhana[1];
                    echo "</label><br/>";
                    //}
                }
            }
        } else {
            $selected_common_practices = $fields['common_list'];
            $all_common_sadhanas = get_all_common_recommended_practices_mongo("1"); //always returns 9 rows
            foreach ($all_common_sadhanas as $sadhana) {
                echo "<label>";
                if (is_item_checked($selected_common_practices, $sadhana[0])) {
                    echo "<input type=\"checkbox\" name=\"common_list[]\" value=\"" . $sadhana[0] . "\" checked>" . $sadhana[1];
                } else {
                    echo "<input type=\"checkbox\" name=\"common_list[]\" value=\"" . $sadhana[0] . "\" >" . $sadhana[1];
                }
                echo "</label><br/>";
            }
        }
        ?>
    </div>
    <?php
}

function display_custom_practices(&$fields, &$sadhanas, $is_registered) {
    if (!isset($_POST['submit'])) {
        ?>

        <div><?php
        if ($is_registered) { //still not a post but for custom/personal practices for registered user.
            foreach ($sadhanas as $sadhana) {
                if ($sadhana[0] == 4) {
                    echo "<label>";
                    echo "<input type=\"text\" name=\"custom_list[]\" value=\"" . $sadhana[1] . "\" >" . $sadhana[2];
                    echo "</label><br/>";
                }
            }
        } else { //still not a post but for first time user.
            //Nothing to loop through from DB as there are no custom practices in db yet for this user.
            ?>
                <div class="col-xs-7">
                    <input class="form-control" name="custom_list[]" type="text" placeholder="<enter your personal practice>"/><br/>
                    <input class="form-control" name="custom_list[]" type="text" placeholder="<enter your personal practice>"/><br/>
                    <input class="form-control" name="custom_list[]" type="text" placeholder="<enter your personal practice>"/><br/>
                    <input class="form-control" name="custom_list[]" type="text" placeholder="<enter your personal practice>"/><br/>
                    <input class="form-control" name="custom_list[]" type="text" placeholder="<enter your personal practice>"
                </div>
                <?php
            }
        } else { //This is beggining of post data for first time or returning use
            $custom_practice_list = $fields['custom_list'];
            //$all_common_sadhanas = get_all_common_recommended_practices_mongo($type); //always returns 9 rows
            foreach ($custom_practice_list as $custom_practice) {
                echo "<label>";
                //Add the input row for the current practice and mark it selected.
                ?>
                <input class="form-control col-xs-7" name="custom_list[]" type="text" placeholder="<enter your personal practice>" 
                       value = "<?php echo(isset($custom_practice) ? $custom_practice : '') ?>" /><br/>
                       <?php
                   }
                   echo "</label><br/>";
                   //}
               }
               ?>
    </div>
    <?php
}

//Not being used currently
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

// Register a new shortcode: [swwvs_manage_practice_sign_ups]
function practice_sign_ups_wrapper() {
    $fields = array();
    $errors = new WP_Error();
    ob_start();
    manage_practice_sign_ups($fields, $errors);
    return ob_get_clean();
}

// Register a new shortcode: [swwv_sadhana_signup]//
add_shortcode('swwvs_manage_practice_sign_ups', 'practice_sign_ups_wrapper');
?>