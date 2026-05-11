<?php
function getGroqSettings($pdo) {
    $stmt = $pdo->query("SELECT setting_key, setting_value FROM settings WHERE setting_key IN ('groq_api_key', 'groq_model')");
    return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
}

function callGroqAI($pdo, $messages, $systemPrompt = "") {
    $settings = getGroqSettings($pdo);
    $apiKey = $settings['groq_api_key'] ?? '';
    $model = $settings['groq_model'] ?? 'llama-3.3-70b-versatile';

    // Auto-map deprecated models if still saved in DB
    $deprecatedModels = ['llama3-70b-8192', 'llama3-8b-8192', 'mixtral-8x7b-32768', 'gemma-7b-it', 'gemma2-9b-it'];
    if (in_array($model, $deprecatedModels)) {
        $model = 'llama-3.3-70b-versatile'; // Just default to the best versatile model
    }

    if (empty($apiKey)) {
        return ['error' => true, 'message' => 'API Key o\'rnatilmagan! Admin paneldan sozlang.'];
    }

    $formattedMessages = [];
    if (!empty($systemPrompt)) {
        $formattedMessages[] = [
            'role' => 'system',
            'content' => $systemPrompt
        ];
    }
    
    foreach ($messages as $msg) {
        $formattedMessages[] = $msg;
    }

    $data = [
        'model' => $model,
        'messages' => $formattedMessages,
        'temperature' => 0.7,
        'max_tokens' => 1500
    ];

    $ch = curl_init('https://api.groq.com/openai/v1/chat/completions');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $apiKey,
        'Content-Type: application/json'
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200) {
        $err = json_decode($response, true);
        return ['error' => true, 'message' => 'Groq xatosi: ' . ($err['error']['message'] ?? 'Noma\'lum xato')];
    }

    $result = json_decode($response, true);
    if (isset($result['choices'][0]['message']['content'])) {
        return ['error' => false, 'content' => $result['choices'][0]['message']['content']];
    }

    return ['error' => true, 'message' => 'Javobni o\'qishda xatolik yuz berdi.'];
}

// Function to get itinerary / guide based on user text
function generateAITourGuide($pdo, $userQuery, $language = 'uz') {
    // We could fetch places/hotels to provide as context to AI, but for now we give it a simple prompt
    $system = "Siz O'zbekiston bo'ylab sayohat bo'yicha 'Silk Road Explorer' saytining aqlli gidisiz. Foydalanuvchiga xushmuomala, professional va ilhomlantiruvchi tilda javob bering. O'zbekistonning tarixiy joylari, mehmonxonalari va restoranlarini tavsiya qiling. Javobingizni Markdown formatida, chiroyli ro'yxatlar bilan yozing. Muloqot tili: " . $language;
    
    $messages = [
        ['role' => 'user', 'content' => $userQuery]
    ];

    return callGroqAI($pdo, $messages, $system);
}

// Admin helper function: generate description
function generateDescriptionWithAI($pdo, $entityName, $entityType, $briefDetails) {
    $system = "Siz 'Silk Road Explorer' dabdabali turizm portali uchun yuqori darajadagi ta'riflar (description) yozuvchi kopiraytersiz. Juda qisqa, quruq ma'lumotlarni jozibador, tarixiy ruh va boy so'z boyligi bilan bezab bering. Faqat bitta paragraf hajmida matn yozing.";
    $userMsg = "Quyidagi $entityType haqida matn yarating. Nomi: $entityName. Qo'shimcha ma'lumot: $briefDetails. (O'zbek tilida).";

    $messages = [
        ['role' => 'user', 'content' => $userMsg]
    ];

    return callGroqAI($pdo, $messages, $system);
}

// Admin helper function: auto-translate
function translateWithAI($pdo, $text, $targetLang) {
    $langName = $targetLang == 'ru' ? 'Rus' : ($targetLang == 'en' ? 'Ingliz' : 'O\'zbek');
    $system = "Siz professional tarjimonsiz. Turizm, tarix va mehmonxona ta'riflarini o'ziga xos uslubni yo'qotmagan holda tarjima qilasiz. Faqat tarjimani qaytaring, ortiqcha izohlar kerak emas.";
    $userMsg = "Quyidagi matnni $langName tiliga tarjima qiling:\n\n$text";

    $messages = [
        ['role' => 'user', 'content' => $userMsg]
    ];

    return callGroqAI($pdo, $messages, $system);
}
