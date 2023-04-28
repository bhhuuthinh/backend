<?php

namespace OBY\GHTK\Model;

use Exception;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\DB\LoggerInterface;
use Magento\Sales\Model\Order;
use Magento\Framework\Webapi\Rest\Request;
use Magento\Sales\Api\OrderRepositoryInterface;
use OBY\GHTK\Api\ApiInterface;
use stdClass;

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
            $order      = $this->orderRepository->get($orderId);
            
            $items      = $order->getItems();
            $products   = [];

            foreach($items as $item){
                $_product['name']           = $item->getName();
                $_product['weight']         = $item->getWeight();
                $_product['quantity']       = $item->getQtyOrdered();
                $_product['product_code']   = $item->getItemId();

                $products[] = $_product;
            }
                
            $_order   = [];
            $_order["id"]               = $order->getId();
            $_order["pick_address_id"]  = 16927840;
            $_order["tel"]              = $order->getShippingAddress()->getTelephone();
            $_order["name"]             = $order->getShippingAddress()->getName();
            $_order["address"]          = $order->getShippingAddress()->getStreetLine(1);
            $_order["province"]         = $order->getShippingAddress()->getCity();
            $_order["district"]         = $order->getShippingAddress()->getRegion();
            $_order["ward"]             = $order->getShippingAddress()->getStreetLine(2);
            $_order["hamlet"]           = 'Khac';
            $_order["is_freeship"]      = 0;
            $_order["pick_date"]        = $order;
            $_order["pick_money"]       = $order->getShippingAmount();
            $_order["value"]            = 0;
            // $_order["pick_option"]      = $order; // COD POST
            $_order["deliver_option"]   = $order->getShippingMethod();
            // $_order["pick_session"]     = $order;
            // $_order["tags"]             = $order;

            $instance   = new ApiCall($this->getConfigValue('base_url'), $this->getConfigValue('token_key'));
            $res        = $instance->ServicesCreateOrder([
                "products"  => $products,
                "order"  => $order,
            ]);
            return json_encode($res);
            
            $payment = new stdClass();
            if($payment->status == 0){
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
        return json_encode($data);
    }

    /**
     * Get the value of a configuration option.
     *
     * @return mixed
     */
    protected function getConfigValue($path)
    {
        return $this->scopeConfig->getValue('payment/ghtk/' . $path);
    }
}
