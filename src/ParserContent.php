<?php

namespace CorreiosParser;

class ParserContent
{
    public function __construct($content)
    {
        $this->doc = new \DOMDocument();
        $this->doc->loadHTML($content);
        $this->xpath = new \DOMXPath($this->doc);
    }

    public function getParsedContent()
    {
        $nodes = $this->xpath->query("//*[contains(@class, 'caixacampoazul') or contains(@class, 'subsecao')]");
        $items = [];
        /** @var \DOMNode $node */
        foreach ($nodes as $node) {
            if ($node->getAttribute('class') == 'caixacampoazul') {
                $items[] = $this->getData($node);
            }

            if ($node->getAttribute('class') == 'subsecao') {
                $subSectionName = strtolower($node->nodeValue);
                $subSectionNode = $node->parentNode->parentNode->childNodes;

                $idx = 7;
                if ($subSectionName == 'origem') {
                    $idx = 3;
                }

                $item = $this->getData($subSectionNode->item($idx));
                $items[][$subSectionName] = $item;
                $item = $this->getAdditionalData($subSectionNode->item($idx));
                $items[][strtolower($node->nodeValue)] = $item;
            }
        }
        return $items;
    }

    private function getData(\DOMNode $node)
    {
        $item = [];
        $index = 1;
        if ($node->childNodes->item($index)->hasAttributes()) {
            if (trim($node->childNodes->item($index)->getAttribute('class')) == 'resposta') {
                $key = trim($node->childNodes->item($index)->nodeValue);
                $item['key'] = $this->clean($key);
            }
        }

        $maxTry = 3;
        while ($maxTry-- > 0) {
            $index++;
            if (null !== $node->childNodes->item($index) && $node->childNodes->item($index)->hasAttributes()) {
                if (trim($node->childNodes->item($index)->getAttribute('class')) == 'respostadestaque') {
                    $item['value'] = $this->clean($node->childNodes->item($index)->nodeValue);
                }
            }
        }

        return $item;
    }

    private function getAdditionalData(\DOMNode $node)
    {
        $item = [];
        $nextId = 4;
        if ($node->childNodes->item($nextId)->hasAttributes()) {
            if (trim($node->childNodes->item($nextId)->getAttribute('class')) == 'resposta') {
                $key = trim($node->childNodes->item($nextId)->nodeValue);
                $item['key'] = str_replace(':', '', $key);
            }
        }

        $nextId = 5;
        if ($node->childNodes->item($nextId)->hasAttributes()) {
            if (trim($node->childNodes->item($nextId)->getAttribute('class')) == 'respostadestaque') {
                $item['value'] = trim($node->childNodes->item($nextId)->nodeValue);
            }
        }

        return $item;
    }

    private function clean($str)
    {
        $str = str_replace([':', "\r\n", "\n", "\r", "\t", "\0", "\x0B"], '', $str);
        $str = trim(preg_replace('/\s+/', ' ', $str));
        return $str;
    }
}
