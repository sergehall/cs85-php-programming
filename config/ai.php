<?php

return [
    'providers' => [
        'lm_studio' => [
            'label' => 'LM Studio',
            'location' => 'Local',
            'base_url' => env('AI_LM_STUDIO_BASE_URL', 'http://127.0.0.1:1234/v1'),
            'api_key' => env('AI_LM_STUDIO_API_KEY', 'lm-studio'),
            'endpoint' => '/chat/completions',
            'connect_timeout' => (int) env('AI_CONNECT_TIMEOUT', 5),
            'timeout' => (int) env('AI_REQUEST_TIMEOUT', 180),
        ],
        'openai' => [
            'label' => 'OpenAI API',
            'location' => 'Online',
            'base_url' => env('OPENAI_API_URL', 'https://api.openai.com/v1'),
            'api_key' => env('OPENAI_API_KEY'),
            'endpoint' => '/chat/completions',
            'connect_timeout' => (int) env('OPENAI_CONNECT_TIMEOUT', 5),
            'timeout' => (int) env('OPENAI_REQUEST_TIMEOUT', 30),
        ],
    ],

    'modes' => [
        'general' => [
            'label' => 'General Tutor',
            'description' => 'Programming questions, explanations, quizzes, and study guidance.',
            'provider' => 'lm_studio',
            'model' => 'qwen/qwen3.6-35b-a3b',
            'model_name' => 'Qwen 3.6 35B A3B',
            'model_profile' => '35B MoE · 4-bit · 20.4 GB',
            'recommended_for' => 'Everyday learning, concept explanations, quizzes, and programming questions.',
            'temperature' => 0.4,
            'prompt' => resource_path('prompts/ai/general.md'),
        ],
        'coding' => [
            'label' => 'Coding Assistant',
            'description' => 'Code review, generation, debugging, and implementation guidance.',
            'provider' => 'lm_studio',
            'model' => 'qwen/qwen3-coder-next',
            'model_name' => 'Qwen 3 Coder Next',
            'model_profile' => '80B · 4-bit · 44.9 GB',
            'recommended_for' => 'Writing code, reviewing changes, debugging errors, and implementation guidance.',
            'temperature' => 0.2,
            'prompt' => resource_path('prompts/ai/coding.md'),
        ],
        'architecture' => [
            'label' => 'Architecture Advisor',
            'description' => 'Architecture, planning, trade-offs, and maintainability reviews.',
            'provider' => 'lm_studio',
            'model' => 'openai/gpt-oss-120b',
            'model_name' => 'OpenAI GPT-OSS 120B',
            'model_profile' => '120B · MXFP4 · 63.4 GB',
            'recommended_for' => 'System design, architecture reviews, technical planning, and trade-off analysis.',
            'temperature' => 0.3,
            'prompt' => resource_path('prompts/ai/architecture.md'),
        ],
        'online' => [
            'label' => 'OpenAI Online',
            'description' => 'Cloud-assisted tutoring and content generation through the OpenAI API.',
            'provider' => 'openai',
            'model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
            'model_name' => 'OpenAI GPT-4o mini',
            'model_profile' => 'Cloud API · Online',
            'recommended_for' => 'Fast online answers, polished drafts, summaries, and a live external API demonstration.',
            'temperature' => 0.4,
            'prompt' => resource_path('prompts/ai/online.md'),
        ],
    ],

    'limits' => [
        'prompt_characters' => (int) env('AI_PROMPT_MAX_CHARACTERS', 8000),
        'history_messages' => (int) env('AI_HISTORY_MESSAGES', 30),
        'max_output_tokens' => (int) env('AI_MAX_OUTPUT_TOKENS', 2048),
        'requests_per_minute' => (int) env('AI_REQUESTS_PER_MINUTE', 10),
    ],

    'tools' => [
        'enabled' => (bool) env('AI_TOOLS_ENABLED', true),
        'max_rounds' => 1,
    ],
];
