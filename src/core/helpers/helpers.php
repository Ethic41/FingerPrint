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

$fmd1 = "AOg4Acgp43NcwEE381mKK5BcZ2Y7ctONm0Jc7NcXz9lnfhPVuSXHWIbzKq4SzIUW5xpWAeiR7rlHJnq_cbcLOwjHsCA3_qoSB93T0o33GpD6n-nd5QFdNJCa4ALPvZaYw9OqN_0GgGtpBA2ou8_8vRRZa7-ieUQEHFeHwR6j1p2i9H9vgNv_jZfGjWsAv42I3H7ttMOzFUxBcnM7-8pFy5P07ssRmGST6Vv4s07IAuxoXQRcdDEl7Q3qfw5umad5XnxE8W0dcY73mYYF-FjFy5gERZnGDCw5xQNap4dmZ44cA19RA49kTy8vV7YJaGXNHpThrkvh3E5Ke5-mRcNo4JnxK4SikeASJHENYp-N9ZcwbFweC_uXm_dDN6dCX25MkZc-d_QFqh0FRpojI2Y3MgYozcOtff3tTSpgfW8A";
$fmd2 = "";
$fmd3 = "";
$fmd4 = "";

$reg_fmd1 = "APhjAcgq43NcwEE3CatxsPwUVZK_bzt-zdZq3yK9CpBRKMPBCcAQLGWKcWI4tK17Q_DXCy5WcLZ7eSUj76CAR1yJKqhOwXRn3IPsjoALkn1QB5dWUyqrbcHgdmpirMyCWmWQ037ndKBdFMT36jG4zMIbxICVxSw1uhOpjM34UDTSoMVSKrYwf-za0oCyP2X_zkSdMairC1hDwD9Pl6smqEe2fCx9lcZPA17CZ5GztQRqIsZ10i9VnoEvhiACOwZmCNqZa-4wkF60IA0CrYF6ax1mrxwLdm4B85s4mCcoy-397sqBaDSnUoV0zU8yMuUFz_OWNdP3Ryd6fmD1RDRmu8ba5jrj5eGYtrA7AfyJ4iwpLfiQalgivn5pQhfXEzNVj0HiFGBrAIvWYuKxUOlMkQZMlA3Z6xEGqvjB6V8ZDjiVkiku4mhhF_ASPMIk12-cROP9KAOFA0naU8MPs8xX6r06bEJu7ARvAPh_Acgq43NcwEE3CatxcIEUVZKyW9cyj89GZz2KJjtc_o_To3Xy-ZZ8bJ3zgaKRehydGerSXhDf-EhEynEB2cb0p0e9j1MNkcS1RHxhPxAGOTl4mNGJBSQLoW0o7foMEohZ_qjQDIOJAcWCx-nbmTUOj2CyEDBWG45sfAJm5suCmDngGtnbdNzdV85QziZC8AjiGu-MwMqug1rbqpNATV8jRcLlyc4ejAhOAAQV3N93kV2uOU34zLS0QKNe3aUm99fDKWIHgbEtmXJ_IY_7ZAOEHh-hC7h2mVjv_QIlkKjl98pM4RFeRBOWCP_uq2ObfX3F_2ztXYJ-7mIv7NBkRUqu9-005xe6Mj00Ufaj1A14gZ4UOLTjWTmnkR2FOXrG2jwaXNgVXNfzFzkK80l5n1bqTaC0aIC_mDrc0ensdimiD7if4PXMA74XBCeibV8BQaXpQR-IGVPgPDOptCSU-1Zeff-fNX3l60R8YgxONb1WVonr9BUZn_bDDoxiLPq_iu4nbwD4aQHIKuNzXMBBNwmrcXD9FFWSPrSZqIGVP99UwBGalXL3B4PJtmTKvWd8Z7c76I5CEAb7euTr7feRUFI5TDf49S4Gq1f6DpWaZRiRk4FTUWfctOG1TE2m-i8t3OoJyzNSDoilDRc-5vVveK6AAXWim9t8t5KOJu_TU0eOzR1g9_-Ee0IDw3dkvG4R6gLEhV_hs_6xQh6NVM75WyTeL4-Hrc2LfEXpabdB6Uli3FYol8O76RHYgwOHewtnPjAIlJBL20kkxusD6D_rlJ99aq0RcChzTeN3m3h1dxzUnCnS79HXVhq5Hhxd4LY4SbnNhGzkLb0CPPRYSNyvgTjCkiPqb5sLcpQ1eV4e0_RegnZRjF5IqFG6jDShAARor6uJqZ2-IjPvW5XocHR2BbOBXi1GbcOe8JlAy8QamrMYOqdpYJeEvYra5tOLzVqALGD1jILnop-UPFUCuFWUvS_aMX7_ZZLR3eioj9tvqXJsbwDofgHIKuNzXMBBNwmrcbDmFFWSpXRUAaSLUGKXwwa7O7YaHBVKp5POEq-awrRrMiySlPRtkhr1xhooo-CMw-IcrbE44g0N0ODEZ9asC2m6tGJZ8GoeXncPsUcFTqCon2xGxOSI4UAneuCt9lvpbpMYQlKSlD7wEz90e46dJbBSehZD4rtgJ2qIRblUgwpMcIxrWtyOPZRBigaBclMjjTWY464pRyklosEajCFKwBZBlFLXzG_B4KWD3UoWmFCXjJ9k03xsBpzHE2WHYJNLUmGI05WTzqphaKWyxansOjVM68ssNjGmFhpkW_JWl8-mgQ9n6H1NodObHzd9dsQgMABmDCxF3tt-1x7rXAMDCGuB8F3HGvYIVjTpqLrbSEn7Bc8ePTK25XcSjMRhPA9faCw4Ncg1aNlpicGkbSV_onSvfzJFXjlcT3Kuke4CkZ4jbcppgE4pdA-riVXUSJWoQQqzcc83IU9iKgyzKVDoqcCipikqPrqGVpFjWC83VGg3VocIbwAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA";
$reg_fmd2 = "";


// to test uncomment this lines
// test_enroll();
// test_verify();
//test_duplicate();