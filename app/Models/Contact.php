<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use GuzzleHttp\Client;
use App\Services\Contacts;

use Exception;

class Contact extends Model
{
    protected $casts = [
        'customFields' => 'array',
        'tags' => 'array',
        'additionalEmails' => 'array',
        'additionalPhones' => 'array',
        'followers' => 'array',
    ];

    public function getFullNameAttribute()
    {
        return ucwords($this->firstNameLowerCase) . ' ' . ucwords($this->lastNameLowerCase);
    }

    public function getdateOfBirthAttribute()
    {
        return Carbon::parse($this->attributes['dateOfBirth'])->format('Y-m-d');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'contact_id', 'id');
    }
}
