<?php
/**
 * -=-<[ Bismillahirrahmanirrahim ]>-=-
 * handles all fingerprint functionalities
 * @authors Dahir Muhammad Dahir
 * @date    2021-03-22 12:03:08
 */

require_once(__DIR__ . "/" . "../vendor/autoload.php");

$client = new Fingerprint\FingerPrintClient("fingerprint_engine:4134", [
    "credentials" => Grpc\ChannelCredentials::createInsecure(),
]);

function enroll_fingerprint($pre_fmd_string_array){
    $enrollment_request = new Fingerprint\EnrollmentRequest();

    $pre_enrolled_fmds = array();

    global $client;
    
    foreach($pre_fmd_string_array as $pre_reg_fmd) {
        $pre_enrollment_fmd = new Fingerprint\PreEnrolledFMD();
        $pre_enrollment_fmd->setBase64PreEnrolledFMD($pre_reg_fmd);
        array_push($pre_enrolled_fmds, $pre_enrollment_fmd);
    }

    $enrollment_request->setFmdCandidates($pre_enrolled_fmds);

    list($enrolled_fmd, $status) = $client->EnrollFingerprint($enrollment_request)->wait();
    
    if ($status->code === Grpc\STATUS_OK) {
        return $enrolled_fmd->getBase64EnrolledFMD();
    }
    else {
        return "enrollment failed" ;
    }
}

function check_duplicate($pre_fmd_string, $enrolled_fmd_string_list){
    global $client;

    $pre_enrolled_fmd = new Fingerprint\PreEnrolledFMD(array("base64PreEnrolledFMD" => $pre_fmd_string));
    $verification_request = new Fingerprint\VerificationRequest(array("targetFMD" => $pre_enrolled_fmd));

    $enrolled_fmds = array();

    foreach($enrolled_fmd_string_list as $hand){
        array_push($enrolled_fmds, new Fingerprint\EnrolledFMD(array("base64EnrolledFMD" => $hand->indexfinger)));
        array_push($enrolled_fmds, new Fingerprint\EnrolledFMD(array("base64EnrolledFMD" => $hand->middlefinger)));
    }

    $verification_request->setFmdCandidates($enrolled_fmds);

    list($response, $status) = $client->CheckDuplicate($verification_request)->wait();
    return $response->getIsDuplicate();

}

function verify_fingerprint($pre_enrolled_fmd_string, $enrolled_fmd_string){
    global $client;

    $pre_enrolled_fmd = new Fingerprint\PreEnrolledFMD();
    $pre_enrolled_fmd->setBase64PreEnrolledFMD($pre_enrolled_fmd_string);

    $enrolled_cand_fmd = new Fingerprint\EnrolledFMD();
    $enrolled_cand_fmd->setBase64EnrolledFMD($enrolled_fmd_string);

    $verification_request = new Fingerprint\VerificationRequest(array("targetFMD" => $pre_enrolled_fmd));
    $verification_request->setFmdCandidates(array($enrolled_cand_fmd));

    list($verification_response, $status) = $client->VerifyFingerprint($verification_request)->wait();

    if ($status->code === Grpc\STATUS_OK) {
        return $verification_response->getMatch();
    }
    else {
        return "verification failed";
    }
}