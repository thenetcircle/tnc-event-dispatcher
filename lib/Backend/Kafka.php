<?php

/*
 * This file is part of the Tnc package.
 *
 * (c) Service Team <benn@thenetcircle.com>
 *
 * file that was distributed with this source code.
 */

namespace Tnc\Componment\EventDispatcher\Backend;

use Tnc\Componment\EventDispatcher\Exception;

class Kafka implements BackendInterface
{
    /**
     * @var string
     */
    private $brokers;
    /**
     * @var array
     */
    private $conf = array();
    /**
     * @var array
     */
    private $topicConf = array();
    /**
     * @var \RdKafka\Producer
     */
    private $producer;
    /**
     * @var \RdKafka\ProducerTopic[]
     */
    private $producerTopics = array();
    /**
     * @var \RdKafka\Consumer
     */
    private $consumer;
    /**
     * @var \RdKafka\ConsumerTopic[]
     */
    private $consumerTopics = array();
    /**
     * @var bool
     */
    private $debug = false;


    /**
     * Kafka constructor.
     *
     * @param string $brokers
     * @param array  $options
     * @param array  $topicConf
     */
    public function __construct($brokers, array $conf = [], array $topicConf = [], $debug = false)
    {
        if (!class_exists('\RdKafka\Producer')) {
            throw new Exception(
                'The kafka backend is based on php-rdKafka(https://github.com/arnaud-lb/php-rdkafka), Please make
                sure it\'s installed.'
            );
        }

        $this->brokers = $brokers;
        $this->debug = $debug;
        $this->initConfigurations($conf, $topicConf);
    }

    /**
     * {@inheritdoc}
     *
     * for each of channel, which support regexp with prefix ^
     */
    public function produce(array $channels, $message)
    {
        foreach($channels as $channel) {

            $topic = $this->getProducerTopic($channel);

            if (!$topic) {
                throw new Exception(
                    sprintf('Create topic failed. Channel: %s', $channel)
                );
            }

            $topic->produce(RD_KAFKA_PARTITION_UA, 0, $message, null);

        }
    }

    /**
     * {@inheritdoc}
     *
     * for each of channel, which support regexp with prefix ^
     */
    public function consume(array $channels, $callback)
    {
        $this->initConsumer();
        $queue = $this->consumer->newQueue();

        foreach($channels as $channel) {

            $topic = $this->getConsumerTopic($channel);
            $topic->consumeQueueStart(RD_KAFKA_OFFSET_STORED);

        }
    }

    /**
     * @param object $kafka
     * @param int    $err
     * @param string $reason
     */
    public function errorCallback($kafka, $err, $reason)
    {
        if ($this->debug) {
            printf("Kafka error: [%s]%s (reason: %s)\n", $err, rd_kafka_err2str($err), $reason);
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
                    "Message Delivery Failed. TopicName: %s, Key: %s, Payload: %s",
                    $message->topic_name,
                    $message->key,
                    $message->payload
                );
            }
        }
    }

    /**
     * @param \RdKafka\KafkaConsumer $consumer
     * @param int                    $err
     * @param array                  $partitions
     */
    public function rebalanceCallback(\RdKafka\KafkaConsumer $consumer, $err, array $partitions = null)
    {
        switch ($err) {
            case RD_KAFKA_RESP_ERR__ASSIGN_PARTITIONS:
                // application may load offets from arbitrary external
                // storage here and update partitions
                // $consumer->assign($partitions);
                break;

            case RD_KAFKA_RESP_ERR__REVOKE_PARTITIONS:
                // Optional explicit manual commit
                // $consumer->commit($partitions);
                // $consumer->assign(NULL);
                break;

            default:
                if ($this->debug) {
                    printf("Rebalance Error: [%s] %s\n", $err, rd_kafka_err2str($err));
                }
                // $consumer->assign(NULL);
                break;
        }
    }

    /**
     * @param array $conf
     * @param array $topicConf
     */
    private function initConfigurations(array $conf, array $topicConf)
    {
        $defaultConf = [
            'both' => [
                'group.id'                           => 'tncEventDispatcher',
                'client.id'                          => __CLASS__,
                'topic.metadata.refresh.sparse'      => 'true',
                'topic.metadata.refresh.interval.ms' => -1,
                'log.connection.close'               => 'false',
                'message.send.max.retries'           => 0,
                'delivery.report.only.error'         => 'true',
                'socket.keepalive.enable'            => 'false',
            ],
            'producer' => [],
            'consumer' => [],
        ];
        if ($this->debug) {
            $defaultConf['both']['debug'] = 'all';
        }
        $this->conf = array_merge_recursive($defaultConf, $conf);

        $defaultTopicConf = [
            'both' => [
                'request.required.acks' => 0,
                'message.timeout.ms'    => 200
            ],
            'producer' => [],
            'consumer' => []
        ];
        $this->topicConf  = array_merge_recursive($defaultTopicConf, $topicConf);
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
        $conf->setErrorCb(array($this, 'errorCallback'));

        foreach ($this->conf['both'] as $_key => $_value) {
            $conf->set($_key, $_value);
        }

        return $conf;
    }

    /**
     * @return \RdKafka\TopicConf
     */
    private function getDefaultTopicConf()
    {
        $topicConf = new \RdKafka\TopicConf();
        foreach ($this->topicConf['both'] as $_key => $_value) {
            $topicConf->set($_key, $_value);
        }

        return $topicConf;
    }

    /**
     * Init producer based on the configuration
     */
    private function initProducer()
    {
        if (!is_null($this->producer)) {
            return;
        }

        $conf = $this->getDefaultConf();
        $conf->setDrMsgCb(array($this, 'deliveryMessageCallback'));
        foreach ($this->conf['producer'] as $_key => $_value) {
            $conf->set($_key, $_value);
        }

        $producer = new \RdKafka\Producer($conf);
        $producer->setLogLevel($this->debug ? LOG_DEBUG : LOG_ERR);
        $producer->addBrokers($this->brokers);

        $this->producer = $producer;
    }

    /**
     * @param string $topicName
     *
     * @return \RdKafka\ProducerTopic
     */
    private function getProducerTopic($topicName)
    {
        $this->initProducer();

        if (!isset($this->producerTopics[$topicName])) {
            $topicConf = $this->getDefaultTopicConf();
            foreach ($this->topicConf['producer'] as $_key => $_value) {
                $topicConf->set($_key, $_value);
            }
            $this->producerTopics[$topicName] = $this->producer->newTopic($topicName, $topicConf);
        }

        return $this->producerTopics[$topicName];
    }

    /**
     * Init consumer based on the configuration
     */
    private function initConsumer()
    {
        if (!is_null($this->consumer)) {
            return;
        }

        $conf = $this->getDefaultConf();
        $conf->setRebalanceCb(array($this, 'rebalanceCallback'));
        foreach ($this->conf['consumer'] as $_key => $_value) {
            $conf->set($_key, $_value);
        }

        $consumer = new \RdKafka\Producer($conf);
        $consumer->setLogLevel($this->debug ? LOG_DEBUG : LOG_ERR);
        $consumer->addBrokers($this->brokers);

        $this->consumer = $consumer;
    }

    /**
     * @param string $topicName
     *
     * @return \RdKafka\ConsumerTopic
     */
    private function getConsumerTopic($topicName)
    {
        $this->initConsumer();

        if (!isset($this->consumerTopics[$topicName])) {
            $topicConf = $this->getDefaultTopicConf();
            foreach ($this->topicConf['consumer'] as $_key => $_value) {
                $topicConf->set($_key, $_value);
            }
            $this->consumerTopics[$topicName] = $this->consumer->newTopic($topicName, $topicConf);
        }

        return $this->consumerTopics[$topicName];
    }
}