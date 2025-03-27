<?php

class SpoonXMLRPCClient
{
    private string $url;
    private array $options;

    public function __construct(string $url, array $options = [])
    {
        $this->url = $url;
        $this->options = array_merge([
            'timeout' => 10,
        ], $options);
    }

    public function execute(string $method, array $parameters = []): mixed
    {
        $requestXML = $this->buildRequestXML($method, $parameters);
        $ch = curl_init($this->url);
        
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $this->options['timeout'],
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $requestXML,
            CURLOPT_HTTPHEADER => [
                'Content-Type: text/xml',
                'Content-Length: ' . strlen($requestXML)
            ]
        ]);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            throw new Exception('cURL Error: ' . $error);
        }

        return $this->decodeResponseXML($response);
    }

    private function buildRequestXML(string $method, array $parameters): string
    {
        $xml = new SimpleXMLElement('<methodCall/>');
        $xml->addChild('methodName', $method);
        $params = $xml->addChild('params');

        foreach ($parameters as $param) {
            $paramElement = $params->addChild('param');
            $paramElement->addChild('value', $this->buildValueXML($param));
        }

        return $xml->asXML();
    }

    private function buildValueXML($parameter): string
    {
        if (is_int($parameter)) {
            return '<value><int>' . $parameter . '</int></value>';
        } elseif (is_bool($parameter)) {
            return '<value><boolean>' . ($parameter ? '1' : '0') . '</boolean></value>';
        } elseif (is_double($parameter)) {
            return '<value><double>' . $parameter . '</double></value>';
        } elseif (is_string($parameter)) {
            return '<value><string>' . htmlspecialchars($parameter, ENT_XML1, 'UTF-8') . '</string></value>';
        } elseif (is_array($parameter)) {
            if (array_keys($parameter) === range(0, count($parameter) - 1)) {
                $xml = '<value><array><data>';
                foreach ($parameter as $item) {
                    $xml .= $this->buildValueXML($item);
                }
                return $xml . '</data></array></value>';
            } else {
                $xml = '<value><struct>';
                foreach ($parameter as $key => $item) {
                    $xml .= '<member><name>' . htmlspecialchars($key, ENT_XML1, 'UTF-8') . '</name>' . $this->buildValueXML($item) . '</member>';
                }
                return $xml . '</struct></value>';
            }
        }
        throw new Exception('Unsupported data type');
    }

    private function decodeResponseXML(string $xml): mixed
    {
        $response = new SimpleXMLElement($xml);
        
        if (isset($response->fault)) {
            throw new Exception('XML-RPC Fault: ' . $this->decodeValue($response->fault->value));
        }

        return $this->decodeValue($response->params->param->value);
    }

    private function decodeValue($value): mixed
    {
        if (isset($value->int) || isset($value->i4)) {
            return (int) ($value->int ?? $value->i4);
        } elseif (isset($value->boolean)) {
            return (bool) ((string) $value->boolean === '1');
        } elseif (isset($value->double)) {
            return (float) $value->double;
        } elseif (isset($value->string)) {
            return (string) $value->string;
        } elseif (isset($value->array)) {
            $result = [];
            foreach ($value->array->data->value as $item) {
                $result[] = $this->decodeValue($item);
            }
            return $result;
        } elseif (isset($value->struct)) {
            $result = [];
            foreach ($value->struct->member as $member) {
                $key = (string) $member->name;
                $result[$key] = $this->decodeValue($member->value);
            }
            return $result;
        }
        throw new Exception('Unsupported XML-RPC value type');
    }
}

?>