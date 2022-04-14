<?php
// GENERATED CODE -- DO NOT EDIT!

// Original file comments:
// 
//
// Bismillahirrahmanirraheem
// Author: Dahir Muhammad Dahir
// Date: 22-01-2021 02:47 PM
// About: I will tell you later
//
//
namespace Fingerprint;

/**
 */
class FingerPrintClient extends \Grpc\BaseStub {

    /**
     * @param string $hostname hostname
     * @param array $opts channel options
     * @param \Grpc\Channel $channel (optional) re-use channel object
     */
    public function __construct($hostname, $opts, $channel = null) {
        parent::__construct($hostname, $opts, $channel);
    }

    /**
     * @param \Fingerprint\EnrollmentRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function EnrollFingerprint(\Fingerprint\EnrollmentRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/fingerprint.FingerPrint/EnrollFingerprint',
        $argument,
        ['\Fingerprint\EnrolledFMD', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Fingerprint\VerificationRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function VerifyFingerprint(\Fingerprint\VerificationRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/fingerprint.FingerPrint/VerifyFingerprint',
        $argument,
        ['\Fingerprint\VerificationResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Fingerprint\VerificationRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function CheckDuplicate(\Fingerprint\VerificationRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/fingerprint.FingerPrint/CheckDuplicate',
        $argument,
        ['\Fingerprint\CheckDuplicateResponse', 'decode'],
        $metadata, $options);
    }

}
