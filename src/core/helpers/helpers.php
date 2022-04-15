<?php
/**
 * -=-<[ Bismillahirrahmanirrahim ]>-=-
 * Helper functions to access fingerprint service
 * @authors Dahir Muhammad Dahir (dahirmuhammad3@gmail.com)
 * @date    2022-04-11 13:52:59
 * @version 1.0.0
 */
require_once(__DIR__ . '/../../../configs.php');
require_once('Requests/src/Autoload.php');
WpOrg\Requests\Autoload::register();

/**
 * $pre_registered_fmd_array has the format
 * [ "index_finger" => [array of 4 pre_reg fmd of index_finger],
 * "middle_finger" => [array of 4 pre_reg fmd of middle_finger] ]
 * 
 * returns a json encoded array of two enrolled_fmds for the index and 
 * middle finger respectively, when successful. 
 * (these are the fmds you should store in your database)
 * otherwise returns 'enrollment failed' as string
 */
function enroll_fingerprint($pre_registered_fmd_array){
    $host = getenv('FP_CLIENT_SERVICE_HOST');
    $enrollment_url = "$host/coreComponents/enroll.php";

    $data = ["data" => json_encode($pre_registered_fmd_array)];

    $response = make_request($enrollment_url, $data);

    return $response;
}

/**
 * $pre_registered_fmd_string is just a string
 * of the fmd you want to verify i.e coming directly
 * from the device
 * 
 * $enrolled_fingers_array has the format
 * [ "index_finger" => enrolled_index_finger_fmd,
 *   "middle_finger" => enrolled_middle_finger_fmd ]
 * 
 * enrolled_index_finger and enrolled_middle_finger are
 * string of fmd coming from your database or any persistent
 * storage you've stored the the enrollment fmd
*/
function verify_fingerprint($pre_registered_fmd_string, $enrolled_fingers_array){
    $host = getenv('FP_CLIENT_SERVICE_HOST');
    $verify_url = "$host/coreComponents/verify.php";
    
    $data = [
        "data" => json_encode([
            "pre_enrolled_finger_data" => $pre_registered_fmd_string,
            "enrolled_index_finger_data" => $enrolled_fingers_array['index_finger'],
            "enrolled_middle_finger_data" => $enrolled_fingers_array['middle_finger']
        ])
    ];

    $response = make_request($verify_url, $data);

    return $response;
}

/**
 * $pre_registered_fmd_string is just a string
 * of the fmd you want to check is duplicate
 * i.e coming directly from the device
 * 
 * $enrolled_hands_array is an array of hands
 * a hand is composed of index_finger and middle_finger
 * fmd string
 * 
 * $enrolled_hand_array has the format
 * [ ["indexfinger" => enrolled_index_finger_fmd1, "middlefinger" => enrolled_middle_finger_fmd1], 
 *   ["indexfinger" => enrolled_index_finger_fmd2, "middlefinger" => enrolled_middle_finger_fmd2],
 *   ["indexfinger" => enrolled_index_finger_fmd3, "middlefinger" => enrolled_middle_finger_fmd3],...
 *   ["indexfinger" => enrolled_index_finger_fmdN, "middlefinger" => enrolled_middle_finger_fmdN]
 * ]
 * 
 * NOTE: enrolled_hand_array should not be more than 3000 hands for optimal result,
 * anything beyond that range you may observe the engine being a bit slow,
 * of course with a lil bit of tinkering and parallelization you can make the
 * engine limitless :) ... but that's for another project :)
 * 
 * the function returns true if is there is duplicate and false otherwise
 */
function is_duplicate_fingerprint($pre_registered_fmd_string, $enrolled_hands_array){
    $host = getenv('FP_CLIENT_SERVICE_HOST');
    $is_duplicate_url = "$host/coreComponents/is_duplicate.php";

    $data = [
        "data" => json_encode(
            [
                "pre_enrolled_finger_data" => $pre_registered_fmd_string,
                "enrolled_hands_list" => $enrolled_hands_array
            ]
        )
    ];

    $response = make_request($is_duplicate_url, $data);

    return $response;
}

function make_request($url, $data){
    
    $headers = array('Content-Type' => 'application/x-www-form-urlencoded');
    
    $response = WpOrg\Requests\Requests::post($url, $headers, $data);
    return $response->body;
}


function test_enroll(){
    global $fmd1;
    global $fmd2;

    $pre_reg_fmd_i = $fmd1;
    $pre_reg_fmd_m = $fmd2;

    // am repeating the same pre_reg fmds cos I am testing in real life
    // each should be a different sample from the device for the same finger
    $index_finger = [$pre_reg_fmd_i, $pre_reg_fmd_i, $pre_reg_fmd_i, $pre_reg_fmd_i];
    $middle_finger = [$pre_reg_fmd_m, $pre_reg_fmd_m, $pre_reg_fmd_m, $pre_reg_fmd_m];

    $pre_reg_fmd_array = ["index_finger" => $index_finger, "middle_finger" => $middle_finger];
    echo enroll_fingerprint($pre_reg_fmd_array);
}

function test_verify(){
    global $fmd1;
    global $reg_fmd1;
    global $reg_fmd2;

    $pre_reg_fmd = $fmd1;
    
    $enrolled_fingers = [
        "index_finger" => $reg_fmd1,
        "middle_finger" => $reg_fmd2
    ];

    echo verify_fingerprint($pre_reg_fmd, $enrolled_fingers);
}

function test_duplicate(){
    global $fmd1;
    global $reg_fmd1;

    $pre_reg_fmd = $fmd1;
    $hand1 = [
        "indexfinger" => $reg_fmd1,
        "middlefinger" => $reg_fmd1
    ];

    $hand2 = [
        "indexfinger" => $reg_fmd1,
        "middlefinger" => $reg_fmd1
    ];

    $enrolled_hand_array = [$hand1, $hand2];

    echo is_duplicate_fingerprint($pre_reg_fmd, $enrolled_hand_array);
}

// to test obtain the fmds from
// your device and insert into the variables

$fmd1 = "";
$fmd2 = "";
$fmd3 = "";
$fmd4 = "";

$reg_fmd1 = "";
$reg_fmd2 = "";


// to test uncomment this lines
// test_enroll();
// test_verify();
//test_duplicate();