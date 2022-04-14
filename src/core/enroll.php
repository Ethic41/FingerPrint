<?php
/**
 * this file uses the enrollment class to
 * enroll users
 * @authors Dahir Muhammad Dahir (dahirmuhammad3@gmail.com)
 * @date    2020-04-18 14:28:39
 */


namespace fingerprint;

require("../core/querydb.php");
require_once("../core/helpers/helpers.php");


if(!empty($_POST["data"])){
    $user_data = json_decode($_POST["data"]);
    $user_id = $user_data->id;
    $index_finger_string_array = $user_data->index_finger;
    $middle_finger_string_array = $user_data->middle_finger;

    $pre_reg_fmd_array = [
        "index_finger" => $index_finger_string_array,
        "middle_finger" => $middle_finger_string_array
    ];

    // this check for duplicate is not necessary, only required if you want to
    // avoid duplicate enrollment of the same finger, also you might have to improve it
    // a bit to make it more robust, considering this is just a proof of concept and we
    // are only checking a single finger
    if (isDuplicate($index_finger_string_array[0]) || isDuplicate($middle_finger_string_array[0])) {
        echo "Duplicate not allowed!";
    }
    else{
        $json_response = enroll_fingerprint($pre_reg_fmd_array);
        $response = json_decode($json_response);
        if ($response !== "enrollment failed"){
            $enrolled_index_finger_fmd_string = $response->enrolled_index_finger;
            $enrolled_middle_finger_fmd_string = $response->enrolled_middle_finger;
            echo setUserFmds($user_id, $enrolled_index_finger_fmd_string, $enrolled_middle_finger_fmd_string);
        }
        else{
            echo "$response";
        }
    }
}
else{
    echo "post request with 'data' field required";
}

function isDuplicate($fmd_to_check_string){

    $allFmds = json_decode(getAllFmds());

    if (!$allFmds){ // there is nothing here, so nothing to do
        return false;
    }

    $enrolled_hand_array = $allFmds;

    $json_response = is_duplicate_fingerprint($fmd_to_check_string, $enrolled_hand_array);
    $response = json_decode($json_response);

    if($response){
        return true;
    }
    else{
        return false;
    }
}
