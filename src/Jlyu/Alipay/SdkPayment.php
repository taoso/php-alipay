<?php
namespace Jlyu\Alipay;

class SdkPayment {
    private $service = 'mobile.securitypay.pay';
    private $partner;
    private $_input_charset = 'UTF-8';
    private $private_key;
    private $public_key;
    private $notify_url;
    private $out_trade_no;
    private $subject;
    private $payment_type = 1;
    private $seller_id;
    private $total_fee;
    private $body;

    private $__no_need_sign_key = array( 'private_key', 'public_key' );

    /**
     * @return string 支付宝无线快捷支付pay方法参数
     */
    public function getPayInfo() {
        $info_str = $this->getInfoStr();
        $sign = $this->rsaSign($info_str);

        return $info_str . '&sign="' . urlencode($sign) . '"&sign_type="RSA"';
    }

    private function getInfoStr() {
        $str = [];
        foreach (get_object_vars($this) as $key => $value) {
            if (substr($key, 0, 2) === '__') continue;
            if (in_array($key, $this->__no_need_sign_key)) continue;
            if (!$value) throw new \Exception("$key is valid!");
            $str[] = $key . '="' . $this->$key . '"';
        }
        sort($str);

        return join('&', $str);
    }

    private function rsaSign($data) {
        $key = openssl_get_privatekey($this->private_key);
        openssl_sign($data, $sign, $key);
        openssl_free_key($key);

        return base64_encode($sign);
    }

    /**
     * @return 成功返回支付宝异步通知内容数组，失败返回false
     */
    public function getNotifyInfo($raw_post_data) {
        $data_items = [];
        $sign = '';

        $raw_post_data = explode('&', $raw_post_data);
        foreach ($raw_post_data as $item) {
            $item = urldecode($item);
            if (substr($item, 0, 10) == 'sign_type=') continue;
            if (substr($item, 0, 5) == 'sign=') {
                $sign = substr($item, 5);
                continue;
            }
            $data_items[] = $item;
        }
        sort($data_items);
        $to_sign_str = join('&', $data_items);

        $ali_pub_key = openssl_get_publickey($this->public_key);
        $isValid = (bool) openssl_verify(
            $to_sign_str, base64_decode($sign), $ali_pub_key);
        openssl_free_key($ali_pub_key);

        if (!$isValid) {
            return false;
        }

        $info = [];
        foreach ($data_items as $item) {
            list($name, $value) = explode('=', $item);
            $info[$name] = $value;
        }

        return $info;
    }

    public function setBody($body) {
        $this->body = $body;
        return $this;
    }

    public function setNotifyUrl($notify_url) {
        $this->notify_url = urlencode($notify_url);
        return $this;
    }

    public function setOutTradeNo($out_trade_no) {
        $this->out_trade_no = $out_trade_no;
        return $this;
    }

    public function setPartner($partner) {
        $this->partner = $partner;
        return $this;
    }

    public function setPrivateKey($private_key) {
        $this->private_key = $private_key;
        return $this;
    }

    public function setPublicKey($public_key) {
        $this->public_key = $public_key;
        return $this;
    }

    public function setSellerId($seller_id) {
        $this->seller_id = $seller_id;
        return $this;
    }

    public function setSubject($subject) {
        $this->subject = $subject;
        return $this;
    }

    public function setTotalFee($total_fee) {
        $this->total_fee = $total_fee;
        return $this;
    }
}
