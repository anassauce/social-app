<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiAIService
{
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
        $this->baseUrl = 'https://generativelanguage.googleapis.com/v1/models/gemini-1.5-flash:generateContent';
    }

    public function generateContent($prompt, $options = [])
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '?key=' . $this->apiKey, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
                'generationConfig' => array_merge([
                    'temperature' => 0.7,
                    'topK' => 1,
                    'topP' => 1,
                    'maxOutputTokens' => 2048,
                    'stopSequences' => []
                ], $options)
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['candidates'][0]['content']['parts'][0]['text'] ?? 'No content generated';
            }

            Log::error('Gemini API error: ' . $response->body());
            throw new Exception('Failed to generate content from Gemini API');

        } catch (Exception $e) {
            Log::error('Gemini AI Service error: ' . $e->getMessage());
            throw new Exception('AI content generation failed: ' . $e->getMessage());
        }
    }

    public function generatePostContent($topic, $style = 'professional', $length = 'medium')
    {
        $lengthMap = [
            'short' => 'Write a brief post (50-100 words)',
            'medium' => 'Write a moderate length post (150-300 words)',
            'long' => 'Write a detailed post (400-600 words)'
        ];

        $styleMap = [
            'professional' => 'in a professional tone',
            'casual' => 'in a casual, friendly tone',
            'motivational' => 'in a motivational, inspiring tone',
            'humorous' => 'in a humorous, funny tone',
            'educational' => 'in an educational, informative tone'
        ];

        $prompt = sprintf(
            "%s about '%s' %s. Make it engaging and suitable for a social media platform. Include relevant hashtags at the end.",
            $lengthMap[$length] ?? $lengthMap['medium'],
            $topic,
            $styleMap[$style] ?? $styleMap['professional']
        );

        return $this->generateContent($prompt);
    }

    public function improveContent($content, $improvements = [])
    {
        $improvementTypes = [
            'grammar' => 'Fix grammar and spelling errors',
            'clarity' => 'Improve clarity and readability',
            'engagement' => 'Make it more engaging and interesting',
            'professional' => 'Make it more professional',
            'concise' => 'Make it more concise and to the point'
        ];

        $requestedImprovements = array_intersect_key($improvementTypes, array_flip($improvements));
        $improvementText = implode(', ', $requestedImprovements);

        $prompt = sprintf(
            "Please improve the following text by focusing on: %s\n\nOriginal text:\n%s\n\nProvide only the improved version without explanations.",
            $improvementText ?: 'overall quality and readability',
            $content
        );

        return $this->generateContent($prompt);
    }
}