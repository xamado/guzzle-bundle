<?php

namespace Xamado\GuzzleBundle\Service\Command;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Guzzle\Common\Event;

use Xamado\GuzzleBundle\Service\Command\SerializerResponseParser;

class CommandListener implements EventSubscriberInterface
{
    private $responseParser;

    public function __construct(SerializerResponseParser $responseParser)
    {
        $this->responseParser = $responseParser;
    }

    public static function getSubscribedEvents()
    {
        return array(
            'client.command.create' => 'onCommandCreate'
        );
    }

    public function onCommandCreate(Event $event)
    {
        $command = $event['command'];

        $command->setResponseParser($this->responseParser);
    }
}