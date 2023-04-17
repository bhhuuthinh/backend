<?php

namespace OBY\Momo\Model;

use OBY\Momo\Api\ApiInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DB\LoggerInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

class Service implements ApiInterface
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(
        LoggerInterface $logger,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->logger = $logger;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @inheritdoc
     */
    public function captureWallet($orderId)
    {
        try {
            // Implement Your Code here
            $order      = $this->orderRepository->get($orderId);

            $config     = [];
            $config['partnerCode'] = $this->getConfigValue('merchant_name');
            $config['accessKey']   = $this->getConfigValue('access_key');
            $config['secret_key']  = $this->getConfigValue('secret_key');
            $config['orderId']     = $orderId;
            $config['amount']      = $order->getTotalDue();
            $config['ipnUrl']      = '';
            $config['redirectUrl'] = '';

            $payment    = new GMomo($config);
            $payment->pay();

            $response = [
                'success' => true,
                'process3d_url' => $payment->process3d_url,
            ];
        } catch (\Exception $e) {
            $response = [
                'success' => false, 
                'message' => $e->getMessage()
            ];
            $this->logger->log($e->getMessage());
        }

        $returnArray = json_encode($response);
        return $returnArray;
    }

    public function ipn()
    {
        
    }

    /**
     * Get the value of a configuration option.
     *
     * @return mixed
     */
    protected function getConfigValue($path)
    {
        return $this->scopeConfig->getValue('payment/momo/' . $path);
    }
}
