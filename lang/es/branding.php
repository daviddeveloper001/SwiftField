<?php

return [
    'title' => 'Personalización del Negocio',
    'navigation_label' => 'Personalización',
    'sections' => [
        'visual_identity' => [
            'title' => 'Identidad Visual',
            'name' => 'Nombre',
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
    'forms' => [
        'customer' => [
            'name' => 'Nombre del Cliente',
            'phone' => 'Teléfono',
            'email' => 'Correo Electrónico',
        ],
        'booking' => [
            'quote_details_label' => 'Detalles de la solicitud',
            'quote_details_placeholder' => 'Escribe aquí los detalles...',
            'quote_required_error' => 'Por favor, especifica los detalles de tu solicitud.',
            'written_address_label' => 'Dirección Escrita',
            'written_address_placeholder' => 'Ej. Manzana X Casa Y',
            'references_label' => 'Indicaciones adicionales / Referencias',
            'references_placeholder' => 'Ej. Frente al granero, apartamento 302',
            'delivery_mode_label' => 'Modalidad de Prestación',
            'delivery_mode_local' => 'Local (En el establecimiento)',
            'delivery_mode_domicilio' => 'A Domicilio',
            'map_label' => 'Ubicación en el Mapa',
            'written_address_required' => 'La dirección escrita es obligatoria.',
            'gps_required' => 'Debes marcar tu ubicación en el mapa.',
        ],
    ],
    'labels' => [
        'customer' => 'Cliente',
        'customers' => 'Clientes',
    ],
    'navigation_groups' => [
        'business_management' => 'Gestión Operativa',
        'online_presence' => 'Presencia Online',
        'settings' => 'Configuración',
    ],
    'notifications' => [
        'updated' => [
            'title' => 'Identidad actualizada',
            'body' => 'Los cambios se han aplicado a todo el sistema.',
        ],
    ],
];
