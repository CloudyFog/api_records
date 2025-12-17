<?php

$JWT_SECRET = "SUPER_SECRET_KEY_123";

function base64url_encode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function base64url_decode($data) {
    return base64_decode(strtr($data, '-_', '+/'));
}

function generate_jwt($payload, $exp = 3600) {
    global $JWT_SECRET;

    $header = ['alg' => 'HS256', 'typ' => 'JWT'];
    $payload['exp'] = time() + $exp;

    $base64Header = base64url_encode(json_encode($header));
    $base64Payload = base64url_encode(json_encode($payload));

    $signature = hash_hmac(
        'sha256',
        "$base64Header.$base64Payload",
        $JWT_SECRET,
        true
    );

    return "$base64Header.$base64Payload." . base64url_encode($signature);
}

function verify_jwt($jwt) {
    global $JWT_SECRET;

    $parts = explode('.', $jwt);
    if (count($parts) !== 3) return false;

    [$header, $payload, $signature] = $parts;

    $valid_signature = base64url_encode(
        hash_hmac('sha256', "$header.$payload", $JWT_SECRET, true)
    );

    $data = json_decode(base64url_decode($payload), true);

    if ($signature !== $valid_signature) return false;
    if ($data['exp'] < time()) return false;

    return $data;
}