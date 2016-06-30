<?php

require __DIR__ . '/../vendor/autoload.php';

$client = new CorreiosParser\Correios();
$content = $client->buscarFrete([
    'cepOrigem' => '01001-001',
    'cepDestino' => '70150-900',
    'peso' => '1.3'
]);

echo $content;
