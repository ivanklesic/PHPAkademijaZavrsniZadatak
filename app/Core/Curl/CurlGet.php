<?php

namespace App\Core\Curl;

use Exception;

class CurlGet
{
    private $endpoint;
    private $options;

    public function __construct($endpoint, array $options = [])
    {
        $this->endpoint = 'https://www.cheapshark.com/api/1.0/' . $endpoint;
        $this->options = $options;
    }

    public function getResponse()
    {
        $url = $this->endpoint . '?' . http_build_query($this->options);

        $ch = curl_init($url);

        curl_setopt($ch, \CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $error    = curl_error($ch);
        $errno    = curl_errno($ch);

        if (is_resource($ch)) {
            curl_close($ch);
        }

        if (0 !== $errno) {
            throw new Exception($error, $errno);
        }

        return $response;
    }
}