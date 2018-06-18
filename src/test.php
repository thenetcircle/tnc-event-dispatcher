<?php
namespace TNC\EventDispatcher;

try {
    /**
     * Create a **Serializer**
     *
     * Serializer is using to serialize your event to be a string.
     * It includes a couple of Normalizers and a Formatter.
     */
    # Specify Normalizers
    $supportedNormalizers = [
        new Serialization\Normalizers\TNCActivityStreams\TNCActivityStreamsWrappedEventNormalizer(),
        new Serialization\Normalizers\TNCActivityStreams\TNCActivityStreamsNormalizer()
    ];

    # Specify Serialization Format
    $formatter  = new Serialization\Formatters\JsonFormatter();

    # Initialize Serializer
    $serializer = new Serializer($supportedNormalizers, $formatter);


    /**
     * 2. Initialize EndPoint
     *
     * If you are going to dispatch a async event, which needs a EndPoint to hold the request and send back to Receiver.
     * Mostly the EndPoint is a Queue, such as Redis, Rabbitmq, Kafka or our another project [EventBus] (https://github .com/thenetcircle/event-bus)
     */
    # Initialize EndPoint (use EventBusEndPoint as a example, could be RabbitMQEndPoint, RedisEndPoint, ...)
    $endPoint = new EndPoints\EventBusEndPoint('http://localhost:8000');


    /**
     * 3. Initialize tnc-event-dispatcher, And add some Listeners
     */
    $dispatcher = new Dispatchers\SymfonyImpl\EventDispatcher($serializer, $endPoint);
    # Suppose we have a Symfony Event Listener and a Event Subscriber here
    $dispatcher->addListener('message.send', new SymfonyEventListener());
    $dispatcher->addSubscriber(new SymfonyEventSubscriber());

    /**
     * 4. Dispatch Events
     */
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

/**
 * 5. Receiving and handling the AsyncEvent
 *
 * After the Event dispatched to the EndPoint, It will be delivered to the Receiver asynchronously.
 * Following are Receiver part, Which could be running on another PHP process
 */
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