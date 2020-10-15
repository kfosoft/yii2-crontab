<?php

namespace kfosoft\cron\commands;

use kfosoft\cron\CronManager;
use kfosoft\cron\jobs\AbstractCronJob;
use kfosoft\cron\jobs\InternalCronJob;
use kfosoft\daemon\Daemon;
use kfosoft\daemon\SingleJobInterface;
use Throwable;
use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\Console;
use yii\log\Logger;

class CronController extends Daemon implements SingleJobInterface
{
    /**
     * @var CronManager
     */
    private $crontabManager;

    /**
     * {@inheritdoc}
     * @throws InvalidConfigException
     */
    public function init(): void
    {
        parent::init();

        $this->crontabManager = Yii::$app->get(CronManager::COMPONENT_NAME);
    }

    /**
     * {@inheritdoc}
     * @throws Throwable
     */
    public function __invoke($job): bool
    {
        $this->stdout(' - Start cron iteration', Console::FG_YELLOW);

        $jobs = $this->crontabManager->getJobs();

        foreach ($jobs as $job) {

            if ($job instanceof InternalCronJob) {
                $job->setApplication(Yii::$app);
            }

            $outputJob = [];

            try {
                $this->stdout(sprintf('%s - Execute command "%s". ', PHP_EOL, $job->getCommand()));
                if (AbstractCronJob::EXECUTED === $job->executeIfNeeded('now', null, $outputJob)) {
                    $this->stdout(sprintf('The command "%s" was executed. Expression: %s. ', $job->getCommand(), $job->getExpression()->getExpression()), Console::FG_GREEN);
                    $this->stdout(sprintf('The output "%s" of command "%s". ', json_encode($outputJob), $job->getCommand()), Console::FG_BLUE);

                } else {
                    $this->stdout(sprintf('The command "%s" wasn\'t executed because now it\'s not needed. Expression: %s. ', $job->getCommand(), $job->getExpression()->getExpression()), Console::FG_GREEN);
                }
            } catch (Throwable $e) {
                $this->stdout(sprintf('Cron job has failed with message "%s". ', $e->getMessage()), Console::FG_RED);
                $this->crontabManager->log($e->getMessage(), Logger::LEVEL_ERROR);
            }
        }

        $this->stdout(sprintf('%s - End cron iteration %s', PHP_EOL, PHP_EOL), Console::FG_YELLOW);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function sleepTime(): int
    {
        return $this->crontabManager->daemonSleepTime;
    }
}
