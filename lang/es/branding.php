<?php

return [
    'title' => 'Personalización del Negocio',
    'navigation_label' => 'Personalización',
    'sections' => [
        'visual_identity' => [
            'title' => 'Identidad Visual',
            'description' => 'Configura los colores y el logotipo corporativo.',
            'logo' => 'Logotipo',
            'primary_color' => 'Color Primario',
            'secondary_color' => 'Color Secundario',
        ],
        'communication' => [
            'title' => 'Comunicación',
            'description' => 'Datos de contacto para integraciones (WhatsApp).',
            'phone' => 'Número de WhatsApp',
        ],
        'location' => [
            'title' => 'Ubicación de Sede',
            'description' => 'Configura la dirección física y coordenadas para el mapa.',
            'address' => 'Dirección',
            'address_placeholder' => 'Busca tu dirección o muévete en el mapa...',
            'latitude' => 'Latitud',
            'longitude' => 'Longitud',
            'map_help' => 'Usa la barra de búsqueda para encontrar tu dirección o mueve el pin directamente en el mapa.',
        ],
    ],
    'notifications' => [
        'updated' => [
            'title' => 'Identidad actualizada',
            'body' => 'Los cambios se han aplicado a todo el sistema.',
        ],
    ],
];
