<?php
/*
 * Author: Dahir Muhammad Dahir
 * Date: 26-April-2020 12:41 AM
 * About: this file is responsible
 * for all Database queries
 */

namespace fingerprint;
require_once("../core/Database.php");

function setUserFmds($user_id, $index_finger_fmd_string, $middle_finger_fmd_string){
    $myDatabase = new Database();
    $sql_query = "update users set indexfinger=?, middlefinger=? where id=?";
    $param_type = "ssi";
    $param_array = [$index_finger_fmd_string, $middle_finger_fmd_string, $user_id];
    $affected_rows = $myDatabase->update($sql_query, $param_type, $param_array);

    if($affected_rows > 0){
        return "success";
    }
    else{
        return "failed in querydb";
    }
}

function getUserFmds($user_id){
    $myDatabase = new Database();
    $sql_query = "select indexfinger, middlefinger from users where id=?";
    $param_type = "i";
    $param_array = [$user_id];
    $fmds = $myDatabase->select($sql_query, $param_type, $param_array);
    return json_encode($fmds);
}

function getUserDetails($user_id){
    $myDatabase = new Database();
    $sql_query = "select username, fullname from users where id=?";
    $param_type = "i";
    $param_array = [$user_id];
    $user_info = $myDatabase->select($sql_query, $param_type, $param_array);
    return json_encode($user_info);
}

function getAllFmds(){
    $myDatabase = new Database();
    $sql_query = "select indexfinger, middlefinger from users where indexfinger != ''";
    $allFmds = $myDatabase->select($sql_query);
    return json_encode($allFmds);
}