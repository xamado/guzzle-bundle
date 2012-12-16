<?php

namespace Xamado\GuzzleBundle\DataCollector;

use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GuzzleDataCollector extends DataCollector
{
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $this->data = array(
            'guzzle' => 'test'
        );
    }

    public function getGuzzle()
    {
        return $this->data['guzzle'];
    }

    public function getName()
    {
        return 'guzzle';
    }
}