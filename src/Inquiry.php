<?php

namespace Codemenco\Inquiry;

use Illuminate\Support\Facades\Http;

trait Inquiry
{
    public function __call($name, $arguments = [])
    {
        return $this->request($name, count($arguments) > 0 ? $arguments[0] : []);
    }

    private function request($name, array $arguments, $type = 'get')
    {
        $method = $this->getMethod($name);
        $path = $this->getPath($name);
        $arguments = $this->getInfo($arguments)[$name]['arguments'];
        if($type == 'post'){
            $response = Http::withoutVerifying()->withToken($this->accessToken)->$method($path, $arguments)
                ->throw(function ($response, $e) {
                    return response(['code' => 0], 200);
                    throw new \Exception($e->getMessage());
                })->json();
            return $this->response($response, $name);
        } else {
            $path = $path .'/'.$method;
            $response = Http::withoutVerifying()->withToken($this->accessToken)->get($path, $arguments)
                ->throw(function ($response, $e) {
                    return response(['code' => 0], 200);
                    throw new \Exception($e->getMessage());
                })->json();
            return $this->response($response, $name);
        }

    }

    private function getMethod($name): string
    {
        $method = $this->getInfo()[$name]['path'];
        return preg_split('/(?=[A-Z])/', $method)[0];
    }

    private function getPath($name)
    {
        $path = $this->baseUrl;
        if ($this->version) {
            $path = $this->baseUrl . $this->version;
        }
        $method = $this->getInfo()[$name]['path'];
        $items = preg_split('/(?=[A-Z])/', $method);

        $i = 1;
        foreach ($items as $item) {
            if ($i > 1) {
                $path .= strtolower($item) . '/';
            }
            $i++;
        }
        return rtrim($path, '/');
    }

    private function response($response, $name): array
    {
        return $this->getInfo([], $response)[$name]['response'];
    }

    /**
     * @param string $baseUrl
     */
    public function setBaseUrl(string $baseUrl): void
    {
        $this->baseUrl = $baseUrl;
    }

    /**
     * @param string $version
     */
    public function setVersion(string $version): void
    {
        $this->version = $version;
    }
}
