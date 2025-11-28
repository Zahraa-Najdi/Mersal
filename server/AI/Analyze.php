<?php

function summarizeChat(): void
{
    include("config.php");
    require_once __DIR__ . "/../connection/connection.php";
    require_once __DIR__ . "/prompts.php";
    require_once __DIR__ . "/../services/MessageService.php";

    $messageService = new MessageService($connection);

    $input = json_decode(file_get_contents('php://input'), true);
    if (!isset($input['chat_id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing chat_id']);
        return;
    }
    $chatId = (int) $input['chat_id'];

    $messages = $messageService->getMessageByChatIdAndStatus($chatId, 'delivered');
    $prompt = createSummaryAIPrompt($messages);

    $headers = [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $GLOBALS['apiKey']
    ];

    $data = [
        'model' => 'gpt-4-turbo',
        'messages' => [['role' => 'user', 'content' => $prompt]],
        'max_tokens' => 1000,
        'temperature' => 0.0
    ];

    $ch = curl_init($GLOBALS['url']);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($data)
    ]);

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        http_response_code(500);
        echo json_encode(['error' => 'cURL error: ' . curl_error($ch)]);
        curl_close($ch);
        return;
    }
    curl_close($ch);

    $responseData = json_decode($response, true);
    if (isset($responseData['error'])) {
        http_response_code(500);
        echo json_encode(['error' => 'OpenAI error: ' . ($responseData['error']['message'] ?? 'Unknown')]);
        return;
    }

    if (!isset($responseData['choices'][0]['message']['content'])) {
        http_response_code(500);
        echo json_encode(['error' => 'Unexpected response from OpenAI']);
        return;
    }

    $AIResponse = trim($responseData['choices'][0]['message']['content']);
    if (str_starts_with($AIResponse, '```json')) {
        $AIResponse = substr($AIResponse, 7);
    }
    if (str_ends_with($AIResponse, '```')) {
        $AIResponse = substr($AIResponse, 0, -3);
    }
    $AIResponse = trim($AIResponse);

    $data = json_decode($AIResponse, true);
    if (
        !is_array($data) ||
        !isset($data['summary']) ||
        !is_string($data['summary']) ||
        json_last_error() !== JSON_ERROR_NONE
    ) {
        http_response_code(500);
        echo json_encode(['error' => 'Invalid AI response format']);
        return;
    }

    header('Content-Type: application/json');
    echo json_encode(['summary' => $data['summary']]);
}

summarizeChat();