<?php

namespace Msh\POSRocket;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Session;

class PosRocketController extends Controller
{
    private $client;
    private $clientId;
    private $clientSecret;
    private $email;
    private $password;
    private $redirectURL;

    private $code; // grant access code

    private $tokenResponse;
    private $token;

    private $businessUserInfo;

    /**
     * PosRocketController constructor.
     */
    public function __construct()
    {
        $this->client = new Client();

        $this->clientId = env('POSROCKET_CLIENT_ID');
        $this->clientSecret = env('POSROCKET_CLIENT_SECRET');
        $this->email = env('POSROCKET_ACCOUNT_EMAIL');
        $this->password = env('POSROCKET_ACCOUNT_PASSWORD');
        $this->redirectURL = env('POSROCKET_REDIRECT_URL');


    }

    /**
     * Authorize
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function connect(Request $request)
    {
        $url = sprintf("http://developer.posrocket.com/oauth/authorize/?redirect_uri=%s&response_type=code&client_id=%s&access_type=offline",
            $this->redirectURL, $this->clientId);

        return redirect($url);
    }

    /**
     * Authorization Listener
     *
     * @param Request $request
     */
    public function connectListener(Request $request)
    {
        if (data_get($request, 'code')) {
            // Store the Grant Code
            $this->code = $request->code;

            // Get Barear Token
            $this->getAccessToken();

            // Get Business Info
             $this->getUserData(); // Access by the route => posrocket/business

            // Get Menu
            $this->getMenuItems(); // Access by the route => posrocket/menu

        }
    }

    /**
     * Get Access Token
     */
    public function getAccessToken()
    {
        $response = $this->client->request('POST', 'http://developer.posrocket.com/oauth/token/', [
            "form_params" => [
                "client_id" => $this->clientId,
                "client_secret" => $this->clientSecret,
                "grant_type" => 'authorization_code',
                "code" => $this->code,
            ],
        ]);

        $this->tokenResponse = json_decode($response->getBody()->getContents());

        $this->token = data_get($this->tokenResponse, 'access_token');
    }

    /**
     * Get User Data
     * name , type , country , currency , end_of_fiscal_day , phone , address , image , timezone
     */
    public function getUserData()
    {
        dd(Session::get('POS_TOKEN'));
        $headers = [
            'Authorization' => 'Bearer ' . Session::get('POS_TOKEN'),
            'Accept' => 'application/json',
        ];

        $response = $this->client->request('GET', 'http://developer.posrocket.com:80/api/v1/me', [
            'headers' => $headers
        ]);

        $this->businessUserInfo = json_decode($response->getBody()->getContents());
    }

    /**
     * Get Menu Items
     */
    public function getMenuItems()
    {
        $headers = [
            'Authorization' => 'Bearer ' . Session::get('POS_TOKEN'),
            'Accept' => 'application/json',
        ];

        $response = $this->client->request('GET', 'http://developer.posrocket.com:80/api/v1/catalog/items', [
            'headers' => $headers
        ]);

        $this->menu = json_decode($response->getBody()->getContents());
    }

}
