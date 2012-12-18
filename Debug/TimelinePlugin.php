<?php

namespace Xamado\GuzzleBundle\Debug;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Debug\Stopwatch;

use Guzzle\Common\Event;

class TimelinePlugin implements EventSubscriberInterface
{
    private $stopwatch;

    public function __construct(Stopwatch $stopwatch = null)
    {
        $this->stopwatch = $stopwatch;
    }

    public static function getSubscribedEvents()
    {
        return array(
            'request.before_send' => array('onRequestBeforeSend', 0),
            'request.complete' => array('onRequestComplete', 255)
        );
    }

    public function onRequestBeforeSend(Event $event)
    {
        if($this->stopwatch == null)
            return;

        $name = (string) $event['request']->getPath();
        $this->stopwatch->start('guzzle ('.$name.')');
    }

    public function onRequestComplete(Event $event)
    {
        if($this->stopwatch == null)
            return;

        $name = (string) $event['request']->getPath();
        $this->stopwatch->stop('guzzle ('.$name.')');
    }
}