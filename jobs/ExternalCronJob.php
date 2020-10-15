<?php

namespace kfosoft\cron\jobs;

class ExternalCronJob extends AbstractCronJob
{
    /**
     * {@inheritdoc}
     */
    protected function execute(?array &$output = null): int
    {
        exec($this->command, $output, $return);

        return $return;
    }
}
