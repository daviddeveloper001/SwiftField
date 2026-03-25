<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Customer extends ModelBase
{
    protected $table = 'customers';

    protected $fillable = [
        'tenant_id',
        'name',
        'email',
        'phone',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    protected function phone(): Attribute
    {
        return Attribute::make(
            get: function (?string $value) {
                if (empty($value)) return $value;
                return str_starts_with($value, '57') ? substr($value, 2) : $value;
            },
            set: function (?string $value) {
                if (empty($value)) return $value;
                $clean = preg_replace('/[^0-9]/', '', $value);
                if (!empty($clean) && !str_starts_with($clean, '57')) {
                    $clean = '57' . $clean;
                }
                return $clean;
            }
        );
    }
}
