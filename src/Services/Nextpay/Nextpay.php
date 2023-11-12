<?php

namespace Codemenco\Inquiry\Services\Nextpay;


use Codemenco\Inquiry\Inquiry;

class Nextpay
{
    use Inquiry;

    public string $baseUrl = 'https://nextpay.org/nx/inquiry/';
    public  $version = false;
    public $accessToken;


    public function __construct()
    {
        $this->accessToken = config('inquiry.nextpay.token');
    }

    public function getInfo($arguments = [], $response = [])
    {
        return [
            'cardInfo' => [
                'path' => 'postCard',
                'arguments' => [
                    'inquiry_api' => $this->accessToken,
                    'card' => (integer)($arguments['cardNumber'] ?? '')
                ],
                'response' => [
                    'code' => $response['code'] ?? '',
                    'cardNumber' => $response['data']['card'] ?? '',
                    'name' => $response['data']['name'] ?? '',
                    'iban' => $response['data']['iban'] ?? '',
                    'deposit' => $response['data']['deposit'] ?? '',
                    'bank' => ''
                ]
            ],
            'matchingCardWithNational' => [
                'path' => 'postCard',
                'arguments' => [
                    'cardNumber' => $arguments['cardNumber'] ?? '',
                    'nationalCode' => $arguments['nationalCode'] ?? '',
                    'birthDate' => isset($arguments['birthday']) ? str_replace('/', '', $arguments['birthday']) : '',
                ],
                'response' => [
                    'code' => 404,
                    'matched' => $response['matched'] ?? false,
                ]
            ],
            'matchingMobileWithNational' => [
                'path' => 'postShahkar',
                'arguments' => [
                    'inquiry_api' => $this->accessToken,
                    'mobile' => $arguments['mobile'] ?? '',
                    'national_id' => $arguments['nationalCode'] ?? ''
                ],
                'response' => [
                    'code' => 200,
                    'matched' => $response['data']['match'] ?? false,
                ]
            ],
            'cardToSheba' => [
                'path' => 'postFullcard',
                'arguments' => [
                    'inquiry_api' => $this->accessToken,
                    'card' => (integer)($arguments['cardNumber'] ?? '')
                ],
                'response' => [
                    'code' => $response['code'] ?? '',
                    'cardNumber' => $response['data']['card'] ?? '',
                    'name' => $response['data']['name'] ?? '',
                    'iban' => $response['data']['iban'] ?? '',
                    'deposit' => $response['data']['deposit'] ?? '',
                    'bank' => ''
                ]
            ],
        ];
    }
}
