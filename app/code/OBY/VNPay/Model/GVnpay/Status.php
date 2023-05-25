<?php

namespace OBY\VNPay\Model;

final class GVnpay_Status {
    const SUCCESS = '00';
    const SUCCESSWITHWARNING = '07';
    const FAIL = '24';
    const ORDER_NOT_FOUND = '01';
    const INVALID_AMOUNT = '04';
    const FAIL_CHECKSUM = '97';
    const ORDER_COMFIRMED = '02';
}