<?php

return [
    'whatsapp' => [
        'booking_submission' => [
            'quotation_request' => 'SOLICITUD DE COTIZACIÓN',
            'new_booking' => 'NUEVA RESERVA DE CITA',
            'auto_confirmed_greeting' => '¡Cita Confirmada con éxito! Te esperamos el día :date.',
            'pending_greeting' => 'Hemos recibido tu solicitud, pronto te confirmaremos.',
            'confirmed_header' => '✅ ¡CITA CONFIRMADA!',
            'confirmed_body' => 'Se ha agendado una nueva cita automáticamente en tu agenda.',
            'pending_header' => '⏳ NUEVA SOLICITUD DE CITA',
            'pending_body' => 'Has recibido una solicitud pendiente. Por favor, ingresa al panel para confirmar o rechazar el servicio.',
            'admin_link_label' => '🔗 Ver en el panel administrativo',
            'service' => 'Servicio',
            'date_time' => 'Fecha/Hora',
            'to_confirm' => 'Por confirmar',
            'cost_summary' => 'Resumen de Costos',
            'shipping' => 'Domicilio',
            'total' => 'Total',
            'customer_data' => 'Datos del Cliente',
            'name' => 'Nombre',
            'phone' => 'Teléfono',
            'service_details' => 'Detalles del Servicio',
            'location' => 'Ubicación del Servicio',
            'footer' => '_Enviado desde el portal de reservas de :tenant_',
        ],
        'confirmation' => [
            'message' => '¡Hola :customer! 👋 Soy de :tenant. Te confirmo que tu servicio de :service para el día :date, duración del servicio :duration minutos, ha sido CONFIRMADO. ¡Nos vemos pronto!',
            'location_registered' => 'Ubicación registrada',
        ],
        'reminder' => [
            'message' => 'Hola :customer, te recordamos tu cita de :service hoy a las :time. ¡Estamos listos para atenderte!',
        ],
    ],
    'fields' => [
        '_delivery_mode' => 'Modalidad',
        '_shipping_fee_applied' => 'Costo de domicilio',
        'detalles_del_servicio' => 'Detalles del Servicio',
        'detalles_de_la_solicitud' => 'Detalles de la solicitud',
    ],
    'values' => [
        'local' => 'En el local',
        'domicilio' => 'A domicilio',
        'yes' => 'Sí',
        'no' => 'No',
    ],
];
