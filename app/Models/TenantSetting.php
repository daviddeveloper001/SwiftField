<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $tenant_id
 * @property string $key
 * @property mixed $value
 */
class TenantSetting extends Model
{
    protected $table = 'tenant_settings';

    protected $fillable = [
        'tenant_id',
        'key',
        'value',
    ];

    protected $casts = [
        'value' => 'json',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
