<?php

require_once __DIR__ . "/LlamaController.php";

$handler = new LlamaController();

$response = $handler->setPrompt("What is php")->execute();

echo $response . PHP_EOL;