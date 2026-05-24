<?php

return [
    'whatsapp' => [
        'booking_submission' => [
            'quotation_request' => 'QUOTATION REQUEST',
            'new_booking' => 'NEW BOOKING RESERVATION',
            'auto_confirmed_greeting' => 'Appointment confirmed successfully! We look forward to seeing you on :date.',
            'pending_greeting' => 'We have received your request, we will confirm soon.',
            'confirmed_header' => '✅ APPOINTMENT CONFIRMED!',
            'confirmed_body' => 'A new appointment has been automatically scheduled in your calendar.',
            'pending_header' => '⏳ NEW APPOINTMENT REQUEST',
            'pending_body' => 'You have received a pending request. Please log in to the panel to confirm or reject the service.',
            'admin_link_label' => '🔗 View in administrative panel',
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
        'location_block' => [
            'title' => '📍 Meeting place',
            'address' => '🏠 Address',
            'how_to_get' => '🗺️ How to get there',
            'your_home' => 'Your Home',
            'unknown_address' => 'Address to be confirmed',
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
