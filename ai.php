<?php
declare(strict_types=1);
session_start();
header('Content-Type: application/json; charset=utf-8');


//Config
$model        = 'gemma3:1b'; 
$apiUrl       = 'http://localhost:11434/api/generate';
$systemPath   = __DIR__ . '/assets/system_prompt.txt';
$max_history  = 12; 

//Helpers
function respond(array $data, int $status = 200): void {
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}


//System prompt laden
$system = @file_get_contents($systemPath);
if ($system === false) {
    error_log("System prompt niet gevonden op: {$systemPath}");
    respond(['response' => 'Systeem prompt niet beschikbaar. Probeer het later opnieuw.'], 500);
}


//Input ophalen
$vraag = trim($_POST['vraag'] ?? '');



//Conversation history (session)
$history = $_SESSION['chat_history'] ?? [];
if (!is_array($history)) {
    $history = [];
}

// Voeg de nieuwe gebruikersvraag toe en clip op max_history
$history[] = "User: " . $vraag;
if (count($history) > $max_history) {
    $history = array_slice($history, -$max_history);
}


//Prompt opbouwen
$conversation = $system
    . "\n\nConversation:\n"
    . implode("\n", $history)
    . "\nAssistant:";

$payload = [
    'model'  => $model,
    'system' => $system,
    'prompt' => $conversation,
    'stream' => false,
];


//API-call naar lokaal AI-model
$ch = curl_init($apiUrl);
curl_setopt_array($ch, [
    CURLOPT_POST           => true,
    CURLOPT_HTTPHEADER     => ['Content-Type: application/json; charset=utf-8', 'Accept: application/json'],
    CURLOPT_POSTFIELDS     => json_encode($payload, JSON_UNESCAPED_UNICODE),
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 200,
]);

$raw    = curl_exec($ch);
$err    = curl_error($ch);
$status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);


//Foutafhandeling
if ($raw === false || $err) {
    respond(['response' => 'Kan geen verbinding maken met het AI-model.'], 502);
}


//Response parseren
$data = json_decode($raw, true);
if (json_last_error() === JSON_ERROR_NONE && isset($data['response'])) {
    $antwoord = $data['response'];
} else {
    $antwoord = trim(is_string($raw) ? $raw : json_encode($raw));
}


//History bijwerken en teruggeven
$history[] = "Assistant: " . $antwoord;
if (count($history) > $max_history) {
    $history = array_slice($history, -$max_history);
}
$_SESSION['chat_history'] = $history;

// Zend het antwoord als JSON terug naar de frontend.
respond(['response' => $antwoord]);
