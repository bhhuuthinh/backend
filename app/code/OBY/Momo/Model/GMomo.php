<?php

namespace OBY\Momo\Model;

class GMomo
{
    public static $key = "momo";

    public $merchant_url;

    public $partnerCode;
    public $accessKey;
    public $orderId;
    public $orderInfo;
    public $amount;
    public $ipnUrl;
    public $redirectUrl;

    public $secret_key;

    public $process3d_url;
    public $failReason;
    public $status;

    public function __construct($data = null)
    {
        foreach($data as $key   => $value){
            $this->{$key}   = $value;
        }
    }

    protected function execPostRequest($request_json)
    {
        $http           = new HTTP();
        $http->url      = $this->merchant_url;
        $http->headers  = [
            'Content-Type'  => 'application/json',
            'Content-Length'=> strlen(json_encode($request_json)),
        ];

        $result = $http->post('/v2/gateway/api/create', $request_json);
        $result->data   = json_decode($result->data);

        if($result->isSuccess)
        {
            return $result->data;
        }
        else
        {
            $this->status = GMomo_Status::FAIL;
            $this->failReason = $result->data->message;
            return false;
        }
    }

    protected function getPayload(){
        $payload = [
            'partnerCode'   => $this->partnerCode,
            'accessKey'     => $this->accessKey,
            'orderId'       => $this->transactionID,
            'orderInfo'     => $this->desc,
            'amount'        => $this->amount,
            'ipnUrl'        => $this->ipnUrl,
            'redirectUrl'   => $this->redirectUrl,
            'requestType'   => 'captureWallet',
            'extraData'     => '',
            'requestId'     => time() . "",
        ];

        return $payload;
    }

    public function pay($params = null){
        $this->transactionID    = uniqid("GD".time());
        $this->desc             = "#DH".str_pad($this->orderId, 9, "0", STR_PAD_LEFT);

        $payload                = $this->getPayload();
        $signature              = $this->generateSignature($payload);
        $payload['lang']        = 'vi';
        $payload['signature']   = $signature;

        $result                 = $this->execPostRequest($payload);

        if($result->resultCode == GMomo_Status::SUCCESS){
            $this->process3d_url    = $result->payUrl;
            return;
        }

        $this->status = GMomo_Status::FAIL;
        $this->failReason = $result->message;
    }

    public function response($request = null){
        $partnerCode    = $_GET["partnerCode"];
        $orderId        = $_GET["orderId"];
        $requestId      = $_GET["requestId"];
        $amount         = $_GET["amount"];
        $orderInfo      = $_GET["orderInfo"];
        $orderType      = $_GET["orderType"];
        $transId        = $_GET["transId"];
        $resultCode     = $_GET['resultCode'];
        $message        = $_GET["message"];
        $payType        = $_GET["payType"];
        $responseTime   = $_GET["responseTime"];
        $extraData      = $_GET["extraData"];
        $m2signature    = $_GET["signature"]; //MoMo signature

        $rawHash = "accessKey=" . $this->accessKey . "&amount=" . $amount . "&extraData=" . $extraData . "&message=" . $message . "&orderId=" . $orderId . "&orderInfo=" . $orderInfo .
            "&orderType=" . $orderType . "&partnerCode=" . $partnerCode . "&payType=" . $payType . "&requestId=" . $requestId . "&responseTime=" . $responseTime . "&resultCode=" .$resultCode .
            "&transId=" .$transId;
        $partnerSignature = hash_hmac("sha256", $rawHash, $this->secret_key);

        if ($m2signature == $partnerSignature) {
            $this->failReason = $message;
        } else {
            $this->failReason = "This transaction could be hacked, please check your signature and returned signature";
        }
    }

    public function getErrorMsg($params = null){
        $errorList = [
            GMomo_Status::SUCCESS   => 'Giao dịch thành công.',
            GMomo_Status::FAIL      => 'Giao dịch thất bại.',
            99      => 'Chữ ký không hợp lệ.',
            9000    => 'Giao dịch đã được xác nhận thành công.',
            42      => 'Giao dịch không hợp lệ hoặc không được tìm thấy.',
        ];

        return isset($errorList[$params]) ? $errorList[$params] : $this->failReason;
    }

    public function confirm($params = null) {
        if(is_null($params)) return;
        $this->status   = $params;
    }

    public function checkSum(array $data): bool
    {
        if(isset($data['signature']))
            unset($data['signature']);

        ksort($data);
        $sign = $this->generateSignature($data);
        return $sign == $data['signature'];
    }

    public function generateSignature(array $data): string
    {
        $data['accessKey']  = $this->accessKey;
        ksort($data);
        $query  = http_build_query($data);
        $query  = urldecode($query);
        return hash_hmac("sha256", $query, $this->secret_key);
    }
}