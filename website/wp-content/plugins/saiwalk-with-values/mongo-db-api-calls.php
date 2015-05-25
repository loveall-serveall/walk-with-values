<?php
/**
  Mongo DB API Calls..
 */

 // NODE API  ROUTES

$SADHANAS = '/sadhanas';  
 // Get all common sadhanas

$SADHANA_REGISTER_OR_UPDATE_API = '/sadhanaupdate'; 
  // register or update users sadhanas

$SADHANATRACK = '/sadhanastrack/';  
  // get a users track data for a given date

$SADHANATRACKED = '/webscreenTracked/';  
  // get a users track data for a given date

$SIGNEDUP = '/signedup/';
  // check if the user signed up 

$USERSIGNEDUPSADHANAS = '/sadhanalist/';
// signed up sadhanas for a user

$CREATETUSER = '/users';

// create a new user in wwv mongo db
function create_user_in_mongodb($newuser) {
   $service_url = API_ROOT_URL.$GLOBALS['CREATETUSER'];
   error_log('create_user_in_mongodb api endpoint '.$service_url.' ', 3, DEFAULT_LOG);

          $obj->userdata = $newuser;
          $wwvappresult =\Httpful\Request::post($service_url)
          ->body(json_encode($obj))
          ->sendsJson()
          ->send();
        error_log('create_user_in_mongodb user created in master db', 3, DEFAULT_LOG);
        return true;
    
}


function update_user_progress_on_mongo(&$fields, &$sadhana_track_db_data, &$track_date, &$user_id) {
    $service_url = API_ROOT_URL . $GLOBALS['SADHANATRACKED'] . $user_id . '/' . $track_date;
    $obj->fields = $fields;
    $response = \Httpful\Request::post($service_url)
            ->body(json_encode($obj))
            ->sendsJson()
            ->send();
    return true;
}

function first_time_signup_mongodb(&$fields, $userid) {
    $service_url = API_ROOT_URL . $GLOBALS['SADHANA_REGISTER_OR_UPDATE_API'];
    error_log('sadhana_signup api endpoint ' . $service_url . ' ', 3, DEFAULT_LOG);
    error_log($userid, 3, DEFAULT_LOG);
    //Constructing the json object that I want to send. Object encoded in the next step.
    $userSignUp = new stdClass();
    $obj->userid = $userid;
    $obj->fields = $fields;
    // calling sadhanaregistration api to sign up a user's sadhanas
    $response = \Httpful\Request::post($service_url)
            ->body(json_encode($obj))
            ->sendsJson()
            ->send();
}

function is_user_signedup($user_id) {
    $service_url = API_ROOT_URL . $GLOBALS['SIGNEDUP'] . $user_id;
    error_log('signupstatus api endpoint ' . $service_url . ' ', 3, DEFAULT_LOG);
    $response = \Httpful\Request::get($service_url)->send();
    $result = json_decode($response->body);
    //echo " inside is user signed up - ";
    //var_dump($result);//
    if ($result->status == 1) {
        return true;
    }
    return false;
}

function get_user_practice_progress_data_from_mongo(&$user_id, &$track_date) {
    $service_url = API_ROOT_URL . $GLOBALS['SADHANATRACK'] . $user_id . '/' . $track_date;
    error_log('signupstatus api endpoint ' . $service_url . ' ', 3, DEFAULT_LOG);
    $response = \Httpful\Request::get($service_url)->send();
    //return $response["type"];
    $result = $response->body;
    //var_dump($result);
    $result = (array) $result;
    $i = 0;
    $tracker_data = array();
    //echo count($result);
    foreach ($result as $row) {
        //$row = (array)$row;
        if ($row->type == 1) {
            foreach ($row->sadhanas as $sadhana) {
                $tracker_data[$i] = array($row->type, $sadhana->id, $sadhana->text, $sadhana->description, $sadhana->checked, $user_id);
                $i +=1;
            }
        } else if ($row->type == 2 || $row->type == 3) {
            foreach ($row->sadhanas as $sadhana) {
                $tracker_data[$i] = array($row->type, $sadhana->id, $sadhana->goal, $sadhana->subgoal, $sadhana->checked, $user_id);
                $i +=1;
            }
        } else if ($row->type == 4) {
            foreach ($row->sadhanas as $sadhana) {
                $tracker_data[$i] = array($row->type, $sadhana->id, $sadhana->text, $sadhana->description, $sadhana->checked, $user_id);
                $i +=1;
            }
        }
    }
    //echo "row : $row->type";
    /* if($row["type"] == 1) {
      $tracker_data[$i] = array($row["type"], $row["id"], $row["text"], $row["description"], $row["checked"], null, null);
      } else {
      $tracker_data[$i] = array($row["type"], $row["id"], $row["text"], $row["subgoal"], $row["checked"], null, null);
      } */
    //var_dump($tracker_data[$i]);
    //echo $row->text;
    //error_log(implode(",", $data[$i]), 3, DEFAULT_LOG);
    //error_log("\n", 3, DEFAULT_LOG);
    // $i += 1;
    //}
    //var_dump($tracker_data);
    return $tracker_data;
}

function get_registered_practices_from_mongo(&$userid) {
    $service_url = API_ROOT_URL . $GLOBALS['USERSIGNEDUPSADHANAS'] . $userid;
    error_log('sadhana list api endpoint ' . $service_url . ' ', 3, DEFAULT_LOG);
    error_log($userid, 3, DEFAULT_LOG);
    $response = \Httpful\Request::get($service_url)->send();
    error_log($response->raw_body, 3, DEFAULT_LOG);
    //echo $response->raw_body;
    //echo "json echo done.. ";//
    $result = $response->body->sadhanaregistrations;
    $settings = $response->body->settings;
    //var_dump($settings);//
   // echo "inside get practice in mongo saicenter : $response->body->settings";
    //var_dump($result);
    $result = (array) $result;
     $settings = (array) $settings;
     //var_dump($settings);
    $data = "";
    $i=0;
    foreach ($settings as $row) {
        if ($i == 0) {
            $data[$i] = array($row -> agegroup, $row->center);
    }
    }
    //var_dump($data);
    $i = 1;
   foreach ($result as $row) {
        // $data[$i] = array($row->sadhana_type_id,$row->id,$row->sadhana_name,$row->sadhana_description,$row->user_id);
        //var_dump($row);//
        if ($row->type == 1) {
            foreach ($row->sadhanas as $sadhana) {
                $data[$i] = array($row->type, $sadhana->id, $sadhana->text, $sadhana->description, $sadhana->selected, $userid);
                $i +=1;
            }
        } else if ($row->type == 2 || $row->type == 3) {
            foreach ($row->sadhanas as $sadhana) {
                $data[$i] = array($row->type, $sadhana->id, $sadhana->goal, $sadhana->subgoal, $userid);
                $i +=1;
            }
        } else if ($row->type == 4) {
            foreach ($row->sadhanas as $sadhana) {
                $data[$i] = array($row->type, $sadhana->text, $sadhana->description, $userid);
                $i +=1;
            }
        }
    }
    return $data;
}

//sadhanas => common 9 sadhanas
function get_all_common_recommended_practices_mongo($typeid) {
   // $service_url = API_ROOT_URL . '/sadhanas';
    $service_url = API_ROOT_URL . $GLOBALS['SADHANAS'];
    
    error_log('recommended common sadhana list api endpoint ' . $service_url . ' ', 3, DEFAULT_LOG);
    $response = \Httpful\Request::get($service_url)->send();
    error_log($response->raw_body, 3, DEFAULT_LOG);
    $result = $response->body;
    //echo " inside mongo call get all common recommended practices ";
    //var_dump($result);
    $result = (array) $result;
    //var_dump($result);
    $data = "";
    $i = 0;
    foreach ($result as $row) {
        $data[$i] = array($row->text, $row->description,$row->webimg);
        $i += 1;
    }
    return $data;
}

function update_sadhanas_mongodb(&$fields, $userid) {
    $service_url = API_ROOT_URL . $GLOBALS['SADHANA_REGISTER_OR_UPDATE_API'];
    error_log('sadhana_update api endpoint ' . $service_url . ' ', 3, DEFAULT_LOG);
    //Constructing the json object that I want to send. Object encoded in the next step..
    $userSignUp = new stdClass();
    $obj->userid = $userid;
    $obj->fields = $fields;
    $response = \Httpful\Request::post($service_url)
            ->body(json_encode($obj))
            ->sendsJson()
            ->send();
}
?>