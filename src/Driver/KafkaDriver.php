<?php

/*
 * This file is part of the Tnc package.
 *
 * (c) Service Team
 *
 * file that was distributed with this source code.
 */

namespace Tnc\Service\EventDispatcher\Driver;

use Tnc\Service\EventDispatcher\Exception;
use Tnc\Service\EventDispatcher\Driver;
use Tnc\Service\EventDispatcher\Internal\AbstractInternalEventProducer;
use Tnc\Service\EventDispatcher\Internal\Event\ErrorEvent;
use Tnc\Service\EventDispatcher\Internal\Event\MessageEvent;

class KafkaDriver extends AbstractInternalEventProducer implements Driver
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
    public function push($channel, $message, $key = null)
    {
        $this->getProducerTopic($channel)->produce(
            \RD_KAFKA_PARTITION_UA, 0, $message, $key
        );
    }

    /**
     * {@inheritdoc}
     *
     * $channel supports regexp with prefix ^
     */
    public function pop($channel, $duration = 5)
    {
        $this->initConsumer();
        $duration = (is_int($duration) && $duration > 0) ? ($duration * 1000) : 5000;

        try {
            if ($channel != $this->subscribingTopic) {
                $this->consumer->subscribe((array)$channel);
                $this->subscribingTopic = $channel;
            }

            $message = $this->consumer->consume($duration);
        }
        catch (\Exception $e) {
            throw new Exception\FatalException('Consuming failed.', $e->getCode(), $e);
        }

        switch ($message->err) {

            case RD_KAFKA_RESP_ERR_NO_ERROR:
                return array($message->payload, null);

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
    }

    /**
     * @param object $kafka
     * @param int    $err
     * @param string $reason
     */
    public function kafkaErrorCallback($kafka, $err, $reason)
    {
        $this->dispatch(ErrorEvent::NAME, new ErrorEvent(
            $err,
            sprintf('error: %s, reason: %s', rd_kafka_err2str($err), $reason),
            '{KafkaDriver::kafkaErrorCallback}'
        ));

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
        if ($message->err !== 0) {

            $this->dispatch(MessageEvent::DELIVERY_FAILED, new MessageEvent(
                $message->topic_name, $message->payload, $message->key
            ));

            if ($this->debug) {
                printf(
                    "{deliveryMessageCallback} Failed, TopicName: %s, Key: %s, Payload: %s",
                    $message->topic_name,
                    $message->key,
                    $message->payload
                );
            }

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
            foreach ($this->options['topic'] as $_key => $_value) {
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

        $this->consumer = new \RdKafka\KafkaConsumer($conf);
    }

    /**
     * @param array $options
     */
    private function initOptions(array $options)
    {
        $defaultOptions = [
            'broker'   => [
                'metadata.broker.list'               => '', // brokers list
                'topic.metadata.refresh.sparse'      => 'true',
                'topic.metadata.refresh.interval.ms' => -1,
                'log.connection.close'               => 'false',
                'message.send.max.retries'           => 2,
                'delivery.report.only.error'         => 'true',
                // 'socket.timeout.ms'                  => 10,
                // 'socket.blocking.max.ms'             => 10,
                'socket.keepalive.enable'            => 'false',
                // 'max.in.flight.requests.per.connection' => 1,
                // 'reconnect.backoff.jitter.ms' => 0,
                'api.version.request'                => 'false',
            ],
            'producer' => [],
            'consumer' => [
                'group.id'  => 'tncEventDispatcher',
                'client.id' => __CLASS__,
            ],
            'topic' => [
                'request.required.acks' => 0,
                // 'request.timeout.ms' => 1000,
                'message.timeout.ms' => 500
            ]
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
        // setting up producer $conf
        $conf = new \RdKafka\Conf();
        if (function_exists('pcntl_sigprocmask')) {
            pcntl_sigprocmask(SIG_BLOCK, array(SIGIO)); // once
            $conf->set('internal.termination.signal', SIGIO); // anytime
        }
        $conf->setErrorCb(array($this, 'kafkaErrorCallback'));

        foreach ($this->options['broker'] as $_key => $_value) {
            $conf->set($_key, $_value);
        }

        return $conf;
    }
}