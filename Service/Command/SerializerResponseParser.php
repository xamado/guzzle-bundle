<?php

namespace Xamado\GuzzleBundle\Service\Command;

use Guzzle\Http\Message\Response;
use Guzzle\Service\Command\ResponseParserInterface;
use Guzzle\Service\Command\AbstractCommand;
use Guzzle\Service\Command\CommandInterface;
use JMS\Serializer\Serializer;

/**
 * Serializer response parser that serializes responses into an Entity
 */
class SerializerResponseParser implements ResponseParserInterface
{
    private $serializableTypes = array(
        'application/json' => 'json',
        'application/xml' => 'xml'
    );

    private $serializer;

    public function __construct(Serializer $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * {@inheritdoc}
     */
    public function parse(CommandInterface $command)
    {
        $response = $command->getRequest()->getResponse();

        // Account for hard coded content-type values specified in service descriptions
        if ($contentType = $command->get('command.expects')) {
            $response->setHeader('Content-Type', $contentType);
        } else {
            $contentType = (string) $response->getHeader('Content-Type');
        }

        return $this->parseForContentType($command, $response, $contentType);
    }

    /**
     * {@inheritdoc}
     */
    public function parseForContentType(AbstractCommand $command, Response $response, $contentType)
    {
        $serializerType = $this->serializableTypes[$contentType];

        $body = $response->getBody();
        $class = $command->getOperation()->getResponseClass();

        $result = $this->serializer->deserialize($body, $class, $serializerType);

        return $result;
    }
}
