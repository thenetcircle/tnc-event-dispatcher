# TNC EventDispatcher:

TNC EventDispatcher is a enhanced version of [Symfony EventDispatcher](https://symfony.com/doc/current/components/event_dispatcher.html), It works as same as Symfony EventDispatcher, just with asynchronous dispatching and receiving ability. It covers all Symfony EventDispatcher functions (like ContainerAware, Traceable etc...).

The Workflow like this:

![Workflow](http://gitlab.thenetcircle.lab/service-team/service-eventdispatcher/uploads/ae847041f3bb46d379c98701f9ada076/EventDispatcher_Diagram.png)

## Installation

### Requirements

- PHP 5.6+

### Use Composer

add following code in your project's composer.json

```json
"repositories": [
    {
      "type": "git",
      "url": "gitlab@gitlab.thenetcircle.lab:service-team/service-eventdispatcher.git"
    }
],
"require": {
    "tnc/event-dispatcher": "v2.x-dev" # notice: use the released version for prod
}
```

update project

```json
composer update
```

## Usage

### In Symfony

- Create a Serializer (which takes care of how to serialize your event)

```php
<?php
use TNC\EventDispatcher\Serializer;
use TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\TNCActivityStreamsWrappedEventNormalizer;
use TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\TNCActivityStreamsNormalizer;
use TNC\EventDispatcher\Serialization\Formatters\JsonFormatter;

# you can define your own Normalizer which just implements the TNC\EventDispatcher\Serialization\Normalizer interface. 
$supportedNormalizers = [
  new TNCActivityStreamsWrappedEventNormalizer(),
  new TNCActivityStreamsNormalizer()
];

# you can define your own Formatter which just implements the TNC\EventDispatcher\Serialization\Formatter interface.
$formatter = new JsonFormatter();

$serializer = new Serializer($supportedNormalizers, $formatter);
```

- Create a EndPoint (for where the event will send to, for example: Redis, Rabbitmq, EventBus)

```php
<?php
use TNC\EventDispatcher\EndPoints\EventBusEndPoint;

$endPoint = new EventBusEndPoint('http://localhost:8000'); 
```

- Now we can create the Dispatcher and attach some listeners.

```php
<?php
use TNC\EventDispatcher\Dispatchers\SymfonyImpl\EventDispatcher;

$dispatcher = new EventDispatcher($serializer, $endpoint);
$dispatcher->addListener(AsyncEvent::NAME, new testListener());
$dispatcher->addSubscriber(new testSubscriber());
```

- Define a Event
  
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

- Then we dispatch the Event

```php
<?php
$dispatcher->dispatch(
  AsyncEvent::NAME, 
  new AsyncEvent([
    'messageId' => '1',
    'messageBody' => 'abc'
  ])
);
```

- After the Event dispatched to the EndPoint, It will come back to us, We use Receiver to accpet it and dispatching to Listeners.

```php
<?php
use TNC\EventDispatcher\Receivers\EventBusReceiver;

# For EventBus Receiver we use HTTP to accept callbacks, For other Receiver implementations, it could be running in a long-term PHP process.
$receiver = new EventBusReceiver();
# $request is Psr Request, which may come from your framework.  
$receiver->newRequest($request);
```

## Support

Benn<benn@thenetcircle.com> from Service Team

## Contributing
Feedbacks and pull requests are welcome and appreciative. You can contact me by mail or slack or open a issue.   
For major changes, please open an issue first to discuss what you would like to change.