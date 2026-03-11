<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Service extends ModelBase
{
    protected $table = 'services';

    protected $fillable = [
        'tenant_id',
        'name',
        'description',
        'price',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
