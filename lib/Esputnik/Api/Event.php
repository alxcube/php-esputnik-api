<?php

namespace Esputnik\Api;

use Esputnik\Model\Event as EventModel;
use Esputnik\Model\EventParam;

class Event extends AbstractApi
{
    /**
     * @param EventModel $event
     * @return \Psr\Http\Message\StreamInterface
     */
    public function trigger(EventModel $event)
    {
        return $this->post('event', [
            'eventTypeKey' => $event->getEventTypeKey(),
            'keyValue' => $event->getKeyValue(),
            'params' => $this->serializeParamsArray($event->getParams())
        ]);
    }

    /**
     * @param EventParam[] $params
     * @return array
     */
    protected function serializeParamsArray(array $params)
    {
        return array_map(function ($param) {
            /**
             * @var EventParam $param
             */
            return [
                'name' => $param->getName(),
                'value' => $param->getValue()
            ];
        }, $params);
    }
}
