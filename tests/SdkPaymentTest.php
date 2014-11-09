<?php

class SdkPaymentTest extends PHPUnit_Framework_TestCase {
    public function testGetPayInfo() {
        $alipay = new Jlyu\Alipay\SdkPayment;
        $alipay->setPartner('2088611164564671')
            ->setPrivateKey(file_get_contents(__DIR__ . '/rsa_private_key.pem'))
            ->setPublicKey(file_get_contents(__DIR__ . '/alipay_public_key.pem'))
            ->setNotifyUrl('http://api.boctor.cn/alipay-confirmation')
            ->setSubject('别扛着咨询费')
            ->setBody('别扛着咨询费')
            ->setOutTradeNo("123")
            ->setSellerId('18170557321@163.com')
            ->setTotalFee('0.01')
            ;
        $info = $alipay->getPayInfo();

        $this->assertStringEqualsFile(
            __DIR__ . '/order_info.txt',
            $info
        );
    }

    public function testGetNotifyInfo() {
        $ali_pub_key = file_get_contents(__DIR__ . '/alipay_public_key.pem');
        $post_data = file_get_contents(__DIR__ . '/notify_raw_post.txt');

        $alipay = new Jlyu\Alipay\SdkPayment;
        $alipay->setPublicKey($ali_pub_key);
        $data = $alipay->getNotifyInfo($post_data);

        $this->assertJsonStringEqualsJsonFile(
            __DIR__ . '/notify_info.json',
            json_encode($data, JSON_UNESCAPED_UNICODE)
        );
    }
}
