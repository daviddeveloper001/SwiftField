<?php

return [
    'whatsapp' => [
        'booking_submission' => [
            'quotation_request' => 'QUOTATION REQUEST',
            'new_booking' => 'NEW BOOKING RESERVATION',
            'greeting' => 'Hello *:tenant*, I have generated a new request through SwiftField.',
            'service' => 'Service',
            'date_time' => 'Date/Time',
            'to_confirm' => 'To be confirmed',
            'cost_summary' => 'Cost Summary',
            'shipping' => 'Delivery',
            'total' => 'Total',
            'customer_data' => 'Customer Data',
            'name' => 'Name',
            'phone' => 'Phone',
            'service_details' => 'Service Details',
            'location' => 'Service Location',
            'footer' => '_Sent from :tenant booking portal_',
        ],
        'confirmation' => [
            'message' => 'Hello :customer! 👋 This is from :tenant. I confirm that your :service service for :date, duration of :duration minutes, has been CONFIRMED. See you soon!',
            'location_registered' => 'Registered location',
        ],
        'reminder' => [
            'message' => 'Hello :customer, we remind you of your :service appointment today at :time. We are ready to assist you!',
        ],
    ],
    'fields' => [
        '_delivery_mode' => 'Mode',
        '_shipping_fee_applied' => 'Delivery fee',
        'detalles_del_servicio' => 'Service Details',
        'detalles_de_la_solicitud' => 'Request details',
    ],
    'values' => [
        'local' => 'On-site',
        'domicilio' => 'At home',
        'yes' => 'Yes',
        'no' => 'No',
    ],
];
