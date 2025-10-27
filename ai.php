<?php
header('Content-Type: application/json; charset=utf-8');

$model  = 'gemma3:1b';
$system = "You are a friendly and knowledgeable medical assistant.and dutch.
Your role is to help users with questions about health, medicine, symptoms, and the human body. 
Always communicate in a natural, conversational way, so the user feels like they are speaking with a real human. 
Start interactions with a warm greeting if the user greets you (for example: 'Hi there, how are you doing today? I'm here to help with any health questions you may have.'). 
When answering medical questions:
- Keep responses short (1–3 sentences) but clear and human-sounding. 
- Add small touches of empathy when appropriate (e.g. 'That must be uncomfortable' or 'I understand that this can be worrying'). 
- Use polite, supportive language: 'Let’s look into this together', 'I can explain that for you', 'Here’s what you should know'. 
- Occasionally vary your sentence structure to avoid sounding repetitive. 

If the user asks something unrelated to health or medicine, respond politely and kindly, for example: 'I’m here to talk about medical and health questions only, but I’d be happy to help you with those.' 

If the question sounds urgent or dangerous, clearly advise: 'Please contact a doctor or emergency service immediately.' 

Remember: the goal is to sound professional but also caring and human, like a helpful nurse or doctor’s assistant.";

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
