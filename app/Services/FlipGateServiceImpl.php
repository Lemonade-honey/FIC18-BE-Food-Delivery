<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use App\Services\Interfaces\FlipGateService;

class FlipGateServiceImpl implements FlipGateService
{
    private $base_url;
    private $secret_key;
    private $encode_key;

    public function __construct()
    {
        $this->base_url = env("FLIP_ENV_URL");
        $this->secret_key = env("FLIP_API_KEY");
        $this->encode_key = 'Basic '.base64_encode($this->secret_key.":");
    }

    public function getBalance()
    {
        $client = new Client();

        $response = $client->get($this->base_url . '/v2/general/balance', [
            'headers' =>[
                "Authorization:".$this->encode_key,
                "Content-Type: application/x-www-form-urlencoded"
            ],
            'auth' => [$this->secret_key . ":", ''],
            'verify' => false
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    public function createBill(string $title, int $amount)
    {
        try {
            $client = new Client();

            $payloads = [
                "title" => $title,
                "amount" => $amount,
                "type" => "SINGLE"
            ];

            $response = $client->post($this->base_url . '/v2/pwf/bill', [
                'headers' =>[
                    "Authorization:".$this->encode_key,
                    "Content-Type: application/x-www-form-urlencoded"
                ],
                'form_params' => $payloads,
                'auth' => [$this->secret_key . ":", ''],
                'verify' => false
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } 
        
        catch (RequestException $e) {
            return [
                'error' => json_decode($e->getMessage())
            ];
        }
    }
}