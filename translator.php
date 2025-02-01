<?php

header("Content-Type: application/json");


if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["error" => "Method not allowed"]);
    exit;
}


$input = json_decode(file_get_contents("php://input"), true);

if (!isset($input["text"]) || !isset($input["targetLanguage"])) {
    http_response_code(400);
    echo json_encode(["error" => "Missing 'text' or 'targetLanguage' in payload."]);
    exit;
}

$text = $input["text"];
$targetLanguage = $input["targetLanguage"];


$api_key = "";

$messages = [
    [
        "role"    => "system",
        "content" => "You are a helpful translation assistant."
    ],
    [
        "role"    => "user",
        "content" => "Translate the following text to $targetLanguage:\n\n$text"
    ]
];


$data = [
    "model"       => "gpt-4o-mini",
    "messages"    => $messages,
    "max_tokens"  => 1000,
    "temperature" => 0.3
];


$ch = curl_init("https://api.openai.com/v1/chat/completions");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Bearer $api_key"
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

$response = curl_exec($ch);
if (curl_errno($ch)) {
    http_response_code(500);
    echo json_encode(["error" => "cURL error: " . curl_error($ch)]);
    exit;
}
curl_close($ch);


echo $response;
?>
