# Installation

## Requirements

- PHP 5.6+

## From Composer

```sh
> composer install tnc/event-dispatcher
```

# Usage

- Create a **Serializer**

**Serializer** is using to serialize your event to be a string.  
It includes a couple of **Normalizer**s and a **Formatter**.

```php
<?php
use TNC\EventDispatcher\Serializer;
use TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\TNCActivityStreamsWrappedEventNormalizer;
use TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\TNCActivityStreamsNormalizer;
use TNC\EventDispatcher\Serialization\Formatters\JsonFormatter;

# here we are using two internal Normalizers
# you can define your own Normalizer which just implements the TNC\EventDispatcher\Serialization\Normalizer interface. 
$supportedNormalizers = [
  new TNCActivityStreamsWrappedEventNormalizer(),
  new TNCActivityStreamsNormalizer()
];

# you can define your own Formatter which just implements the TNC\EventDispatcher\Serialization\Formatter interface.
$formatter = new JsonFormatter();

$serializer = new Serializer($supportedNormalizers, $formatter);
```

- Create a **EndPoint** 

If you are going to dispatch a async event, which needs a **EndPoint** to hold the request and send back to **Receiver**.  
Mostly the **EndPoint** is a Queue, such as Redis, Rabbitmq, Kafka or our another project [EventBus](https://github.com/thenetcircle/event-bus)

```php
<?php
use TNC\EventDispatcher\EndPoints\EventBusEndPoint;

$endPoint = new EventBusEndPoint('http://localhost:8000'); 
```

- Initialize **tnc-event-dispatcher** and attach some listeners.

```php
<?php
use TNC\EventDispatcher\Dispatchers\SymfonyImpl\EventDispatcher;

$dispatcher = new EventDispatcher($serializer, $endPoint);
$dispatcher->addListener(AsyncEvent::NAME, new testListener());
$dispatcher->addSubscriber(new testSubscriber());
```

## Define a Event
  
TNC EventDispatcher supports normal Event and async Event, Since normal Event works as same as Symfony EventDispatcher, We just use a async Event for example here. 

```php
<?php
use Symfony\Component\EventDispatcher\Event;
use TNC\EventDispatcher\Interfaces\Event\TNCActivityStreamsEvent;

/**
 * AsyncEvent will be send to the EndPoint according to the serialization strategy
 *
 * For here we implements TNCActivityStreamsEvent, And we defined TNCActivityStreamsNormalizer, Which will normalizes
 * this event to be ActivityStreams format.
 */
class AsyncEvent extends Event implements TNCActivityStreamsEvent
{
    const NAME = 'message.send';

    private $data = [];

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Returns transport mode of this event
     *
     * It supports one of these:
     *  - "sync"      works as same as origin event, the event will be dispatched to listeners directly.
     *  - "sync_plus" after the event dispatched to local listeners, it will be sent to the EndPoint for other remote
     *                listeners as well, but it will not be dispatch again if the receiver got it.
     *  - "async"     the event will be sent to EndPoint only, and after receiver got it, will be dispatched to
     *                listeners.
     *
     * @see \TNC\EventDispatcher\Interfaces\Event\TransportableEvent
     *
     * @return string
     */
    public function getTransportMode()
    {
        return self::TRANSPORT_MODE_ASYNC;
    }

    /**
     * Normalizes a Event to be a Activity
     *
     * @param \TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\DefaultActivityBuilder $builder
     *
     * @return \TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\Impl\Activity
     *
     * @throws NormalizeException
     */
    public function normalize($builder)
    {
        $builder->setFromArray([
          'id' => $this->data['messageId'],
          'content' => $this->data
        ]);
        return $builder->getActivity();
    }

    /**
     * Denormalizes a Acitivty to be a Event
     *
     * @param \TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\Impl\Activity $activity
     *
     * @throws DenormalizeException
     */
    public function denormalize($activity)
    {
        $this->data = $activity->getContent();
    }
}
```

## Then we dispatch the Event

```php
<?php
try {
    $dispatcher->dispatch(
      AsyncEvent::NAME, 
      new AsyncEvent([
        'messageId' => '1',
        'messageBody' => 'abc'
      ])
    );
}
catch (\Exception $e) {
    // Handling Exception
}
```

## Handler the async event

After the Event dispatched to the EndPoint, It will be deliveried to the Receiver asynchronously.
Following are Receiver part, Which could be running on another PHP process

```php
<?php
try {
    # Initialize a Receiver(use EventBusReceiver as a example here, could be RabbitMQReceiver, RedisReceiver, ...)
    $receiver = new Receivers\EventBusReceiver();
    # Set Dispatcher we defined before
    $receiver->withDispatcher($dispatcher);
    # Dispatch the serliazed-event we received from the EndPoint
    $receiver->dispatch($serliazedEvent);
}
catch (\Exception $e) {
    // Handling Exception
}
```