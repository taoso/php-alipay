支付宝快捷支付服务端工具包
==========================

最近做的一个小项目用到了支付宝快捷支付，整理了一下服务端的代码。
希望大家少走弯路。

功能特性
========

- 生成移动sdk商品信息字符串
- 验证并解析支付宝异步通知（仅RSA签名）
- 提供 Laravel 集成支持
- 目前仅支持必选字段

使用方法
--------

### 安装

```
composer require jlyu/alipay
```

生成sdk商品信息参数示例：

```php
<?php
$alipay = new Jlyu\Alipay\SdkPayment;
// 设置商家合作id
$alipay->setPartner('2088xxxx')
    // 设置商家私钥
    ->setPrivateKey(file_get_contents(__DIR__ . '/rsa_private_key.pem'))
    // 设置支付宝公钥
    ->setPublicKey(file_get_contents(__DIR__ . '/alipay_public_key.pem'))
    // 设置异步通知链接
    ->setNotifyUrl('http://api.xxx.cn/alipay-confirmation')
    // 设置商品标题
    ->setSubject('XXXX咨询费')
    // 设置商品详情
    ->setBody('XXXX咨询费')
    // 设置商家订单号
    ->setOutTradeNo("123")
    // 设置商家支付宝账号
    ->setSellerId('18170@163.com')
    // 设置支付费用
    ->setTotalFee('0.01');

$info = $alipay->getPayInfo();
```

解析支付宝异步通知：

```php
<?php
$ali_pub_key = file_get_contents(__DIR__ . '/alipay_public_key.pem');
// POST原始数据
// 通过 file_get_contents('php://stdin') 获取
// 此处为方便测试，我们将获取的内容存入文件
$post_data = file_get_contents(__DIR__ . '/notify_raw_post.txt');

$alipay = new Jlyu\Alipay\SdkPayment;
$alipay->setPublicKey($ali_pub_key);
$data = $alipay->getNotifyInfo($post_data);
```

### Laravel 集成

在`app/config/app.php`中添加

```
'providers' => array(
    'Jlyu\Alipay\AlipayServiceProvider',
)
```

创建`app/config/pay.php`，内容如下：

```php
<?php
return [
    'ali' => [
        // 合作标识
        'partner_id'         => '2088xxx',
        // 商户私钥
        'rsa_private_key'    => file_get_contents('path/to/key.pem'),
        // 阿里公钥
        'ali_rsa_public_key' => file_get_contents('path/to/ali_key.pem'),
        // 商品名称
        'subject'            => 'XXX咨询费',
        // 商品详情
        'body'               => 'XXX咨询费',
        // 收款账号
        'account'            => '18170557321@163.com',
        // 异步通知连接
        'notify_url'         => 'http://api.xxx.cn/alipay-confirmation',
    ],
];
```

注意，支付宝的公钥文件内容为（来自支付宝官方示例程序包）：

```
-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCnxj/9qwVfgoUh/y2W89L6BkRA
FljhNhgPdyPuBV64bfQNN1PjbCzkIM6qRdKBoLPXmKKMiFYnkd6rAoprih3/PrQE
B/VsW8OoM8fxn67UDYuyBTqA23MML9q1+ilIZwBC2AQ2UBVOrFXfFl75p6/B5Ksi
NG9zpgmLCUYuLkxpLQIDAQAB
-----END PUBLIC KEY-----
```

商户私钥文件格式如下：

```
-----BEGIN RSA PRIVATE KEY-----
MIICXgIBAAKBgQCVSuqRO99TThu8gM1drM8ayF2EtjK8vcYRjSjIa0GD1q6aC2i+
yc+artdGcWkr9JqXeScnCgi9F0w0r+e0pp2EBVPuNkbWzDRcpPvC/eSSHVauc+zs
0krjhTGoHZRvrXuCAqqjD878Eb5QiOGgpupneTjivMH3dQP9gPQ5lIkMUQIDAQAB
AoGASxgM1xLbqaZ/Uxkis7zJy/n++FNBQCjg6VEss2sn4C3rU3brsBQJBhBuHnPL
aB24aPq60+s7LAn8/f+BOCAa2Fm6E7XGTVLlCD4DQKnwz3PxUC1zJPfjQ1RBwSw/
Hc7Ry7Ihmm4LlMvFAHDhAwcm/sxCkIh1IysJdBH2LnNJTgUCQQDF262IP8/YmhvZ
76qrIvYuZt4yD7wIypsz1+aExwwSuamZZ4GE4FhCT+SyrcI7+T8A8l8uFip6hJo1
wR65A2tbAkEAwSnKkB0K3yz6stZdqabsN94JYG2B1rGEHElCIPs49IZ6v4+2
FXOefQFPtV/BH9788lK50TRcGiuywwJBAKJg5GzSOejAqSTVvZJP0gxI3gfl
w+LPLEqC4KuNk8n2V0sPmEsNt0FMwhsl4SdJKbzELPrstBXyp4CAKo+aT8sCQQCo
PLXofuMRLiPLz1kUggYLQp/4FGiZjVL7L/Mgtq4Mi3QtlAX0OVEcwVzsQh8v0QyT
dCrIj3bPt62PwILXaGfxAkEAp1TzjiWA+aAj+CIOyASwZUxwLO+RnUW7tXc6hWnQ
NoIGoCo2hkC8FeJjIZ8Du9V0itcDEwYr2rsm5J1/8YXuQQ==
-----END RSA PRIVATE KEY-----
```
