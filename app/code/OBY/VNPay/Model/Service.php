<?php

namespace OBY\VNPay\Model;

use Exception;
use OBY\VNPay\Api\ApiInterface;
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
    public function createOrder($orderId)
    {
        try {
            // Implement Your Code here
            /** @var Order $order*/
            $order = ObjectManager::getInstance()->create(Order::class)->load($orderId);
            $config     = [];
            $config['TmnCode'] = $this->getConfigValue('vnp_TmnCode');
            $config['secret_key']  = $this->getConfigValue('secret_key');
            $config['orderId']     = $order->getId();
            $config['amount']      = $order->getTotalDue();
            $config['ipnUrl']      = $this->getConfigValue('ipn_url');
            $config['Returnurl'] = $this->getConfigValue('vnp_Returnurl');
            
            // Production: https://payment.momo.vn
            // Sandbox: https://test-payment.momo.vn
            $config['merchant_url'] = $this->getConfigValue('sandbox_flag') == 1 ? $this->getConfigValue('sandbox_payment_url') : $this->getConfigValue('payment_url');

            $payment    = new GVnpay($config);
            $payment->pay();

            if($payment->status == GVnpay_Status::SUCCESS){
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
        return [$data];
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

        if($result_code == GVnpay_Status::SUCCESS){
            // Update order
            $extra_data     = $request['extraData'];
            $extra_data     = base64_decode($extra_data);
            $extra_data     = json_decode($extra_data, true);

            $orderId       = $extra_data['orderId'];
            /** @var Order $order*/
            $order = ObjectManager::getInstance()->create(Order::class)->load($orderId);
            $order->setStatus(Order::STATE_PROCESSING);

            $this->orderRepository->save($order);

            header("HTTP/1.1 204");
            exit;
        }
        else{
            $gateway = new GVnpay();
            $response['resultCode']     = $result_code;
            $response['message']        = $gateway->getErrorMsg($request['resultCode']);
            ksort($response);
            // $response['signature']      = $gateway->generateSignature($response);

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
        return $this->scopeConfig->getValue('payment/vnpay/' . $path);
    }
}
