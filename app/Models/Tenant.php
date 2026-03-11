<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tenant extends ModelBase
{
    protected $table = 'tenants';

    protected $fillable = [
        'name',
        'slug',
        'whatsapp_number',
        'whatsapp_token',
    ];
}
