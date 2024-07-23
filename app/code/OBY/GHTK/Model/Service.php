<?php

namespace OBY\GHTK\Model;

use Exception;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DB\LoggerInterface;
use Magento\Sales\Model\Order;
use Magento\Framework\Webapi\Rest\Request;
use Magento\Sales\Api\OrderRepositoryInterface;
use OBY\GHTK\Api\ApiInterface;
use OBY\GHTK\Model\Carrier\Shipping;
use OBY\GHTK\Model\Carrier\ShippingXfast;

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

    protected $productRepository;

    protected $_code;

    public function __construct(
        Request $request,
        LoggerInterface $logger,
        ScopeConfigInterface $scopeConfig,
        OrderRepositoryInterface $orderRepository,
        ProductRepository $productRepository
    ) {
        $this->request = $request;
        $this->logger = $logger;
        $this->scopeConfig = $scopeConfig;
        $this->orderRepository = $orderRepository;
        $this->productRepository = $productRepository;
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
            $pick_address_id = null;

            /** @var \Magento\Sales\Model\Order\Item\Interceptor $item */
            foreach($items as $item){
                $_product['name']           = $item->getName();
                $_product['weight']         = $item->getWeight() ?? 0.2;
                $_product['quantity']       = round($item->getQtyOrdered(), 0);
                $_product['product_code']   = $item->getItemId();

                if(empty($pick_address_id)){
                    $product            = $this->productRepository->getById($item->getProductId());
                    $pick_address_id    = $product->getCustomAttribute('warehouse')->getValue();
                }
                $products[] = $_product;
            }

            $pick_address_id            = $pick_address_id ?: $this->getConfigValue('pick_address_id');

            $_order   = [];
            $_order["id"]               = $order->getId().'-'.time();
            $_order["pick_name"]        = "Agriamazing";

            $_order["pick_address_id"]  = $pick_address_id;
            $_order["pick_tel"]         = $this->getConfigValue('pick_tel');
            $_order["pick_province"]    = "Hà Nội";
            $_order["pick_district"]    = "Hoàn Kiếm";
            $_order["pick_address"]     = "Số 1";

            $_order["name"]             = $order->getShippingAddress()->getName();
            $_order["address"]          = $order->getShippingAddress()->getStreetLine(1);
            $_order["province"]         = $order->getShippingAddress()->getCity();
            $_order["district"]         = $order->getShippingAddress()->getRegion();
            $_order["ward"]             = $order->getShippingAddress()->getStreetLine(2);
            $_order["street"]           = "";
            $_order["hamlet"]           = "Khác";
            $_order["tel"]              = $order->getShippingAddress()->getTelephone();
            $_order["email"]            = $order->getEmailCustomerNote();

            $_order["is_freeship"]      = 0;

            // Get the payment information
            $payment = $order->getPayment();

            // Get the payment method code
            $paymentMethodCode = $payment->getMethod();
            if($paymentMethodCode == 'cashondelivery'){
                $_order["pick_money"]       = round($order->getGrandTotal());
                $_order["pick_option"]      = "cod";
            } else {
                $_order["pick_money"]       = 0;
            }

            // if($this->_code == 'ghtkxfast'){
            //     $_order["deliver_option"]   = ShippingXfast::DELIVER_OPTION;
            // } else if ($this->_code == 'ghtk') {
            //     $_order["deliver_option"]   = Shipping::DELIVER_OPTION;
            // }

            // $_order["total_weight"]     = 1;

            // Các thông tin thêm
            $_order["value"]            = round($order->getGrandTotal());

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
