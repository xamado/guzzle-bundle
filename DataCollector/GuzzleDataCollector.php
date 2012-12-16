<?php

namespace Xamado\GuzzleBundle\DataCollector;

use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Guzzle\Log\ArrayLogAdapter;

use Symfony\Component\DependencyInjection\SimpleXMLElement;

class GuzzleDataCollector extends DataCollector
{
    private $arrayLogAdapter;

    public function __construct(ArrayLogAdapter $arrayLogAdapter)
    {
        $this->arrayLogAdapter = $arrayLogAdapter;
    }

    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $logs = $this->arrayLogAdapter->getLogs();
        $requests = array();

        foreach ($logs as $log)
        {
            $requestLog = $this->getPrettyLog($log);
            $requests[] = $requestLog;
        }

        $this->data['requests'] = $requests;
    }

    public function getRequests()
    {
        return $this->data['requests'];
    }

    public function getName()
    {
        return 'guzzle';
    }

    private function getPrettyLog($log)
    {
        $requestLog = array();

        $requestLog['message'] = $log['message'];
        $requestLog['error'] = $log['extras']['response']->isError();

        // Parse and store the request headers and body
        $request = $log['extras']['request'];

        $requestLog['request_headers'] = $request->getRawHeaders();

        $requestBody = $request->getQuery(true);
        /*if($request->isContentType('json'))
            $requestBody = $this->getPrettyJson($requestBody);
        else if($request->isContentType('xml'))
            $requestBody = $this->getPrettyXml($requestBody);*/
        $requestLog['request_body'] = $requestBody;

        // Parse and store the response headers and body
        $response = $log['extras']['response'];

        $requestLog['response_headers'] = $response->getRawHeaders();

        $responseBody = $response->getBody(true);
        if($response->isContentType('json'))
            $responseBody = $this->getPrettyJson($responseBody);
        else if($response->isContentType('xml'))
            $responseBody = $this->getPrettyXml($responseBody);
        $requestLog['response_body'] = $responseBody;

        return $requestLog;
    }

    /*
     * Credits: http://www.php.net/manual/en/function.json-encode.php#80339
     */
    private function getPrettyJson($json)
    {
        $tab = "  ";
        $new_json = "";
        $indent_level = 0;
        $in_string = false;

        $json_obj = json_decode($json);

        if($json_obj === false)
            return false;

        $json = json_encode($json_obj);
        $len = strlen($json);

        for($c = 0; $c < $len; $c++)
        {
            $char = $json[$c];
            switch($char)
            {
                case '{':
                case '[':
                    if(!$in_string)
                    {
                        $new_json .= $char . "\n" . str_repeat($tab, $indent_level+1);
                        $indent_level++;
                    }
                    else
                    {
                        $new_json .= $char;
                    }
                    break;
                case '}':
                case ']':
                    if(!$in_string)
                    {
                        $indent_level--;
                        $new_json .= "\n" . str_repeat($tab, $indent_level) . $char;
                    }
                    else
                    {
                        $new_json .= $char;
                    }
                    break;
                case ',':
                    if(!$in_string)
                    {
                        $new_json .= ",\n" . str_repeat($tab, $indent_level);
                    }
                    else
                    {
                        $new_json .= $char;
                    }
                    break;
                case ':':
                    if(!$in_string)
                    {
                        $new_json .= ": ";
                    }
                    else
                    {
                        $new_json .= $char;
                    }
                    break;
                case '"':
                    if($c > 0 && $json[$c-1] != '\\')
                    {
                        $in_string = !$in_string;
                    }
                default:
                    $new_json .= $char;
                    break;
            }
        }

        return $new_json;
    }

    /*
     * Credits: http://gdatatips.blogspot.com.ar/2008/11/xml-php-pretty-printer.html
     */
    private function getPrettyXml($xml, $html_output=false)
    {
        $xml_obj = new SimpleXMLElement($xml);
        $level = 4;
        $indent = 0; // current indentation level
        $pretty = array();

        // get an array containing each XML element
        $xml = explode("\n", preg_replace('/>\s*</', ">\n<", $xml_obj->asXML()));

        // shift off opening XML tag if present
        if (count($xml) && preg_match('/^<\?\s*xml/', $xml[0])) {
            $pretty[] = array_shift($xml);
        }

        foreach ($xml as $el) {
            if (preg_match('/^<([\w])+[^>\/]*>$/U', $el)) {
                // opening tag, increase indent
                $pretty[] = str_repeat(' ', $indent) . $el;
                $indent += $level;
            } else {
                if (preg_match('/^<\/.+>$/', $el)) {
                    $indent -= $level;  // closing tag, decrease indent
                }
                if ($indent < 0) {
                    $indent += $level;
                }
                $pretty[] = str_repeat(' ', $indent) . $el;
            }
        }
        $xml = implode("\n", $pretty);
        return ($html_output) ? htmlentities($xml) : $xml;
    }
}