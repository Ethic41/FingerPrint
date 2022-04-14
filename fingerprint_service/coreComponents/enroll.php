<?php
/**
 * this file uses the enrollment class to
 * enroll users
 * @authors Dahir Muhammad Dahir (dahirmuhammad3@gmail.com)
 * @date    2020-04-18 14:28:39
 */

require_once("../coreComponents/basicRequirements.php");

if (!empty($_POST["data"])) {
    $user_data = json_decode($_POST["data"]);

    $index_finger_string_array = $user_data->index_finger;
    $middle_finger_string_array = $user_data->middle_finger;

    $enrolled_index_finger = enroll_fingerprint($index_finger_string_array);
    $enrolled_middle_finger = enroll_fingerprint($middle_finger_string_array);

    if ($enrolled_index_finger !== "enrollment failed" && $enrolled_middle_finger !== "enrollment failed"){
        # todo: return the enrolled fmds instead
        $output = ["enrolled_index_finger"=>$enrolled_index_finger, "enrolled_middle_finger"=>$enrolled_middle_finger];
        echo json_encode($output);
    }
    else {
        echo json_encode("enrollment failed!");
    }
} else {
    echo json_encode("error! no data provided in post request");
}

