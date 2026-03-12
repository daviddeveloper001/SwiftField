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
            $formattedKey = ucwords(str_replace(['_', '-'], ' ', $key));
            $formattedValue = is_array($value) ? implode(', ', $value) : (string) $value;
            $formatted[] = "- {$formattedKey}: {$formattedValue}";
        }

        return implode("\n", $formatted);
    }
}
