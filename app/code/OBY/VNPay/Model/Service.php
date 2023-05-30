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

            $config['merchant_url'] = $this->getConfigValue('sandbox_flag') == 1 ? $this->getConfigValue('sandbox_payment_url') : $this->getConfigValue('payment_url');

            $payment    = new GVnpay($config);
            $payment->pay();

            $dir    = '../var/log/vnpay';
            if (!is_dir($dir)) {
                mkdir($dir);
            }
            $dir .= '/' . date("mY", time());
            if (!is_dir($dir)) {
                mkdir($dir);
            }
            file_put_contents($dir . '/create_order_' . $order->getId() . '.log', $payment->process3d_url);

            if ($payment->status == GVnpay_Status::SUCCESS) {
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

        if (!in_array($_SERVER['REMOTE_ADDR'], [
            '113.160.92.202',
            '113.52.45.78',
            '116.97.245.130',
            '42.118.107.252',
            '113.20.97.250',
            '203.171.19.146',
            '103.220.87.4',
            '103.220.86.4',
        ])) {
            http_response_code(404);
            die();
        }
        try {
            $dir    = '../var/log/vnpay';
            if (!is_dir($dir)) {
                mkdir($dir);
            }
            $dir .= '/' . date("mY", time());
            if (!is_dir($dir)) {
                mkdir($dir);
            }
            $file_name = 'ipn_'. $this->request->get('vnp_TxnRef') . '_' . microtime(true) . '.log';
            file_put_contents($dir . '/' . $file_name , $_SERVER['REMOTE_ADDR'].json_encode($_GET));

            $result_code    = $this->request->get('vnp_ResponseCode');

            header('Content-Type: application/json; charset=utf-8');

            $config     = [];
            $config['TmnCode'] = $this->getConfigValue('vnp_TmnCode');
            $config['secret_key']  = $this->getConfigValue('secret_key');
            $config['ipnUrl']      = $this->getConfigValue('ipn_url');
            $config['Returnurl'] = $this->getConfigValue('vnp_Returnurl');

            $config['merchant_url'] = $this->getConfigValue('sandbox_flag') == 1 ? $this->getConfigValue('sandbox_payment_url') : $this->getConfigValue('payment_url');

            $gateway    = new GVnpay($config);
            // checksum
            if (!$gateway->checkSum($_GET)) {
                $result_code = GVnpay_Status::FAIL_CHECKSUM;
                goto return_value;
            }

            try {
                // Update order
                $orderId    = $this->request->get('vnp_TxnRef');

                /** @var Order $order*/
                $order = $this->orderRepository->get($orderId);

                if (round($order->getTotalDue()) * 100 != $this->request->get('vnp_Amount')) {
                    $result_code = GVnpay_Status::INVALID_AMOUNT;
                    goto return_value;
                }

                if ($order->getStatus() == Order::STATE_PROCESSING || $order->getStatus() == Order::STATE_CANCELED) {
                    $result_code = GVnpay_Status::ORDER_CONFIRMED;
                    goto return_value;
                }

                if ($result_code == GVnpay_Status::SUCCESS) {
                    // Payment success
                    $order->setStatus(Order::STATE_PROCESSING);
                    $this->orderRepository->save($order);
                    $result_code    = GVnpay_Status::SUCCESS;
                    goto return_value;
                } else {
                    // Payment fail
                    $order->setStatus(Order::STATE_CANCELED);
                    $this->orderRepository->save($order);
                    $result_code    = GVnpay_Status::SUCCESS;
                    goto return_value;
                }
            } catch (Exception $e2) {
                $result_code    = GVnpay_Status::ORDER_NOT_FOUND;
                goto return_value;
            }
        } catch (Exception $e) {
            $result_code    = GVnpay_Status::OTHER_ERROR;
            goto return_value;
        }

        return_value:
        $response               = [];
        $response['RspCode']    = $result_code;
        $response['Message']    = $gateway->getErrorMsg($result_code);
        ksort($response);
        die(json_encode($response));
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
