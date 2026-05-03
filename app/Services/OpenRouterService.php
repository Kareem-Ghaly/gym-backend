<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class OpenRouterService
{
    protected $apiKey;
    protected $baseUrl = 'https://openrouter.ai/api/v1/chat/completions';

    public function __construct()
    {
        $this->apiKey = env('OPENAI_API_KEY');
    }

    public function askAi($userMessage)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type'  => 'application/json',
            'HTTP-Referer'  => env('APP_URL', 'http://localhost'),
            'X-Title'       => 'Gym App Project',
        ])
        ->withoutVerifying()
        ->post($this->baseUrl, [
            'model' => 'openai/gpt-4o-mini',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are a professional gym coach. Respond ONLY in valid JSON format. The JSON should have: "title", "exercises" (array), "nutrition" (array), and "advice" (array). Use Arabic language for values.'
                ],
                ['role' => 'user', 'content' => $userMessage],
            ],
            'response_format' => ['type' => 'json_object']
        ]);

        if ($response->failed()) {
            throw new \Exception('OpenRouter API Error: ' . json_encode($response->json()));
        }

        return $response->json()['choices'][0]['message']['content'];
    }
}