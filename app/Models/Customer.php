<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
}
