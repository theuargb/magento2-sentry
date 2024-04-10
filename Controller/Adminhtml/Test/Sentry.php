<?php

namespace JustBetter\Sentry\Controller\Adminhtml\Test;

use JustBetter\Sentry\Helper\Data;
use JustBetter\Sentry\Plugin\MonologPlugin;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Result\PageFactory;
use Psr\Log\LoggerInterface;

class Sentry extends Action
{
    /**
     * @var PageFactory
     */
    protected PageFactory $resultPageFactory;
    /**
     * @var Json
     */
    private Json $jsonSerializer;
    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;
    /**
     * @var Data
     */
    private Data $helperSentry;
    /**
     * @var MonologPlugin
     */
    private MonologPlugin $monologPlugin;
    /**
     * Authorization level of a basic admin session.
     *
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'JustBetter_Sentry::sentry';

    /**
     * Sentry constructor.
     *
     * @param Context         $context
     * @param PageFactory     $resultPageFactory
     * @param Json            $jsonSerializer
     * @param LoggerInterface $logger
     * @param Data            $helperSentry
     * @param MonologPlugin   $monologPlugin
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Json $jsonSerializer,
        LoggerInterface $logger,
        Data $helperSentry,
        MonologPlugin $monologPlugin
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->jsonSerializer = $jsonSerializer;
        $this->logger = $logger;
        $this->helperSentry = $helperSentry;
        $this->monologPlugin = $monologPlugin;
        parent::__construct($context);
    }

    /**
     * Execute view action.
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $result = ['status' => false];

        $activeWithReason = $this->helperSentry->isActiveWithReason();

        if ($activeWithReason['active']) {
            try {
                if ($this->helperSentry->isPhpTrackingEnabled()) {
                    $this->monologPlugin->addRecord(\Monolog\Logger::ALERT, 'TEST message from Magento 2', []);
                    $result['status'] = true;
                    $result['content'] = __('Check sentry.io which should hold an alert');
                } else {
                    $result['content'] = __('Php error tracking must be enabled for testing');
                }
            } catch (\Exception $e) {
                $result['content'] = $e->getMessage();
                $this->logger->critical($e);
            }
        } else {
            $result['content'] = implode(PHP_EOL, $activeWithReason['reasons']);
        }

        return $this->getResponse()->representJson(
            $this->jsonSerializer->serialize($result)
        );
    }
}
