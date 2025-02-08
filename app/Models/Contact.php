<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use GuzzleHttp\Client;
use Exception;

class Contact extends Model
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

    public function getContacts($name, $page)
    {
        $filters = [
            [
                'group' => 'OR',
                'filters' => [
                    [
                        'field' => 'tags',
                        'operator' => 'eq',
                        'value' => ['wowfriday_plan mensual'],
                    ],
                    [
                        'field' => 'tags',
                        'operator' => 'eq',
                        'value' => ['wowfriday_plan anual'],
                    ],
                ],
            ],
        ];

        $data = [
            'locationId' => $this->config->location_id,
            'page' => intval($page),
            'pageLimit' => 20,
            'filters' => $filters,
        ];

        try {
            $response = $this->client->post('contacts/search', [
                'headers' => [
                    'Accept' => 'application/json',
                    'Version' => '2021-07-28',
                    'Authorization' => 'Bearer ' . $this->config->access_token,
                ],
                'json' => $data,
            ]);

            return json_decode($response->getBody(), true);
        } catch (Exception $e) {
            if ($e->getCode() == 401) {
                return response()->json(['error' => 'Unauthorized request'], 401);
            }
            return response()->json(['error' => 'Request failed'], 500);
        }
    }
}
