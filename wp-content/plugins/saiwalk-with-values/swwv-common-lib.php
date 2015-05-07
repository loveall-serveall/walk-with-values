<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function get_practice_sign_up_form_fields() {
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

function sanitize_practice_sign_up_form_fields(&$fields) {
    if (!isset($fields['common_list'])) {
        $fields['common_list'] = '';
    }
    $fields['healthybody_goal'] = isset($fields['healthybody_goal']) ? sanitize_text_field($_POST['healthybody_goal']) : '';
    $fields['healthymind_goal'] = isset($fields['healthymind_goal']) ? sanitize_text_field($_POST['healthymind_goal']) : '';
    //$fields['healthybody_subgoals'] = isset($fields['healthybody_subgoals']) ? sanitize_text_field($_POST['healthybody_subgoals']) : '';
    //$fields['healthymind_subgoals'] = isset($fields['healthymind_subgoals']) ? sanitize_text_field($_POST['healthymind_subgoals']) : '';
}

function validate_practice_sign_up_form_fields(&$fields, &$errors) {
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

function display_form_errors(&$errors) {
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

//Will be used for user defined/added common practices since user has flexibility
//to update/add/remove the custom practices.
function is_item_checked(&$chkname, $value) {
    foreach ($chkname as $chkval) {
        if ($chkval == $value) {
            return true;
        }
    }
    return false;
}
?>