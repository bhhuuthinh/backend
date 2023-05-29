<?php

namespace OBY\VNPay\Model;

class GVnpay
{
    public static $key = "vnpay";

    public $merchant_url;

    public $TmnCode;
    public $accessKey;
    public $orderId;
    public $orderInfo;
    public $amount;
    public $ipnUrl;
    public $Returnurl;

    public $secret_key;

    public $process3d_url;
    public $failReason;
    public $status;

    public $transactionID;
    public $transactionDate;
    public $desc;

    public $cardType;

    public function __construct($data = null)
    {
        foreach ($data as $key   => $value) {
            $this->{$key}   = $value;
        }
    }

    public function pay($params = null)
    {
        //Expire
        // $startTime = date("YmdHis");
        // $expire = date('YmdHis',strtotime('+15 minutes',strtotime($startTime)));
        $vnp_TxnRef = rand(1, 10000); //Mã giao dịch thanh toán tham chiếu của merchant

        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $this->TmnCode,
            "vnp_Amount" => round($this->amount) * 100,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $_SERVER['REMOTE_ADDR'],
            "vnp_Locale" => 'vn',
            "vnp_OrderInfo" => "Thanh toan GD:" + $vnp_TxnRef,
            "vnp_OrderType" => "other",
            "vnp_ReturnUrl" => $this->Returnurl,
            "vnp_TxnRef" => $this->orderId,
            // "vnp_ExpireDate" => $expire
        );

        // if (isset($vnp_BankCode) && $vnp_BankCode != "") {
        //     $inputData['vnp_BankCode'] = $vnp_BankCode;
        // }

        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $this->merchant_url . "?" . $query;
        if (isset($this->secret_key)) {
            $vnpSecureHash =   hash_hmac('sha512', $hashdata, $this->secret_key);
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }
        $this->status   = GVnpay_Status::SUCCESS;
        $this->process3d_url = $vnp_Url;
    }

    public function response($request = null)
    {
        $inputData = array();
        $data = $request;
        foreach ($data as $key => $value) {
            if (substr($key, 0, 4) == "vnp_") {
                $inputData[$key] = $value;
            }
        }
        $this->transactionID    = $inputData['vnp_TransactionNo'];
        $this->transactionDate  = strtotime($inputData['vnp_PayDate']);
        $this->status           = $inputData['vnp_ResponseCode'];
        $this->cardType         = $inputData['vnp_CardType'];

        if ($inputData['vnp_ResponseCode'] != GVnpay_Status::SUCCESS) {
            $message = "Mã lỗi: " . $inputData['vnp_ResponseCode'];
            $reasonCodeText = $this->getErrorMsg($inputData['vnp_ResponseCode']);
            $message .= ", Message: $reasonCodeText.";
            $this->failReason = $message;
        }
    }

    public function getErrorMsg($params = null)
    {
        $errorList = [
            GVnpay_Status::SUCCESSWITHWARNING => 'Trừ tiền thành công. Giao dịch bị nghi ngờ (liên quan tới lừa đảo, giao dịch bất thường).',
            '09' => 'Giao dịch không thành công do: Thẻ/Tài khoản của khách hàng chưa đăng ký dịch vụ InternetBanking tại ngân hàng.',
            '10' => 'Giao dịch không thành công do: Khách hàng xác thực thông tin thẻ/tài khoản không đúng quá 3 lần',
            '11' => 'Giao dịch không thành công do: Đã hết hạn chờ thanh toán. Xin quý khách vui lòng thực hiện lại giao dịch.',
            '12' => 'Giao dịch không thành công do: Thẻ/Tài khoản của khách hàng bị khóa.',
            '13' => 'Giao dịch không thành công do Quý khách nhập sai mật khẩu xác thực giao dịch (OTP). Xin quý khách vui lòng thực hiện lại giao dịch.',
            GVnpay_Status::FAIL => 'Giao dịch không thành công do: Khách hàng hủy giao dịch',
            '51' => 'Giao dịch không thành công do: Tài khoản của quý khách không đủ số dư để thực hiện giao dịch.',
            '65' => 'Giao dịch không thành công do: Tài khoản của Quý khách đã vượt quá hạn mức giao dịch trong ngày.',
            '75' => 'Ngân hàng thanh toán đang bảo trì.',
            '79' => 'Giao dịch không thành công do: KH nhập sai mật khẩu thanh toán quá số lần quy định. Xin quý khách vui lòng thực hiện lại giao dịch',
            GVnpay_Status::ORDER_NOT_FOUND  => 'Order not found',
            GVnpay_Status::INVALID_AMOUNT  => 'Invalid amount',
            GVnpay_Status::FAIL_CHECKSUM  => 'Invalid signature',
            GVnpay_Status::SUCCESS  => 'Confirm Success',
            GVnpay_Status::ORDER_CONFIRMED  => 'Order already confirmed',
            GVnpay_Status::OTHER_ERROR  => 'Unknown error',
        ];

        return isset($errorList[$params]) ? $errorList[$params] : $this->failReason;
    }

    public function confirm($params = null)
    {
        if (is_null($params)) return;

        $new_status = $params;
        $this->status    = $new_status;
    }

    public function checkSum($inputData){
        $sign = $inputData['vnp_SecureHash'];
        unset($inputData['vnp_SecureHash']);
        
        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        if (isset($this->secret_key)) {
            $vnpSecureHash =   hash_hmac('sha512', $hashdata, $this->secret_key);
        }

        return $sign == $vnpSecureHash;
    }
}
