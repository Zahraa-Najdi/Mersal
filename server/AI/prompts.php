<?php
function createSummaryAIPrompt(array $messages): string
{
    $text = '';
    foreach ($messages as $m) {
        $sender = ($m['sender_name'] ?? 'User') . ': ';
        $text .= $sender . ($m['message'] ?? '') . PHP_EOL;
    }

    return 'Summarise the following chat in two or three short sentences.
            Return ONLY a JSON object, no extra text:
            {"summary":"your summary here"}
            Chat:
            ' . $text;
}