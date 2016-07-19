<?php

namespace Tnc\Service\EventDispatcher\Consumer;

use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;
use Psr\Log\LogLevel;

class SimpleLogger implements LoggerInterface
{
    use LoggerTrait;

    private $logLevel;

    private $logLevelPriority = [
            LogLevel::EMERGENCY => 1,
            LogLevel::ALERT     => 2,
            LogLevel::CRITICAL  => 3,
            LogLevel::ERROR     => 4,
            LogLevel::WARNING   => 5,
            LogLevel::NOTICE    => 6,
            LogLevel::INFO      => 7,
            LogLevel::DEBUG     => 8,
        ];

    public function __construct($logLevel = LogLevel::DEBUG)
    {
        $this->logLevel = $this->logLevelPriority[$logLevel];
    }

    /**
     * {@inheritdoc}
     */
    public function log($level, $message, array $context = array())
    {
        if ($this->logLevelPriority[$level] > $this->logLevel) {
            return;
        }

        $context = array_merge(
            [
                'pid'        => getmypid(),
                'created_at' => (new \DateTime())->format(\DateTime::RFC3339),
            ],
            $context
        );

        printf("%s %s%s\n", $level, $message, $context ? (' ' . json_encode($context)) : '');
    }
}