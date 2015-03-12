<?php namespace Lv\Alipay;

use Illuminate\Support\ServiceProvider;

class AlipayServiceProvider extends ServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('alipay.sdk', function () {
            $pay = new SdkPayment;
            $pay ->setPartner(\Config::get('pay.ali.partner_id'))
                ->setPrivateKey(\Config::get('pay.ali.rsa_private_key'))
                ->setPublicKey(\Config::get('pay.ali.ali_rsa_public_key'))
                ->setSellerId(\Config::get('pay.ali.account'))
                ->setNotifyUrl(\Config::get('pay.ali.notify_url'))
                ->setSubject(\Config::get('pay.ali.subject'))
                ->setBody(\Config::get('pay.ali.body'));

            return $pay;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('alipay.sdk');
    }

}
