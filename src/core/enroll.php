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
require_once("../core/Verification.php");

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
            /*we use pre-registration fmd because that is
            how we implemented verify dll*/
            if(!isDuplicate($encoder->createValidBase64FMD($index_finger_string_array[0]))){
                $enrolled_index_finger_fmd_string = $encoder->base64UrlEncode($enrolled_index_finger_fmd_string);
                $enrolled_middle_finger_fmd_string = $encoder->base64UrlEncode($enrolled_middle_finger_fmd_string);
                echo setUserFmds($user_id, $enrolled_index_finger_fmd_string, $enrolled_middle_finger_fmd_string);
            }
            else{
                echo "Duplicate not allowed!";
            }
        }
        else{
            echo "Try Again";
        }
    }
    else{
        echo "Please try again";
    }
}
else{
    echo "nothing came in";
}

function isDuplicate($fmd_to_check_string){
    $encoder = new UrlEncode();
    $identifier = new Verification();

    $identifier->setFmdStringToIdentify($fmd_to_check_string);

    $allFmds = json_decode(getAllFmds());

    if (!$allFmds){ // there is nothing here, so nothing to do
        return false;
    }

    $identifier->clearFmdList();

    foreach ($allFmds as $hand){
        $identifier->addRegisteredFmds([$encoder->createValidBase64FMD($hand->indexfinger)]);
        $identifier->addRegisteredFmds([$encoder->createValidBase64FMD($hand->middlefinger)]);
    }

    $identifyResult = $identifier->identify();

    if($identifyResult === "success"){
        return true;
    }
    elseif ($identifyResult === "failed no matching"){
        return false;
    }
    else{
        return true;
    }
}
