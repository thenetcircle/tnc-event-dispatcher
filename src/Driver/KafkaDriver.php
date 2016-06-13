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

class KafkaDriver implements Driver
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
     * @param array $options
     * @param bool  $debug
     *
     * @throws Exception\FatalException
     */
    public function __construct(array $options = [], $debug = false)
    {
        if (!class_exists('\RdKafka')) {
            throw new Exception\FatalException(
                'Dependency missed, php-rdKafka(https://github.com/arnaud-lb/php-rdkafka).'
            );
        }

        $this->debug = $debug;
        $this->initOptions($options);
    }

    /**
     * {@inheritdoc}
     *
     * $channel supports regexp with prefix ^
     */
    public function push($channel, $message, $timeout = 200, $group = null)
    {
        $this->initProducer();
        $this->getProducerTopic($channel, $timeout)->produce(\RD_KAFKA_PARTITION_UA, 0, $message, $group);
    }

    /**
     * {@inheritdoc}
     *
     * $channel supports regexp with prefix ^
     */
    public function pop($channel, $timeout = 120000)
    {
        $this->initConsumer();

        try {
            if ($channel != $this->subscribingTopic) {
                $this->consumer->subscribe((array)$channel);
                $this->subscribingTopic = $channel;
            }

            $message = $this->consumer->consume($timeout);
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
        // TODO: Implement ack() method.
    }

    /**
     * @param object $kafka
     * @param int    $err
     * @param string $reason
     */
    public function kafkaErrorCallback($kafka, $err, $reason)
    {
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
                'message.send.max.retries'           => 0,
                'delivery.report.only.error'         => 'true',
                // 'socket.timeout.ms'                  => 10,
                'socket.blocking.max.ms'             => 10,
                'socket.keepalive.enable'            => 'false',
                // 'max.in.flight.requests.per.connection' => 1,
                // 'reconnect.backoff.jitter.ms' => 0,
            ],
            'producer' => [],
            'consumer' => [
                'group.id'  => 'tncEventDispatcher',
                'client.id' => __CLASS__,
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
     * @param string $topicName
     * @param int    $timeout
     */
    private function getProducerTopic($topicName, $timeout)
    {
        if (!isset($this->producerTopics[$topicName])) {

            $topicConf = new \RdKafka\TopicConf();
            $topicConf->set('request.required.acks', 1);
            $topicConf->set('request.timeout.ms', $timeout);
            $topicConf->set('message.timeout.ms', $timeout);
            $topicConf->setPartitioner(\RD_KAFKA_MSG_PARTITIONER_CONSISTENT);

            $this->producerTopics[$topicName] = $this->producer->newTopic($topicName, $topicConf);

        }

        return $this->producerTopics[$topicName];
    }
}