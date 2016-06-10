<?php

/*
 * This file is part of the Tnc package.
 *
 * (c) Service Team
 *
 * file that was distributed with this source code.
 */

namespace Tnc\Service\EventDispatcher\Pipe;

use Tnc\Service\EventDispatcher\Exception\DefaultException;
use Tnc\Service\EventDispatcher\Pipe;

class KafkaPipe implements Pipe
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
     * @var \RdKafka\KafkaConsumer
     */
    private $consumer;
    /**
     * @var callable
     */
    private $errorCallback = null;
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
            throw new DefaultException(
                'The kafka backend is based on php-rdKafka(https://github.com/arnaud-lb/php-rdkafka), Please make
                sure it\'s installed.'
            );
        }

        $this->brokers = $brokers;
        $this->debug   = $debug;
        $this->initConfigurations($conf, $topicConf);
    }

    /**
     * {@inheritdoc}
     *
     * for each of channel, which support regexp with prefix ^
     */
    public function produce(array $channels, $message, $key = null, $timeout = 0)
    {
        foreach ($channels as $channel) {

            $topic = $this->getProducerTopic($channel);

            if (!$topic) {
                throw new DefaultException(
                    sprintf('Create topic failed. Channel: %s', $channel)
                );
            }

            $topic->produce(RD_KAFKA_PARTITION_UA, 0, $message, $key);

        }
    }

    /**
     * {@inheritdoc}
     *
     * for each of channel, which support regexp with prefix ^
     */
    public function consume(array $channels, $callback, $timeout = (120 * 1000))
    {
        $this->initConsumer();
        $this->consumer->subscribe($channels);
        while (true) {
            $message = $this->consumer->consume($timeout);

            switch ($message->err) {
                case RD_KAFKA_RESP_ERR_NO_ERROR:
                    call_user_func($callback, $message->topic_name, $message->payload, $message->key);
                    break;
                case RD_KAFKA_RESP_ERR__PARTITION_EOF:
                    $this->throwError(self::ERROR_TYPE_NO_DATA, "No more messages, will wait for more.");
                    break;
                case RD_KAFKA_RESP_ERR__TIMED_OUT:
                    $this->throwError(self::ERROR_TYPE_TIMEOUT, "Timeout");
                    break;
                default:
                    $this->throwError(self::ERROR_TYPE_ERROR, $message->errstr(), $message->err);
                    break;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setErrorCallback($errorCallback)
    {
        $this->errorCallback = $errorCallback;
    }

    /**
     * @param object $kafka
     * @param int    $err
     * @param string $reason
     */
    public function kafkaErrorCallback($kafka, $err, $reason)
    {
        $this->log(sprintf("Kafka error: [%s]%s (reason: %s)\n", $err, rd_kafka_err2str($err), $reason));
        $this->throwError(self::ERROR_TYPE_ERROR, $reason, $err);
    }

    /**
     * @param \RdKafka\Producer $producer
     * @param \RdKafka\Message  $message
     */
    public function deliveryMessageCallback(\RdKafka\Producer $producer, \RdKafka\Message $message)
    {
        if ($message->err !== 0) {

            $this->log(sprintf(
                           "Message Delivery Failed. TopicName: %s, Key: %s, Payload: %s",
                           $message->topic_name,
                           $message->key,
                           $message->payload
                       ));

            $this->throwError(self::ERROR_TYPE_ERROR, $message->err, $message->errstr());

        }
    }

    /**
     * @param array $conf
     * @param array $topicConf
     */
    private function initConfigurations(array $conf, array $topicConf)
    {
        $defaultConf = [
            'both'     => [
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
            'both'     => [
                'request.required.acks' => 1,
            ],
            'producer' => [
                'message.timeout.ms'    => 200,
            ],
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
        $conf->setErrorCb(array($this, 'kafkaErrorCallback'));

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
        $topicConf->setPartitioner(RD_KAFKA_MSG_PARTITIONER_CONSISTENT);

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
     * Init consumer based on the configuration
     */
    private function initConsumer()
    {
        if (!is_null($this->consumer)) {
            return;
        }

        $conf = $this->getDefaultConf();
        $conf->set('metadata.broker.list', $this->brokers);
        foreach ($this->conf['consumer'] as $_key => $_value) {
            $conf->set($_key, $_value);
        }

        $topicConf = $this->getDefaultTopicConf();
        foreach ($this->topicConf['consumer'] as $_key => $_value) {
            $topicConf->set($_key, $_value);
        }
        $conf->setDefaultTopicConf($topicConf);

        $consumer       = new \RdKafka\KafkaConsumer($conf);
        $this->consumer = $consumer;
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
     * call $errorCallback if it's set
     *
     * @param int    $type
     * @param string $errStr
     * @param int    $errNum
     */
    private function throwError($type, $errStr, $errNum = 0)
    {
        if(is_null($this->errorCallback)) {
            return;
        }

        call_user_func($this->errorCallback, $type, $errStr, $errNum);
    }

    /**
     * debug log
     *
     * @param string $message
     */
    private function log($message)
    {
        if ($this->debug) {
            printf("%s\n", $message);
        }
    }
}