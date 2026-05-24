<?php

return [
    'title' => 'Business Customization',
    'navigation_label' => 'Customization',
    'sections' => [
        'visual_identity' => [
            'title' => 'Visual Identity',
            'description' => 'Configure your corporate colors and logo.',
            'logo' => 'Logo',
            'primary_color' => 'Primary Color',
            'secondary_color' => 'Secondary Color',
        ],
        'communication' => [
            'title' => 'Communication',
            'description' => 'Contact details for integrations (WhatsApp).',
            'phone' => 'WhatsApp Number',
        ],
        'location' => [
            'title' => 'Headquarters Location',
            'description' => 'Configure the physical address and coordinates for the map.',
            'address' => 'Address',
            'address_placeholder' => 'Search your address or move on the map...',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'map_help' => 'Use the search bar to find your address or move the pin directly on the map.',
        ],
    ],
    'notifications' => [
        'updated' => [
            'title' => 'Identity updated',
            'body' => 'Changes have been applied throughout the system.',
        ],
    ],
];
