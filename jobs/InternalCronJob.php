<?php

namespace kfosoft\cron\jobs;

use Cron\CronExpression;
use Exception;
use yii\base\Response;
use yii\console\Application;

class InternalCronJob extends AbstractCronJob
{
    /**
     * @var Application
     */
    private $application;

    /**
     * @var array
     */
    private $input;

    /**
     * InternalCronJob constructor.
     *
     * @param CronExpression $expression
     * @param string         $command
     * @param array          $params
     */
    public function __construct(CronExpression $expression, string $command, array $params = [])
    {
        parent::__construct($expression, $command);

        $this->input = $params;
    }

    /**
     * @param Application $application
     */
    public function setApplication(Application $application): void
    {
        $this->application = $application;
    }

    /**
     * {@inheritDoc}
     * @throws Exception
     */
    protected function execute(?array &$output = null): int
    {
        $result = $this->application->runAction($this->command, $this->input);
        return $result instanceof Response ? $result->exitStatus : (int)$result;
    }
}
