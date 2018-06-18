# Summary

Serializer is a core component of tnc-event-dispatcher, It helps to serilize Events to be transportable, Also unserilize Serialized-Events to be Event objects again.

Serializer is inspired by [Symfony Serializer](https://symfony.com/doc/current/components/serializer.html), It also includes two parts, Normalizer and Formatter, The workflow like this: 

![Workflow Of Serializer](https://symfony.com/doc/current/_images/serializer_workflow.png)

Normalizer takes care of transform Events to be a Array and the reverse, And Formatter is working on transform the Array to be a formatted string and the reverse, like Json, XML, ...

# Example

```php
<?php
namespace TNC\EventDispatcher;

# Specify Normalizers
$supportedNormalizers = [
    new Serialization\Normalizers\TNCActivityStreams\TNCActivityStreamsWrappedEventNormalizer(),
    new Serialization\Normalizers\TNCActivityStreams\TNCActivityStreamsNormalizer()
];

# Specify Serialization Format
$formatter  = new Serialization\Formatters\JsonFormatter();

# Initialize Serializer
$serializer = new Serializer($supportedNormalizers, $formatter);
```

# Normalizer

## Activity Normalizers

### Activity Streams

Activity Streams is a data format which defines a Activity, For more details please check their [Document](http://activitystrea.ms/specs/json/1.0/)

### TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\TNCActivityStreamsWrappedEventNormalizer

Norlimize a WrappedEvent to be a Activity Streams Array or the reverse

- Supported Normalization

$object instanceof WrappedEvent

- Supported Denormalization

$className == WrappedEvent::class

### Serialization\Normalizers\TNCActivityStreams\TNCActivityStreamsNormalizer

Norlimize a Event to be a Activity Streams Array or the reverse

- Supported Normalization

$object instanceof TNCActivityStreamsEvent

- Supported Denormalization

is_subclass_of($className, TNCActivityStreamsEvent::class)

# Formatter

## JSON Formatter

### TNC\EventDispatcher\Serialization\Formatters\JsonFormatter