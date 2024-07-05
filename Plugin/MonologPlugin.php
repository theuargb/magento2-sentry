<?php

namespace JustBetter\Sentry\Plugin;

use JustBetter\Sentry\Helper\Data;
use JustBetter\Sentry\Model\SentryLog;
use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\Logger\Monolog;
use Monolog\DateTimeImmutable;

class MonologPlugin extends Monolog
{
    protected Data $sentryHelper;
    protected SentryLog $sentryLog;
    protected DeploymentConfig $deploymentConfig;
    /**
     * {@inheritdoc}
     */
    public function __construct(
        $name,
        Data $sentryHelper,
        SentryLog $sentryLog,
        DeploymentConfig $deploymentConfig,
        array $handlers = [],
        array $processors = []
    ) {
        $this->sentryHelper = $sentryHelper;
        $this->sentryLog = $sentryLog;
        $this->deploymentConfig = $deploymentConfig;
        parent::__construct($name, $handlers, $processors);
    }

    /**
     * Adds a log record to Sentry.
     *
     * @param int    $level   The logging level
     * @param string $message The log message
     * @param array  $context The log context
     *
     * @return bool Whether the record has been processed
     */
    public function addRecord(
        $level,
        $message,
        $context = []
    ) {
        if ($this->deploymentConfig->isAvailable() && $this->sentryHelper->isActive()) {
            $this->sentryLog->send($message, $level, $context);
        }

        return parent::addRecord($level, $message, $context);
    }
}
