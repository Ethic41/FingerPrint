<?php
require __DIR__ . '/../src/core/validation.php';
function check($condition, $message) {
    if (!$condition) throw new RuntimeException($message);
}
$valid = ['id' => 1, 'index_finger' => array_fill(0, 4, 'YWJj'), 'middle_finger' => array_fill(0, 4, 'ZGVm')];
check(fingerprint_payload(json_encode($valid), true)->id === 1, 'valid enrollment');
foreach ([null, '{', '{}', json_encode(['id' => -1]), str_repeat('a', 100001)] as $raw) {
    try { fingerprint_payload($raw, true); throw new RuntimeException('invalid payload accepted'); }
    catch (InvalidArgumentException $expected) {}
}
check(!fingerprint_sample_valid('ab<script>'), 'unsafe encoding');
check(!fingerprint_sample_valid(str_repeat('a', 8193)), 'oversized sample');
check(fingerprint_sample_valid('a_b-'), 'URL-safe encoding');
$verify = ['id' => '1', 'index_finger' => ['YWJj']];
check(fingerprint_payload(json_encode($verify))->id === '1', 'verification payload');
echo "PHP validation tests passed\n";
