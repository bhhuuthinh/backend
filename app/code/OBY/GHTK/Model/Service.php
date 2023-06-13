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
use OBY\GHTK\Model\Carrier\ShippingXfast;
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

    protected $_code;

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

            $shipping_method    = $order->getShippingMethod(true);
            $this->_code        = $shipping_method->getData('method');

            $items      = $order->getItems();
            $products   = [];

            // foreach($items as $item){
            //     $_product['name']           = $item->getName();
            //     $_product['weight']         = $item->getWeight() ?? 0.2;
            //     $_product['quantity']       = round($item->getQtyOrdered(), 0);
            //     $_product['product_code']   = $item->getItemId();

            //     $products[] = $_product;
            // }
                
            $_order   = [];
            $_order["id"]               = $order->getId();
            $_order["pick_name"]        = "Ông Bà Yêu";
            
            $_order["pick_address_id"]  = 16927840;
            $_order["pick_address"]     = "71/3 Nguyen Van Thuong";
            $_order["pick_province"]    = "TP Hồ Chí Minh";
            $_order["pick_district"]    = "Quận Bình Thạnh";
            $_order["pick_ward"]        = "Phường 25";

            $_order["pick_tel"]         = "0789279669";

            $_order["name"]             = $order->getShippingAddress()->getName();
            $_order["address"]          = $order->getShippingAddress()->getStreetLine(1);
            $_order["province"]         = $order->getShippingAddress()->getCity();
            $_order["district"]         = $order->getShippingAddress()->getRegion();
            $_order["ward"]             = $order->getShippingAddress()->getStreetLine(2);
            $_order["street"]           = "";
            $_order["hamlet"]           = "Khác";
            $_order["tel"]              = $order->getShippingAddress()->getTelephone();
            $_order["email"]            = $order->getEmailCustomerNote();
            // $_order["is_freeship"]      = 0;
            $_order["total_weight"]     = 1;

            if($this->_code == ShippingXfast::DELIVER_OPTION){
                $_order["pick_money"]       = round($order->getShippingAmount());
                $_order["pick_option"]      = "cod";
                $_order["deliver_option"]   = $order->getShippingMethod();
            } else{
                $_order["pick_money"]       = 0;
            }

            // Các thông tin thêm
            $_order["value"]            = round($order->getShippingAmount());

            $instance   = new ApiCall($this->getConfigValue('base_url'), $this->getConfigValue('token_key'));
            $res        = $instance->ServicesCreateOrder([
                "products"  => $products,
                "order"  => $_order,
            ]);

            if(!$res->data->success){
                $data = [
                    'success' => false,
                    'data' => $res,
                ];
            } else{
                $data = [
                    'success' => true,
                    'data' => $res,
                ];
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
     * Get the value of a configuration option.
     *
     * @return mixed
     */
    protected function getConfigValue($path)
    {
        return $this->scopeConfig->getValue('carriers/'.$this->_code.'/'.$path);
    }
}
