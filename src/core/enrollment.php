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

    /*
     * Responsible for creating enrollment, it requires that fmd_strings have
     * been loaded into the fmd_string_list*/
    function createEnrollment(){
        //instantiating FPLib class
        $myFPLib = new dotnet("MyFPLib, Version=1.0.0.0, Culture=neutral, PublicKeyToken=b4f00bc1afff20a5", "MyFPLib.FPLib");

        /*
         * fmd_strings in fmd_string_list are converted to XFmd objects
         * which get loaded into myFPLib object
         * */
        foreach ($this->fmd_string_list as $fmd_string){
            $myXFmd = $myFPLib->CreateFmdFromBase64String($fmd_string);
            if($myXFmd->Version() !== ""){
                $myFPLib->AddXFmd($myXFmd);
            }
            else{
                return "failed";
            }
        }

        /*
         * using the loaded XFmd objects in myFPLib object to
         * create enrollment, if enrollment is successful, a Base64 encoded
         * fmd_string is returned for storage in database
         * */
        $myXFmdResult = $myFPLib->CreateEnrollment();
        if($myXFmdResult->ResultCode() === "DP_SUCCESS"){
            return $myFPLib->CreateBase64FmdStringFromXFmd($myXFmdResult->Fmd());
        }
        else{
            return "failed";
        }
    }
    /*
     * fmd_strings in fmd_string_arrays are Base64UrlDecoded i.e converted
     * to valid Base64 string and then loaded into fmd_string_list
     * */
    function addFMDString($fmd_string_array){
        $encoder = new UrlEncode();
        foreach ($fmd_string_array as $fmd_string){
            $this->fmd_string_list[] = $encoder->createValidBase64FMD($fmd_string);
        }
    }

    /*
     * if there are existing fmd string in the object
     * remove them
     * */
    function clearFMDs(){
        $this->fmd_string_list = array();
    }
}
