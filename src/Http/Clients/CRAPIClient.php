<?php

namespace Canzell\Http\Clients;

use GuzzleHttp\Client as Guzzle;


class CRAPIClient extends HttpClient
{

    private $config;
    private $expiresAt = null;
    private $token;

    public function __construct() {
        $this->config = config('crapi-client');
        $this->authenticate();

        // Configure Client
        $client = new Guzzle([
            'base_uri' => $this->config['base_uri'],
            'headers' => [
                'Authorization' => "Bearer {$this->token}",
            ]
        ]);
        
        parent::__construct($client, $this->config['overrides']);
    }

    public function __call($name, $args)
    {
        $this->authenticate();
        return parent::__call($name, $args);
    }

    private function authenticate()
    {
        if ($this->expiresAt && $this->expiresAt > time()) return;
        
        $config = $this->config;

        if ($config['token']) $this->token = $config['token'];
        else {
            $endpoint = $this->override('auth/token', $config['overrides']);
            $body = (new Guzzle)->post($endpoint, [
                'json' => [
                    'client_id' => $config['client_id'],
                    'client_secret' => $config['client_secret']
                ]
            ])->getBody();
            $body = json_decode($body);
            $this->expiresAt = $body->expires_at;
            $this->token = $body->access_token;
        }
    }

}