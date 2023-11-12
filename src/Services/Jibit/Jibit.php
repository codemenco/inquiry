<?php

namespace Codemenco\Inquiry\Services\Jibit;



use Codemenco\Gateway\Jibit\PhpFileCache;
use Codemenco\Inquiry\Inquiry;

class Jibit
{
    use Inquiry;

    protected string $baseUrl = 'https://napi.jibit.ir/ide/';
    protected string $version = 'v1/';
    public $accessToken, $refreshToken, $apiKey, $secretKey;
    private $cache;

    public function __construct()
    {
        ini_set('memory_limit', '-1');
        $this->cache = new PhpFileCache();
        $this->generateToken();
        $this->apiKey = config('inquiry.jibit.api_key');
        $this->secretKey = config('inquiry.jibit.secret_key');
    }

    /**
     * @return mixed
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }


    /**
     * @param mixed $accessToken
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
    }

    /**
     * @param mixed $refreshToken
     */
    public function setRefreshToken($refreshToken)
    {
        $refreshToken1 = $refreshToken;
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
                'path' => 'services/matching',
                'arguments' => [
                    'mobileNumber' => $arguments['mobileNumber'] ?? '',
                    'nationalCode' => $arguments['nationalCode'] ?? ''
                ],
                'response' => [
                    'code' => 200,
                    'matched' => $response['matched'] ?? false,
                ]
            ],
            'cardToSheba' => [
                'path' => 'cards',
                'arguments' => [
                    'number' => $arguments['number'] ?? '',
                    'iban' => "true",
                ],
                'response' => [
                    'code' => isset($response['number']) ? 200 : 400,
                    'number' => $response['number'] ?? '',
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

    private function generateToken($isForce = false)
    {
        $cache = new PhpFileCache();
        $cache->eraseExpired();

        if ($isForce === false && $cache->isCached('accessToken')) {
            return $this->setAccessToken($cache->retrieve('accessToken'));
        } else if ($cache->isCached('refreshToken')) {
            $this->generateNewToken();
            if ($refreshToken !== 'ok') {
                $this->generateNewToken();
            }
        } else {
            $this->generateNewToken();
        }
        return 'unExcepted Err in generateToken.';
    }

    private function refreshTokens()
    {
        $data = [
            'accessToken' => $this->cache->retrieve('accessToken'),
            'refreshToken' => $this->cache->retrieve('refreshToken'),
        ];
        dd($data);
        $result = $this->request('refreshToken',$data, 'post');
        if (empty($result['accessToken'])) {
            return 'Err in refresh token.';
        }
        if (!empty($result['accessToken'])) {
            $this->cache->store('accessToken', $result['accessToken'], 24 * 60 * 60 - 60);
            $this->cache->store('refreshToken', $result['refreshToken'], 48 * 60 * 60 - 60);
            $this->setAccessToken($result['accessToken']);
            $this->setRefreshToken($result['refreshToken']);
            return 'ok';
        }

        return 'unExcepted Err in refreshToken.';
    }


    private function generateNewToken()
    {
        $this->apiKey = config('inquiry.jibit.api_key');
        $this->secretKey = config('inquiry.jibit.secret_key');
        $result = $this->request('generateToken', array(), 'post');

        if (empty($result['accessToken'])) {
            return 'Err in generate new token.';
        }
        if (!empty($result['accessToken'])) {
            $this->cache->store('accessToken', $result['accessToken'], 24 * 60 * 60 - 60);
            $this->cache->store('refreshToken', $result['refreshToken'], 48 * 60 * 60 - 60);
            $this->setAccessToken($result['accessToken']);
            $this->setRefreshToken($result['refreshToken']);
            return 'ok';
        }
        return 'unExcepted Err in generateNewToken.';
    }
}
