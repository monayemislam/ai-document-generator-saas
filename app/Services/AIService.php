<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIService
{
    protected $baseUrl;
    protected $timeout;

    public function __construct()
    {
        $this->baseUrl = env('LM_STUDIO_URL', 'http://localhost:1234');
        $this->timeout = 60;
    }

    public function generateDocument($type, $parameters)
    {
        try {
            $prompt = $this->buildPrompt($type, $parameters);

            $response = Http::timeout($this->timeout)
                ->post($this->baseUrl . '/v1/chat/completions', [
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => $prompt
                        ]
                    ],
                    'temperature' => 0.7,
                    'max_tokens' => 2000,
                    'model' => 'mistral-7b-instruct-v0.2'
                ]);

            if ($response->successful()) {
                return $response->json()['choices'][0]['message']['content'] ?? null;
            }

            Log::error('AI Service Error', [
                'response' => $response->json(),
                'status' => $response->status()
            ]);

            return null;

        } catch (\Exception $e) {
            Log::error('AI Service Exception', [
                'message' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    protected function buildPrompt($type, $parameters)
    {
        $templates = [
            'proposal' => <<<EOT
                Create a professional business proposal with these details:

                CLIENT INFORMATION:
                - Client Name: {$parameters['client_name']}
                - Project Name: {$parameters['project_name']}
                - Budget: {$parameters['budget']}
                - Timeline: {$parameters['timeline']}
                - Scope: {$parameters['scope']}

                Structure it with:
                1. Executive Summary
                2. Project Overview
                3. Scope of Work
                4. Timeline and Milestones
                5. Budget Breakdown
                6. Terms and Conditions
                EOT,

            'invoice' => <<<EOT
                Create a professional invoice with these details:

                BILLING DETAILS:
                - Client: {$parameters['client_name']}
                - Project: {$parameters['project_name']}
                - Amount: {$parameters['budget']}
                - Due Date: {$parameters['timeline']}

                Include:
                1. Invoice Number
                2. Company Details
                3. Client Details
                4. Itemized Services
                5. Payment Terms
                6. Total Amount Due
                EOT,
        ];

        return $templates[$type] ?? '';
    }
}
