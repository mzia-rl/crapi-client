<?php

namespace Canzell\Http\Clients;

use GuzzleHttp\Client as Guzzle;

class HttpClient {

    private $client;

    public function __construct(
        Guzzle $client = null,
        private array $overrides = [],
        private bool $shouldParseJsonResponse = true
    ) {
        if ($client) $this->client = $client;
        else $this->client = new Guzzle();
    }

    public function __call($name, $args)
    {
        $client = $this->client;
        if (method_exists($client, $name)) {
            
            if (is_string($args[0])) {
                $args[0] = $this->override($args[0], $this->overrides);
            } else {
                $args[0]['url'] = $this->override($args[0]['url'], $this->overrides);
            }

            $res = call_user_func_array([$client, $name], $args);
            $body = $res->getBody();
            $type = $res->getHeaderLine('content-type');
            
            if ($this->shouldParseJsonResponse) {
                $isContentJson = strpos($type, 'application/json') !== false;
                if ($isContentJson) {
                    $body = json_decode($body);
                    if ($body === null) $body = $res->getBody();
                }
                return $body;
            } else {
                return $res;
            }

        } else throw new \Error('Call to undefined method! '.$name);
    }

    protected function override($path, $overrides)
    {
        foreach ($overrides as $regex => $base_uri) {
            $substitute = preg_replace($regex, $base_uri, $path);
            if ($substitute != $path) return $substitute;
        }
        return $path;
    }


    public static function __callStatic($name, $arguments)
    {
        $client = new static();
        return $client->{$name}(...$arguments);
    }

}