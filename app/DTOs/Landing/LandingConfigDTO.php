<?php

namespace App\DTOs\Landing;

readonly class LandingConfigDTO
{
    public function __construct(
        public string $theme_id,
        public string $primary_color,
        public string $secondary_color,
        public array $sections,
        public array $meta = []
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            theme_id: $data['theme_id'] ?? 'default',
            primary_color: $data['primary_color'] ?? '#3b82f6',
            secondary_color: $data['secondary_color'] ?? '#1e40af',
            sections: collect($data['sections'] ?? self::defaultSections())
                ->sortBy('order')
                ->values()
                ->toArray(),
            meta: $data['meta'] ?? []
        );
    }

    private static function defaultSections(): array
    {
        return [
            ['type' => 'hero', 'order' => 1, 'content' => ['title' => 'Bienvenido']],
            ['type' => 'services', 'order' => 2, 'content' => []],
            ['type' => 'contact', 'order' => 3, 'content' => []],
        ];
    }
}
