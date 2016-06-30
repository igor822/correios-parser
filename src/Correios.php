<?php

namespace CorreiosParser;

class Correios
{
    const SERVICE_SEDEX = '40010';
    const SERVICE_SEDEX_10 = '40215';
    const SERVICE_SEDEX_COBRAR = '40045';
    const SERVICE_SEDEX_HOJE = '40290';
    const SERVICE_PAC = '41106';

    const FORMAT_CAIXA = 1;
    const FORMAT_ROLO = 2;
    const FORMAT_ENVELOPE = 3;

    const WARN_RECEIVE = 'S';
    const WARN_NOT_RECEIVE = 'N';

    private $url;

    public function __construct()
    {
        $this->url = 'http://m.correios.com.br/movel/calculaPrecos.do';
    }

    public function buscarFrete($data)
    {
        $response = $this->request($data);

        $fetcher = new ParserContent($response);
        $items = $fetcher->getParsedContent();

        $parser = new ResponseBuilder($items);
        $response = $parser->parse();

        return json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    private function request($data)
    {
        $defaultData = $this->loadDefaultData();
        $data = array_merge($defaultData, $data);
        $ch = curl_init($this->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        $content = curl_exec($ch);

        curl_close($ch);

        return $content;
    }

    private function loadDefaultData()
    {
        return [
            'servico' => self::SERVICE_SEDEX,
            'cepOrigem' => '',
            'cepDestino' => '',
            'peso' => '',
            'formato' => self::FORMAT_CAIXA,
            'comprimento' => '16',
            'altura' => '11',
            'largura' => '11',
            'diametro' => '',
            'maoPropria' => 'N',
            'valorDeclarado' => '',
            'avisoRecebimento' => self::WARN_NOT_RECEIVE,
            'metodo' => 'calcular'
        ];
    }
}
