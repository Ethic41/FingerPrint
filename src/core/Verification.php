<?php
/*
 * Author: Dahir Muhammad Dahir
 * Date: 26-April-2020 5:47 PM
 * About: I will tell you later
 */


namespace fingerprint;

use DOTNET;

class Verification{
    private $registered_fmd_list;
    private $fmd_to_identify_string;

    function __construct(){
        $this->registered_fmd_list = array();
    }

    function identify(){
        $myFPLib = new dotnet("MyFPLib, Version=1.0.0.0, Culture=neutral, PublicKeyToken=b4f00bc1afff20a5", "MyFPLib.FPLib");

        foreach ($this->registered_fmd_list as $fmd_string){
            $myXFmd = $myFPLib->CreateIdentifyFmdFromBase64String($fmd_string);
            if($myXFmd->Version() !== ""){
                $myFPLib->AddIdentifyXFmd($myXFmd);
            }
        }

        $myXFmdToIdentify = $myFPLib->CreateVerifyFmdFromBase64String($this->fmd_to_identify_string);
        $identifyResult = $myFPLib->Identify($myXFmdToIdentify);

        if($identifyResult->ResultCode() === "DP_SUCCESS"){
            $count = 0;
            foreach ($identifyResult->Indexes() as $index){
                $count += 1;
            }
            if ($count > 0){
                return "success";
            }
            else{
                return "failed no matching";
            }
        }
        else{
            return "failed from outside";
        }
    }

    function setFmdStringToIdentify($fmd_string){
        $this->fmd_to_identify_string = $fmd_string;
    }

    function addRegisteredFmds($fmd_string_array){
        //fmd_string should already be base64 url decoded
        foreach ($fmd_string_array as $fmd_string){
            $this->registered_fmd_list[] = $fmd_string;
        }
    }

    function clearFmdList(){
        $this->registered_fmd_list = array();
    }
}
