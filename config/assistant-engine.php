<?php

return [
    'api-url' => env('ASSISTANT_ENGINE_API', 'https://api.assistant-engine.com/v1/'),
    'api-key' => env('ASSISTANT_ENGINE_TOKEN'),
    'llm-provider-key' => env('OPENAI_API_KEY'),

    "chat" => [
        "render-assistant-message-as-markdown" => true,

        "disable-assistant-icon" => false,
        "disable-user-input" => false,

        "open-ai-recorder" => [
            "activate" => true,
            "open-ai-key" => env('OPENAI_API_KEY'),
            "language" => "en"
        ]
    ],
    'filament-assistant' => [
        'button' => [
            'show' => true,
            'options' => [
                'label' => 'Assistant',
                'size' => \Filament\Support\Enums\ActionSize::ExtraLarge,
                'color' => \Filament\Support\Colors\Color::Sky,
                'icon' => 'heroicon-o-chat-bubble-bottom-center-text'
            ]
        ],

        'conversation-option' => [
            'assistant-key' => env('ASSISTANT_ENGINE_ASSISTANT_KEY'),
            'conversation-resolver' => \AssistantEngine\Filament\Resolvers\ConversationOptionResolver::class,
            'context-resolver' => \AssistantEngine\Filament\Resolvers\ContextResolver::class
        ],

        'sidebar' => [
            'render' => true,
            'width' => 500,
            'show-without-trigger' => false
        ],
    ]

];
