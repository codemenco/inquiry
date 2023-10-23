<?php

namespace Codemenco\Inquiry\Services\Jibit;



use Codemenco\Inquiry\Inquiry;

class Jibit
{
    use Inquiry;

    protected string $baseUrl = 'https://napi.jibit.ir/ide/';
    protected string $version = 'v1/';
    protected $accessToken, $refreshToken, $apiKey, $secretKey;

    public function __construct()
    {
        $this->accessToken = config('inquiry.jibit.access_token');
        $this->refreshToken = config('inquiry.jibit.refresh_token');
        $this->apiKey = config('inquiry.jibit.api_key');
        $this->secretKey = config('inquiry.jibit.secret_key');
    }

    public function getInfo($arguments = [], $response = [])
    {
        return [
            'cardInfo' => [
                'path' => 'getCards',
                'arguments' => [
                    'number' => $arguments['cardNumber'] ?? '',
                ],
                'response' => [
                    'code' => isset($response['number']) ? 200 : 400,
                    'cardNumber' => $response['number'] ?? '',
                    'name' => $response['cardInfo']['ownerName'] ?? '',
                    'iban' => $response['cardInfo']['iban'] ?? '',
                    'deposit' => $response['cardInfo']['depositNumber'] ?? '',
                    'bank' => $response['cardInfo']['bank'] ?? ''
                ]
            ],
            'matchingCardWithNational' => [
                'path' => 'getServicesMatching',
                'arguments' => [
                    'cardNumber' => $arguments['cardNumber'] ?? '',
                    'nationalCode' => $arguments['nationalCode'] ?? '',
                    'birthDate' => isset($arguments['birthday']) ? str_replace('/', '', $arguments['birthday']) : '',
                ],
                'response' => [
                    'code' => 200,
                    'matched' => $response['matched'] ?? false,
                ]
            ],
            'matchingMobileWithNational' => [
                'path' => 'getServicesMatching',
                'arguments' => [
                    'inquiry_api' => $this->accessToken,
                    'mobileNumber' => $arguments['mobile'] ?? '',
                    'nationalCode' => $arguments['nationalCode'] ?? ''
                ],
                'response' => [
                    'code' => 200,
                    'matched' => $response['matched'] ?? false,
                ]
            ],
            'cardToSheba' => [
                'path' => 'getCards',
                'arguments' => [
                    'number' => $arguments['cardNumber'] ?? '',
                    'iban' => "true",
                ],
                'response' => [
                    'code' => isset($response['number']) ? 200 : 400,
                    'cardNumber' => $response['number'] ?? '',
                    'name' => @$response['ibanInfo']['owners'][0]['firstName'] . ' ' . @$response['ibanInfo']['owners'][0]['lastName'] ?? '',
                    'iban' => $response['ibanInfo']['iban'] ?? '',
                    'deposit' => $response['ibanInfo']['depositNumber'] ?? '',
                    'bank' => $response['ibanInfo']['bank'] ?? ''
                ]
            ],
            'generateToken' => [
                'path' => 'postTokensGenerate',
                'arguments' => [
                    'apiKey' => $this->apiKey,
                    'secretKey' => $this->secretKey
                ],
                'response' => [
                    'code' => 200,
                    'accessToken' => $response['accessToken'] ?? '',
                    'refreshToken' => $response['refreshToken'] ?? '',
                ]
            ],
            'refreshToken' => [
                'path' => 'postTokensRefresh',
                'arguments' => [
                    'accessToken' => $this->accessToken,
                    'refreshToken' => $this->refreshToken
                ],
                'response' => [
                    'code' => 200,
                    'accessToken' => $response['accessToken'] ?? '',
                    'refreshToken' => $response['refreshToken'] ?? '',
                ]
            ]
        ];
    }
}
