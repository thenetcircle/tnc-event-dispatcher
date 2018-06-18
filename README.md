# TNC EventDispatcher

[![Build Status](https://travis-ci.org/thenetcircle/tnc-event-dispatcher.svg?branch=master)](https://travis-ci.org/thenetcircle/tnc-event-dispatcher)

TNC EventDispatcher is a alternative of [Symfony event-dispatcher](https://symfony.com/doc/current/components/event_dispatcher.html) for supporting asynchronous and unified structured events.  
It works as same as Symfony event-dispatcher, has same interface and a few more options, can replace Symfony event-dispatcher seamlessly.

<a href="https://thenetcircle.github.io/tnc-event-dispatcher/assets/tnc_event_dispatcher_workflow.png" target="_blank">![Workflow](https://thenetcircle.github.io/tnc-event-dispatcher/assets/tnc_event_dispatcher_workflow.png)</a>

For more details please check the [Document](https://thenetcircle.github.io/tnc-event-dispatcher), or go through [Quick Start](https://thenetcircle.github.io/tnc-event-dispatcher/quickstart) for a quick overview.

## Usage

```php
<?php
namespace TNC\EventDispatcher;

try {
    # Specify Normalizers
    $supportedNormalizers = [
        new Serialization\Normalizers\TNCActivityStreams\TNCActivityStreamsWrappedEventNormalizer(),
        new Serialization\Normalizers\TNCActivityStreams\TNCActivityStreamsNormalizer()
    ];

    # Specify Serialized Format
    $formatter  = new Serialization\Formatters\JsonFormatter();

    # Initialize Serialize
    $serializer = new Serializer($supportedNormalizers, $formatter);


    # Initialize EndPoint (use EventBusEndPoint as a example, could be RabbitMQEndPoint, RedisEndPoint, ...)
    $endPoint = new EndPoints\EventBusEndPoint('http://localhost:8000');


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
}
catch (\Exception $e) {
    // Handling Exception
}

# Following are Receiver part, Which could be running on another PHP process
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

## Contributing
Feedbacks and pull requests are welcome and appreciative. You can contact me by mail or slack or open a issue.   
For major changes, please open an issue first to discuss what you would like to change.

## Change Logs
[Click to check Change Logs](https://thenetcircle.github.io/tnc-event-dispatcher/change_logs)