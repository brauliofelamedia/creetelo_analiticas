<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Contact;

class Transaction extends Model
{
    public function contact()
    {
        return $this->hasOne(Contact::class, 'id', 'id');
    }
}

