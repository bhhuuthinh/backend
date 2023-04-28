<?php 

namespace OBY\GHTK\Model;

class ApiCall
{
    protected static $instance;
    protected $token_key;
    protected $base_url;

    private $http;

    private const ACTION_SERVICES_SHIPMENT_FEE = '/services/shipment/fee';
    private const ACTION_SERVICES_CREATE_ORDER = '/services/shipment/order/?ver=1.5';

    private function buildHeaders(){
        return [
			'Token' => $this->token_key,
		];
    }

    public function __construct($base_url, $token_key)
    {
        $this->token_key    = $token_key;
        $this->base_url     = $base_url;

        $http = new HTTP();
        $http->url  = $this->base_url;
        $http->headers  = $this->buildHeaders();

        $this->http = $http;
    }

    protected function post($end_point, $params = null)
    {
        $result     = $this->http->post($end_point, $params);
        $result     = json_decode($result);
		$result	    = json_decode($result->data);

        return $result;
    }

    protected function get($end_point, $query = null)
    {
        $result     = $this->http->get($end_point, $query);
        $result     = json_decode($result);
		$result	    = json_decode($result->data);

        return $result;
    }

    /** @return mixed */
    public function ServicesShipmentFee($params){
        return $this->get(self::ACTION_SERVICES_SHIPMENT_FEE, $params);
    }

    /** @return mixed */
    public function ServicesCreateOrder($params){
        return $this->get(self::ACTION_SERVICES_CREATE_ORDER, $params);
    }

}
