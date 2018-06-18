# Summary

Dispatcher is the user interface of tnc-event-dispatcher, It holds Serializer and EndPoint, binds Listeners, accepts Event and dispatches them to corresponding Listeners or EndPoint.  
**tnc-event-dispatcher** is gonna to have multiple Dispatcher implementations to adapte different frameworks. Currently only Symfony Event Dispatcher is implemented. 

# Symfony Event Dispatcher

Symfony Event Dispatcher Implementation intends to replace the original symfony-event-dispatcher seamlessly.  
They have similar user interface, Just some more extra options.

## Define a Event

For Event port, The original Events are fully supported without any changes. Just define the event and dispatch it.  
But there are some more event types tnc-event-dispatcher supports. They are:

- sync  
  works as same as origin event, the event will be dispatched to listeners directly.
- sync_plus   
  same as "sync" mode, except that after the event has been dispatched to local listeners, i will also send to EndPoint for remote listeners, but it will not be dispatched to local listeners again.
- async  
  the event will be sent to EndPoint only, and after the receiver got it, will be dispatched to listeners.
- both  
  the event will be dispatched to local listeners who are listening on the event name, and then send to EndPoint as well, after the receiver got it, will be dispatched to listeners who are listening on "$eventName.async"
  
 Let's define a AsyncEvent, Which is as same as Symfony Event with implements TransportableEvent interface(have to implement getTransportMode method to specify the type of the Event).

```php
<?php
use Symfony\Component\EventDispatcher\Event;
use TNC\EventDispatcher\Interfaces\Event\TransportableEvent;

/**
 * AsyncEvent will be send to the EndPoint
 */
class AsyncEvent extends Event implements TransportableEvent
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
     *
     *  - "sync"      works as same as origin event, the event will be dispatched to listeners directly.
     *
     *  - "sync_plus" same as "sync" mode, except that after the event has been dispatched to local listeners, i will
     *                also send to EndPoint for remote listeners, but it will not be dispatched to local listeners
     *                again.
     *
     *  - "async"     the event will be sent to EndPoint only, and after the receiver got it, will be dispatched to
     *                listeners.
     *
     *  - "both"      the event will be dispatched to local listeners who are listening on the event name, and then
     *                send to EndPoint as well, after the receiver got it, will be dispatched to listeners who are
     *                listening on "$eventName.async"
     *
     * @see \TNC\EventDispatcher\Interfaces\Event\TransportableEvent
     *
     * @return string
     */
    public function getTransportMode()
    {
        // TODO: Implement getTransportMode() method.
    }
}
```

## Dispatch the Event

Dispatcher has totally same interface of symfony-event-dispatcher

```php
<?php
# Initialize tnc-event-dispatcher
$dispatcher = new Dispatchers\SymfonyImpl\EventDispatcher($serializer, $endPoint);

# Suppose we have a Symfony Event Listener and a Event Subscriber here
$dispatcher->addListener('message.send', new SymfonyEventListener());
$dispatcher->addSubscriber(new SymfonyEventSubscriber());

# Dispatch a event 'message.send', the event will be sent to the EndPoint
$dispatcher->dispatch(
    'message.send',
    new AsyncEvent(
        [
            'messageId'   => '1',
            'messageBody' => 'abc'
        ]
    )
);
```