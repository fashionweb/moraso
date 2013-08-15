<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Cart
{
    private static $_instance = null;
    private $_cart;

    public static function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    final private function __construct()
    {
        $this->_cart = new Zend_Session_Namespace('cart');

        if (!isset($this->_cart->articles)) {
            $this->_cart->articles = array();
        }

        if (!isset($this->_cart->properties)) {
            $this->_cart->properties = array();
        }
    }

    final private function __clone()
    {

    }

    public function addArticle($id, $qty = 1)
    {
        $oldQty = 0;

        if (isset($this->_cart->articles[$id]) && !empty($this->_cart->articles[$id])) {
            $oldQty = (int) $this->_cart->articles[$id];
        }

        $newQty = (int) ($oldQty + $qty);

        $this->_cart->articles[$id] = $newQty;

        if ($this->_cart->articles[$id] === $newQty) {
            return true;
        }

        return false;
    }

    public function removeArticle($id)
    {
        unset($this->_cart->articles[$id]);
    }

    public function getArticles()
    {
        return (object) $this->_cart->articles;
    }

    public function getArticleQty($id)
    {
        return (int) $this->_cart->articles[$id];
    }

    public function setProperty($key, $value)
    {
        $this->_cart->properties[$key] = $value;
    }

    public function getProperty($key)
    {
        if (isset($this->_cart->properties[$key])) {
            return $this->_cart->properties[$key];
        }
        
        return null;
    }

    public function getProperties()
    {
        return $this->_cart->properties;
    }

    public function getAmount()
    {
        $amount = 0;

        $articles = $this->getArticles();

        foreach ($articles as $idart => $qty) {
            $idartlang = Moraso_Util::getIdArtLang($idart);

            $articleProperties = Aitsu_Persistence_ArticleProperty::factory($idartlang)->load();

            $articlePropertyCart = (object) $articleProperties->cart;

            $amount = bcadd($amount, bcmul($articlePropertyCart->price->value, $qty, 2), 2);
        }

        return $amount;
    }

    public function createOrder()
    {
        $payment = $this->getProperty('payment');

        $this->_cart->order_id = Moraso_Db::put('_cart_order', 'order_id', array(
            'payment_method' => $payment['method'],
            'timestamp' => date('Y-m-d H:i:s')
            ));

        $articles = $this->getArticles();

        foreach ($articles as $id => $qty) {
            $id = Moraso_Db::put('_cart_order_has_article', 'id', array(
                'order_id' => $this->_cart->order_id,
                'article_id' => $id,
                'qty' => $qty
                ));
        }

        $this->_cart->customer_id = 29;
    }

    public function doCheckout()
    {
        Moraso_Db::put('_cart_order', 'order_id', array(
            'order_id' => $this->_cart->order_id,
            'ordered' => true
            ));

        $this->saveCustomerInformations();

        $this->sendMail();

        $this->flush();

        return true;
    }

    public function saveCustomerInformations()
    {
        $delivery = $this->getProperty('delivery');
        $billing = $this->getProperty('billing');

        Moraso_Db::put('_cart_order_has_customer', 'id', array(
            'order_id' => $this->_cart->order_id,
            'customer_id' => $this->_cart->customer_id,
            'delivery_title' => $delivery['title'],
            'delivery_firstname' => $delivery['name']['first'],
            'delivery_secondname' => $delivery['name']['middle'],
            'delivery_lastname' => $delivery['name']['last'],
            'delivery_street' => $delivery['street'],
            'delivery_housenumber' => $delivery['housenumber'],
            'delivery_postal_code' => $delivery['postal_code'],
            'delivery_city' => $delivery['city'],
            'delivery_telephone' => $delivery['telephone'],
            'delivery_fax' => $delivery['fax'],
            'delivery_email' => $delivery['email'],
            'billing_title' => $billing['title'],
            'billing_firstname' => $billing['name']['first'],
            'billing_secondname' => $billing['name']['middle'],
            'billing_lastname' => $billing['name']['last'],
            'billing_street' => $billing['street'],
            'billing_housenumber' => $billing['housenumber'],
            'billing_postal_code' => $billing['postal_code'],
            'billing_city' => $billing['city'],
            'billing_telephone' => $billing['telephone'],
            'billing_fax' => $billing['fax'],
            'billing_email' => $billing['email']
            ));
    }

    public function sendMail()
    {        
        $delivery = $this->getProperty('delivery');
        $billing = $this->getProperty('billing');

        Aitsu_Event::raise('frontend.cart.checkout', array(
            'delivery' => $delivery,
            'billing' => $billing,
            'receiver' => isset($billing['same_than_delivery']) && $billing['same_than_delivery'] === 'on' ? $delivery : $billing,
            'cart' => $this->_cart,
            'articles' => $this->getArticles()
            ));
    }

    public function getOrderId()
    {
        return $this->_cart->order_id;
    }

    public static function setPaymentStatus($order_id, $paymentData)
    {
        Moraso_Db::put('_cart_order', 'order_id', array(
            'order_id' => $order_id,
            'payed' => $paymentData['status'] === 'SUCCESS' ? true : false,
            'additional_info' => serialize($paymentData)
            ));
    }

    public static function getPaymentStatus($order_id)
    {
        return Aitsu_Db::fetchOne('SELECT payed FROM _cart_order WHERE order_id =:order_id', array('::order_id' => $order_id));
    }

    public function flush()
    {
        unset($this->_cart->articles);
    }

    public static function getPaymentStrategy($order_id = null, $payment_method = null)
    {
        if (!is_null($order_id)) {
            $payment_method = Moraso_Db::fetchOne('SELECT payment_method FROM _cart_order WHERE order_id =:order_id', array(
                ':order_id' => $order_id
                ));
        }

        switch ($payment_method) {
            case 'paypal':
            $paymentStrategy = new Moraso_Cart_Payment_Strategy_Paypal();
            break;
            case 'creditcard':
            $paymentStrategy = new Moraso_Cart_Payment_Strategy_Wirecard();
            break;
            default:
            $paymentStrategy = new Moraso_Cart_Payment_Strategy_Cash();
        }

        return $paymentStrategy;
    }

    public static function getPaymentStrategies()
    {
        $paymentStrategies = Aitsu_Util_Dir::scan(LIBRARY_PATH . '/Moraso/Cart/Payment/Strategy', '*.php');

        $strategies = array();
        foreach ($paymentStrategies as $paymentStrategy) {
            $strategies[] = pathinfo($paymentStrategy, PATHINFO_FILENAME);
        }

        return $strategies;
    }
}