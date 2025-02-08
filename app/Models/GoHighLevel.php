<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class GoHighLevel extends Model
{
    private $config;
    private $client_id;
    private $client_secret;

    public function __construct() {
        $this->config = Config::where('id', 1)->first();
        $this->client_id = env('GHL_CLIENT_ID');
        $this->client_secret = env('GHL_CLIENT_SECRET');
    }
}
