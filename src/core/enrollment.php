<?php
/**
 * Create Enrollment for new user
 * @authors Dahir Muhammad Dahir (dahirmuhammad3@gmail.com)
 * @date    2020-04-18 08:39:00
 */

namespace fingerprint;

use DOTNET;
use http\Url;

require("../core/UrlEncode.php");

class Enrollment {

    private $fmd_string_list;
    private $enrolled_fingers;
    
    function __construct(){
        $this->fmd_string_list = array();
        $this->enrolled_fingers = array();
    }

    function createEnrollment(){
        $myFPLib = new dotnet("MyFPLib, Version=1.0.0.0, Culture=neutral, PublicKeyToken=b4f00bc1afff20a5", "MyFPLib.FPLib");

        foreach ($this->fmd_string_list as $fmd_string){
            $myXFmd = $myFPLib->CreateFmdFromBase64String($fmd_string);
            if($myXFmd->Version() !== ""){
                $myFPLib->AddXFmd($myXFmd);
            }
            else{
                return "failed";
            }
        }

        $myXFmdResult = $myFPLib->CreateEnrollment();
        if($myXFmdResult->ResultCode() === "DP_SUCCESS"){
            return $myFPLib->CreateBase64FmdStringFromXFmd($myXFmdResult->Fmd());
        }
        else{
            return "failed";
        }
    }

    function addFMDString($fmd_string_array){
        $encoder = new UrlEncode();
        foreach ($fmd_string_array as $fmd_string){
            $this->fmd_string_list[] = $encoder->createValidBase64FMD($fmd_string);
        }
    }

    function clearFMDs(){
        $this->fmd_string_list = array();
    }
}
