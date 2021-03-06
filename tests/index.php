<?php

require __DIR__ . '/../vendor/autoload.php';

$client = new CorreiosParser\Correios();

$calculate = [
    ['01001-001', '70150-900'],
    ['70150-900', '03245-000'],
    ['11035-110', '03244-030'],
    ['40296-700', '70843-090'],
    ['80730-970', '11035-110']
];

echo '<pre>';
foreach ($calculate as $item) {
    $content = $client->buscarFrete([
        'cepOrigem' => $item[0],
        'cepDestino' => $item[1],
        'peso' => '1.3'
    ], 'json');

    if (is_array($content)) {
        print_r($content);
    } else {
        echo $content . '<br /><br /><br />';
    }

    sleep(3);
}
