<?php

namespace CorreiosParser;

class ResponseBuilder
{
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function parse()
    {
        $items = [];
        foreach ($this->data as $item) {
            $this->addItem($item, $items);
        }
        return $items;
    }

    private function splitItem($value, $delimiter)
    {
        if (strpos($value, $delimiter) !== false) {
            $items = explode($delimiter, $value);
            return array_map("trim", $items);
        }

        return $value;
    }

    private function addItem($item, &$items)
    {
        $keys = $this->keys();
        if (in_array(key($item), ['origem', 'destino'])) {
            $originKey = $keys[$item[key($item)]['key']];
            $items[key($item)][$originKey] = $this->filterValues($originKey, $item[key($item)]['value']);
        } else {
            $originKey = $keys[$item['key']];
            if (is_array($originKey)) {
                $splitKeys = $this->splitItem($item['key'], '/');
                $splitValues = $this->splitItem($item['value'], '/');

                foreach ($splitKeys as $k => $v) {
                    $value = $splitValues;
                    if (is_array($splitValues)) {
                        $value = $splitValues[$k];
                    }
                    $this->addItem(['key' => $v, 'value' => $value], $items);
                }
                if ($splitKeys) {
                    return;
                }

                $items[$item['key']] = $item['value'];
            } else {
                $items[$originKey] = $this->filterValues($keys[$item['key']], $item['value']);
            }
        }
    }

    private function filterValues($key, $value)
    {
        $priceFormat = function($value) {
            preg_match('([0-9\.,]+)', $value, $matches);
            if ([] == $matches || $matches[0] == '') {
                return 0.0;
            }
            $price = str_replace('.', '', $matches[0]);
            return floatval(str_replace(',', '.', $price));
        };

        $rules = [
            'prazo' => function ($value) {
                preg_match('([0-9]+)', $value, $matches);
                return intval($matches[0]);
            },
            'peso' => function ($value) {
                preg_match('([0-9\.]+)', $value, $matches);
                return floatval($matches[0]);
            },
            'servico' => function ($value) {
                return strtolower($value);
            },
            'dimensoes' => function ($value) {
                list($length, $height, $width) = preg_split('/ (x?)/', $value);
                return [
                    'comprimento' => intval($length),
                    'altura' => intval($height),
                    'largura' => intval($width)
                ];
            },
            'frete' => $priceFormat,
            'total' => $priceFormat,
            'valor_declarado' => $priceFormat,
            'entrega_sabado' => function ($value) {
                return $value == 'Sim' ? 1 : 0;
            }
        ];

        if (is_array($key)) {
            return $value;
        }

        if (array_key_exists($key, $rules)) {
            $filteredValue = $rules[$key]($value);
            return $filteredValue;
        }

        return $value;
    }

    private function keys()
    {
        return [
            'Serviço' => 'servico',
            'Prazo de entrega' => 'prazo',
            'Valor Declarado' => 'valor_declarado',
            'Entrega domiciliar' => 'entrega_domiciliar',
            'Entrega sábado' => 'entrega_sabado',
            'Mão própria' => 'mao_propria',
            'Aviso de recebimento' => 'aviso_recebimento',
            'Seguro obrigatório' => 'seguro',
            'Formato' => 'formato',
            'Dimensões(cm) / Peso' => [
                'Dimensões(cm)' => 'dimensoes',
                'Peso' => 'peso'
            ],
            'Valor do frete' => 'frete',
            'Valor total' => 'total',
            'CEP' => 'cep',
            'Cidade/UF' => 'cidade',
            'Dimensões(cm)' => 'dimensoes',
            'Peso' => 'peso'
        ];
    }
}
