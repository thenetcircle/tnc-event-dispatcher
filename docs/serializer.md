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

## ActivityStreams Normalizers

Activity Streams is a data format which defines a action, an activity consists of an actor, a verb, an an object, and a target. It tells the story of a person performing an action on or with an object. For more details please check their [Document](http://activitystrea.ms/specs/json/1.0/)  
TNC EventDispatcher internally providers [ActivityStreams](http://activitystrea.ms/specs/json/1.0/) Normalizers implementation.

### Conventions

- EventName
  - Use only lowercase letters, numbers, dots (.) and underscores (_);
  - Prefix names with a namespace followed by a dot (e.g. order., user.*);
  - End names with a verb that indicates what action it is (e.g. user.login, payment.subscribe).

### Classes

- **TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\TNCActivityStreamsWrappedEventNormalizer** 
  Norlimize a WrappedEvent to be a Activity Streams Array or the reverse

| Supported Normalization | Supported Denormalization |
|-------------------------|---------------------------|
| $object instanceof WrappedEvent | $className == WrappedEvent::class |

- **Serialization\Normalizers\TNCActivityStreams\TNCActivityStreamsNormalizer**  
  Norlimize a Event to be a Activity Streams Array or the reverse

| Supported Normalization | Supported Denormalization |
|-------------------------|---------------------------|
| $object instanceof TNCActivityStreamsEvent | is_subclass_of($className, TNCActivityStreamsEvent::class) |

### Usage

In tnc-event-dispatcher, ActivityStreams is supported by TNCActivityStreamsNormalizer.

TNCActivityStreamsNormalizer only supports object which implements TNCActivityStreamsEvent interface(see above), If you check the source code of the Normalizer, it looks like this:

```php
<?php
class TNCActivityStreamsNormalizer {
    
# ...    

    public function supportsNormalization($object)
    {
        return ($object instanceof TNCActivityStreamsEvent);
    }
    
    public function supportsDenormalization($data, $className)
    {
        if (!class_exists($className)) {
            return false;
        }

        return is_subclass_of($className, TNCActivityStreamsEvent::class);
    }
    
# ...        

}
```

So a proper Event which wants to be normalized as ActivityStreams format, it have to implement TNCActivityStreamsEvent interface.

```php
<?php
class TestEvent implements TNCActivityStreamsEvent
{
    public function getTransportMode()
    
    public function normalize(ActivityBuilderInterface $builder)
    
    public function denormalize(Activity $activity)
}
```

When TNCActivityStreamsNormalizer do normalize, it will call the "normalize" method of the object, with a ActivityBuilderInterface implementation(default is DefaultActivityBuilder, can be changed as a constructor parameter of TNCActivityStreamsNormalizer).
When do denormalize, it will call the "denormalize" method with a Activity instance(without call constructor), you can restore the Event here.

```php
<?php
$normalizer = new TNCActivityStreamsNormalizer();
$event = new TestEvent();

$normalizedData = $normalizer->normalize($event);
$restoredEvent = $normalizer->denormalize($normalizedData, TestEvent::class);
```

#### DefaultActivityBuilder

TNCActivityStreamsNormalizer come with a default ActivityBuilderInterface implementation which is DefaultActivityBuilder, it can be used to create a Activity.   
For more usage, visit the test cases.

```php
<?php
$builder = new DefaultActivityBuilder();

# fill the Activity by array
$builder->setFromArray([
   'actor' => [
     'objectType' => 'type2',
     'id' => 'id4',
     'content' => 'content',
     'attachments' => [ # the rule of attachments is as same as ActivityObject
       ['subtype1', 'subid1'],
       'subid2',
       [
         'objectType' => 'subtype3',
         'id' => 'subid3'
       ]
     ]
   ]
 ]);

# use methods
$builder->setId('123');
$builder->setVerb('message.send');
$builder->setPublished('now');
$builder->setActor((new ActivityObject())->setId('123'));
...

# get the built Activity
$builder->getActivity();
```


### User Cases

#### Case1: benn logged in 

- Event

I choose "user.login" as the EventName, Which is following the convention "namespace.verb"

Example Code:

```php
<?php
class UserLoginEvent implements TNCActivityStreamsEvent
{
    const NAME = "user.login";
    
    private $user = null;
    
    public function __constructor(User $user) { $this->user = $user; }
    
    public function getTransportMode() { return self::TRANSPORT_MODE_ASYNC; }
    
    /**
     * @param \TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\DefaultActivityBuilder $builder 
     */
    public function normalize($builder)
    {
        $builder->setActor(
          (new ActivityObject())->setObjectType('user')->setId($this->user->getId())
        );
        return $builder->getActivity();
    }
    
    /**
     * @param \TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\Impl\Activity $activity 
     */
    public function denormalize($activity) 
    {
        $this->user = UsersRepository::find($activity->getActor()->getId());
    }
}

$dispatcher->dispatch(UserLoginEvent::NAME, new UserLoginEvent($user));
```

- Formatted Data (ActivityStreams)

<table>
  <tr>
    <th>title</th>
    <td>user.login</td>
  </tr>
  <tr>
    <th>verb</th>
    <td>login</td>
  </tr>
  <tr>
    <th>actor</th>
    <td>{ objectType: "user", id: 123456 }</td>
  </tr>    
  <tr>
    <th>id</th>
    <td>Generated Unique String</td>
  </tr>
  <tr>
    <th>published</th>
    <td>2017-10-13T11:31:34+08:00</td>
  </tr>
  <tr>
    <th>provider</th>
    <td>{ objectType: "community", id: "Poppen" }</td>
  </tr>
  <tr>
    <th>generator</th>
    <td>{ id: "tnc-event-dispatcher", content: { mode: "async", class: "UserLoginEvent" } }</td>
  </tr>
</table>

#### Case2: benn visited __fan's profile__ 

Example Code:

```php
<?php
class ProfileVisitEvent implements TNCActivityStreamsEvent
{
    const NAME = "user.visit";
    
    private $visitor = null;
    private $target = null;
    
    public function __constructor(User $visitor, User $target) { 
        $this->visitor = $visitor;
        $this->target = $target;
    }
    
    public function getTransportMode() { return self::TRANSPORT_MODE_ASYNC; }
    
    /**
     * @param \TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\DefaultActivityBuilder $builder 
     */
    public function normalize($builder)
    {
        $builder->setFromArray(
          [
            'actor' => ['user', $this->visitor->getId()],
            'object' => ['profile', $this->target->getName()]
          ]
        );
        return $builder->getActivity();
    }
    
    /**
     * @param \TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\Impl\Activity $activity 
     */
    public function denormalize($activity) 
    {
        $this->visitor = UsersRepository::find($activity->getActor()->getId());
        $this->target = UsersRepository::findByName($activity->getObject()->getId());
    }
}

$dispatcher->dispatch(ProfileVisitEvent::NAME, new ProfileVisitEvent($visitor, $target));
```

- Formatted Data (ActivityStreams)

<table>
  <tr>
    <th>title</th>
    <td>user.visit</td>
  </tr>
  <tr>
    <th>verb</th>
    <td>visit</td>
  </tr>
  <tr>
    <th>actor</th>
    <td>{ objectType: "user", id: 12345 }</td>
  </tr>
  <tr>
    <th>object</th>
    <td>{ objectType: "profile", id: "fan" }</td>
  </tr>
  <tr>
    <th>id</th>
    <td>Generated Unique String</td>
  </tr>
  <tr>
    <th>published</th>
    <td>2017-10-13T11:31:34+08:00</td>
  </tr>
  <tr>
    <th>provider</th>
    <td>{ objectType: "community", id: "Poppen" }</td>
  </tr>
  <tr>
    <th>generator</th>
    <td>{ id: "tnc-event-dispatcher", content: { mode: "async", class: "UserLoginEvent" } }</td>
  </tr>
</table>


#### Case3: benn sent a message __to__ leo 


Example Code:

```php
<?php
class MessageSendEvent implements TNCActivityStreamsEvent
{
    const NAME = "message.send";
    
    private $sender = null;
    private $receiver = null;
    private $message = null;
    
    public function __constructor(User $sender, Message $message, User $receiver) { 
        $this->sender = $sender;
        $this->message = $message;
        $this->receiver = $receiver;
    }
    
    public function getTransportMode() { return self::TRANSPORT_MODE_ASYNC; }
    
    /**
     * @param \TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\DefaultActivityBuilder $builder 
     */
    public function normalize($builder)
    {
        $builder->setFromArray(
          [
            'actor' => ['user', $this->visitor->getId()],
            'object' => ['message', $this->message->getId()],
            'target' => ['user', $this->receiver->getId()]
          ]
        );
        return $builder->getActivity();
    }
    
    /**
     * @param \TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\Impl\Activity $activity 
     */
    public function denormalize($activity) 
    {
        $this->sender = UsersRepository::find($activity->getActor()->getId());
        $this->message = UsersRepository::find($activity->getObject()->getId());
        $this->receiver = UsersRepository::find($activity->getTarget()->getId());
    }
}

$dispatcher->dispatch(MessageSendEvent::NAME, new MessageSendEvent($visitor, $target));
```

- Formatted Data (ActivityStreams)

<table>
  <tr>
    <th>title</th>
    <td>message.send</td>
  </tr>
  <tr>
    <th>verb</th>
    <td>send</td>
  </tr>
  <tr>
    <th>actor</th>
    <td>{ objectType: "user", id: 12345 }</td>
  </tr>
  <tr>
    <th>object</th>
    <td>{ objectType: "message", id: 112231 }</td>
  </tr>
  <tr>
      <th>target</th>
      <td>{ objectType: "user", id: 88929 }</td>
    </tr>
  <tr>
    <th>id</th>
    <td>Generated Unique String</td>
  </tr>
  <tr>
    <th>published</th>
    <td>2017-10-13T11:31:34+08:00</td>
  </tr>
  <tr>
    <th>provider</th>
    <td>{ objectType: "community", id: "Poppen" }</td>
  </tr>
  <tr>
    <th>generator</th>
    <td>{ id: "tnc-event-dispatcher", content: { mode: "async", class: "UserLoginEvent" } }</td>
  </tr>
</table>

# Formatter

## JSON Formatter

### TNC\EventDispatcher\Serialization\Formatters\JsonFormatter