const { test } = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');
const source = fs.readFileSync('src/js/custom.js', 'utf8');

function load(extra = {}) {
    const context = vm.createContext({ console, URL, Promise, ...extra });
    vm.runInContext(source, context);
    return context;
}

test('missing vendor SDK does not crash application initialization', () => {
    const context = load();
    assert.match(vm.runInContext('myReader.reader.initializationError', context), /SDK is unavailable/);
});

test('form encoding preserves reserved characters and nested installation URL', () => {
    let request;
    const context = load({
        Fingerprint: { SampleFormat: { Intermediate: 1 }, WebApi: function() {} },
        document: { baseURI: 'http://localhost/FingerPrint/' },
        XMLHttpRequest: function() {
            request = this;
            this.open = (method, url) => { this.url = String(url); };
            this.setRequestHeader = () => {};
            this.send = payload => { this.payload = payload; };
        }
    });
    vm.runInContext(`readyForEnroll = () => true;
        myReader.currentHand = new Hand();
        myReader.currentHand.id = 1;
        myReader.currentHand.index_finger = Array(4).fill('ab+&=');
        myReader.currentHand.middle_finger = Array(4).fill('cd_');
        serverEnroll();`, context);
    assert.equal(request.url, 'http://localhost/FingerPrint/src/core/enroll.php');
    assert.equal(JSON.parse(new URLSearchParams(request.payload).get('data')).index_finger[0], 'ab+&=');
});

test('enrollment without captured samples does not send a request', () => {
    const context = load();
    let message;
    context.showMessage = value => { message = value; };
    vm.runInContext('readyForEnroll = () => true; serverEnroll();', context);
    assert.match(message, /four samples/);
});
