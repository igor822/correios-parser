<?php

require_once __DIR__ . '/src/Client.php';
require_once __DIR__ . '/src/Fetcher.php';
require_once __DIR__ . '/src/Parser.php';

$client = new CorreiosParser\Client();
$content = $client->request('03245-000', '04244-030', '1.3');

$fetcher = new CorreiosParser\Fetcher($content);
$items = $fetcher->getContentParsed();

$parser = new \CorreiosParser\Parser($items);
$response = $parser->parse();

//echo json_encode($response);
var_dump($response);
