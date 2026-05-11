<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Booking;
use App\Traits\FormatCustomValues;

class WhatsAppNotificationService
{
    use FormatCustomValues;

    /**
     * Sanitiza el número de teléfono para enlaces de WhatsApp.
     */
    private function sanitizePhone(?string $phone): string
    {
        if (!$phone) return '';
        // Elimina espacios, guiones, signos '+' y cualquier otro caracter no numérico
        return preg_replace('/[^0-9]/', '', $phone);
    }

    /**
     * Generate the WhatsApp URL for the inbound message (Client to Owner).
     * Used by the booking form to send the request to the proprietor.
     *
     * @param Booking $booking
     * @return string
     */
    public function getBookingSubmissionUrl(Booking $booking): string
    {
        // Require eager-loaded relations to prevent N+1 issues
        $booking->loadMissing(['tenant', 'service', 'customer']);

        $tenant = $booking->tenant;
        $service = $booking->service;
        $customer = $booking->customer;

        // Recuperar configuración de WhatsApp desde el modelo Tenant (que usa settings)
        $config = $tenant->whatsapp_config;
        $phone = $this->sanitizePhone($config['phone'] ?? null);
        
        // Fallback al número de soporte si no hay número de tenant
        if (empty($phone)) {
            $phone = $this->sanitizePhone(config('app.support_whatsapp'));
        }

        $isQuote = $booking->scheduled_at === null;
        $isAutoConfirm = $service->auto_confirm ?? false;

        $header = $isAutoConfirm 
            ? __('notifications.whatsapp.booking_submission.confirmed_header')
            : __('notifications.whatsapp.booking_submission.pending_header');

        $greeting = $isAutoConfirm
            ? __('notifications.whatsapp.booking_submission.confirmed_body')
            : __('notifications.whatsapp.booking_submission.pending_body');

        $typeLabel = $isQuote 
            ? __('notifications.whatsapp.booking_submission.quotation_request') 
            : $header;

        $scheduledAt = !$isQuote 
            ? $booking->scheduled_at->format('d M Y - h:i A') 
            : __('notifications.whatsapp.booking_submission.to_confirm');

        $deliveryMode = $booking->custom_values['_delivery_mode'] ?? 'local';
        $translatedMode = __("notifications.values.{$deliveryMode}");
        $tag = strtoupper($translatedMode);
        
        $message = "✨ *[{$tag}] {$typeLabel}* ✨\n\n";
        $message .= "{$greeting}\n\n";
        
        $message .= "🛠️ *" . __('notifications.whatsapp.booking_submission.service') . ":* {$service->name}\n";
        $message .= "📅 *" . __('notifications.whatsapp.booking_submission.date_time') . ":* {$scheduledAt}\n\n";

        $price = (float) $service->price;
        $shippingFee = (float) ($booking->custom_values['_shipping_fee_applied'] ?? 0);
        $total = $price + $shippingFee;

        $message .= "💰 *" . __('notifications.whatsapp.booking_submission.cost_summary') . ":*\n";
        $message .= "- " . __('notifications.whatsapp.booking_submission.service') . ": $" . number_format($price, 2) . "\n";
        if ($shippingFee > 0) {
            $message .= "- " . __('notifications.whatsapp.booking_submission.shipping') . ": $" . number_format($shippingFee, 2) . "\n";
        }
        $message .= "- *" . __('notifications.whatsapp.booking_submission.total') . ":* $" . number_format($total, 2) . "\n\n";

        $message .= "👤 *" . __('notifications.whatsapp.booking_submission.customer_data') . ":*\n";
        $message .= "- *" . __('notifications.whatsapp.booking_submission.name') . ":* {$customer->name}\n";
        $message .= "- *" . __('notifications.whatsapp.booking_submission.phone') . ":* {$customer->phone}\n";
        
        if (!empty($booking->custom_values)) {
            $message .= "\n📝 *" . __('notifications.whatsapp.booking_submission.service_details') . ":*\n";
            $message .= $this->formatCustomValuesToString((array) $booking->custom_values) . "\n";
        }

        if ($booking->lat && $booking->lng) {
            $message .= "\n📍 *" . __('notifications.whatsapp.booking_submission.location') . ":*\n";
            $message .= "https://www.google.com/maps?q={$booking->lat},{$booking->lng}\n";
        }

        // Enlace directo al panel administrativo
        $adminUrl = route('filament.admin.resources.bookings.index', ['tenant' => $tenant->slug]);
        $message .= "\n" . __('notifications.whatsapp.booking_submission.admin_link_label') . ":\n{$adminUrl}";

        $message .= "\n\n" . __('notifications.whatsapp.booking_submission.footer', ['tenant' => $tenant->name]);

        return "https://wa.me/{$phone}?text=" . urlencode(trim($message));
    }

    /**
     * Generate a direct chat link with the customer.
     *
     * @param Booking $booking
     * @return string
     */
    public function getInboundUrl(Booking $booking): string
    {
        $booking->loadMissing(['customer']);
        $customerPhone = preg_replace('/[^0-9]/', '', $booking->customer->phone);

        return "https://wa.me/{$customerPhone}";
    }

    /**
     * Generate the WhatsApp URL for the confirmation message (Owner to Client).
     *
     * @param Booking $booking
     * @return string
     */
    public function getConfirmationUrl(Booking $booking): string
    {
        $booking->loadMissing(['tenant', 'service', 'customer']);

        $tenant = $booking->tenant;
        $service = $booking->service;
        $customer = $booking->customer;

        $customerPhone = preg_replace('/[^0-9]/', '', $customer->phone);
        $scheduledAt = $booking->scheduled_at->format('d M Y - h:i A');

        $message = __('notifications.whatsapp.confirmation.message', [
            'customer' => $customer->name,
            'tenant' => $tenant->name,
            'service' => $service->name,
            'date' => $scheduledAt,
            'duration' => $tenant->duration ?? 0,
        ]);

        if ($booking->lat && $booking->lng) {
            $message .= "\n\n📍 " . __('notifications.whatsapp.confirmation.location_registered') . ":\n";
            $message .= "https://www.google.com/maps?q={$booking->lat},{$booking->lng}";
        }

        return "https://wa.me/{$customerPhone}?text=" . urlencode(trim($message));
    }

    /**
     * Generate the WhatsApp URL for a reminder message.
     *
     * @param Booking $booking
     * @return string
     */
    public function getReminderUrl(Booking $booking): string
    {
        $booking->loadMissing(['tenant', 'service', 'customer']);

        $tenant = $booking->tenant;
        $service = $booking->service;
        $customer = $booking->customer;

        $customerPhone = preg_replace('/[^0-9]/', '', $customer->phone);
        $time = $booking->scheduled_at->format('h:i A');

        $message = __('notifications.whatsapp.reminder.message', [
            'customer' => $customer->name,
            'service' => $service->name,
            'time' => $time,
        ]);

        return "https://wa.me/{$customerPhone}?text=" . urlencode(trim($message));
    }
}
