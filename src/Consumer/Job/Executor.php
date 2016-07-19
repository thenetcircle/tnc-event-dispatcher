<?php

namespace Tnc\Service\EventDispatcher\Consumer\Job;

use RdKafka\Message;
use Tnc\Service\EventDispatcher\Consumer\Process;
use Tnc\Service\EventDispatcher\EventWrapper;
use Tnc\Service\EventDispatcher\Exception\FatalException;
use Tnc\Service\EventDispatcher\Exception\NoDataException;
use Tnc\Service\EventDispatcher\Exception\TimeoutException;
use Tnc\Service\EventDispatcher\ExternalDispatcher;
use Tnc\Service\EventDispatcher\Pipeline;

final class Executor
{
    const MAX_FAILED_TIMES = 5;

    /**
     * @var Pipeline
     */
    private $pipeline;

    /**
     * @var ExternalDispatcher
     */
    private $externalDispatcher;

    /**
     * @var string
     */
    private $channels;

    public function __construct(ExternalDispatcher $externalDispatcher, Pipeline $pipeline, $channels = null)
    {
        $this->externalDispatcher = $externalDispatcher;
        $this->pipeline           = $pipeline;
        $this->channels           = $channels;
    }

    public function __invoke(Process $process)
    {
        $process->getLogger()->debug(
            sprintf(
                'Executor [%d] is running, Parent [%d], Will listen to channel %s.',
                $process->getId(),
                posix_getppid(),
                implode(', ', $this->channels ?: $this->pipeline->getChannelDetective()->getListeningChannels())
            )
        );

        $failedTimes     = 0;
        $acceptedJobsNum = 0;

        while (true) {

            try {

                /** @var EventWrapper $eventWrapper */
                /** @var Message $receipt */
                list($eventWrapper, $receipt) = $this->pipeline->pop(1000, $this->channels);

                printf('Parent [%d]', posix_getppid());

                if ($eventWrapper === null) {

                    $process->getLogger()->warning(
                        sprintf('Executor [%d] got a null event, receipt: %s.', $process->getId(), serialize($receipt))
                    );

                } elseif (0) {
                    // TODO check if there is a listener, otherwise maybe unsubscribe the topic
                } else {

                    $acceptedJobsNum++;
                    $event = $eventWrapper->getEvent();

                    // TODO do dispatch
                    $process->getLogger()->debug(
                        sprintf(
                            'Executor [%d], AcceptedJobs: %d, Got a new event %s, Topic: %s, Partition: %d LastOffset: %d.',
                            $process->getId(),
                            $acceptedJobsNum,
                            get_class($event),
                            $receipt->topic_name,
                            $receipt->partition,
                            $receipt->offset
                        )
                    );

                    $this->pipeline->ack($receipt);

                    // TODO waiting for ack finished then shutdown

                }

            } catch (NoDataException $e) {
                $process->getLogger()->debug(
                    sprintf(
                        'Executor [%d], There is no data in upstream backend.',
                        $process->getId()
                    )
                );
            } catch (TimeoutException $e) {
                $process->getLogger()->debug(
                    sprintf(
                        'Executor [%d], Fetch data from upstream backend timeout, will try it again.',
                        $process->getId()
                    )
                );
            } catch (FatalException $e) {

                $failedTimes++;

                $process->getLogger()->error(
                    sprintf(
                        'Executor [%d], Fetch data from upstream backend failed %d times, ErrorCode: %d, ErrorMessage: %s.',
                        $process->getId(),
                        $failedTimes,
                        $e->getCode(),
                        $e->getMessage()
                    )
                );

                if ($failedTimes > self::MAX_FAILED_TIMES) {

                    $process->getLogger()->warning(
                        sprintf(
                            'Executor [%d] exceed the max failed times %d, Will exit.',
                            $process->getId(),
                            self::MAX_FAILED_TIMES
                        )
                    );

                    usleep(100000); // sleep 100 milliseconds then exit, to pretect autorestart

                    exit(1);
                }

                usleep(1000000);

            }

        }
    }
}