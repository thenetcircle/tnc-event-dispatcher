<?php

/*
 * This file is part of the Tnc package.
 *
 * (c) Service Team
 *
 * file that was distributed with this source code.
 */

namespace Tnc\Service\EventDispatcher\Backend;

use Tnc\Service\EventDispatcher\Backend;
use Tnc\Service\EventDispatcher\Exception;
use Tnc\Service\EventDispatcher\Event\Internal\ErrorEvent;
use Tnc\Service\EventDispatcher\Event\Internal\MessageEvent;

class KafkaBackend extends AbstractBackend
{
    /**
     * @var \RdKafka\Producer
     */
    private $producer;
    /**
     * @var \RdKafka\ProducerTopic[]
     */
    private $producerTopics = array();

    /**
     * @var \RdKafka\KafkaConsumer
     */
    private $consumer;
    /**
     * @var string
     */
    private $subscribingTopic;

    /**
     * @var array
     */
    private $options = array();
    /**
     * @var bool
     */
    private $debug = false;


    /**
     * KafkaPipeline constructor.
     *
     * @param string $brokers
     * @param array  $options
     * @param bool   $debug
     *
     * @throws Exception\FatalException
     */
    public function __construct($brokers, array $options = [], $debug = false)
    {
        if (!class_exists('\RdKafka')) {
            throw new Exception\FatalException(
                'Dependency missed, php-rdKafka(https://github.com/arnaud-lb/php-rdkafka).'
            );
        }

        $this->debug                               = $debug;
        $options['broker']['metadata.broker.list'] = $brokers;
        $this->initOptions($options);
    }

    /**
     * $channel supports regexp with prefix ^
     *
     * @param string|null $key
     *
     * {@inheritdoc}
     */
    public function push($channels, $message, $key = null)
    {
        foreach ($channels as $channel) {
            $this->getProducerTopic($channel)->produce(
                \RD_KAFKA_PARTITION_UA,
                0,
                $message,
                $key
            );
        }
    }

    /**
     * {@inheritdoc}
     *
     * $channel supports regexp with prefix ^
     */
    public function pop($channels, $timeout)
    {
        $this->initConsumer();

        try {
            if ($channels != $this->subscribingTopic) {
                $this->consumer->subscribe($channels);
                $this->subscribingTopic = $channels;
            }

            $message = $this->consumer->consume($timeout);
        } catch (\Exception $e) {
            throw new Exception\FatalException('Consuming failed.', $e->getCode(), $e);
        }

        switch ($message->err) {

            case RD_KAFKA_RESP_ERR_NO_ERROR:
                return array($message->payload, $message);

            case RD_KAFKA_RESP_ERR__PARTITION_EOF:
                throw new Exception\NoDataException("No more messages, will wait for more.");

            case RD_KAFKA_RESP_ERR__TIMED_OUT:
                throw new Exception\TimeoutException();

            default:
                throw new Exception\FatalException($message->errstr(), $message->err);

        }
    }

    /**
     * {@inheritdoc}
     */
    public function ack($receipt)
    {
        $this->initConsumer();
        $this->consumer->commitAsync($receipt);
    }

    /**
     * @param object $kafka
     * @param int    $err
     * @param string $reason
     */
    public function kafkaErrorCallback($kafka, $err, $reason)
    {
        $this->eventDispatcher->dispatch(
            ErrorEvent::NAME,
            new ErrorEvent(
                $err,
                sprintf('error: %s, reason: %s', rd_kafka_err2str($err), $reason),
                '{KafkaDriver::kafkaErrorCallback}'
            )
        );

        if ($this->debug) {
            printf("{kafkaErrorCallback} [%s]%s (reason: %s)\n", $err, rd_kafka_err2str($err), $reason);
        }
    }

    /**
     * @param \RdKafka\Producer $producer
     * @param \RdKafka\Message  $message
     */
    public function deliveryMessageCallback(\RdKafka\Producer $producer, \RdKafka\Message $message)
    {
        $this->eventDispatcher->dispatch(
            MessageEvent::DELIVERY_FAILED,
            new MessageEvent(
                $message->topic_name, $message->payload, $message->key, $message->err
            )
        );

        if ($this->debug) {
            printf(
                "{deliveryMessageCallback} Failed, TopicName: %s, Key: %s, Payload: %s, ErrorCode: %s",
                $message->topic_name,
                $message->key,
                $message->payload,
                $message->err
            );
        }
    }

    /**
     * @param string $topicName
     */
    private function getProducerTopic($topicName)
    {
        $this->initProducer();

        if (!isset($this->producerTopics[$topicName])) {

            $topicConf = new \RdKafka\TopicConf();
            foreach ($this->options['producer_topic'] as $_key => $_value) {
                $topicConf->set($_key, $_value);
            }
            $topicConf->setPartitioner(\RD_KAFKA_MSG_PARTITIONER_CONSISTENT);

            $this->producerTopics[$topicName] = $this->producer->newTopic($topicName, $topicConf);

        }

        return $this->producerTopics[$topicName];
    }

    /**
     * Initialize producer
     */
    private function initProducer()
    {
        if (!is_null($this->producer)) {
            return;
        }

        $conf = $this->getDefaultConf();

        if (function_exists('pcntl_sigprocmask')) {
            pcntl_sigprocmask(SIG_BLOCK, array(SIGIO)); // once
            $conf->set('internal.termination.signal', SIGIO); // anytime
        }

        $conf->setDrMsgCb(array($this, 'deliveryMessageCallback'));
        foreach ($this->options['producer'] as $_key => $_value) {
            $conf->set($_key, $_value);
        }

        $producer = new \RdKafka\Producer($conf);
        $producer->setLogLevel($this->debug ? LOG_DEBUG : LOG_ERR);
        $this->producer = $producer;
    }

    /**
     * Initialize consumer
     */
    private function initConsumer()
    {
        if (!is_null($this->consumer)) {
            return;
        }

        $conf = $this->getDefaultConf();
        foreach ($this->options['consumer'] as $_key => $_value) {
            $conf->set($_key, $_value);
        }

        $topicConf = new \RdKafka\TopicConf();
        foreach ($this->options['consumer_topic'] as $_key => $_value) {
            $topicConf->set($_key, $_value);
        }
        $conf->setDefaultTopicConf($topicConf);

        $this->consumer = new \RdKafka\KafkaConsumer($conf);
    }

    /**
     * @param array $options
     */
    private function initOptions(array $options)
    {
        $defaultOptions = [
            'broker'         => [
                'metadata.broker.list'       => '', // brokers list
                'log.connection.close'       => 'false',
                'delivery.report.only.error' => 'true',
                'api.version.request'        => 'false',
                // 'socket.timeout.ms'                  => 10,
                // 'socket.blocking.max.ms'             => 10,
                // 'reconnect.backoff.jitter.ms' => 0,
            ],
            'producer'       => [
                'topic.metadata.refresh.sparse'         => 'true',
                'topic.metadata.refresh.interval.ms'    => 600000,
                'socket.blocking.max.ms'                => 50,
                'socket.keepalive.enable'               => 'false',
                'message.send.max.retries'              => 0,
                'max.in.flight.requests.per.connection' => 1000,
                'compression.codec'                     => 'gzip',
            ],
            'producer_topic' => [
                'request.required.acks' => 1,
                'message.timeout.ms'    => 1000
                // 'request.timeout.ms' => 1000,
            ],
            'consumer'       => [
                'group.id'                           => 'EventDispatcherService',
                'client.id'                          => 'EventDispatcherBuildInConsumer',
                'topic.metadata.refresh.sparse'      => 'false',
                'topic.metadata.refresh.interval.ms' => 30000,
                'queued.max.messages.kbytes'         => 100000,
                'socket.keepalive.enable'            => 'true',
                'enable.auto.commit'                 => 'false',
                'enable.auto.offset.store'           => 'false',
            ],
            'consumer_topic' => [
                'auto.offset.reset'   => 'earliest',
                /*'offset.store.method' => 'file',
                'offset.store.path'   => '/tmp/kafka-offset',*/
            ],
        ];

        if ($this->debug) {
            $defaultOptions['broker']['debug'] = 'all';
        }
        $this->options = array_replace_recursive($defaultOptions, $options);

        if (empty($this->options['broker']['metadata.broker.list'])) {
            throw new Exception\FatalException('brokers list does not set.');
        }
    }

    /**
     * @return \RdKafka\Conf
     */
    private function getDefaultConf()
    {
        $conf = new \RdKafka\Conf();
        $conf->setErrorCb(array($this, 'kafkaErrorCallback'));
        foreach ($this->options['broker'] as $_key => $_value) {
            $conf->set($_key, $_value);
        }

        return $conf;
    }
}
