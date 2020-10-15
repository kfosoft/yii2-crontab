<?php

namespace kfosoft\cron;

use kfosoft\cron\commands\CronController;
use kfosoft\cron\jobs\AbstractCronJob;
use Cron\CronExpression;
use kfosoft\cron\jobs\CommandType;
use Yii;
use yii\base\BootstrapInterface;
use yii\base\Component;
use yii\helpers\ArrayHelper;
use yii\log\Logger;

class CronManager extends Component implements BootstrapInterface
{
    public const COMPONENT_NAME = 'cron-manager';

    /**
     * @var array|CronExpression[]
     */
    public $tab = [];

    /**
     * @var int
     */
    public $daemonSleepTime = 60;

    /**
     * @var array
     */
    private $jobs = [];

    /**
     * {@inheritdoc}
     */
    public function bootstrap($app): void
    {
        $app->controllerMap = ArrayHelper::merge($app->controllerMap, ['cron' => CronController::class]);
    }

    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        parent::init();

        foreach ($this->tab as $config) {
            $commandClassName = CommandType::getClassNameType($config['type']);

            $params = [
                new CronExpression($config['expression']),
                $config['command'],
            ];

            if (CommandType::INTERNAL === $commandClassName && isset($config['params'])) {
                $params[] = $config['params'];
            }

            $this->jobs[] = new $commandClassName(...$params);
        }
    }

    /**
     * @return array|AbstractCronJob[]
     */
    public function getJobs(): array
    {
        return $this->jobs;
    }

    /**
     * @param string $message
     * @param int $level
     */
    public function log(string $message, $level = Logger::LEVEL_TRACE): void
    {
        Yii::$app->log->logger->log($message, $level);
        Yii::$app->log->logger->flush(true);
    }
}
