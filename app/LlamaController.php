<?php

require_once __DIR__ . "/vendor/autoload.php";
require_once __DIR__ . "/HttpClient.php";

$dotenv = Dotenv\Dotenv::createImmutable("../" . __DIR__);
$dotenv->load();

define("API_TOKEN", $_ENV['REPLICATE_API_TOKEN']);

class LlamaController
{
    private $HttpClient;
    public $maxResponseTokens = 3000;
    public $waitTime = 5;
    const URL = 'https://api.replicate.com/v1/predictions';
    const VERSION = "02e509c789964a7ea8736978a43525956ef40397be9033abf9fd2badfe68c9e3";

    public function __construct()
    {
        $this->HttpClient = new HttpClient();
        $this->HttpClient->setUpRequest(self::URL, "POST")->addHttpHeader("Authorization: Token " . API_TOKEN);
    }

    public function setPrompt($prompt)
    {
        $postData = ["version" => self::VERSION, "input" => ["prompt" => $prompt, "max_new_tokens" => $this->maxResponseTokens]];
        $this->HttpClient->setPostData($postData);
        return $this;
    }

    public function setMaxReturnTokens($maxResponseTokens)
    {
        $this->maxResponseTokens = $maxResponseTokens;
        return $this;
    }

    public function setWaitTime($waitTime)
    {
        $this->waitTime = $waitTime;
    }

    public function execute()
    {
        // Send the prompt to the model
        $prediction = $this->HttpClient->execute();
        if ($prediction['error'] !== null) {
            exit($prediction['error']);
        }

        // Wait For the model to respond to the prompt
        echo "waiting for model to respond..." . PHP_EOL;
        sleep($this->waitTime);

        // Get the response
        $handle = new HttpClient();
        $result = $handle->setUpRequest($prediction['urls']['get'], "GET")->addHttpHeader("Authorization: Token " . API_TOKEN)->execute();
        return implode("", $result['output']);
    }
}
