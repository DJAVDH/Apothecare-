<?php
header('Content-Type: application/json; charset=utf-8');

$model  = 'gemma3:1b';
$systemPath = __DIR__ . '/assets/system_prompt.txt';
$system = file_get_contents($systemPath);

if ($system === false) {
    error_log("System prompt niet gevonden op: {$systemPath}");
    echo json_encode(['response' => 'Systeem prompt niet beschikbaar. Probeer het later opnieuw.']);
    exit;
}

$vraag = trim($_POST['vraag'] ?? '');

if ($vraag === '') {
    echo json_encode(['response' => '']);
    exit;
}

$payload = [
    'model'  => $model,
    'system' => $system,
    'prompt' => $vraag,
    'stream' => false
];

$ch = curl_init('http://localhost:11434/api/generate');
curl_setopt_array($ch, [
    CURLOPT_POST           => true,
    CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
    CURLOPT_POSTFIELDS     => json_encode($payload, JSON_UNESCAPED_UNICODE),
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 10,
]);

$raw = curl_exec($ch);
$err  = curl_error($ch);
$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($raw === false || $err) {
    echo json_encode(['response' => 'Kan geen verbinding maken met het AI-model.']);
    exit;
}

$data = json_decode($raw, true);

if (json_last_error() === JSON_ERROR_NONE && isset($data['response'])) {
    $antwoord = $data['response'];
} else {
    // fallback to raw body
    $antwoord = trim(is_string($raw) ? $raw : json_encode($raw));
}

echo json_encode(['response' => $antwoord], JSON_UNESCAPED_UNICODE);

?>
