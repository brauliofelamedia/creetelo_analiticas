<?php

namespace App\Http\Controllers;

use App\Models\GoHighLevel;
use Illuminate\Http\Request;
use App\Models\Config;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;

class GoHighLevelController extends Controller
{
    private $config;
    private $client_id;
    private $client_secret;
    private $url;

    public function __construct()
    {
        $this->config = Config::where('id', 1)->first();
        $this->client_id = env('GHL_CLIENT_ID');
        $this->client_secret = env('GHL_CLIENT_SECRET');
        $this->url = route('authorization');
    }

    //Token, Refresh Toke and Access Token
    public function token()
    {
        $goHighLevel = new GoHighLevel();
        return $goHighLevel->getToken();
    }

    public function authorization(Request $request)
    {
        $this->config->code = $request->code;
        $this->config->save();

        try {
            $response = Http::asForm()->post('https://services.leadconnectorhq.com/oauth/token', [
                'client_id' => $this->client_id,
                'client_secret' => $this->client_secret,
                'grant_type' => 'authorization_code',
                'code' => $this->config->code,
                'user_type' => 'Company'
            ]);

            $response->throw();
            $data = $response->json();

            // Handle successful response and return access token
            $this->config->access_token = $data['access_token'];
            $this->config->refresh_token = $data['refresh_token'];
            $this->config->company_id = $data['companyId'];
            $this->config->location_id = $data['locationId'];
            $this->config->save();

            return redirect()->route('finish');

        } catch (\Throwable $exception) {
            return $exception->getMessage();
            //return response()->json(['error' => 'Error exchanging code'], 500);
        }
    }

    public function finish()
    {
        /*$contactServices = new GoHighLevel();
        $users = $contactServices->getContacts(null, null);*/
        return 'Se termino la configuración';
    }

    public function connect()
    {
        $client_id = $this->client_id;
        if ($client_id) {
            $url = "https://marketplace.leadconnectorhq.com/oauth/chooselocation?response_type=code&redirect_uri=" . $this->url . "&client_id=" . $client_id . "&scope=contacts.readonly&loginWindowOpenMode=self";
            return redirect()->away($url);
        } else {
            return 'No se ha asignado el client_id';
        }
    }
}
