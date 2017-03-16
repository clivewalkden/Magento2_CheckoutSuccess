<?php

namespace Sozo\CheckoutSuccess\Magento\Checkout\Model\Session;

use Magento\Framework\Api\Search\SearchCriteriaFactory;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderRepository;
use Magento\Sales\Model\ResourceModel\Order\Collection;

/**
 * Class SuccessValidator
 * @package Vendor\Module\Plugin\Magento\Checkout\Model\Session
 */
class SuccessValidator
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $orderCollectionFactory;

    /**
     * SuccessValidator constructor.
     * @param OrderRepository $orderRepository
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */
    public function __construct(
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->orderCollectionFactory = $orderCollectionFactory;
    }

    /**
     * @param \Magento\Checkout\Model\Session\SuccessValidator $successValidator
     * @param boolean $returnValue
     * @return boolean
     */
    public function afterIsValid(\Magento\Checkout\Model\Session\SuccessValidator $successValidator, $returnValue)
    {
        /** @var Order $order */
        $order = $this->orderCollectionFactory->create()
            ->setPageSize(1)
            ->setOrder('entity_id', 'DESC')
            ->addFieldToFilter('status', ['eq' => 'complete'])
            ->getFirstItem();

        if ($order->getId()) {
            $this->checkoutSession->setLastOrderId($order->getId());
            $this->checkoutSession->setLastQuoteId($order->getQuoteId());
            $this->checkoutSession->setLastSuccessQuoteId($order->getQuoteId());
            return true;
        }

        return $returnValue;
    }
}
