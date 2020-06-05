<?php
/*
 * Author: Dahir Muhammad Dahir
 * Date: 26-April-2020 5:44 PM
 * About: identification and verification
 * will be carried out in this file
 */

namespace fingerprint;

require_once("../core/UrlEncode.php");
require_once("../core/Verification.php");
require_once("../core/querydb.php");

$encoder = new UrlEncode();
$identifier = new Verification();

if(!empty($_POST["data"])) {
    $user_data = json_decode($_POST["data"]);
    $user_id = $user_data->id;
    //this is not necessarily index_finger it could be
    //any finger we wish to identify
    $index_finger_string = $encoder->createValidBase64FMD($user_data->index_finger[0]); //url base64 decoding

    $hand_data = json_decode(getUserFmds($user_id));
    $registered_index_finger = $encoder->createValidBase64FMD($hand_data[0]->indexfinger);
    $registered_middle_finger = $encoder->createValidBase64FMD($hand_data[0]->middlefinger);

    $identifier->clearFmdList();
    $identifier->addRegisteredFmds([$registered_index_finger, $registered_middle_finger]);
    $identifier->setFmdStringToIdentify($index_finger_string);

    $identifyResult = $identifier->identify();
    if($identifyResult === "success"){
        echo getUserDetails($user_id);
    }
    else{
        echo json_encode($identifyResult);
    }
}

else{
    echo "nothing found!";
}
