<?php
/**
 * Clive Walkden
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category    Clive Walkden
 * @package     CliveWalkden_CheckoutSuccess
 * @copyright   Copyright (c) 2018 Clive Walkden (https://clivewalkden.co.uk)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 */

namespace CliveWalkden\CheckoutSuccess\Magento\Checkout\Model\Session;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Api\Search\SearchCriteriaFactory;
use Magento\Checkout\Model\Session;
use Magento\Checkout\Model\Session\SuccessValidator as MagentoSuccessValidator;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderRepository;
use Magento\Sales\Model\ResourceModel\Order\Collection;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Store\Model\ScopeInterface;
use Psr\Log\LoggerInterface;

/**
 * Class SuccessValidator
 * @package Vendor\Module\Plugin\Magento\Checkout\Model\Session
 */
class SuccessValidator
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $_orderCollectionFactory;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * SuccessValidator constructor.
     * @param LoggerInterface $logger
     * @param CollectionFactory $orderCollectionFactory
     * @param Session $checkoutSession
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        LoggerInterface $logger, //log injection
        CollectionFactory $orderCollectionFactory,
        Session $checkoutSession,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->_logger = $logger;
        $this->_checkoutSession = $checkoutSession;
        $this->_orderCollectionFactory = $orderCollectionFactory;
        $this->_scopeConfig = $scopeConfig;
    }

    /**
     * @param \Magento\Checkout\Model\Session\SuccessValidator $successValidator
     * @param boolean $returnValue
     * @return boolean
     */
    public function afterIsValid(MagentoSuccessValidator $successValidator, $returnValue)
    {
        $this->_logger->addDebug('after called');

        if ($this->_scopeConfig->getValue('dev/checkoutsuccess/enable',
            ScopeInterface::SCOPE_STORE)) {
            /** @var Order $order */
            $order = $this->_orderCollectionFactory->create()
                ->setPageSize(1)
                ->setOrder('entity_id', 'DESC')
                ->addFieldToFilter('status', ['in' => ['complete', 'processing']])
                ->getFirstItem();
            $this->_logger->addDebug('Order ID: ' . $order->getId());
            $this->_logger->addDebug('Customer Email: ' . $order->getCustomerEmail());

            if ($order->getId()) {
                $this->_logger->addDebug('order found');
                $this->_checkoutSession->setLastOrderId($order->getId());
                $this->_checkoutSession->setLastQuoteId($order->getQuoteId());
                $this->_checkoutSession->setLastSuccessQuoteId($order->getQuoteId());
                $this->_logger->addDebug('return true');
                return true;
            }
        }

        $this->_logger->addDebug('return default');
        return $returnValue;
    }
}
