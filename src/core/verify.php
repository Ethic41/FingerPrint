<?php
/*
 * Author: Dahir Muhammad Dahir
 * Date: 26-April-2020 5:44 PM
 * About: identification and verification
 * will be carried out in this file
 */

namespace fingerprint;

require_once(__DIR__ . "/helpers/helpers.php");
require_once(__DIR__ . "/querydb.php");
require_once(__DIR__ . '/validation.php');
$validated_data = fingerprint_read_request();

if(!empty($_POST["data"])) {
    $user_data = $validated_data;
    $user_id = $user_data->id;
    //this is not necessarily index_finger it could be
    //any finger we wish to identify
    $pre_reg_fmd_string = $user_data->index_finger[0];

    $hand_data = json_decode(getUserFmds($user_id));
    if (!is_array($hand_data) || !$hand_data
        || !fingerprint_sample_valid($hand_data[0]->indexfinger ?? null)
        || !fingerprint_sample_valid($hand_data[0]->middlefinger ?? null)) {
        http_response_code(404);
        echo json_encode(['ok' => false, 'error' => 'User has no complete enrollment.']);
        exit;
    }

    $enrolled_fingers = [
        "index_finger" => $hand_data[0]->indexfinger,
        "middle_finger" => $hand_data[0]->middlefinger
    ];

    $json_response = verify_fingerprint($pre_reg_fmd_string, $enrolled_fingers);
    $response = json_decode($json_response);

    if($response === "match"){
        echo getUserDetails($user_id);
    }
    else{
        echo json_encode("failed");
    }
}

else{
    echo "post request with 'data' field required";
}
