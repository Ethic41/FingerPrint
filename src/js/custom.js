/**
 * Custom implementation for the FingerPrint
 * Reader and other JS functions
 * @authors Dahir Muhammad Dahir (dahirmuhammad3@gmail.com)
 * @date    2020-04-14 17:06:41
 * @version 1.0.0
 */


let currentFormat = Fingerprint.SampleFormat.Intermediate;

let FingerprintSdkTest = (function () {
    function FingerprintSdkTest() {
        let _instance = this;
        this.operationToRestart = null;
        this.acquisitionStarted = false;
        // instatiating the fingerprint sdk here
        this.sdk = new Fingerprint.WebApi;
        this.sdk.onDeviceConnected = function (e) {
            // Detects if the device is connected for which acquisition started
            showMessage("Scan your finger");
        };
        this.sdk.onDeviceDisconnected = function (e) {
            // Detects if device gets disconnected - provides deviceUid of disconnected device
            showMessage("Device disconnected");
        };
        this.sdk.onCommunicationFailed = function (e) {
            // Detects if there is a failure in communicating with U.R.U web SDK
            showMessage("Communication Failed")
        };
        this.sdk.onSamplesAcquired = function (s) {
            // Sample acquired event triggers this function
            storeSample(s);
        };
        this.sdk.onQualityReported = function (e) {
            // Quality of sample acquired - Function triggered on every sample acquired
            //document.getElementById("qualityInputBox").value = Fingerprint.QualityCode[(e.quality)];
        }

    }

    // this is were finger print capture takes place
    FingerprintSdkTest.prototype.startCapture = function () {
        if (this.acquisitionStarted) // Monitoring if already started capturing
            return;
        let _instance = this;
        showMessage("");
        this.operationToRestart = this.startCapture;
        this.sdk.startAcquisition(currentFormat, "").then(function () {
            _instance.acquisitionStarted = true;

            //Disabling start once started
            //disableEnableStartStop();

        }, function (error) {
            showMessage(error.message);
        });
    };
    
    FingerprintSdkTest.prototype.stopCapture = function () {
        if (!this.acquisitionStarted) //Monitor if already stopped capturing
            return;
        let _instance = this;
        showMessage("");
        this.sdk.stopAcquisition().then(function () {
            _instance.acquisitionStarted = false;

            //Disabling stop once stopped
            //disableEnableStartStop();

        }, function (error) {
            showMessage(error.message);
        });
    };

    FingerprintSdkTest.prototype.getInfo = function () {
        let _instance = this;
        return this.sdk.enumerateDevices();
    };

    FingerprintSdkTest.prototype.getDeviceInfoWithID = function (uid) {
        let _instance = this;
        return  this.sdk.getDeviceInfo(uid);
    };
    
    return FingerprintSdkTest;
})();


class Reader{
    constructor(){
        this.reader = new FingerprintSdkTest();
        this.selectFieldID = null;
        this.currentStatusField = null;
        /**
         * @type {Hand}
         */
        this.currentHand = null;
    }

    readerSelectField(selectFieldID){
        this.selectFieldID = selectFieldID;
    }

    setStausField(statusFieldID){
        this.currentStatusField = statusFieldID;
    }

    displayReader(){
        let readers = this.reader.getInfo();  // grab available readers here
        let id = this.selectFieldID;
        let selectField = document.getElementById(id);
        selectField.innerHTML = `<option>Select Fingerprint Reader</option>`;
        readers.then(function(availableReaders){  // when promise is fulfilled
            if(availableReaders.length > 0){
                showMessage("");
                for(let reader of availableReaders){
                    selectField.innerHTML += `<option value="${reader}" selected>${reader}</option>`;
                }
            }
            else{
                showMessage("Please Connect the Fingerprint Reader");
            }
        })
    }
}

class Hand{
    constructor(){
        this.id = 0;
        this.index_finger = [];
        this.middle_finger = [];
    }

    addIndexFingerSample(sample){
        this.index_finger.push(sample);
    }

    addMiddleFingerSample(sample){
        this.middle_finger.push(sample);
    }

    generateFullHand(){
        let id = this.id;
        let index_finger = this.index_finger;
        let middle_finger = this.middle_finger;
        return JSON.stringify({id, index_finger, middle_finger});
    }
}

let myReader = new Reader();

function beginEnrollment(){
    setReaderSelectField("enrollReaderSelect");
    myReader.setStausField("enrollmentStatusField");
}

function beginIdentification(){
    setReaderSelectField("verifyReaderSelect");
    myReader.setStausField("verifyIdentityStatusField");
}

function setReaderSelectField(fieldName){
    myReader.readerSelectField(fieldName);
    myReader.displayReader();
}

function showMessage(message, message_type="error"){
    let types = new Map();
    types.set("success", "text-success");
    types.set("error", "text-danger");
    let statusFieldID = myReader.currentStatusField;
    if(statusFieldID){
        let statusField = document.getElementById(statusFieldID);
        statusField.innerHTML = `<p class="my-text7 my-pri-color my-3 ${types.get(message_type)} font-weight-bold">${message}</p>`;
    }
}

function beginCapture(){
    myReader.currentHand = new Hand();
    myReader.reader.startCapture();
    showNextNotEnorlledItem();
}

function clearCapture(){
    clearInputs();
    clearPrints();
    clearHand();
    myReader.reader.stopCapture();
}

function clearInputs(){
    document.getElementById("userID").value = "";
    document.getElementById("userIDVerify").value = "";
    let id = myReader.selectFieldID;
    let selectField = document.getElementById(id);
    selectField.innerHTML = `<option>Select Fingerprint Reader</option>`;
}

function clearPrints(){
    let indexFingers = document.getElementById("indexFingers");
    let middleFingers = document.getElementById("middleFingers");
    let verifyFinger = document.getElementById("verificationFinger");

    if (indexFingers){
        for(let indexfingerElement of indexFingers.children){
            indexfingerElement.innerHTML = `<span class="icon icon-indexfinger-not-enrolled" title="not_enrolled"></span>`;
        }
    }

    if (middleFingers){
        for(let middlefingerElement of middleFingers.children){
            middlefingerElement.innerHTML = `<span class="icon icon-middlefinger-not-enrolled" title="not_enrolled"></span>`;
        }
    }

    if (verifyFinger){
        for(let finger of verifyFinger.children){
            finger.innerHTML = `<span class="icon icon-indexfinger-not-enrolled" title="not_enrolled"></span>`;
        }
    }
}

function clearHand(){
    myReader.currentHand = null;
}

function showSampleCaptured(){
    let nextElementID = getNextNotEnrolledID();
    let markup = null;
    if(nextElementID.startsWith("index")){
        markup = `<span class="icon icon-indexfinger-enrolled" title="enrolled"></span>`;
    }

    if(nextElementID.startsWith("middle")){
        markup = `<span class="icon icon-middlefinger-enrolled" title="enrolled"></span>`;
    }

    if(nextElementID !== "" && markup){
        let nextElement = document.getElementById(nextElementID);
        nextElement.innerHTML = markup;
    }
}

function showNextNotEnorlledItem(){
    let nextElementID = getNextNotEnrolledID();
    let markup = null;
    if(nextElementID.startsWith("index")){
        markup = `<span class="icon capture-indexfinger" title="not_enrolled"></span>`;
    }

    if(nextElementID.startsWith("middle")){
        markup = `<span class="icon capture-middlefinger" title="not_enrolled"></span>`;
    }

    if(nextElementID !== "" && markup){
        let nextElement = document.getElementById(nextElementID);
        nextElement.innerHTML = markup;
    }
}

/**
 * @returns {string}
 */
function getNextNotEnrolledID(){
    let indexFingers = document.getElementById("indexFingers");
    let middleFingers = document.getElementById("middleFingers");

    let indexFingerElement = findElementNotEnrolled(indexFingers);
    let middleFingerElement = findElementNotEnrolled(middleFingers);

    if (indexFingerElement !== null){
        return indexFingerElement.id;
    }

    if (middleFingerElement !== null){
        return middleFingerElement.id;
    }

    return "";
}

/**
 * 
 * @param {Element} element
 * @returns {Element}
 */
function findElementNotEnrolled(element){
    if (element){
        for(let fingerElement of element.children){
            if(fingerElement.firstElementChild.title === "not_enrolled"){
                return fingerElement;
            }
        }
    }

    return null;
}

function storeSample(sample){
    addNewSample(sample);

}

function addNewSample(sample){
    let samples = JSON.parse(sample.samples);
    console.log(samples[0].Data);
    let sampleData = Fingerprint.b64UrlTo64(samples[0].Data);
    console.log(sampleData);

    let nextElementID = getNextNotEnrolledID();

    if(nextElementID.startsWith("index")){
        myReader.currentHand.addIndexFingerSample(sampleData);
        showSampleCaptured();
        showNextNotEnorlledItem();
        return;
    }

    if(nextElementID.startsWith("middle")){
        myReader.currentHand.addMiddleFingerSample(sampleData);
        showSampleCaptured();
        showNextNotEnorlledItem();
        return;
    }
}