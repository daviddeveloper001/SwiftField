<?php

declare(strict_types=1);

namespace App\Traits;

trait FormatCustomValues
{
    /**
     * Format a custom values array into a readable string.
     *
     * @param array $customValues
     * @return string
     */
    protected function formatCustomValuesToString(array $customValues): string
    {
        if (empty($customValues)) {
            return '';
        }

        $formatted = [];
        foreach ($customValues as $key => $value) {
            // Traducir Key usando notifications.fields
            $label = __("notifications.fields.{$key}");
            if ($label === "notifications.fields.{$key}") {
                $label = ucwords(str_replace(['_', '-'], ' ', (string)$key));
            }
            
            // Traducir Value usando notifications.values
            $valStr = is_array($value) ? implode(', ', $value) : (string) $value;
            $translatedVal = __("notifications.values." . strtolower($valStr));
            
            if ($translatedVal !== "notifications.values." . strtolower($valStr)) {
                $valStr = $translatedVal;
            }

            $formatted[] = "• {$label}: {$valStr}";
        }

        return implode("\n", $formatted);
    }
}
