<?php

namespace OBY\Momo\Model;

use OBY\Momo\Api\ApiInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\DB\LoggerInterface;
use Magento\Sales\Model\Order;

class Service implements ApiInterface
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

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
            /** @var Order $order*/
            $order = ObjectManager::getInstance()->create(Order::class)->load($orderId);

            $config     = [];
            $config['partnerCode'] = $this->getConfigValue('partner_code');
            $config['accessKey']   = $this->getConfigValue('access_key');
            $config['secret_key']  = $this->getConfigValue('secret_key');
            $config['orderId']     = $orderId;
            $config['amount']      = $order->getTotalDue();
            $config['ipnUrl']      = 'http://13.212.189.157/';
            $config['redirectUrl'] = 'http://13.212.189.157/';
            
            // Production: https://payment.momo.vn
            // Sandbox: https://test-payment.momo.vn
            $config['merchant_url'] = $this->getConfigValue('sandbox_flag') == 1 ? $this->getConfigValue('sandbox_payment_url') : $this->getConfigValue('payment_url');

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
