<?php

declare (strict_types=1);

// A cURL http client class
class HttpClient
{
    private $handle;
    private array $headers;

    public function __construct(string $content_type = "Content-Type: application/json")
    {
        $this->handle = curl_init();
        $this->headers[] = $content_type;
        return $this;
    }

    public function setUpRequest(string $url, string $method)
    {
        $method = trim(strtoupper($method));
        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
        ];

        if ($method == "POST") {
            $options[CURLOPT_POST] = true;
        }
        curl_setopt_array($this->handle, $options);
        return $this;
    }

    public function setPostData(array $postData, $encodingFormat = "json")
    {
        curl_setopt($this->handle, CURLOPT_POSTFIELDS, json_encode($postData));
        return $this;
    }

    public function addHttpHeader(string $header)
    {
        $this->headers[] = $header;
        return $this;
    }

    public function execute()
    {
        curl_setopt($this->handle, CURLOPT_HTTPHEADER, $this->headers);
        $result = curl_exec($this->handle);
        if (($error = curl_error($this->handle)) == true) {
            exit($error);
        }

        return json_decode($result, true);
    }
}
