<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationChannel extends Model
{
    protected $table = 'notification_channels';

    protected $fillable = [
        'name',
        'driver',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
