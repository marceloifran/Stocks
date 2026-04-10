<?php

namespace App\Services;

use OpenAI;

class QuotationParserService
{
    protected $client;

    public function __construct()
    {
        $apiKey = config('services.openai.key') ?? env('OPENAI_API_KEY');
        if ($apiKey) {
            $this->client = OpenAI::client($apiKey);
        }
    }

    /**
     * Parse raw email text using AI to extract price and delivery time.
     */
    public function parse(string $text): array
    {
        if (!$this->client) {
            // Fallback for missing API key: simulate parsing or return error
            return [
                'price' => null,
                'delivery_time' => 'Error: No OpenAI API Key configured',
            ];
        }

        $prompt = "Extract the following information from this supplier email response as JSON:
        - price (should be a number, ignore currency symbols)
        - delivery_time (as a short string, e.g., '3 days', 'Next week')

        Email Content:
        \"{$text}\"";

        try {
            $response = $this->client->chat()->create([
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a helpful assistant that extracts data into strict JSON format.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'response_format' => ['type' => 'json_object'],
            ]);

            $result = json_decode($response->choices[0]->message->content, true);

            return [
                'price' => $result['price'] ?? null,
                'delivery_time' => $result['delivery_time'] ?? 'Unknown',
            ];

        } catch (\Exception $e) {
            return [
                'price' => null,
                'delivery_time' => 'AI Error: ' . $e->getMessage(),
            ];
        }
    }
}
