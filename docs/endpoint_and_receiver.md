# Summary

EndPoint intends to be the place which holds/stores Events for a while, Usually it's a Queue Implementation, for example: RabbitMQ EndPoint, Redis EndPoint, Kafka EndPoint. In our company, we use our own [EventBus](https://github.com/thenetcircle/event-bus) as a EndPoint.  
Receiver is the reverse of EndPoint, It pulls Events from corresponding EndPoint and send it back to Dispatcher again. So it really depends what EndPoint is, there are also corresponding Receivers like: RabbitMQ Receover, Redis Receover, Kafka Receover, EventBus Receiver, ...

EndPoint and Receiver is simple:  
Any class implements TNC\EventDispatcher\Interfaces\EndPoint interface could be a EndPoint,   
Any class implements TNC\EventDispatcher\Interfaces\Receiver could be a Receiver.

# Implementations

## EventBus

- EndPoint

<table>
  <tr>
    <th>class</th><td>TNC\EventDispatcher\EndPoints\EventBusEndPoint</td>
  </tr>
  <tr>
    <th>dependencies</th><td>
    EventBus
    </td>
  </tr>
  <tr>
    <th>arguments</th><td></td>
  </tr>
</table>  

- Receiver

<table>
  <tr>
    <th>class</th><td>TNC\EventDispatcher\Receivers\EventBusReceiver</td>
  </tr>
  <tr>
    <th>dependencies</th><td>
    EventBus
    </td>
  </tr>
  <tr>
    <th>arguments</th><td></td>
  </tr>
</table>  

## Redis

- EndPoint

<table>
  <tr>
    <th>class</th><td>TNC\EventDispatcher\EndPoints\Redis\PHPRedisEndPoint</td>
  </tr>
  <tr>
    <th>dependencies</th><td>
    Redis<br>
    PHPRedis
    </td>
  </tr>
  <tr>
    <th>arguments</th><td></td>
  </tr>
</table>  

- Receiver

Not implemented yet

## RabbitMQ

- EndPoint

Not implemented yet

- Receiver

Not implemented yet

## Kafka

- EndPoint

Not implemented yet

- Receiver

Not implemented yet