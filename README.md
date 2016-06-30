# Correios Parser

Essa biblioteca consiste em fazer uma busca de frete no site dos Correios

Atualmente (v0.2.0) está atendendo somente o cálculo de Frete

Como Funciona
----------------------

Segue abaixo um código para exemplo

```php
$correios = new CorreiosParser\Correios();
$content = $correios->buscarFrete([
    'cepOrigem' => '01001-001',
    'cepDestino' => '70150-900',
    'peso' => '1.3'
]);

echo $content;
```

Retorno em JSON:

```json
{  
   "servico":"sedex",
   "prazo":1,
   "valor_declarado":0,
   "entrega_sabado":1,
   "formato":"Caixa/Pacote",
   "dimensoes":{  
      "comprimento":16,
      "altura":11,
      "largura":11
   },
   "peso":1.3,
   "frete":46.2,
   "total":46.2,
   "origem":{  
      "cep":"01001001",
      "cidade":"São Paulo / SP"
   },
   "destino":{  
      "cep":"70150900",
      "cidade":"Brasília / DF"
   }
}
```

Pode-se adicionar mais opções:

```php
[
  'servico' => 40010, // sedex
  'cepOrigem' => '',
  'cepDestino' => '',
  'peso' => '',
  'formato' => 1, // caixa
  'comprimento' => '16', // em cm, mínimo é 16
  'altura' => '11', // em cm, mínimo é 11
  'largura' => '11', // em cm, mínimo é 11
  'diametro' => '',
  'maoPropria' => 'N', // N = Não, S = Sim
  'valorDeclarado' => '100,00', // opcional, formato Real
  'avisoRecebimento' => 'N', // N = Não, S = Sim
  'metodo' => 'calcular' // obrigatório
];
```

Caso ocorra algum erro na requisição haverá o retorno

```json
{ "erro" : 1 }
```



Motivação
------------------------

Ao utilizar uma biblioteca ou mesmo fazer uma busca no WebService dos Correios e não há um retorno esperar (ou mesmo o
WS caiu) podemos com essa biblioteca fazer um "Fall back" para buscar diretamente no site.