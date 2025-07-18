<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use App\Models\Config;
use GuzzleHttp\Client;
use Exception;

class Opportunity
{
    private $client;
    private $config;
    private $client_id;
    private $client_secret;

    public function __construct()
    {
        //Get Config
        $this->config = Config::where('id',1)->first();

        //Client and Client Secret
        $this->client_id = env('GHL_CLIENT_ID');
        $this->client_secret = env('GHL_CLIENT_SECRET');

        $this->client = new Client([
            'base_uri' => 'https://services.leadconnectorhq.com',
        ]);
    }

    public function Opportunities($contactId = null)
    {
        try {
            $query = [
                'locationId' => $this->config->location_id,
            ];

            if ($contactId) {
                $query['contactId'] = $contactId;
            }

            $response = $this->client->get('opportunities/search', [
                'headers' => [
                    'Accept' => 'application/json',
                    'Version' => '2021-07-28',
                    'Authorization' => 'Bearer ' . $this->config->access_token,
                ],
                'query' => $query
            ]);

            $data = json_decode($response->getBody(), true);
            return response()->json($data, 200);

        } catch (Exception $e) {
            if ($e->getCode() == 401) {
                return response()->json(['error' => 'Unauthorized request'], 401);
            }

            return response()->json(['error' => 'Request failed', 'message' => $e->getMessage(), 'code' => $e->getCode()], 500);
        }
    }
}