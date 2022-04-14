<?php
/**
 * -=-<[ Bismillahirrahmanirrahim ]>-=-
 * @authors Dahir Muhammad Dahir
 * @date    2021-02-21 14:35:40
 * 
 */

/* this is just a sample script to show how to
interface with the generated client code,
your implementation ultimately depends on your needs
 */

require_once(__DIR__ . "/" . "./vendor/autoload.php");

$fmd1 = "";
$fmd2 = "";
$fmd3 = "";
$fmd4 = "";

$reg_fmd1 = "";
$reg_fmd2 = "";

$pre_reg_fmd_list = array($fmd1, $fmd2, $fmd3, $fmd4);
$reg_fmd_list = array($reg_fmd1, $reg_fmd2);


$client = new Fingerprint\FingerPrintClient("localhost:4134", [
    "credentials" => Grpc\ChannelCredentials::createInsecure(),
]);


function enroll_fingerprint() {
    $enrollment_request = new Fingerprint\EnrollmentRequest();

    $pre_enrolled_fmds = array();

    global $pre_reg_fmd_list;
    global $client;
    
    foreach($pre_reg_fmd_list as $pre_reg_fmd) {
        $pre_enrollment_fmd = new Fingerprint\PreEnrolledFMD();
        $pre_enrollment_fmd->setBase64PreEnrolledFMD($pre_reg_fmd);
        array_push($pre_enrolled_fmds, $pre_enrollment_fmd);
    }

    $enrollment_request->setFmdCandidates($pre_enrolled_fmds);

    list($enrolled_fmd, $status) = $client->EnrollFingerprint($enrollment_request)->wait();
    
    if ($status->code === Grpc\STATUS_OK) {
        echo $enrolled_fmd->getBase64EnrolledFMD();
    }
    else {
        echo "Error: " . $status->code . " " . $status->details ;
    }
}

function verify_fingerprint() {
    $pre_enrolled_fmd = new Fingerprint\PreEnrolledFMD();
    global $fmd1;
    global $reg_fmd1;
    global $client;
    $pre_enrolled_fmd->setBase64PreEnrolledFMD($fmd1);

    $enrolled_cand_fmd = new Fingerprint\EnrolledFMD();
    $enrolled_cand_fmd->setBase64EnrolledFMD($reg_fmd1);

    $verification_request = new Fingerprint\VerificationRequest(array("targetFMD" => $pre_enrolled_fmd));
    //$verification_request->setTargetFMD($pre_enrolled_fmd);
    $verification_request->setFmdCandidates(array($enrolled_cand_fmd));

    list($verification_response, $status) = $client->VerifyFingerprint($verification_request)->wait();

    if ($status->code === Grpc\STATUS_OK) {
        echo $verification_response->getMatch();
    }
    else {
        echo "Error: " . $status->code . " " . $status->details ;
    }
}

function check_duplicate($client) {
    global $fmd2;
    global $reg_fmd_list;
    $pre_enrolled_fmd = new Fingerprint\PreEnrolledFMD(array("base64PreEnrolledFMD" => $fmd2));
    $verification_request = new Fingerprint\VerificationRequest(array("targetFMD" => $pre_enrolled_fmd));

    $enrolled_fmds = array();

    foreach($reg_fmd_list as $reg_fmd) {
        array_push($enrolled_fmds, new Fingerprint\EnrolledFMD(array("base64EnrolledFMD" => $reg_fmd)));
    }

    $verification_request->setFmdCandidates($enrolled_fmds);

    list($response, $status) = $client->CheckDuplicate($verification_request)->wait();
    echo $response->getIsDuplicate();
}

//enroll_fingerprint();
//verify_fingerprint();
check_duplicate($client);
