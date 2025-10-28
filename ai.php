<?php
// Start PHP session zodat we per gebruiker een korte conversatiegeschiedenis
// kunnen bewaren tussen verzoeken. Moet vóór enige output staan.
session_start();

// Zet response Content-Type naar JSON met juiste charset — de frontend
// verwacht JSON terug van dit script.
header('Content-Type: application/json; charset=utf-8');

$model  = 'gemma3:1b'; // model identificatie (lokale / externe modelnaam)

// Laad het system prompt (instructies voor het model) uit een bestand.
// Het system prompt bevat regels over stijl, rol en beperkingen.
$systemPath = __DIR__ . '/assets/system_prompt.txt';
$system = file_get_contents($systemPath);

if ($system === false) {
    // Als het system prompt niet gevonden wordt, log en geef een
    // duidelijke foutmelding terug aan de frontend.
    error_log("System prompt niet gevonden op: {$systemPath}");
    echo json_encode(['response' => 'Systeem prompt niet beschikbaar. Probeer het later opnieuw.']);
    exit;
}

// Haal de gebruiker-vraag op uit POST data en trim whitespace.
$vraag = trim($_POST['vraag'] ?? '');

// Als er geen vraag is gestuurd, stuur een lege response terug en stop.
if ($vraag === '') {
    echo json_encode(['response' => '']);
    exit;
}

// --- conversation memory (session) ---
// We bewaren een korte geschiedenis in de PHP session zodat het model
// context heeft van voorgaande vragen/antwoorden. Houd dit kort ivm
// token-limiet van het model.
$max_history = 12; // bewaar de laatste N regels (aanpasbaar)
$history = $_SESSION['chat_history'] ?? [];

// Voeg de nieuwe gebruikersvraag toe aan de lokale history array.
// We voegen het nu toe zodat de prompt ook de huidige vraag bevat.
$history[] = "User: " . $vraag;
if (count($history) > $max_history) {
    // Bewaar alleen de laatste N regels om de prompt kort te houden.
    $history = array_slice($history, -$max_history);
}

// Bouw de volledige prompt die we naar het model sturen. Dit bestaat uit
// het system prompt (rol/instructies) gevolgd door de conversatiegeschiedenis
// en een 'Assistant:'-token waar het model zijn antwoord kan aanvullen.
$conversation = $system . "\n\nConversation:\n" . implode("\n", $history) . "\nAssistant:";

$payload = [
    'model'  => $model,
    // Optioneel veld met system prompt (afhankelijk van API kan dit
    // redundant zijn, maar het is handig om expliciet mee te sturen).
    'system' => $system,
    // We sturen hier de samengestelde conversatie als prompt zodat het
    // model de volledige context heeft.
    'prompt' => $conversation,
    'stream' => false
];


// Roep het lokale AI-model aan via HTTP (cURL). Pas URL en headers
// aan indien je een andere API gebruikt.
$ch = curl_init('http://localhost:11434/api/generate');
curl_setopt_array($ch, [
    CURLOPT_POST           => true,
    CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
    CURLOPT_POSTFIELDS     => json_encode($payload, JSON_UNESCAPED_UNICODE),
    CURLOPT_RETURNTRANSFER => true,
    // Timeout verhogen tijdens testen; productie kan korter.
    CURLOPT_TIMEOUT        => 200,
]);

$raw = curl_exec($ch);
$err  = curl_error($ch);
$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Controleer of de request zelf goed ging
if ($raw === false || $err) {
    echo json_encode(['response' => 'Kan geen verbinding maken met het AI-model.']);
    exit;
}

// Probeer JSON te decoderen. API's verschillen — controleer welke
// sleutel het antwoord bevat ('response', 'text', 'output', etc.).
$data = json_decode($raw, true);

if (json_last_error() === JSON_ERROR_NONE && isset($data['response'])) {
    // Veel lokale wrappers gebruiken 'response' als veldnaam.
    $antwoord = $data['response'];
} else {
    // Fallback: stuur de ruwe body terug. Handig tijdens debugging.
    $antwoord = trim(is_string($raw) ? $raw : json_encode($raw));
}

// Sla het assistant-antwoord op in de sessie-geschiedenis zodat het
// bij volgende requests weer met de prompt meegestuurd kan worden.
$history[] = "Assistant: " . $antwoord;
if (count($history) > $max_history) {
    $history = array_slice($history, -$max_history);
}
$_SESSION['chat_history'] = $history;

// Zend het antwoord als JSON terug naar de frontend.
echo json_encode(['response' => $antwoord], JSON_UNESCAPED_UNICODE);

?>
