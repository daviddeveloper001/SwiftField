<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\BaseModel;

class SystemException extends BaseModel
{
    use MassPrunable;

    protected $fillable = [
        'message',
        'file',
        'line',
        'stack_trace',
        'user_id',
        'tenant_id',
        'url',
        'method',
        'status',
    ];

    /**
     * Determine the prunable query.
     */
    public function prunable(): Builder
    {
        return static::where('created_at', '<=', now()->subDays(30));
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
