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
     * @var array
     */
    private static $producersPool = array();

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
     * @var RdKafka\Producer
     */
    private $producer;
    /**
     * @var RdKafka\ProducerTopic[]
     */
    private $topics = array();
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
        if(!class_exists('RdKafka\Producer')) {
            throw new Exception(
                'The kafka backend is based on php-rdKafka(https://github.com/arnaud-lb/php-rdkafka), Please make
                sure it\'s installed.'
            );
        }

        $this->brokers = $brokers;

        $defaultConf = [
            'group.id' => 'tncEventDispatcher',
            'client.id' => __CLASS__,
            'topic.metadata.refresh.sparse'      => 'true',
            'topic.metadata.refresh.interval.ms' => -1,
            'log.connection.close'               => 'false',
            'message.send.max.retries'           => 0,
            'delivery.report.only.error'         => 'true',
            'socket.keepalive.enable'            => 'false',
        ];
        if($debug) {
            $defaultConf['debug'] = 'all';
        }
        $this->conf = array_merge($defaultConf, $conf);

        $defaultTopicConf = [
            'request.required.acks' => 0,
            'message.timeout.ms'    => 200
        ];
        $this->topicConf  = array_merge($defaultTopicConf, $topicConf);

        $this->debug = $debug;
    }

    /**
     * {@inheritdoc}
     */
    public function produce($channel, $message)
    {
        $topic = $this->getTopic($channel);

        if(!$topic) {
            throw new Exception(
                sprintf('Create topic failed. Channel: %s', $channel)
            );
        }

        $topic->produce(RD_KAFKA_PARTITION_UA, 0, $message);
    }

    /**
     * {@inheritdoc}
     */
    public function consume($channel, $callback)
    {

    }

    /**
     * @param object $kafka
     * @param int    $err
     * @param string $reason
     */
    public function errorCallback($kafka, $err, $reason)
    {
        if($this->debug) {
            printf("Kafka error: [%s]%s (reason: %s)\n", $err, rd_kafka_err2str($err), $reason);
        }
    }

    /**
     * @param RdKafka\Producer $producer
     * @param RdKafka\Message  $message
     */
    public function deliveryMessageCallback(RdKafka\Producer $producer, RdKafka\Message $message)
    {
        if($message->err !== 0) {
            if($this->debug) {
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
     * Init producer based on the configuration, If there is one in the pool, will take it.
     */
    private function initProducer()
    {
        if(!is_null($this->producer)) {
            return;
        }

        if(!isset(self::$producersPool[$this->brokers])) {

            // setting up producer $conf
            $conf = new RdKafka\Conf();
            if(function_exists('pcntl_sigprocmask')) {
                pcntl_sigprocmask(SIG_BLOCK, array(SIGIO)); // once
                $conf->set('internal.termination.signal', SIGIO); // anytime
            }
            foreach($this->conf as $_key => $_value) {
                $conf->set($_key, $_value);
            }
            $conf->setErrorCb(array($this, 'errorCallback'));
            $conf->setDrMsgCb(array($this, 'deliveryMessageCallback'));

            $producer = new RdKafka\Producer($conf);
            $producer->setLogLevel($this->debug ? LOG_DEBUG : LOG_ERR);
            $producer->addBrokers($this->brokers);

            self::$producersPool[$this->brokers] = $producer;

        }
        $this->producer = self::$producersPool[$this->brokers];
    }

    /**
     * @param string $topicName
     *
     * @return RdKafka\ProducerTopic
     */
    private function getTopic($topicName)
    {
        $this->initProducer();

        if(!isset($this->topics[$topicName])) {
            // setting up topic conf
            $topicConf = new RdKafka\TopicConf();
            foreach($this->topicConf as $_key => $_value) {
                $topicConf->set($_key, $_value);
            }
            $this->topics[$topicName] = $this->producer->newTopic($topicName, $topicConf);
        }

        return $this->topics[$topicName];
    }
}