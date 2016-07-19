<?php

namespace Tnc\Service\EventDispatcher\Consumer\Job;

use Tnc\Service\EventDispatcher\Consumer\Process;
use Tnc\Service\EventDispatcher\EventWrapper;
use Tnc\Service\EventDispatcher\Exception\FatalException;
use Tnc\Service\EventDispatcher\Exception\NoDataException;
use Tnc\Service\EventDispatcher\Exception\TimeoutException;
use Tnc\Service\EventDispatcher\ExternalDispatcher;
use Tnc\Service\EventDispatcher\Pipeline;

final class Fetcher
{
    const MAX_FAILED_TIMES = 5;

    /**
     * @var int
     */
    private $workersNum;

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
    private $channel;

    /**
     * @var array
     */
    private $unAcknowledgedMessages = [];

    public function __construct(ExternalDispatcher $externalDispatcher, Pipeline $pipeline, $workersNum, $channel = null)
    {
        $this->externalDispatcher = $externalDispatcher;
        $this->workersNum         = $workersNum;
        $this->pipeline           = $pipeline;
        $this->channel            = $channel;
    }

    public function __invoke(Process $process)
    {
        $process->getLogger()->debug(sprintf('Fetcher [%d] is running.', $process->getId()));

        $jobQueue     = $process->getQueue('job');
        $receiptQueue = $process->getQueue('receipt');

        $process->getLogger()->debug(
            sprintf('Fetcher [%d] will listen to channel %s.', $process->getId(), $this->channel)
        );

        while (true) {

            $failedTimes = 0;

            try {

                //TODO set consumer client id

                /** @var EventWrapper $eventWrapper */
                list($eventWrapper, $receipt) = $this->pipeline->pop(600000, $this->channel);

                if ($eventWrapper === null) {

                    $process->getLogger()->warning(
                        sprintf('Fetcher [%d] got a null event, receipt: %s.', $process->getId(), $receipt)
                    );

                } elseif (0) {
                    // TODO check if there is a listener, otherwise maybe unsubscribe the topic
                } else {

                    // if no free workers, will waiting
                    while ($jobQueue->length() > 5) {
                        usleep(50000);
                    }

                    $event          = $eventWrapper->getEvent();
                    $targetWorkerId = $this->getTargetWorkerId($event->getGroup());
                    $message        = [$process->getId(), $event, $receipt];

                    if ($jobQueue->push($targetWorkerId, $message)) {

                        $process->getLogger()->debug(
                            sprintf(
                                'Fetcher [%d] has pushed a job to a worker [%d].',
                                $process->getId(), $targetWorkerId
                            )
                        );

                        $receipt = $receiptQueue->pop($process->getId(), 600000);
                        if ($receipt !== false) {
                            $process->getLogger()->debug(
                                sprintf(
                                    'Fetcher [%d] has received a receipt, from a worker [%d]. Receipt: %s',
                                    $process->getId(), $targetWorkerId, var_export($receipt, true)
                                )
                            );

                            $this->pipeline->ack($receipt);
                        } else {
                            $process->getLogger()->warning(
                                sprintf(
                                    'Fetcher [%d] receiving a receipt failed, from a worker [%d], LastErrorCode: %d.',
                                    $process->getId(), $targetWorkerId, $receiptQueue->getLastErrorCode()
                                )
                            );
                        }

                    } else {
                        $process->getLogger()->warning(
                            sprintf(
                                'Fetcher [%d] pushing a job to a worker [%d] failed, LastErrorCode: %d.',
                                $process->getId(), $targetWorkerId, $jobQueue->getLastErrorCode()
                            )
                        );
                    }
                }

            }
            catch (NoDataException $e) {
                $process->getLogger()->debug(
                    sprintf(
                        'Fetcher [%d], There is no data in upstream backend.',
                        $process->getId()
                    )
                );
            }
            catch (TimeoutException $e) {
                $process->getLogger()->debug(
                    sprintf(
                        'Fetcher [%d], Fetch data from upstream backend timeout, will try it again.',
                        $process->getId()
                    )
                );
            }
            catch (FatalException $e) {
                $failedTimes++;
                $process->getLogger()->error(
                    sprintf(
                        'Fetcher [%d], Fetch data from upstream backend failed %d times, ErrorCode: %d, ErrorMessage: %s.',
                        $process->getId(), $failedTimes, $e->getCode(), $e->getMessage()
                    )
                );

                if ($failedTimes > self::MAX_FAILED_TIMES) {

                    $process->getLogger()->warning(
                        sprintf(
                            'Fetcher [%d] exceed the max failed times %d, Will exit.',
                            $process->getId(), self::MAX_FAILED_TIMES
                        )
                    );

                    break;
                }

                usleep(1000000);
            }

        }
    }

    protected function getTargetWorkerId($group)
    {
        return ((int)abs(crc32($group)) % $this->workersNum) + 1;
    }
}