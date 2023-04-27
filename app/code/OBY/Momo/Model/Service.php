<?php

namespace OBY\Momo\Model;

use Exception;
use OBY\Momo\Api\ApiInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\DB\LoggerInterface;
use Magento\Sales\Model\Order;
use Magento\Framework\Webapi\Rest\Request;

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

    /**
     * @var Request
     */
    protected $request;

    public function __construct(
        Request $request,
        LoggerInterface $logger,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->request = $request;
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
            $config['ipnUrl']      = $this->getConfigValue('ipn_url');
            $config['redirectUrl'] = $this->getConfigValue('redirect_url');
            
            // Production: https://payment.momo.vn
            // Sandbox: https://test-payment.momo.vn
            $config['merchant_url'] = $this->getConfigValue('sandbox_flag') == 1 ? $this->getConfigValue('sandbox_payment_url') : $this->getConfigValue('payment_url');

            $payment    = new GMomo($config);
            $payment->pay();

            if($payment->status == GMomo_Status::SUCCESS){
                $response = [
                    'success' => true,
                    'process3d_url' => $payment->process3d_url,
                ];
            } else {
                throw new Exception($payment->failReason);
            }
        } catch (\Exception $e) {
            $response = [
                'success' => false, 
                'message' => $e->getMessage()
            ];
            $this->logger->log($e->getMessage());
        }

        return $response;
    }

    /**
     * @inheritdoc
     */
    public function ipn()
    {    
        $request   = $this->request->getContent();
        $request   = json_decode($request, true);
        $result_code    = $request['resultCode'];

        if($request['resultCode'] == GMomo_Status::SUCCESS){
            // Update order
            return "";
        }
        else{
            $gateway = new GMomo();
            $response['resultCode']     = $result_code;
            $response['message']        = $gateway->getErrorMsg($request['resultCode']);
            ksort($response);
            $response['signature']      = $gateway->generateSignature($response);
            return $response;
        }
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
