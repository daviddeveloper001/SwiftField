<?php

namespace App\Traits;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Filament\Facades\Filament;

trait BelongsToTenant
{
    public static function bootBelongsToTenant(): void
    {
        static::creating(function (Model $model) {
            if (empty($model->tenant_id) && Filament::getTenant()) {
                $model->tenant_id = Filament::getTenant()->id;
            }
        });

        static::addGlobalScope('tenant', function (Builder $builder) {
            if (Filament::getTenant()) {
                $builder->where('tenant_id', Filament::getTenant()->id);
            }
        });
    }

    public function tenant(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
