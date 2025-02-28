<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = [
        'email',
        'currency',
        'amount',
        'status',
        'livemode',
        'create_time',
        'entityType',
        'entityId',
        'providerType',
        'sourceType',
        'subscription_id',
        'lead_id'
    ];
}
