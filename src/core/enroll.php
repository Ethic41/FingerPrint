<?php
/**
 * this file uses the enrollment class to
 * enroll users
 * @authors Dahir Muhammad Dahir (dahirmuhammad3@gmail.com)
 * @date    2020-04-18 14:28:39
 */


namespace fingerprint;

require("../core/enrollment.php");
require_once("../core/UrlEncode.php");
require("../core/querydb.php");

$enroller = new Enrollment();
$encoder = new UrlEncode();

if(!empty($_POST["data"])){
    $user_data = json_decode($_POST["data"]);
    $user_id = $user_data->id;
    $index_finger_string_array = $user_data->index_finger;
    $middle_finger_string_array = $user_data->middle_finger;

    $enroller->addFMDString($index_finger_string_array);
    $enrolled_index_finger_fmd_string = $enroller->createEnrollment();
    if(strlen($enrolled_index_finger_fmd_string) > 20){
        $enroller->clearFMDs();
        $enroller->addFMDString($middle_finger_string_array);
        $enrolled_middle_finger_fmd_string = $enroller->createEnrollment();
        if(strlen($enrolled_middle_finger_fmd_string) > 20){
            $enrolled_index_finger_fmd_string = $encoder->base64UrlEncode($enrolled_index_finger_fmd_string);
            $enrolled_middle_finger_fmd_string = $encoder->base64UrlEncode($enrolled_middle_finger_fmd_string);
            echo setUserFmds($user_id, $enrolled_index_finger_fmd_string, $enrolled_middle_finger_fmd_string);
        }
        else{
            echo "enrollment failed. here";
        }
    }
    else{
        echo "enrollment failed. try again";
    }
}
else{
    echo "nothing came in";
}
