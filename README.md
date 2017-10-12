# TNC EventDispatcher:

TNC EventDispatcher is a enhanced version of [Symfony EventDispatcher](https://symfony.com/doc/current/components/event_dispatcher.html), It works as same as Symfony EventDispatcher, just with asynchronous dispatching and receiving ability. It covers all Symfony EventDispatcher functions (like ContainerAware, Traceable etc...).

The Workflow like this:

![Workflow](http://gitlab.thenetcircle.lab/service-team/service-eventdispatcher/uploads/ae847041f3bb46d379c98701f9ada076/EventDispatcher_Diagram.png)

Visit the [Wiki](http://gitlab/service-team/service-eventdispatcher/wikis/home) for more details, There is also a [Quick Start](http://gitlab/service-team/service-eventdispatcher/wikis/quickstart) for you to have a quick understanding of usage.
You may also need to know [Serialization](http://gitlab/service-team/service-eventdispatcher/wikis/serialization) and [ActivityStreams](http://gitlab/service-team/service-eventdispatcher/wikis/activity-streams)

## Related Projects

- [EventBus](https://github.com/thenetcircle/event-bus) A events distributing system with various different data sources and targets 
- [EventDispatcher Bundle](http://gitlab/service-team/bundle-eventdispatcher) Symfony Bundle of EventDispatcher
- [EventDispatcher Demo Project](http://gitlab/service-team/eventdispatcher-demo) A demo project of EventDispatcher Bundle based on Symfony Framework

## Installation

### Requirements

- PHP 5.6+

### Using Composer

- add following code in your project's composer.json

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

- update project

```json
composer update
```

## Support

Benn<<benn@thenetcircle.com>> from Service Team

## Contributing
Feedbacks and pull requests are welcome and appreciative. You can contact me by mail or slack or open a issue.   
For major changes, please open an issue first to discuss what you would like to change.