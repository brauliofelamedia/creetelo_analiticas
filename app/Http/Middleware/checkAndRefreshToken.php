<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\GoHighLevel;
use App\Models\Config;
use GuzzleHttp\Client;

class checkAndRefreshToken
{
    private $client;
    private $config;
    private $client_id;
    private $client_secret;

    public function __construct()
    {
        $this->client_id = env('GHL_CLIENT_ID');
        $this->client_secret = env('GHL_CLIENT_SECRET');

        $this->config = Config::where('id',1)->first();
        $this->client = new Client([
            'base_uri' => 'https://services.leadconnectorhq.com',
        ]);
    }

    public function handle(Request $request, Closure $next): Response
    {
        // Fetch configuration data
        $config = Config::where('id', 1)->firstOrFail();
        $GoHighLevel = new GoHighLevel();
        $token = $GoHighLevel->checkToken();

        if($token->getStatusCode() == 401){
            $this->generateToken();
        }

        return $next($request);
    }

    private function generateToken()
    {
        try {
            $response = $this->client->post('https://services.leadconnectorhq.com/oauth/token', [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
                'form_params' => [
                    'client_id' => $this->client_id,
                    'client_secret' => $this->client_secret,
                    'grant_type' => 'refresh_token',
                    'code' => $this->config->code,
                    'refresh_token' => $this->config->refresh_token,
                ],
            ]);

            $statusCode = $response->getStatusCode();
            $responseBody = $response->getBody()->getContents();

            if ($statusCode === 200) {
                $responseData = json_decode($responseBody, true);

                $config = Config::where('id',1)->first();
                $config->access_token = $responseData['access_token'];
                $config->refresh_token = $responseData['refresh_token'];
                $config->save();

            } else {
                return response()->json(['error' => 'Token exchange failed'], $statusCode);
            }
        } catch (Exception $e) {
            return response()->json(['error' => 'Request failed'], 500);
        }
    }
}
