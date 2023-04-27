<?php

namespace OBY\Momo\Model;

use Exception;
use OBY\Momo\Api\ApiInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\DB\LoggerInterface;
use Magento\Sales\Model\Order;
use Magento\Framework\Webapi\Rest\Request;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\OrderRepository;

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

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    public function __construct(
        Request $request,
        LoggerInterface $logger,
        ScopeConfigInterface $scopeConfig,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->request = $request;
        $this->logger = $logger;
        $this->scopeConfig = $scopeConfig;
        $this->orderRepository = $orderRepository;
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
            $config['orderId']     = $order->getId();
            $config['amount']      = $order->getTotalDue();
            $config['ipnUrl']      = $this->getConfigValue('ipn_url');
            $config['redirectUrl'] = $this->getConfigValue('redirect_url');
            
            // Production: https://payment.momo.vn
            // Sandbox: https://test-payment.momo.vn
            $config['merchant_url'] = $this->getConfigValue('sandbox_flag') == 1 ? $this->getConfigValue('sandbox_payment_url') : $this->getConfigValue('payment_url');

            $payment    = new GMomo($config);
            $payment->pay();

            if($payment->status == GMomo_Status::SUCCESS){
                $data = [
                    'success' => true,
                    'process3d_url' => $payment->process3d_url,
                ];
            } else {
                throw new Exception($payment->failReason);
            }
        } catch (\Exception $e) {
            $data = [
                'success' => false, 
                'message' => $e->getMessage()
            ];
            $this->logger->log($e->getMessage());
        }

        header('Content-Type: application/json; charset=utf-8');
        die(json_encode($data));
    }

    /**
     * @inheritdoc
     */
    public function ipn()
    {    
        $request   = $this->request->getContent();
        $request   = json_decode($request, true);

        $result_code    = $request['resultCode'];

        header('Content-Type: application/json; charset=utf-8');

        if($request['resultCode'] == GMomo_Status::SUCCESS){
            // Update order
            $extra_data     = $request['extraData'];
            $extra_data     = base64_decode($extra_data);
            $extra_data     = json_decode($extra_data);

            $orderId       = $extra_data['orderId'];
            /** @var Order $order*/
            $order = ObjectManager::getInstance()->create(Order::class)->load($orderId);
            $order->setStatus(Order::STATE_PROCESSING);

            $this->orderRepository->save($order);

            header("HTTP/1.1 204 NO CONTENT");
            return "";
        }
        else{
            $gateway = new GMomo();
            $response['resultCode']     = $result_code;
            $response['message']        = $gateway->getErrorMsg($request['resultCode']);
            ksort($response);
            $response['signature']      = $gateway->generateSignature($response);

            die(json_encode($response));
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
