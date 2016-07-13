<?php

namespace Tnc\Service\EventDispatcher\Consumer\Job;

use Tnc\Service\EventDispatcher\Consumer\Process;
use Tnc\Service\EventDispatcher\EventWrapper;
use Tnc\Service\EventDispatcher\Exception\FatalException;
use Tnc\Service\EventDispatcher\Exception\NoDataException;
use Tnc\Service\EventDispatcher\Exception\TimeoutException;
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
     * @var string
     */
    private $topic;

    public function __construct(Pipeline $pipeline, $topic, $workersNum)
    {
        $this->workersNum = $workersNum;
        $this->pipeline   = $pipeline;
        $this->topic      = $topic;
    }

    public function __invoke(Process $process)
    {
        $process->getLogger()->debug(sprintf('Fetcher [%d] is running.', $process->getId()));

        $jobQueue     = $process->getQueue('job');
        $receiptQueue = $process->getQueue('receipt');

        $process->getLogger()->debug(sprintf('Fetcher [%d] will listen to topic %s.', $process->getId(), $this->topic));

        while (true) {

            $failedTimes = 0;

            try {

                // if no free workers, will waiting
                while ($jobQueue->length() > 5) {
                    usleep(50000);
                }

                /** @var EventWrapper $eventWrapper */
                list($eventWrapper, $receipt) = $this->pipeline->pop($this->topic, 600000);

                if ($eventWrapper === null) {

                    $process->getLogger()->warning(
                        sprintf('Fetcher [%d] got a null event, receipt: %s.', $process->getId(), $receipt)
                    );

                } elseif (0) {
                    // TODO check if there is a listener, otherwise maybe unsubscribe the topic
                } else {

                    $targetWorkerId = $this->getTargetWorkerId($eventWrapper->getGroup());
                    $message        = [
                        $process->getId(),
                        $eventWrapper,
                        $receipt
                    ];

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

    protected function getTargetWorkerId($channel)
    {
        return ((int)abs(crc32($channel)) % $this->workersNum) + 1;
    }
}