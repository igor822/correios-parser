<?php

require __DIR__ . '/../vendor/autoload.php';

$client = new CorreiosParser\Correios();
$content = $client->buscarFrete([
    'cepOrigem' => '03245-000',
    'cepDestino' => '04244-030',
    'peso' => '1.3'
]);

echo $content;
