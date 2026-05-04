<?php

return [
    'whatsapp' => [
        'booking_submission' => [
            'quotation_request' => 'SOLICITUD DE COTIZACIÓN',
            'new_booking' => 'NUEVA RESERVA DE CITA',
            'greeting' => 'Hola *:tenant*, he generado un nuevo requerimiento a través de SwiftField.',
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
