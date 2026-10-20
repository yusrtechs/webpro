<?php
require 'vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

header('Content-Type: application/json');

// Get raw POST input and decode JSON
$input = json_decode(file_get_contents("php://input"), true);

if (!isset($input['client_uuid']) || !isset($input['terminal_id'])) {
    http_response_code(400);
    echo json_encode(["error" => "Missing client_uuid or terminal_id"]);
    exit;
}

$client_uuid = $input['client_uuid'];
$terminal_id = $input['terminal_id'];

try {
    $privateKey = file_get_contents('./1785435990247-private-key.pem'); // Adjust path if needed

    $payload = [
        "data" => [
            "ops" => "auth",
            "client_uuid" => $client_uuid,
            "terminal_id" => $terminal_id
        ]
    ];

    $jwt = JWT::encode($payload, $privateKey, 'RS256');

    // Optionally save to file
    file_put_contents('token.txt', $jwt);

    echo json_encode(["token" => $jwt]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Token generation failed", "details" => $e->getMessage()]);
}
