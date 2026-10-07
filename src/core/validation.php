<?php
/** Pure validation helpers; no device, database or SDK required. */
function fingerprint_sample_valid($value): bool
{
    return is_string($value) && strlen($value) >= 2 && strlen($value) <= 8192
        && preg_match('/^[A-Za-z0-9_-]+={0,2}$/D', $value) === 1;
}

function fingerprint_payload($raw, $enrollment = false): object
{
    if (!is_string($raw) || strlen($raw) > 100000) {
        throw new InvalidArgumentException('Missing or oversized fingerprint payload.');
    }
    $data = json_decode($raw);
    if (!is_object($data) || !isset($data->id)
        || filter_var($data->id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) === false) {
        throw new InvalidArgumentException('A positive integer user ID is required.');
    }
    foreach ($enrollment ? ['index_finger', 'middle_finger'] : ['index_finger'] as $field) {
        $samples = $data->$field ?? null;
        if (!is_array($samples) || count($samples) !== ($enrollment ? 4 : 1)) {
            throw new InvalidArgumentException('Incorrect fingerprint sample count.');
        }
        foreach ($samples as $sample) {
            if (!fingerprint_sample_valid($sample)) {
                throw new InvalidArgumentException('Invalid fingerprint sample encoding.');
            }
        }
    }
    return $data;
}

function fingerprint_read_request($enrollment = false): object
{
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
        header('Allow: POST');
        http_response_code(405);
        exit('POST required.');
    }
    try {
        return fingerprint_payload($_POST['data'] ?? null, $enrollment);
    } catch (InvalidArgumentException $exception) {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode(['ok' => false, 'error' => $exception->getMessage()]);
        exit;
    }
}
