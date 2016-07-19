<?php

namespace Tnc\Service\EventDispatcher\Consumer\Job;

use Tnc\Service\EventDispatcher\Consumer\Process;
use Tnc\Service\EventDispatcher\Event\ActivityStreamsEvent;
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

    /**
     * @var Process
     */
    private $process;

    public function __construct(ExternalDispatcher $externalDispatcher, Pipeline $pipeline, $channels = null)
    {
        $this->externalDispatcher = $externalDispatcher;
        $this->pipeline           = $pipeline;
        $this->channels           = $channels;
    }

    public function errorHandler($errno, $errstr, $errfile, $errline, $errcontext)
    {
        $this->process->getLogger()->error(
            sprintf(
                'Executor<%d>, Error Occured. Errno: %d, ErrStr: %s, ErrFile: %s, ErrLine: %s, ErrContext: %s',
                $this->process->getPid(),
                $errno, $errstr, $errfile, $errline, implode(', ', $errcontext)
            )
        );

        /* Don't execute PHP internal error handler */
        return true;
    }

    public function __invoke(Process $process)
    {
        $this->process = $process;

        $process->getLogger()->debug(
            sprintf(
                'Executor<%d> is running, Parent<%d>, Will listen to channel %s.',
                $process->getPid(),
                $process->getParentPid(),
                implode(', ', $this->channels ?: $this->pipeline->getChannelDetective()->getListeningChannels())
            )
        );

        set_error_handler(array($this, 'errorHandler'), E_ERROR | E_USER_ERROR);

        $pdo = new \PDO('mysql:dbname=poppen;host=moleman-poppen;', 'poppen_admin', 'NetNet2');
        $failedTimes     = 0;
        $acceptedJobsNum = 0;

        while (true) {

            // TODO Automatically reload newest code

            if ($process->getParentPid() === 1) { // master exited
                sleep(5); // sleep 5 seconds for clean up, e.g. async acknowledges
                exit(0);
            }

            try {

                /** @var EventWrapper $eventWrapper */
                /** @var ActivityStreamsEvent $event */
                list($eventWrapper, $receipt) = $this->pipeline->pop(500000, $this->channels);

                if ($eventWrapper === null) {

                    $process->getLogger()->warning(
                        sprintf('Executor<%d> got a null event, receipt: %s.', $process->getPid(), serialize($receipt))
                    );

                } elseif (0) {
                    // TODO check if there is a listener, otherwise maybe unsubscribe the topic
                } else {

                    try { // TODO handler failed jobs

                        $acceptedJobsNum++;
                        $event = $eventWrapper->getEvent();

                        // TODO do dispatch
                        $process->getLogger()->debug(
                            sprintf(
                                'Executor<%d>, Got a new event %s, Topic: %s, Partition: %d, LastOffset: %d.',
                                $process->getPid(),
                                get_class($event),
                                $receipt->topic_name,
                                $receipt->partition,
                                $receipt->offset
                            )
                        );

                        $sql =
                            'UPDATE kafka_consumer_test SET status=1, u_time='.microtime(true).' WHERE id="'.$event->getId(
                            ).'"';
                        $pdo->exec($sql);

                        $this->pipeline->ack($receipt);

                    } catch (\Exception $e) {

                        $process->getLogger()->error(
                            sprintf(
                                'Executor<%d>, Job execute failed, ErrorCode: %d, ErrorMessage: %s.',
                                $process->getPid(),
                                $e->getCode(),
                                $e->getMessage()
                            )
                        );

                    }

                }

            } catch (NoDataException $e) {
                $process->getLogger()->debug(
                    sprintf(
                        'Executor<%d>, There is no data in upstream backend.',
                        $process->getPid()
                    )
                );
            } catch (TimeoutException $e) {
                $process->getLogger()->debug(
                    sprintf(
                        'Executor<%d>, Fetch data from upstream backend timeout, will try it again.',
                        $process->getPid()
                    )
                );
            } catch (FatalException $e) {

                $failedTimes++;

                $process->getLogger()->error(
                    sprintf(
                        'Executor<%d>, Fetch data from upstream backend failed %d times, ErrorCode: %d, ErrorMessage: %s.',
                        $process->getPid(),
                        $failedTimes,
                        $e->getCode(),
                        $e->getMessage()
                    )
                );

                if ($failedTimes >= self::MAX_FAILED_TIMES) {

                    $process->getLogger()->warning(
                        sprintf(
                            'Executor<%d> exceed the max failed times %d, Will exit.',
                            $process->getPid(),
                            self::MAX_FAILED_TIMES
                        )
                    );

                    sleep(5); // sleep 5 seconds then exit, to protect auto-restart
                    exit(1);
                }

                sleep(1); // sleep 1 second then retry

            }

        }
    }
}