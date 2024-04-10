<?php

namespace JustBetter\Sentry\Plugin;

use Magento\Customer\CustomerData\Customer;
use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Customer\Model\Session;

class LogrocketCustomerInfo
{
    protected CurrentCustomer $currentCustomer;
    protected Session $customerSession;
    public function __construct(CurrentCustomer $currentCustomer, Session $customerSession)
    {
        $this->currentCustomer = $currentCustomer;
        $this->customerSession = $customerSession;
    }

    public function afterGetSectionData(Customer $subject, $result)
    {
        if ($this->customerSession->isLoggedIn()) {
            $customer = $this->currentCustomer->getCustomer();

            $result['email'] = $customer->getEmail();
            $result['fullname'] = $customer->getFirstname().' '.$customer->getLastname();
        }

        return $result;
    }
}
